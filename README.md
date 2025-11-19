# Magento 2 / Adobe Commerce Store/Website Matcher

Automatic store/website code matcher for multi-store Magento 2 / Adobe Commerce setups based on HTTP_HOST.

[![Latest Stable Version](https://poser.pugx.org/hryvinskyi/magento2-store-matcher/v/stable)](https://packagist.org/packages/hryvinskyi/magento2-store-matcher)
[![Total Downloads](https://poser.pugx.org/hryvinskyi/magento2-store-matcher/downloads)](https://packagist.org/packages/hryvinskyi/magento2-store-matcher)
[![PayPal donate button](https://img.shields.io/badge/paypal-donate-yellow.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=volodymyr%40hryvinskyi%2ecom&lc=UA&item_name=Magento%202%20Defer%20Javascript&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted "Donate once-off to this project using Paypal")
[![Latest Unstable Version](https://poser.pugx.org/hryvinskyi/magento2-store-matcher/v/unstable)](https://packagist.org/packages/oakcms/oakcms)
[![License](https://poser.pugx.org/hryvinskyi/magento2-store-matcher/license)](https://packagist.org/packages/hryvinskyi/magento2-store-matcher)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/hryvinskyi/magento2-store-matcher/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/hryvinskyi/magento2-store-matcher/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/hryvinskyi/magento2-store-matcher/badges/build.png?b=master)](https://scrutinizer-ci.com/g/hryvinskyi/magento2-store-matcher/build-status/master)


## Overview

This Composer package automatically sets the correct Magento store or website code based on the incoming HTTP_HOST header,
eliminating the need to manually configure store matching in `pub/index.php`.

## Features

- **Zero configuration** - Works automatically after installation
- **Performance optimized** - Stores/Websites can be cached by opcache (static array in app/etc/store-hosts.php)
- **Local overrides** - Support for environment-specific configuration
- **Composer autoload** - Uses standard Composer autoload mechanism

## Installation

1. Run the following Composer command in your Magento 2 / Adobe Commerce project root:

```bash
composer require hryvinskyi/magento2-store-matcher
```

The post-install script will automatically create `app/etc/store-hosts.php` and `app/etc/store-hosts.local.php` if they don't exist.

## Configuration

### Main Configuration

Edit `app/etc/store-hosts.php` to define your store and website host mappings:

```php
return [
    'store' => [
        'default' => ['www.example.com', 'example.com'],
        'french' => ['fr.example.com', 'french.example.com'],
        'german' => ['de.example.com', 'german.example.com'],
        // ... more stores
    ],
    'website' => [
        'base' => ['www.example.com'],
        'europe' => ['eu.example.com'],
        // ... more websites
    ],
];
```

### Local Overrides

For environment-specific configuration, create `app/etc/store-hosts.local.php`:

```php
return [
    'store' => [
        'default' => ['test.example.test', 'localhost'],
        'en_store' => ['en.example.test'],
    ],
];
```

This file will be automatically merged with the main configuration and can be gitignored.

## How It Works

1. Composer's autoload mechanism loads `src/autoload.php` on every request
2. The autoload file checks if running in web context (not CLI)
3. It calls `StoreMatcher::match()` which contains all the matching logic
4. The matcher loads configuration from `app/etc/store-hosts.php`
5. It modifies `$_SERVER` with `PARAM_RUN_TYPE` and `PARAM_RUN_CODE`
6. Magento bootstrap uses these values to determine the store/website

## Requirements

- PHP >= 8.1
- Magento 2.4+

## License

MIT

## Author

Volodymyr Hryvinskyi <volodymyr@hryvinskyi.com>
