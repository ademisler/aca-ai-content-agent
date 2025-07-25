<?php

use Action_Scheduler\WP_CLI\Migration_Command;
use Action_Scheduler\Migration\Controller;

/**
 * Class ActionScheduler
 *
 * @codeCoverageIgnore
 */
abstract class ActionScheduler {

	/**
	 * Plugin file path.
	 *
	 * @var string
	 */
	private static $plugin_file = '';

	/**
	 * ActionScheduler_ActionFactory instance.
	 *
	 * @var ActionScheduler_ActionFactory
	 */
	private static $factory = null;

	/**
	 * Data store is initialized.
	 *
	 * @var bool
	 */
	private static $data_store_initialized = false;

	/**
	 * Factory.
	 */
	public static function factory() {
		if ( ! isset( self::$factory ) ) {
			self::$factory = new ActionScheduler_ActionFactory();
		}
		return self::$factory;
	}

	/**
	 * Get Store instance.
	 */
	public static function store() {
		return ActionScheduler_Store::instance();
	}

	/**
	 * Get Lock instance.
	 */
	public static function lock() {
		return ActionScheduler_Lock::instance();
	}

	/**
	 * Get Logger instance.
	 */
	public static function logger() {
		return ActionScheduler_Logger::instance();
	}

	/**
	 * Get QueueRunner instance.
	 */
	public static function runner() {
		return ActionScheduler_QueueRunner::instance();
	}

	/**
	 * Get AdminView instance.
	 */
	public static function admin_view() {
		return ActionScheduler_AdminView::instance();
	}

	/**
	 * Get the absolute system path to the plugin directory, or a file therein
	 *
	 * @static
	 * @param string $path Path relative to plugin directory.
	 * @return string
	 */
	public static function plugin_path( $path ) {
		$base = dirname( self::$plugin_file );
		if ( $path ) {
			return trailingslashit( $base ) . $path;
		} else {
			return untrailingslashit( $base );
		}
	}

	/**
	 * Get the absolute URL to the plugin directory, or a file therein
	 *
	 * @static
	 * @param string $path Path relative to plugin directory.
	 * @return string
	 */
	public static function plugin_url( $path ) {
		return plugins_url( $path, self::$plugin_file );
	}

	/**
	 * Autoload.
	 *
	 * @param string $class Class name.
	 */
	public static function autoload( $class ) {
		$d           = DIRECTORY_SEPARATOR;
		$classes_dir = self::plugin_path( 'classes' . $d );
		$separator   = strrpos( $class, '\\' );
		if ( false !== $separator ) {
			if ( 0 !== strpos( $class, 'Action_Scheduler' ) ) {
				return;
			}
			$class = substr( $class, $separator + 1 );
		}

		if ( 'Deprecated' === substr( $class, -10 ) ) {
			$dir = self::plugin_path( 'deprecated' . $d );
		} elseif ( self::is_class_abstract( $class ) ) {
			$dir = $classes_dir . 'abstracts' . $d;
		} elseif ( self::is_class_migration( $class ) ) {
			$dir = $classes_dir . 'migration' . $d;
		} elseif ( 'Schedule' === substr( $class, -8 ) ) {
			$dir = $classes_dir . 'schedules' . $d;
		} elseif ( 'Action' === substr( $class, -6 ) ) {
			$dir = $classes_dir . 'actions' . $d;
		} elseif ( 'Schema' === substr( $class, -6 ) ) {
			$dir = $classes_dir . 'schema' . $d;
		} elseif ( strpos( $class, 'ActionScheduler' ) === 0 ) {
			$segments = explode( '_', $class );
			$type     = isset( $segments[1] ) ? $segments[1] : '';

			switch ( $type ) {
				case 'WPCLI':
					$dir = $classes_dir . 'WP_CLI' . $d;
					break;
				case 'DBLogger':
				case 'DBStore':
				case 'HybridStore':
				case 'wpPostStore':
				case 'wpCommentLogger':
					$dir = $classes_dir . 'data-stores' . $d;
					break;
				default:
					$dir = $classes_dir;
					break;
			}
		} elseif ( self::is_class_cli( $class ) ) {
			$dir = $classes_dir . 'WP_CLI' . $d;
		} elseif ( strpos( $class, 'CronExpression' ) === 0 ) {
			$dir = self::plugin_path( 'lib' . $d . 'cron-expression' . $d );
		} elseif ( strpos( $class, 'WP_Async_Request' ) === 0 ) {
			$dir = self::plugin_path( 'lib' . $d );
		} else {
			return;
		}

		if ( file_exists( $dir . "{$class}.php" ) ) {
			include $dir . "{$class}.php";
			return;
		}
	}

