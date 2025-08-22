# 示例代码

## 目录

- [基本示例](#基本示例)
- [消息类型示例](#消息类型示例)
- [群组管理示例](#群组管理示例)
- [用户管理示例](#用户管理示例)
- [Laravel 集成示例](#laravel-集成示例)
- [错误处理示例](#错误处理示例)

## 基本示例

### 发送简单文本消息

```php
<?php

require_once 'vendor/autoload.php';

use Yuxin\Feishu\Message;
use Yuxin\Feishu\Enums\MessageTypeEnum;

// 创建消息实例
$message = new Message('your_app_id', 'your_app_secret');

// 发送文本消息给用户
$message->send(
    'user_open_id',
    MessageTypeEnum::Text->value,
    'Hello, World!'
);

echo "消息发送成功！\n";
```

### 发送群组消息

```php
<?php

require_once 'vendor/autoload.php';

use Yuxin\Feishu\Message;
use Yuxin\Feishu\Group;
use Yuxin\Feishu\Enums\MessageTypeEnum;
use Yuxin\Feishu\Enums\ReceiveIDTypeEnum;
use Yuxin\Feishu\Enums\UserIDTypeEnum;

// 创建实例
$message = new Message('your_app_id', 'your_app_secret');
$group = new Group('your_app_id', 'your_app_secret');

// 搜索群组
$chatId = $group->search('测试群组');

// 发送群组消息
$message->send(
    $chatId,
    MessageTypeEnum::Text->value,
    '这是一条群组消息',
    UserIDTypeEnum::OpenID->value,
    ReceiveIDTypeEnum::ChatID->value
);

echo "群组消息发送成功！\n";
```

## 消息类型示例

:::tip
💡 **提示**: 关于消息内容结构的详细说明，请参考 [飞书官方文档](https://open.feishu.cn/document/server-docs/im-v1/message-content-description/create_json)。
:::

### 富文本消息

```php
<?php

use Yuxin\Feishu\Message;
use Yuxin\Feishu\Enums\MessageTypeEnum;

$message = new Message('your_app_id', 'your_app_secret');

// 构建富文本内容
$content = [
    'zh_cn' => [
        'title' => '系统通知',
        'content' => [
            [
                'tag' => 'text',
                'text' => '您有一条新的系统通知'
            ],
            [
                'tag' => 'a',
                'text' => '点击查看详情',
                'href' => 'https://example.com/notification'
            ],
            [
                'tag' => 'at',
                'user_name' => '管理员'
            ]
        ]
    ]
];

$message->send(
    'user_open_id',
    MessageTypeEnum::Post->value,
    $content
);
```

### 卡片消息

```php
<?php

use Yuxin\Feishu\Message;
use Yuxin\Feishu\Enums\MessageTypeEnum;

$message = new Message('your_app_id', 'your_app_secret');

// 构建卡片消息内容
$content = [
    'config' => [
        'wide_screen_mode' => true,
        'enable_forward' => true
    ],
    'header' => [
        'title' => [
            'tag' => 'plain_text',
            'content' => '任务提醒'
        ],
        'template' => 'blue'
    ],
    'elements' => [
        [
            'tag' => 'div',
            'text' => [
                'tag' => 'lark_md',
                'content' => '**任务名称**: 完成项目文档\n**截止时间**: 2024-01-15 18:00\n**优先级**: 高'
            ]
        ],
        [
            'tag' => 'action',
            'actions' => [
                [
                    'tag' => 'button',
                    'text' => [
                        'tag' => 'plain_text',
                        'content' => '查看详情'
                    ],
                    'type' => 'primary',
                    'url' => 'https://example.com/task/123'
                ],
                [
                    'tag' => 'button',
                    'text' => [
                        'tag' => 'plain_text',
                        'content' => '标记完成'
                    ],
                    'type' => 'default',
                    'value' => [
                        'action' => 'mark_complete',
                        'task_id' => '123'
                    ]
                ]
            ]
        ]
    ]
];

$message->send(
    'user_open_id',
    MessageTypeEnum::Interactive->value,
    $content
);
```

### 图片消息

```php
<?php

use Yuxin\Feishu\Message;
use Yuxin\Feishu\Enums\MessageTypeEnum;

$message = new Message('your_app_id', 'your_app_secret');

// 注意：需要先上传图片到飞书服务器获取 image_key
$content = [
    'image_key' => 'img_xxxxxxxxxxxxx'
];

$message->send(
    'user_open_id',
    MessageTypeEnum::Image->value,
    $content
);
```

## 群组管理示例

### 搜索并发送群组消息

```php
<?php

use Yuxin\Feishu\Message;
use Yuxin\Feishu\Group;
use Yuxin\Feishu\Enums\MessageTypeEnum;
use Yuxin\Feishu\Enums\ReceiveIDTypeEnum;
use Yuxin\Feishu\Enums\UserIDTypeEnum;
use Yuxin\Feishu\Exceptions\GroupNotFoundException;

$message = new Message('your_app_id', 'your_app_secret');
$group = new Group('your_app_id', 'your_app_secret');

try {
    // 搜索群组
    $chatId = $group->search('开发团队');

    // 发送群组消息
    $message->send(
        $chatId,
        MessageTypeEnum::Text->value,
        '大家好！今天下午3点有团队会议，请准时参加。',
        UserIDTypeEnum::OpenID->value,
        ReceiveIDTypeEnum::ChatID->value
    );

    echo "群组消息发送成功！\n";
} catch (GroupNotFoundException $e) {
    echo "群组未找到: " . $e->getMessage() . "\n";
} catch (\Exception $e) {
    echo "发送失败: " . $e->getMessage() . "\n";
}
```

## 用户管理示例

### 根据邮箱获取用户ID

```php
<?php

use Yuxin\Feishu\User;
use Yuxin\Feishu\Enums\UserIDTypeEnum;

$user = new User('your_app_id', 'your_app_secret');

try {
    // 根据邮箱获取用户OpenID
    $openId = $user->getId('user@example.com', UserIDTypeEnum::OpenID->value);
    echo "用户OpenID: " . $openId . "\n";

    // 根据邮箱获取用户UnionID
    $unionId = $user->getId('user@example.com', UserIDTypeEnum::UnionID->value);
    echo "用户UnionID: " . $unionId . "\n";
} catch (\Exception $e) {
    echo "获取用户ID失败: " . $e->getMessage() . "\n";
}
```

### 根据手机号获取用户ID

```php
<?php

use Yuxin\Feishu\User;
use Yuxin\Feishu\Enums\UserIDTypeEnum;

$user = new User('your_app_id', 'your_app_secret');

try {
    // 根据手机号获取用户ID
    $userId = $user->getId('13800138000', UserIDTypeEnum::UserID->value);
    echo "用户ID: " . $userId . "\n";
} catch (\Exception $e) {
    echo "获取用户ID失败: " . $e->getMessage() . "\n";
}
```
