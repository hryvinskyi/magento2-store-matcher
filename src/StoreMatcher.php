<?php
/**
 * Copyright (c) 2025. Volodymyr Hryvinskyi. All rights reserved.
 * Author: Volodymyr Hryvinskyi <volodymyr@hryvinskyi.com>
 * GitHub: https://github.com/hryvinskyi
 */

declare(strict_types=1);

namespace Hryvinskyi\StoreMatcher;

use Magento\Store\Model\StoreManager;

use function array_replace_recursive;
use function dirname;
use function file_exists;

class StoreMatcher
{
    private static ?array $config = null;
    private static ?array $hostMap = null;

    /**
     * Match HTTP_HOST and set store/website code in params array
     *
     * @param array|null $params Reference to params array to modify (defaults to $_SERVER)
     * @param string|null $configPath Path to store-hosts.php configuration file
     * @return array Modified params array with PARAM_RUN_TYPE and PARAM_RUN_CODE set
     */
    public static function match(array|null &$params = null, string|null $configPath = null): array
    {
        // Default to $_SERVER if no params provided
        if ($params === null) {
            $params = &$_SERVER;
        }

        $httpHost = $params['HTTP_HOST'] ?? '';

        // Early return if no host
        if ($httpHost === '') {
            return $params;
        }

        // Load and build host map once
        if (self::$hostMap === null) {
            self::loadConfig($configPath);
            self::buildHostMap();
        }

        // Check if host matches any store/website
        if (isset(self::$hostMap[$httpHost])) {
            [$type, $code] = self::$hostMap[$httpHost];
            $params[StoreManager::PARAM_RUN_TYPE] = $type;
            $params[StoreManager::PARAM_RUN_CODE] = $code;
        }

        return $params;
    }

    /**
     * Load configuration from files
     *
     * @param string|null $configPath
     * @return void
     */
    private static function loadConfig(?string $configPath): void
    {
        if ($configPath === null) {
            $configPath = dirname(__DIR__, 4) . '/app/etc/store-hosts.php';
        }

        if (!file_exists($configPath)) {
            self::$config = ['store' => [], 'website' => []];
            return;
        }

        self::$config = require $configPath;

        // Merge with local overrides if exists
        $localConfigPath = dirname($configPath) . '/store-hosts.local.php';
        if (file_exists($localConfigPath)) {
            $local = require $localConfigPath;
            self::$config = array_replace_recursive(self::$config, $local);
        }
    }

    /**
     * Build reverse host-to-code map
     *
     * @return void
     */
    private static function buildHostMap(): void
    {
        self::$hostMap = [];

        // Process stores first (higher priority)
        foreach (self::$config['store'] ?? [] as $code => $hosts) {
            foreach ($hosts as $host) {
                self::$hostMap[$host] = ['store', $code];
            }
        }

        // Process websites (lower priority - won't override stores)
        foreach (self::$config['website'] ?? [] as $code => $hosts) {
            foreach ($hosts as $host) {
                // Only set if not already set by a store
                if (!isset(self::$hostMap[$host])) {
                    self::$hostMap[$host] = ['website', $code];
                }
            }
        }
    }

    /**
     * Reset cached configuration (useful for testing)
     *
     * @return void
     */
    public static function resetConfig(): void
    {
        self::$config = null;
        self::$hostMap = null;
    }
}