	/**
	 * Initialize the plugin
	 *
	 * @static
	 * @param string $plugin_file Plugin file path.
	 */
	public static function init( $plugin_file ) {
		self::$plugin_file = $plugin_file;
		spl_autoload_register( array( __CLASS__, 'autoload' ) );

		/**
		 * Fires in the early stages of Action Scheduler init hook.
		 */
		do_action( 'action_scheduler_pre_init' );

		require_once self::plugin_path( 'functions.php' );
		ActionScheduler_DataController::init();

		$store                      = self::store();
		$logger                     = self::logger();
		$runner                     = self::runner();
		$admin_view                 = self::admin_view();
		$recurring_action_scheduler = new ActionScheduler_RecurringActionScheduler();

		// Ensure initialization on plugin activation.
		if ( ! did_action( 'init' ) ) {
			// phpcs:ignore Squiz.PHP.CommentedOutCode
			add_action( 'init', array( $admin_view, 'init' ), 0, 0 ); // run before $store::init().
			add_action( 'init', array( $store, 'init' ), 1, 0 );
			add_action( 'init', array( $logger, 'init' ), 1, 0 );
			add_action( 'init', array( $runner, 'init' ), 1, 0 );
			add_action( 'init', array( $recurring_action_scheduler, 'init' ), 1, 0 );

			add_action(
				'init',
				/**
				 * Runs after the active store's init() method has been called.
				 *
				 * It would probably be preferable to have $store->init() (or it's parent method) set this itself,
				 * once it has initialized, however that would cause problems in cases where a custom data store is in
				 * use and it has not yet been updated to follow that same logic.
				 */
				function () {
					self::$data_store_initialized = true;

					/**
					 * Fires when Action Scheduler is ready: it is safe to use the procedural API after this point.
					 *
					 * @since 3.5.5
					 */
					do_action( 'action_scheduler_init' );
				},
				1
			);
		} else {
			$admin_view->init();
			$store->init();
			$logger->init();
			$runner->init();
			$recurring_action_scheduler->init();
			self::$data_store_initialized = true;

			/**
			 * Fires when Action Scheduler is ready: it is safe to use the procedural API after this point.
			 *
			 * @since 3.5.5
			 */
			do_action( 'action_scheduler_init' );
		}

		if ( apply_filters( 'action_scheduler_load_deprecated_functions', true ) ) {
			require_once self::plugin_path( 'deprecated/functions.php' );
		}

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::add_command( 'action-scheduler', 'ActionScheduler_WPCLI_Scheduler_command' );
			WP_CLI::add_command( 'action-scheduler', 'ActionScheduler_WPCLI_Clean_Command' );
			WP_CLI::add_command( 'action-scheduler action', '\Action_Scheduler\WP_CLI\Action_Command' );
			WP_CLI::add_command( 'action-scheduler', '\Action_Scheduler\WP_CLI\System_Command' );
			if ( ! ActionScheduler_DataController::is_migration_complete() && Controller::instance()->allow_migration() ) {
				$command = new Migration_Command();
				$command->register();
			}
		}

		/**
		 * Handle WP comment cleanup after migration.
		 */
		if ( is_a( $logger, 'ActionScheduler_DBLogger' ) && ActionScheduler_DataController::is_migration_complete() && ActionScheduler_WPCommentCleaner::has_logs() ) {
			ActionScheduler_WPCommentCleaner::init();
		}

