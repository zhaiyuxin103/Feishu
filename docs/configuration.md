# 配置指南

## 目录

- [基本配置](#基本配置)
- [环境变量配置](#环境变量配置)
- [Laravel 配置](#laravel-配置)
- [HTTP 客户端配置](#http-客户端配置)
- [错误处理配置](#错误处理配置)

## 基本配置

### 获取飞书应用凭证

1. 登录 [飞书开放平台](https://open.feishu.cn/)
2. 创建或选择您的应用
3. 在应用详情页面获取以下信息：
   - **App ID**: 应用的唯一标识符
   - **App Secret**: 应用的密钥

### 手动配置

```php
<?php

use Yuxin\Feishu\Message;
use Yuxin\Feishu\Group;
use Yuxin\Feishu\User;

// 创建实例时直接传入配置
$message = new Message('your_app_id', 'your_app_secret');
$group = new Group('your_app_id', 'your_app_secret');
$user = new User('your_app_id', 'your_app_secret');
```

## 环境变量配置

### 在 .env 文件中配置

```env
# 飞书应用配置
FEISHU_APP_ID=cli_xxxxxxxxxxxxx
FEISHU_APP_SECRET=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### 使用环境变量

```php
<?php

use Yuxin\Feishu\Message;

// 从环境变量读取配置
$appId = $_ENV['FEISHU_APP_ID'] ?? getenv('FEISHU_APP_ID');
$appSecret = $_ENV['FEISHU_APP_SECRET'] ?? getenv('FEISHU_APP_SECRET');

$message = new Message($appId, $appSecret);
```

## Laravel 配置

### 发布配置文件

```bash
php artisan vendor:publish --tag=feishu-config
```

这将在 `config/feishu.php` 创建配置文件：

```php
<?php

return [
    'app_id' => env('FEISHU_APP_ID'),
    'app_secret' => env('FEISHU_APP_SECRET'),
];
```

### 使用 Laravel 配置

```php
<?php

use Yuxin\Feishu\Message;

// 使用 Laravel 配置
$message = new Message(
    config('feishu.app_id'),
    config('feishu.app_secret')
);

// 或者使用服务容器
$message = app('feishu.message');
```

### 自定义配置

您可以在 `config/feishu.php` 中添加更多配置选项：

```php
<?php

return [
    'app_id' => env('FEISHU_APP_ID'),
    'app_secret' => env('FEISHU_APP_SECRET'),

    // 自定义配置
    'timeout' => env('FEISHU_TIMEOUT', 30),
    'retry_attempts' => env('FEISHU_RETRY_ATTEMPTS', 3),
    'log_requests' => env('FEISHU_LOG_REQUESTS', false),
];
```

## HTTP 客户端配置

### 设置 Guzzle 选项

```php
<?php

use Yuxin\Feishu\Message;

$message = new Message('app_id', 'app_secret');

// 设置 HTTP 客户端选项
$message->setGuzzleOptions([
    'timeout' => 30,           // 请求超时时间（秒）
    'connect_timeout' => 10,   // 连接超时时间（秒）
    'verify' => false,         // 是否验证 SSL 证书
    'proxy' => 'http://proxy.example.com:8080', // 代理设置
    'headers' => [
        'User-Agent' => 'MyApp/1.0',
    ],
]);
```

### 常用配置选项

```php
<?php

$guzzleOptions = [
    // 超时设置
    'timeout' => 30,
    'connect_timeout' => 10,

    // SSL 设置
    'verify' => true,  // 生产环境建议为 true
    'cert' => '/path/to/cert.pem',
    'ssl_key' => '/path/to/key.pem',

    // 代理设置
    'proxy' => 'http://proxy.example.com:8080',
    'proxy_user' => 'username',
    'proxy_pass' => 'password',

    // 请求头设置
    'headers' => [
        'User-Agent' => 'MyApp/1.0',
        'Accept' => 'application/json',
    ],

    // 重试设置
    'retry_on_status' => [500, 502, 503, 504],
    'max_retry_attempts' => 3,
];

$message->setGuzzleOptions($guzzleOptions);
```

## 错误处理配置

### 基本错误处理

```php
<?php

use Yuxin\Feishu\Exceptions\HttpException;
use Yuxin\Feishu\Exceptions\InvalidArgumentException;
use Yuxin\Feishu\Exceptions\GroupNotFoundException;

try {
    $message->send('user_id', 'text', 'Hello');
} catch (HttpException $e) {
    // API 调用失败
    error_log("飞书API错误: " . $e->getMessage());
} catch (InvalidArgumentException $e) {
    // 参数错误
    error_log("参数错误: " . $e->getMessage());
} catch (GroupNotFoundException $e) {
    // 群组未找到
    error_log("群组未找到: " . $e->getMessage());
} catch (\Exception $e) {
    // 其他错误
    error_log("未知错误: " . $e->getMessage());
}
```

### Laravel 中的错误处理

```php
<?php

use Illuminate\Support\Facades\Log;

try {
    app('feishu.message')->send('user_id', 'text', 'Hello');
} catch (\Exception $e) {
    Log::error('飞书消息发送失败', [
        'error' => $e->getMessage(),
        'user_id' => 'user_id',
        'trace' => $e->getTraceAsString()
    ]);

    // 可以选择发送到备用通知渠道
    // $this->sendToBackupChannel($message);
}
```

### 自定义错误处理

```php
<?php

class FeishuErrorHandler
{
    public function handle(\Exception $e, array $context = []): void
    {
        $errorData = [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'context' => $context,
        ];

        // 记录错误日志
        error_log(json_encode($errorData));

        // 发送错误通知
        $this->sendErrorNotification($errorData);

        // 重试逻辑
        if ($this->shouldRetry($e)) {
            $this->retry($context);
        }
    }

    private function shouldRetry(\Exception $e): bool
    {
        return $e instanceof HttpException && in_array($e->getCode(), [500, 502, 503, 504]);
    }

    private function retry(array $context): void
    {
        // 实现重试逻辑
    }

    private function sendErrorNotification(array $errorData): void
    {
        // 发送错误通知到管理员
    }
}

// 使用自定义错误处理
$errorHandler = new FeishuErrorHandler();

try {
    $message->send('user_id', 'text', 'Hello');
} catch (\Exception $e) {
    $errorHandler->handle($e, [
        'user_id' => 'user_id',
        'message_type' => 'text'
    ]);
}
```

## 生产环境配置建议

### 安全配置

```php
<?php

// 生产环境配置
$productionConfig = [
    'timeout' => 30,
    'connect_timeout' => 10,
    'verify' => true,  // 验证 SSL 证书
    'headers' => [
        'User-Agent' => 'MyApp/1.0',
    ],
];

// 开发环境配置
$developmentConfig = [
    'timeout' => 60,
    'verify' => false,  // 开发环境可以关闭 SSL 验证
    'debug' => true,
];
```

### 监控和日志

```php
<?php

// 添加请求日志
$message->setGuzzleOptions([
    'on_stats' => function ($stats) {
        $duration = $stats->getHandlerStats()['total_time'] ?? 0;
        Log::info('飞书API请求', [
            'method' => $stats->getRequest()->getMethod(),
            'uri' => $stats->getRequest()->getUri(),
            'duration' => $duration,
            'status_code' => $stats->getResponse()->getStatusCode(),
        ]);
    }
]);
```

### 缓存配置

```php
<?php

// 缓存访问令牌（示例）
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
