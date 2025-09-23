# Feishu SDK

[![Latest Version on Packagist](https://img.shields.io/packagist/v/zhaiyuxin/feishu?style=for-the-badge)](https://packagist.org/packages/zhaiyuxin/feishu)
[![Total Downloads on Packagist](https://img.shields.io/packagist/dt/zhaiyuxin/feishu?style=for-the-badge)](https://packagist.org/packages/zhaiyuxin/feishu)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=for-the-badge)](LICENSE)
[![Build Status](https://img.shields.io/github/actions/workflow/status/zhaiyuxin103/feishu/tests.yml?style=for-the-badge)](https://github.com/zhaiyuxin103/feishu/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/zhaiyuxin103/feishu?style=for-the-badge)](https://codecov.io/gh/zhaiyuxin103/feishu)

A clean and powerful PHP SDK for Feishu (Lark) API with Laravel integration.

## Installation

```bash
composer require zhaiyuxin/feishu
```

## Quick Start

```php
use Yuxin\Feishu\Facades\Feishu;

// Send a message
Feishu::message()->send('user_id', 'text', 'Hello, World!');

// Search for a group
$chatId = Feishu::group()->search('group_name');

// Get user info
$userInfo = Feishu::user()->getInfo('user_id');

// Get access token
$token = Feishu::accessToken()->getToken();
```

## Documentation

For complete documentation, visit our [documentation site](https://feishu-nine.vercel.app/).

## Contributing

Please see [CONTRIBUTING.md](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## License

The Feishu SDK is open-sourced software licensed under the [MIT license](LICENSE).