		add_action( 'action_scheduler/migration_complete', 'ActionScheduler_WPCommentCleaner::maybe_schedule_cleanup' );
	}

	/**
	 * Check whether the AS data store has been initialized.
	 *
	 * @param string $function_name The name of the function being called. Optional. Default `null`.
	 * @return bool
	 */
	public static function is_initialized( $function_name = null ) {
		if ( ! self::$data_store_initialized && ! empty( $function_name ) ) {
			$message = sprintf(
				/* translators: %s function name. */
				__( '%s() was called before the Action Scheduler data store was initialized', 'action-scheduler' ),
				esc_attr( $function_name )
			);
			_doing_it_wrong( esc_html( $function_name ), esc_html( $message ), '3.1.6' );
		}

		return self::$data_store_initialized;
	}

	/**
	 * Determine if the class is one of our abstract classes.
	 *
	 * @since 3.0.0
	 *
	 * @param string $class The class name.
	 *
	 * @return bool
	 */
	protected static function is_class_abstract( $class ) {
		static $abstracts = array(
			'ActionScheduler'                            => true,
			'ActionScheduler_Abstract_ListTable'         => true,
			'ActionScheduler_Abstract_QueueRunner'       => true,
			'ActionScheduler_Abstract_Schedule'          => true,
			'ActionScheduler_Abstract_RecurringSchedule' => true,
			'ActionScheduler_Lock'                       => true,
			'ActionScheduler_Logger'                     => true,
			'ActionScheduler_Abstract_Schema'            => true,
			'ActionScheduler_Store'                      => true,
			'ActionScheduler_TimezoneHelper'             => true,
			'ActionScheduler_WPCLI_Command'              => true,
		);

		return isset( $abstracts[ $class ] ) && $abstracts[ $class ];
	}

	/**
	 * Determine if the class is one of our migration classes.
	 *
	 * @since 3.0.0
	 *
	 * @param string $class The class name.
	 *
	 * @return bool
	 */
	protected static function is_class_migration( $class ) {
		static $migration_segments = array(
			'ActionMigrator'  => true,
			'BatchFetcher'    => true,
			'DBStoreMigrator' => true,
			'DryRun'          => true,
			'LogMigrator'     => true,
			'Config'          => true,
			'Controller'      => true,
			'Runner'          => true,
			'Scheduler'       => true,
		);

		$segments = explode( '_', $class );
		$segment  = isset( $segments[1] ) ? $segments[1] : $class;

		return isset( $migration_segments[ $segment ] ) && $migration_segments[ $segment ];
	}

	/**
	 * Determine if the class is one of our WP CLI classes.
	 *
	 * @since 3.0.0
	 *
	 * @param string $class The class name.
	 *
	 * @return bool
	 */
	protected static function is_class_cli( $class ) {
		static $cli_segments = array(
			'QueueRunner'                             => true,
			'Command'                                 => true,
			'ProgressBar'                             => true,
			'\Action_Scheduler\WP_CLI\Action_Command' => true,
			'\Action_Scheduler\WP_CLI\System_Command' => true,
		);

		$segments = explode( '_', $class );
		$segment  = isset( $segments[1] ) ? $segments[1] : $class;

		return isset( $cli_segments[ $segment ] ) && $cli_segments[ $segment ];
	}

	/**
	 * Clone.
	 */
	final public function __clone() {
		trigger_error( 'Singleton. No cloning allowed!', E_USER_ERROR ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
	}

	/**
	 * Wakeup.
	 */
	final public function __wakeup() {
		trigger_error( 'Singleton. No serialization allowed!', E_USER_ERROR ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
	}

	/**
	 * Construct.
	 */
	final private function __construct() {}

	/** Deprecated **/

	/**
	 * Get DateTime object.
	 *
	 * @param null|string $when     Date/time string.
	 * @param string      $timezone Timezone string.
	 */
	public static function get_datetime_object( $when = null, $timezone = 'UTC' ) {
		_deprecated_function( __METHOD__, '2.0', 'wcs_add_months()' );
		return as_get_datetime_object( $when, $timezone );
	}

	/**
	 * Issue deprecated warning if an Action Scheduler function is called in the shutdown hook.
	 *
	 * @param string $function_name The name of the function being called.
	 * @deprecated 3.1.6.
	 */
	public static function check_shutdown_hook( $function_name ) {
		_deprecated_function( __FUNCTION__, '3.1.6' );
	}
}
