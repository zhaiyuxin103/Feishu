# 常见问题解答 (FAQ)

## 基本问题

### Q: 这个 SDK 支持哪些 PHP 版本？

A: 本 SDK 需要 PHP 8.1 或更高版本。我们使用 PHP 8.1+ 的新特性，如枚举类型、联合类型等，以确保代码的类型安全和现代化。

### Q: 如何获取飞书应用的 App ID 和 App Secret？

A:

1. 登录 [飞书开放平台](https://open.feishu.cn/)
2. 创建或选择您的应用
3. 在应用详情页面可以找到 App ID 和 App Secret

### Q: 支持哪些消息类型？

A: 目前支持以下消息类型：

- 文本消息 (text)
- 富文本消息 (post)
- 图片消息 (image)
- 文件消息 (file)
- 音频消息 (audio)
- 视频消息 (media)
- 表情消息 (sticker)
- 卡片消息 (interactive)
- 分享群组消息 (share_chat)
- 分享用户消息 (share_user)
- 系统消息 (system)

## 安装和配置

### Q: 如何安装 SDK？

A: 使用 Composer 安装：

```bash
composer require zhaiyuxin/feishu
```

### Q: 在 Laravel 中如何配置？

A:

1. 在 `.env` 文件中添加配置：

```env
FEISHU_APP_ID=your_app_id
FEISHU_APP_SECRET=your_app_secret
```

2. 发布配置文件（可选）：

```bash
php artisan vendor:publish --tag=feishu-config
```

3. 使用服务容器：

```php
app('feishu.message')->send('user_id', 'text', 'Hello');
```

### Q: 如何设置环境变量？

A: 在您的 `.env` 文件中添加：

```env
FEISHU_APP_ID=cli_xxxxxxxxxxxxx
FEISHU_APP_SECRET=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

## 使用问题

### Q: 如何发送消息给用户？

A:

```php
use Yuxin\Feishu\Message;
use Yuxin\Feishu\Enums\MessageTypeEnum;

$message = new Message('app_id', 'app_secret');
$message->send('user_open_id', MessageTypeEnum::Text->value, 'Hello, World!');
```

### Q: 如何发送消息给群组？

A:

```php
use Yuxin\Feishu\Message;
use Yuxin\Feishu\Group;
use Yuxin\Feishu\Enums\MessageTypeEnum;
use Yuxin\Feishu\Enums\ReceiveIDTypeEnum;
use Yuxin\Feishu\Enums\UserIDTypeEnum;

$message = new Message('app_id', 'app_secret');
$group = new Group('app_id', 'app_secret');

// 搜索群组
$chatId = $group->search('群组名称');

// 发送群组消息
$message->send(
    $chatId,
    MessageTypeEnum::Text->value,
    '群组消息',
    UserIDTypeEnum::OpenID->value,
    ReceiveIDTypeEnum::ChatID->value
);
```

### Q: 如何根据邮箱获取用户ID？

A:

```php
use Yuxin\Feishu\User;
use Yuxin\Feishu\Enums\UserIDTypeEnum;

$user = new User('app_id', 'app_secret');
$userId = $user->getId('user@example.com', UserIDTypeEnum::OpenID->value);
```

### Q: 如何发送富文本消息？

:::tip
💡 **提示**: 关于消息内容结构的详细说明，请参考 [飞书官方文档](https://open.feishu.cn/document/server-docs/im-v1/message-content-description/create_json)。
:::

A:

```php
$content = [
    'zh_cn' => [
        'title' => '消息标题',
        'content' => [
            [
                'tag' => 'text',
                'text' => '这是文本内容'
            ],
            [
                'tag' => 'a',
                'text' => '这是链接',
                'href' => 'https://example.com'
            ]
        ]
    ]
];

$message->send('user_id', MessageTypeEnum::Post->value, $content);
```

### Q: 如何发送卡片消息？

A:

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
                'tag' => 'lark_md',
                'content' => '**内容**: 这是卡片内容'
            ]
        ]
    ]
];

$message->send('user_id', MessageTypeEnum::Interactive->value, $content);
```

## 错误处理

### Q: 如何处理 API 调用失败？

A:

```php
use Yuxin\Feishu\Exceptions\HttpException;

try {
    $message->send('user_id', 'text', 'Hello');
} catch (HttpException $e) {
    echo "API错误: " . $e->getMessage();
    echo "错误代码: " . $e->getCode();
}
```

### Q: 如何处理群组未找到的错误？

A:

```php
use Yuxin\Feishu\Exceptions\GroupNotFoundException;

try {
    $chatId = $group->search('不存在的群组');
} catch (GroupNotFoundException $e) {
    echo "群组未找到: " . $e->getMessage();
}
```

### Q: 如何处理参数错误？

A:

```php
use Yuxin\Feishu\Exceptions\InvalidArgumentException;

try {
    $message->send('user_id', 'invalid_type', 'Hello');
} catch (InvalidArgumentException $e) {
    echo "参数错误: " . $e->getMessage();
}
```

## 性能优化

### Q: 如何优化 HTTP 请求性能？

A:

```php
// 设置 HTTP 客户端选项
$message->setGuzzleOptions([
    'timeout' => 30,
    'connect_timeout' => 10,
    'verify' => true,
]);
```

### Q: 如何缓存访问令牌？

A: 您可以实现自定义的访问令牌缓存：

```php
class CachedAccessToken
{
    private $cache;
    private $accessToken;

    public function __construct(AccessToken $accessToken, $cache)
    {
        $this->accessToken = $accessToken;
        $this->cache = $cache;
    }

    public function getAccessToken(): string
    {
        $cachedToken = $this->cache->get('feishu_access_token');

        if ($cachedToken) {
            return $cachedToken;
        }

        $token = $this->accessToken->getAccessToken();
        $this->cache->put('feishu_access_token', $token, 7200); // 缓存2小时

        return $token;
    }
}
```

## 调试问题

### Q: 如何启用调试模式？

A:

```php
// 设置详细的 HTTP 请求日志
$message->setGuzzleOptions([
    'debug' => true,
    'on_stats' => function ($stats) {
        $duration = $stats->getHandlerStats()['total_time'] ?? 0;
        error_log("API请求: {$stats->getRequest()->getMethod()} {$stats->getRequest()->getUri()} - {$duration}s");
    }
]);
```

### Q: 如何查看详细的错误信息？

A:

```php
try {
    $message->send('user_id', 'text', 'Hello');
} catch (\Exception $e) {
    error_log("错误详情: " . $e->getMessage());
    error_log("错误文件: " . $e->getFile() . ":" . $e->getLine());
    error_log("错误堆栈: " . $e->getTraceAsString());
}
```

## 安全相关

### Q: 如何安全地存储 App Secret？

A:

1. 使用环境变量存储敏感信息
2. 不要将 App Secret 提交到版本控制系统
3. 在生产环境中使用安全的配置管理

```env
# .env 文件（不要提交到 Git）
FEISHU_APP_SECRET=your_secret_here
```

### Q: 如何验证 SSL 证书？

A:

```php
// 生产环境建议启用 SSL 验证
$message->setGuzzleOptions([
    'verify' => true,
]);
```

## 其他问题

### Q: 支持哪些用户ID类型？

A: 支持以下用户ID类型：

- `union_id`: 用户的 union_id
- `open_id`: 用户的 open_id
- `user_id`: 用户的 user_id
- `email`: 用户的邮箱
- `chat_id`: 群组ID

### Q: 如何获取更多帮助？

A:

- 查看 [API 参考文档](/api-reference)
- 查看 [示例代码](/examples)
- 在 [GitHub Issues](https://github.com/zhaiyuxin103/Feishu/issues) 中提问
- 发送邮件到 zhaiyuxin103@hotmail.com

### Q: 如何贡献代码？

A: 请查看我们的 [贡献指南](/contributing)，了解如何参与项目开发。

---

如果您的问题没有在这里找到答案，请随时在 GitHub Issues 中提问，我们会尽快回复您。
