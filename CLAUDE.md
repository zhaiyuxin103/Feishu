# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a PHP Feishu (Lark) SDK library that provides a clean interface for interacting with Feishu's messaging and group management APIs. The library supports sending messages, managing groups and users, with automatic access token management.

## Development Commands

### Essential Commands

- `composer install` - Install dependencies
- `composer lint` - Run code linting (Pint + PHPStan)
- `composer test` - Run tests with Pest
- `composer serve` - Start development server (Laravel workbench)

### Testing

- Tests use Pest framework with Mockery for HTTP mocking
- Run single test: `./vendor/bin/pest tests/Unit/MessageTest.php`
- Coverage and test configuration in `phpunit.xml`

### Code Quality

- Laravel Pint for code formatting
- PHPStan for static analysis
- Both run via `composer lint`

## Architecture

### Core Components

**Main Classes:**

- `Message` (`src/Message.php`) - Primary messaging functionality with support for multiple message types
- `AccessToken` (`src/AccessToken.php`) - Handles Feishu API authentication and token management
- `Group` (`src/Group.php`) - Group search and management operations
- `User` (`src/User.php`) - User information and ID management
- `ServiceProvider` (`src/ServiceProvider.php`) - Laravel integration with dependency injection

**Supporting Structure:**

- `Contracts/` - Interface definitions for testability
- `Enums/` - Type-safe enums for message types, user ID types, and receive ID types
- `Exceptions/` - Custom exception classes for API errors and validation

### Key Design Patterns

1. **Dependency Injection**: All main classes accept optional injected dependencies for testability
2. **Enum-based Validation**: Uses PHP 8.1+ enums for type-safe parameter validation
3. **Guzzle HTTP Client**: Configurable HTTP client with consistent base URI and options
4. **Laravel Integration**: Full service container support with automatic bindings

### API Integration

- **Base URL**: `https://open.feishu.cn/open-apis/`
- **Authentication**: Tenant access token obtained via app credentials
- **Message Types**: Text, image, file, post, audio, media, sticker, interactive, share_chat, share_user, system
- **ID Types**: Support for union_id, open_id, email, chat_id, user_id with proper validation

### Laravel Integration

The package provides automatic Laravel integration through:

- Service provider with singleton bindings
- Configuration file publishing
- Environment variable support (`FEISHU_APP_ID`, `FEISHU_APP_SECRET`)
- Service container aliases: `feishu.message`, `feishu.group`, `feishu.user`, `feishu.access_token`

### Testing Strategy

- **Unit Tests**: Comprehensive test coverage for all main classes
- **HTTP Mocking**: Uses Mockery to mock Guzzle HTTP client responses
- **Exception Testing**: Tests all error conditions and API failures
- **Laravel Testing**: Uses Orchestra Testbench for Laravel integration tests

### Configuration

- `config/feishu.php` - Main configuration file
- Environment variables: `FEISHU_APP_ID`, `FEISHU_APP_SECRET`
- Workbench setup in `workbench/` for development and testing

## Working with the Codebase

### Adding New Features

1. Follow existing enum patterns for new types
2. Implement corresponding interfaces in `Contracts/`
3. Add comprehensive tests with Pest
4. Update service provider bindings if needed
5. Document new features following existing PHPDoc standards

### Error Handling

- Use custom exceptions from `src/Exceptions/`
- Validate parameters using enum cases
- Handle HTTP errors gracefully with proper exception messages
- Test all error paths in unit tests

### Message Content Structure

- Message content should follow Feishu's official documentation structure
- Support both string (JSON) and array content formats
- Reference: [https://open.feishu.cn/document/server-docs/im-v1/message-content-description/create_json](https://open.feishu.cn/document/server-docs/im-v1/message-content-description/create_json)
