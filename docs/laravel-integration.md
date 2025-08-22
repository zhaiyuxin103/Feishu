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
