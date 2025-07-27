<?php

/**
 * Define the internationalization functionality
 */
class ACA_i18n {

    /**
     * Load the plugin text domain for translation
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'aca-ai-content-agent',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }
}