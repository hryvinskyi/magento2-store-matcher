#!/usr/bin/env php
<?php
/**
 * Copyright (c) 2025. Volodymyr Hryvinskyi. All rights reserved.
 * Author: Volodymyr Hryvinskyi <volodymyr@hryvinskyi.com>
 * GitHub: https://github.com/hryvinskyi
 */

declare(strict_types=1);

// Determine paths
$vendorDir = dirname(__DIR__, 3); // packages/hryvinskyi/magento2-store-matcher -> vendor
$basePath = dirname($vendorDir); // vendor -> project root
$etcDir = $basePath . '/app/etc';
$storeHostsFile = $etcDir . '/store-hosts.php';
$storeHostsLocalFile = $etcDir . '/store-hosts.local.php';

echo "Hryvinskyi Store Matcher: Checking installation...\n";

// Check if app/etc directory exists
if (!is_dir($etcDir)) {
    echo "  Warning: app/etc directory not found. Skipping installation.\n";
    exit(0);
}

// Check if app/etc directory is writable
if (!is_writable($etcDir)) {
    echo "  Warning: app/etc directory is not writable. Cannot create configuration files.\n";
    echo "  Please create app/etc/store-hosts.php manually.\n";
    exit(0);
}

// Configuration template with instructions
$configTemplate = <<<'PHP'
<?php
/**
 * Store and Website Host Configuration
 *
 * This file contains configuration data mapping hostnames to store/website codes.
 * For environment-specific overrides, create store-hosts.local.php (gitignored).
 *
 * Usage:
 * - Map hostnames to store codes for specific store views
 * - Map hostnames to website codes for entire websites
 * - Store matches take precedence over website matches
 *
 * Example Configuration:
 * return [
 *     'store' => [
 *         'default' => ['www.example.com', 'example.com'],
 *         'french' => ['fr.example.com', 'french.example.com'],
 *         'german' => ['de.example.com', 'german.example.com'],
 *     ],
 *     'website' => [
 *         'base' => ['www.example.com'],
 *         'europe' => ['eu.example.com'],
 *     ],
 * ];
 *
 * Local Overrides (store-hosts.local.php):
 * return [
 *     'store' => [
 *         'default' => ['test.example.test', 'example.test'],
 *     ],
 * ];
 */

declare(strict_types=1);

return [
    'store' => [],
    'website' => [],
];

PHP;

$localConfigTemplate = <<<'PHP'
<?php
/**
 * Local Store and Website Host Configuration
 *
 * This file is for environment-specific host mappings and is gitignored.
 * It will be merged with store-hosts.php configuration using array_replace_recursive.
 *
 * Use this file for:
 * - Local development hostnames (*.testsite, *.test, localhost, etc.)
 * - Environment-specific staging/production URLs
 * - Temporary testing configurations
 *
 * Example:
 * return [
 *     'store' => [
 *         'default' => ['test.example.test', 'localhost'],
 *         'en_store' => ['en.example.test'],
 *     ],
 * ];
 */

declare(strict_types=1);

return [
    'store' => [],
    'website' => [],
];

PHP;

// Check if store-hosts.php exists
if (file_exists($storeHostsFile)) {
    echo "  Configuration file found: app/etc/store-hosts.php\n";
} else {
    // store-hosts.php doesn't exist - create configuration with template
    echo "  Configuration file not found. Creating configuration with instructions...\n";
    if (file_put_contents($storeHostsFile, $configTemplate) !== false) {
        echo "  Created: app/etc/store-hosts.php\n";
    } else {
        echo "  Error: Failed to create app/etc/store-hosts.php\n";
        exit(1);
    }
}

// Check if store-hosts.local.php exists
if (!file_exists($storeHostsLocalFile)) {
    echo "  Creating local configuration file...\n";
    if (file_put_contents($storeHostsLocalFile, $localConfigTemplate) !== false) {
        echo "  Created: app/etc/store-hosts.local.php\n";
    } else {
        echo "  Warning: Failed to create app/etc/store-hosts.local.php\n";
    }
}

echo "  Installation complete!\n";
exit(0);