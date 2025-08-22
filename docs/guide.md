# 使用指南

## 目录

- [安装](#安装)
- [配置](#配置)
- [基本使用](#基本使用)
- [消息类型](#消息类型)
- [Laravel 集成](#laravel-集成)
- [错误处理](#错误处理)
- [最佳实践](#最佳实践)

## 安装

### 通过 Composer 安装

```bash
composer require zhaiyuxin/feishu
```

### 系统要求

- PHP 8.1 或更高版本
- Guzzle HTTP 客户端
- Laravel 12+（可选，用于 Laravel 集成）

## 配置

### 获取飞书应用凭证

1. 登录 [飞书开放平台](https://open.feishu.cn/)
2. 创建或选择您的应用
3. 在应用详情页面获取 `App ID` 和 `App Secret`

### 环境变量配置

在您的 `.env` 文件中添加以下配置：

```env
FEISHU_APP_ID=cli_xxxxxxxxxxxxx
FEISHU_APP_SECRET=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### 手动配置

如果您不想使用环境变量，可以在代码中直接传入：

```php
$message = new Message('your_app_id', 'your_app_secret');
```

## 基本使用

### 发送文本消息

```php
use Yuxin\Feishu\Message;
use Yuxin\Feishu\Enums\MessageTypeEnum;

$message = new Message('app_id', 'app_secret');

// 发送给用户
$message->send(
    'user_open_id',
    MessageTypeEnum::Text->value,
    'Hello, World!'
);

// 发送给群组
$message->send(
    'chat_id',
    MessageTypeEnum::Text->value,
    '这是一条群组消息'
);
```

### 搜索群组

```php
use Yuxin\Feishu\Group;

$group = new Group('app_id', 'app_secret');

// 搜索群组并获取群组ID
$chatId = $group->search('测试群组');
echo "群组ID: " . $chatId;
```

### 获取用户ID

```php
use Yuxin\Feishu\User;
use Yuxin\Feishu\Enums\UserIDTypeEnum;

$user = new User('app_id', 'app_secret');

// 根据邮箱获取用户ID
$userId = $user->getId('user@example.com', UserIDTypeEnum::OpenID->value);

// 根据手机号获取用户ID
$userId = $user->getId('13800138000', UserIDTypeEnum::UnionID->value);
```

## 消息类型

:::tip
💡 **提示**: 关于消息内容结构的详细说明，请参考 [飞书官方文档](https://open.feishu.cn/document/server-docs/im-v1/message-content-description/create_json)。
:::

### 文本消息

最简单的消息类型，支持纯文本内容。

```php
$message->send(
    'user_id',
    MessageTypeEnum::Text->value,
    '这是一条文本消息'
);
```

### 富文本消息

支持更丰富的内容格式，包括标题、段落、列表等。

```php
$content = [
    'zh_cn' => [
        'title' => '消息标题',
        'content' => [
            [
                'tag' => 'text',
                'text' => '这是第一段文本'
            ],
            [
                'tag' => 'a',
                'text' => '这是一个链接',
                'href' => 'https://example.com'
            ],
            [
                'tag' => 'at',
                'user_name' => '用户名'
            ]
        ]
    ]
];

$message->send(
    'user_id',
    MessageTypeEnum::Post->value,
    $content
);
```

### 图片消息

发送图片消息需要先上传图片到飞书服务器。

```php
// 注意：这里需要先实现图片上传功能
$content = [
    'image_key' => 'img_xxxxxxxxxxxxx'
];

$message->send(
    'user_id',
    MessageTypeEnum::Image->value,
    $content
);
```

### 文件消息

发送文件消息需要先上传文件到飞书服务器。

```php
$content = [
    'file_key' => 'file_xxxxxxxxxxxxx'
];

$message->send(
    'user_id',
    MessageTypeEnum::File->value,
    $content
);
```

### 卡片消息

发送交互式卡片消息。

```php
$content = [
    'config' => [
        'wide_screen_mode' => true
    ],
    'header' => [
        'title' => [
            'tag' => 'plain_text',
            'content' => '卡片标题'
        ]
    ],
    'elements' => [
        [
            'tag' => 'div',
            'text' => [
                'tag' => 'plain_text',
                'content' => '卡片内容'
            ]
        ]
    ]
];

$message->send(
    'user_id',
    MessageTypeEnum::Interactive->value,
    $content
);
```

## Laravel 集成

### 安装 Laravel 服务提供者

在 Laravel 项目中，SDK 会自动注册服务提供者。如果没有自动注册，请在 `config/app.php` 中手动添加：

```php
'providers' => [
    // ...
    Yuxin\Feishu\ServiceProvider::class,
],
```

### 发布配置文件

```bash
php artisan vendor:publish --tag=feishu-config
```

### 使用服务容器

```php
use Yuxin\Feishu\Enums\MessageTypeEnum;
use Yuxin\Feishu\Enums\ReceiveIDTypeEnum;
use Yuxin\Feishu\Enums\UserIDTypeEnum;

// 发送消息
app('feishu.message')->send(
    'user_id',
    MessageTypeEnum::Text->value,
    'Hello from Laravel!'
);

// 搜索群组
$group = app('feishu.group')->search('测试群组');

// 发送群组消息
app('feishu.message')->send(
    $group,
    MessageTypeEnum::Text->value,
    '群组消息',
    UserIDTypeEnum::OpenID->value,
    ReceiveIDTypeEnum::ChatID->value
);
```

### 在控制器中使用

```php
<?php

namespace App\Http\Controllers;

use Yuxin\Feishu\Enums\MessageTypeEnum;

class NotificationController extends Controller
{
    public function sendNotification()
    {
        try {
            app('feishu.message')->send(
                'user_id',
                MessageTypeEnum::Text->value,
                '您有一条新的通知'
            );

            return response()->json(['message' => '通知发送成功']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
```

## 错误处理

### 基本错误处理

```php
use Yuxin\Feishu\Exceptions\HttpException;
use Yuxin\Feishu\Exceptions\InvalidArgumentException;
use Yuxin\Feishu\Exceptions\GroupNotFoundException;

try {
    $message->send('user_id', 'text', 'Hello');
} catch (HttpException $e) {
    // API 调用失败
    echo "API错误: " . $e->getMessage();
    echo "错误代码: " . $e->getCode();
} catch (InvalidArgumentException $e) {
    // 参数错误
    echo "参数错误: " . $e->getMessage();
} catch (GroupNotFoundException $e) {
    // 群组未找到
    echo "群组未找到: " . $e->getMessage();
} catch (\Exception $e) {
    // 其他错误
    echo "未知错误: " . $e->getMessage();
}
```

### Laravel 中的错误处理

```php
use Illuminate\Support\Facades\Log;

try {
    app('feishu.message')->send('user_id', 'text', 'Hello');
} catch (\Exception $e) {
    Log::error('飞书消息发送失败', [
        'error' => $e->getMessage(),
        'user_id' => 'user_id'
    ]);

    // 可以选择重试或发送到备用通知渠道
}
```

## 最佳实践

### 1. 使用枚举类型

始终使用枚举类型来确保类型安全：

```php
use Yuxin\Feishu\Enums\MessageTypeEnum;
use Yuxin\Feishu\Enums\UserIDTypeEnum;
use Yuxin\Feishu\Enums\ReceiveIDTypeEnum;

// 推荐
$message->send(
    'user_id',
    MessageTypeEnum::Text->value,
    'Hello'
);

// 不推荐
$message->send('user_id', 'text', 'Hello');
```

### 2. 错误处理

始终包含适当的错误处理：

```php
try {
    $result = $message->send('user_id', MessageTypeEnum::Text->value, 'Hello');
    if ($result) {
        // 发送成功
    }
} catch (\Exception $e) {
    // 记录错误并处理
    Log::error('消息发送失败', ['error' => $e->getMessage()]);
}
```

### 3. 配置管理

使用环境变量管理敏感信息：

```php
// 推荐
$message = new Message(
    config('feishu.app_id'),
    config('feishu.app_secret')
);

// 不推荐
$message = new Message('hardcoded_app_id', 'hardcoded_app_secret');
```

### 4. 性能优化

对于频繁使用的实例，考虑使用单例模式或依赖注入：

```php
// 在 Laravel 中使用服务容器
$message = app('feishu.message');

// 或者使用依赖注入
public function __construct(
    private Message $message
) {}
```

### 5. 消息内容格式化

对于复杂的消息内容，使用辅助函数：

```php
function formatNotificationMessage($title, $content, $actionUrl = null)
{
    $elements = [
        [
            'tag' => 'div',
            'text' => [
                'tag' => 'plain_text',
                'content' => $content
            ]
        ]
    ];

    if ($actionUrl) {
        $elements[] = [
            'tag' => 'action',
            'actions' => [
                [
                    'tag' => 'button',
                    'text' => [
                        'tag' => 'plain_text',
                        'content' => '查看详情'
                    ],
                    'url' => $actionUrl
                ]
            ]
        ];
    }

    return [
        'config' => [
            'wide_screen_mode' => true
        ],
        'header' => [
            'title' => [
                'tag' => 'plain_text',
                'content' => $title
            ]
        ],
        'elements' => $elements
    ];
}

// 使用
$content = formatNotificationMessage('系统通知', '您有一条新的消息', 'https://example.com');
$message->send('user_id', MessageTypeEnum::Interactive->value, $content);
```
