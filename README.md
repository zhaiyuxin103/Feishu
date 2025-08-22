# 飞书 SDK

[![Latest Version on Packagist](https://img.shields.io/packagist/v/zhaiyuxin/feishu?style=for-the-badge)](https://packagist.org/packages/zhaiyuxin/feishu)
[![Total Downloads on Packagist](https://img.shields.io/packagist/dt/zhaiyuxin/feishu?style=for-the-badge)](https://packagist.org/packages/zhaiyuxin/feishu)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=for-the-badge)](LICENSE)
[![Build Status](https://img.shields.io/github/actions/workflow/status/zhaiyuxin103/feishu/tests.yml?style=for-the-badge)](https://github.com/zhaiyuxin103/feishu/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/zhaiyuxin103/feishu?style=for-the-badge)](https://codecov.io/gh/zhaiyuxin103/feishu)

一个简单易用的 PHP 飞书 SDK，支持发送消息、管理群组和用户等功能。

## ✨ 特性

- 🚀 简洁的 API 设计，快速上手
- 📨 发送消息（支持文本、图片、文件、富文本等多种消息类型）
- 👥 群组管理（搜索、创建等）
- 👤 用户管理（获取用户信息和 ID）
- 🔐 访问令牌管理（自动获取和刷新）
- 🛡️ 类型安全（使用 PHP 8.1+ 枚举类型）
- 🧪 完整测试（使用 Pest 进行单元测试）
- 🏗️ Laravel 集成支持

## 📋 系统要求

- PHP >= 8.0
- Laravel >= 10.0
- Composer

## 🚀 快速开始

### 安装

```bash
composer require zhaiyuxin/feishu
```

### 基本使用

```php
use Yuxin\Feishu\Message;
use Yuxin\Feishu\Group;
use Yuxin\Feishu\Enums\MessageTypeEnum;
use Yuxin\Feishu\Enums\ReceiveIDTypeEnum;
use Yuxin\Feishu\Enums\UserIDTypeEnum;

// 发送消息
$message = new Message('app_id', 'app_secret');
$message->send('user_id', MessageTypeEnum::Text->value, 'Hello, World!');

// 群组操作
$group = new Group('app_id', 'app_secret');
$chatId = $group->search('群组名称');
$message->send(
    $chatId,
    MessageTypeEnum::Text->value,
    '群组消息',
    UserIDTypeEnum::OpenID->value,
    ReceiveIDTypeEnum::ChatID->value,
);
```

### Laravel 集成

配置环境变量：

在 `.env` 文件中添加飞书应用配置：

```env
FEISHU_APP_ID=your_app_id
FEISHU_APP_SECRET=your_app_secret
```

使用服务容器：

```php
// 发送给用户
app('feishu.message')->send('user_id', 'text', 'Hello, world!');

// 发送给群组
$group = app('feishu.group')->search('群组名称');
app('feishu.message')->send($group, 'text', '群组消息');
```

> [!TIP]
> 💡 **提示**: 关于消息内容结构的详细说明，请参考 [飞书官方文档](https://open.feishu.cn/document/server-docs/im-v1/message-content-description/create_json)。

## 📦 项目结构

```
src/                    # 核心源码
├── AccessToken.php    # 访问令牌管理
├── Group.php          # 群组管理
├── Message.php        # 消息发送
├── User.php           # 用户管理
├── ServiceProvider.php # Laravel 服务提供者
├── Contracts/         # 接口定义
├── Enums/            # 枚举类型
└── Exceptions/       # 异常处理

config/                # 配置文件
└── feishu.php        # 飞书配置

workbench/             # Laravel 集成示例
└── app/Http/Controllers/
    └── MessageController.php

docs/                  # 详细文档
```

## 🔧 开发

```bash
# 安装依赖
composer install

# 代码检查
composer lint

# 运行测试
composer test

# 启动开发服务器
composer run serve
```

## 📚 文档

详细的使用文档和 API 参考请查看 [docs/](https://feishu-nine.vercel.app/) 。

## 📄 许可证

MIT License
