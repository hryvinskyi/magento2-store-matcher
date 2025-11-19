<?php
/**
 * Copyright (c) 2025. Volodymyr Hryvinskyi. All rights reserved.
 * Author: Volodymyr Hryvinskyi <volodymyr@hryvinskyi.com>
 * GitHub: https://github.com/hryvinskyi
 */

declare(strict_types=1);

use Hryvinskyi\StoreMatcher\StoreMatcher;

// Only run in web request context (not CLI)
if (PHP_SAPI === 'cli' || !isset($_SERVER['HTTP_HOST'])) {
    return;
}

StoreMatcher::match();