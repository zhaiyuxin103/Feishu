# Laravel 集成示例

## 在控制器中使用

### 基本通知控制器

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Yuxin\Feishu\Enums\MessageTypeEnum;

class NotificationController extends Controller
{
    public function sendNotification(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|string',
            'message' => 'required|string'
        ]);

        try {
            app('feishu.message')->send(
                $request->user_id,
                MessageTypeEnum::Text->value,
                $request->message
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
```

### 依赖注入方式

```php
<?php

namespace App\Http\Controllers;

use Yuxin\Feishu\Message;
use Yuxin\Feishu\Enums\MessageTypeEnum;

class NotificationController extends Controller
{
    public function __construct(private Message $message) {}

    public function sendNotification(Request $request)
    {
        $this->message->send(
            $request->user_id,
            MessageTypeEnum::Text->value,
            $request->message
        );
    }
}
```

## 在服务类中使用

### 通知服务类

```php
<?php

namespace App\Services;

use Yuxin\Feishu\Enums\MessageTypeEnum;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function sendTaskReminder(string $userId, array $taskData): bool
    {
        try {
            $content = $this->buildTaskReminderContent($taskData);

            app('feishu.message')->send(
                $userId,
                MessageTypeEnum::Interactive->value,
                $content
            );

            Log::info('任务提醒发送成功', ['user_id' => $userId]);
            return true;
        } catch (\Exception $e) {
            Log::error('任务提醒发送失败', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function buildTaskReminderContent(array $taskData): array
    {
        return [
            'config' => ['wide_screen_mode' => true],
            'header' => [
                'title' => ['tag' => 'plain_text', 'content' => '任务提醒'],
                'template' => 'red'
            ],
            'elements' => [
                [
                    'tag' => 'div',
                    'text' => [
                        'tag' => 'lark_md',
                        'content' => "**任务**: {$taskData['title']}\n**截止时间**: {$taskData['deadline']}"
                    ]
                ]
            ]
        ];
    }
}
```

## 在队列中使用

```php
<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Yuxin\Feishu\Enums\MessageTypeEnum;

class SendFeishuNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private string $userId,
        private string $message
    ) {}

    public function handle(): void
    {
        app('feishu.message')->send(
            $this->userId,
            MessageTypeEnum::Text->value,
            $this->message
        );
    }
}
```

## 错误处理

```php
<?php

use Yuxin\Feishu\Exceptions\HttpException;
use Yuxin\Feishu\Exceptions\InvalidArgumentException;
use Illuminate\Support\Facades\Log;

try {
    app('feishu.message')->send('user_id', 'text', 'Hello');
} catch (HttpException $e) {
    Log::error('飞书API错误', ['error' => $e->getMessage()]);
} catch (InvalidArgumentException $e) {
    Log::error('飞书参数错误', ['error' => $e->getMessage()]);
} catch (\Exception $e) {
    Log::error('飞书未知错误', ['error' => $e->getMessage()]);
}
```

## 事件监听

SDK 提供了完整的事件系统，可以监听各种操作成功的事件：

### 注册事件监听器

在 `EventServiceProvider` 中注册：

```php
<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Listeners\LogFeishuMessage;
use Yuxin\Feishu\Events\MessageSent;
use Yuxin\Feishu\Events\UserSearched;
use Yuxin\Feishu\Events\GroupSearched;
use Yuxin\Feishu\Events\AccessTokenGenerated;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        MessageSent::class => [
            LogFeishuMessage::class,
        ],
        UserSearched::class => [
            LogUserSearch::class,
        ],
        GroupSearched::class => [
            LogGroupSearch::class,
        ],
        AccessTokenGenerated::class => [
            LogTokenGeneration::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
```

### 创建监听器

```php
<?php

namespace App\Listeners;

use Yuxin\Feishu\Events\MessageSent;
use Illuminate\Support\Facades\Log;

class LogFeishuMessage
{
    public function handle(MessageSent $event): void
    {
        Log::channel('feishu')->info('消息发送成功', [
            'to' => $event->to,
            'message_type' => $event->messageType,
            'message_id' => $event->messageId,
            'timestamp' => now(),
        ]);
    }
}
```

### 事件订阅者

如果需要监听多个事件，可以使用订阅者模式：

```php
<?php

namespace App\Listeners;

use Yuxin\Feishu\Events\MessageSent;
use Yuxin\Feishu\Events\UserSearched;
use Yuxin\Feishu\Events\GroupSearched;
use Yuxin\Feishu\Events\AccessTokenGenerated;
use Illuminate\Support\Facades\Log;

class FeishuEventSubscriber
{
    public function handleMessageSent(MessageSent $event): void
    {
        Log::info('消息发送成功', [
            'to' => $event->to,
            'message_type' => $event->messageType,
            'message_id' => $event->messageId,
        ]);
    }

    public function handleUserSearched(UserSearched $event): void
    {
        Log::info('用户搜索成功', [
            'username' => $event->username,
            'user_id' => $event->getUserId(),
        ]);
    }

    public function handleGroupSearched(GroupSearched $event): void
    {
        Log::info('群组搜索成功', [
            'query' => $event->query,
            'chat_id' => $event->getChatId(),
        ]);
    }

    public function handleAccessTokenGenerated(AccessTokenGenerated $event): void
    {
        Log::info('访问令牌生成', [
            'from_cache' => $event->isFromCache(),
            'token' => $event->getToken(),
        ]);
    }

    public function subscribe($events): array
    {
        return [
            MessageSent::class => 'handleMessageSent',
            UserSearched::class => 'handleUserSearched',
            GroupSearched::class => 'handleGroupSearched',
            AccessTokenGenerated::class => 'handleAccessTokenGenerated',
        ];
    }
}
```

在 `EventServiceProvider` 中注册订阅者：

```php
<?php

namespace App\Providers;

use App\Listeners\FeishuEventSubscriber;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $subscribe = [
        FeishuEventSubscriber::class,
    ];
}
```
