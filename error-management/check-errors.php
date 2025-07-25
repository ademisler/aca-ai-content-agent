<?php
/**
 * Simple syntax error scanner for ACA - AI Content Agent.
 *
 * Recursively scans all PHP files under the plugin directory and runs
 * `php -l` on each file. Any syntax errors are recorded in
 * `error-management/error-report.log`.
 */

$pluginDir = dirname(__DIR__);
$iterator  = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($pluginDir)
);

$errors = [];
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $command = 'php -l ' . escapeshellarg($file->getPathname()) . ' 2>&1';
        $output  = shell_exec($command);
        if ($output === null) {
            $errors[$file->getPathname()] = 'Failed to execute php -l';
        } elseif (strpos($output, 'No syntax errors detected') === false) {
            $errors[$file->getPathname()] = trim($output);
        }
    }
}

$logFile = __DIR__ . '/error-report.log';
$logHandle = fopen($logFile, 'w');
foreach ($errors as $path => $message) {
    fwrite($logHandle, $path . "\n" . $message . "\n\n");
}

fclose($logHandle);

if (empty($errors)) {
    echo "No syntax errors detected.\n";
} else {
    echo "Syntax errors found in " . count($errors) . " file(s).\n";
    echo "Details written to {$logFile}\n";
}
