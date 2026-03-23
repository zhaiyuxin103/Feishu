# API 参考

## 目录

- [Message 类](#message-类)
- [Group 类](#group-类)
- [User 类](#user-类)
- [AccessToken 类](#accesstoken-类)
- [Feishu Facade](#feishu-facade)
- [枚举类型](#枚举类型)
- [异常处理](#异常处理)

## Message 类

用于发送各种类型的消息到飞书用户或群组。

### 构造函数

```php
public function __construct(
    string $appId,
    string $appSecret,
    ?AccessTokenInterface $accessTokenInstance = null,
    ?UserInterface $userInstance = null
)
```

**参数：**

- `$appId` - 飞书应用的 App ID
- `$appSecret` - 飞书应用的 App Secret
- `$accessTokenInstance` - 访问令牌实例（可选）
- `$userInstance` - 用户实例（可选）

### 方法

#### send()

发送消息到指定用户或群组。

```php
public function send(
    string $to,
    string $messageType,
    string|array $content,
    string $userIdType = UserIDTypeEnum::OpenID->value,
    string $receiveIdType = ReceiveIDTypeEnum::OpenID->value
): bool
```

**参数：**

- `$to` - 接收者 ID（用户ID、群组ID等）
- `$messageType` - 消息类型，使用 `MessageTypeEnum` 中的值
- `$content` - 消息内容，可以是字符串或数组
- `$userIdType` - 用户ID类型，默认为 `open_id`
- `$receiveIdType` - 接收者ID类型，默认为 `open_id`

**返回值：** `bool` - 发送成功返回 `true`

**异常：**

- `InvalidArgumentException` - 参数无效时抛出
- `HttpException` - API 调用失败时抛出

**示例：**

```php
use Yuxin\Feishu\Message;
use Yuxin\Feishu\Enums\MessageTypeEnum;
use Yuxin\Feishu\Enums\ReceiveIDTypeEnum;
use Yuxin\Feishu\Enums\UserIDTypeEnum;

$message = new Message('app_id', 'app_secret');

// 发送文本消息给用户
$message->send(
    'user_open_id',
    MessageTypeEnum::Text->value,
    'Hello, World!'
);

// 发送富文本消息给群组
$message->send(
    'chat_id',
    MessageTypeEnum::Post->value,
    [
        'zh_cn' => [
            'title' => '标题',
            'content' => [
                [
                    'tag' => 'text',
                    'text' => '这是一段文本内容'
                ]
            ]
        ]
    ],
    UserIDTypeEnum::OpenID->value,
    ReceiveIDTypeEnum::ChatID->value
);
```

:::tip
💡 **提示**: 关于消息内容结构的详细说明，请参考 [飞书官方文档](https://open.feishu.cn/document/server-docs/im-v1/message-content-description/create_json)。
:::

#### setGuzzleOptions()

设置 Guzzle HTTP 客户端的选项。

```php
public function setGuzzleOptions(array $options): void
```

**参数：**

- `$options` - Guzzle 选项数组

**示例：**

```php
$message->setGuzzleOptions([
    'timeout' => 30,
    'verify' => false
]);
```

## Group 类

用于管理飞书群组，包括搜索群组等功能。

### 构造函数

```php
public function __construct(
    string $appId,
    string $appSecret,
    ?AccessTokenInterface $accessTokenInstance = null,
    ?UserInterface $userInstance = null
)
```

### 方法

#### search()

根据群组名称搜索群组。

```php
public function search(
    string $query,
    string $userIdType = UserIDTypeEnum::OpenID->value
): string
```

**参数：**

- `$query` - 搜索关键词（群组名称）
- `$userIdType` - 返回 owner 的 ID 类型，默认为 `open_id`

**返回值：** `string` - 群组 ID

**异常：**

- `GroupNotFoundException` - 群组未找到时抛出
- `HttpException` - API 调用失败时抛出

**示例：**

```php
use Yuxin\Feishu\Group;
use Yuxin\Feishu\Enums\UserIDTypeEnum;

$group = new Group('app_id', 'app_secret');

// 搜索群组
$chatId = $group->search('测试群组');
echo "群组ID: " . $chatId;
```

## User 类

用于管理飞书用户，包括获取用户ID等功能。

### 构造函数

```php
public function __construct(
    string $appId,
    string $appSecret,
    ?AccessTokenInterface $accessTokenInstance = null
)
```

### 方法

#### getId()

根据邮箱或手机号获取用户ID。

```php
public function getId(string $username, string $type = 'union_id'): string
```

**参数：**

- `$username` - 用户邮箱或手机号
- `$type` - 返回的用户ID类型，默认为 `union_id`

**返回值：** `string` - 用户ID

**异常：**

- `InvalidArgumentException` - 参数无效时抛出
- `HttpException` - 用户未找到或API调用失败时抛出

**示例：**

```php
use Yuxin\Feishu\User;
use Yuxin\Feishu\Enums\UserIDTypeEnum;

$user = new User('app_id', 'app_secret');

// 根据邮箱获取用户ID
$userId = $user->getId('user@example.com', UserIDTypeEnum::OpenID->value);
echo "用户OpenID: " . $userId;

// 根据手机号获取用户ID
$userId = $user->getId('13800138000', UserIDTypeEnum::UnionID->value);
echo "用户UnionID: " . $userId;
```

## AccessToken 类

用于管理飞书应用的访问令牌。

### 构造函数

```php
public function __construct(string $appId, string $appSecret)
```

### 方法

#### getToken()

获取访问令牌。

```php
public function getToken(): string
```

**返回值：** `string` - 访问令牌

**异常：**

- `HttpException` - 获取令牌失败时抛出

**示例：**

```php
use Yuxin\Feishu\AccessToken;

$accessToken = new AccessToken('app_id', 'app_secret');
$token = $accessToken->getToken();
echo "访问令牌: " . $token;
```

## Facades

SDK 提供了两种 Facade 使用方式：独立 Facade 和 Feishu 管理器 Facade。

### 独立 Facade（推荐）

每个服务都有独立的 Facade，可以直接调用方法，无需通过管理器中转：

| Facade                             | 类                         | 服务容器别名          |
| ---------------------------------- | -------------------------- | --------------------- |
| `Yuxin\Feishu\Facades\Message`     | `Yuxin\Feishu\Message`     | `feishu.message`      |
| `Yuxin\Feishu\Facades\Group`       | `Yuxin\Feishu\Group`       | `feishu.group`        |
| `Yuxin\Feishu\Facades\User`        | `Yuxin\Feishu\User`        | `feishu.user`         |
| `Yuxin\Feishu\Facades\AccessToken` | `Yuxin\Feishu\AccessToken` | `feishu.access_token` |

```php
use Yuxin\Feishu\Facades\Message;
use Yuxin\Feishu\Facades\Group;
use Yuxin\Feishu\Facades\User;
use Yuxin\Feishu\Facades\AccessToken;

// 发送消息
Message::send('user_id', 'text', 'Hello, World!');

// 搜索群组
$chatId = Group::search('群组名称');

// 获取用户ID
$userId = User::getId('user@example.com');

// 获取访问令牌
$token = AccessToken::getToken();
```

### Feishu 管理器 Facade

通过统一入口访问所有服务：

```php
use Yuxin\Feishu\Facades\Feishu;

Feishu::message()->send('user_id', 'text', 'Hello!');
$chatId = Feishu::group()->search('群组名称');
$userId = Feishu::user()->getId('user@example.com');
$token = Feishu::accessToken()->getToken();
```

管理器内部使用懒加载缓存，多次调用 `Feishu::message()` 返回同一实例。

### 依赖注入

SDK 注册了类型别名，支持通过类型提示自动注入：

```php
use Yuxin\Feishu\Message;
use Yuxin\Feishu\Group;

class NotificationController extends Controller
{
    public function __construct(
        private Message $message,
        private Group $group,
    ) {}
}
```

### 服务容器

```php
// 通过别名
app('feishu.message')->send('user_id', 'text', 'Hello!');

// 通过类名（等价）
app(Yuxin\Feishu\Message::class)->send('user_id', 'text', 'Hello!');
```

### Laravel 配置

在使用之前，请确保已经在 `.env` 文件中配置了飞书应用信息：

```env
FEISHU_APP_ID=your_app_id
FEISHU_APP_SECRET=your_app_secret
```

## 枚举类型

### MessageTypeEnum

消息类型枚举，定义了所有支持的消息类型。

```php
enum MessageTypeEnum: string
{
    case Text        = 'text';        // 文本消息
    case Image       = 'image';       // 图片消息
    case File        = 'file';        // 文件消息
    case Post        = 'post';        // 富文本消息
    case Audio       = 'audio';       // 音频消息
    case Media       = 'media';       // 视频消息
    case Sticker     = 'sticker';     // 表情消息
    case Interactive = 'interactive'; // 卡片消息
    case ShareChat   = 'share_chat';  // 分享群组消息
    case ShareUser   = 'share_user';  // 分享用户消息
    case System      = 'system';      // 系统消息
}
```

### UserIDTypeEnum

用户ID类型枚举。

```php
enum UserIDTypeEnum: string
{
    case UnionID = 'union_id'; // 用户的 union_id
    case OpenID  = 'open_id';  // 用户的 open_id
    case UserID  = 'user_id';  // 用户的 user_id
}
```

### ReceiveIDTypeEnum

接收者ID类型枚举。

```php
enum ReceiveIDTypeEnum: string
{
    case UnionID = 'union_id'; // 用户的 union_id
    case OpenID  = 'open_id';  // 用户的 open_id
    case Email   = 'email';    // 用户的邮箱
    case ChatID  = 'chat_id';  // 群组ID
    case UserID  = 'user_id';  // 用户的 user_id
}
```

## 异常处理

SDK 定义了以下异常类：

### HttpException

HTTP 请求异常，当 API 调用失败时抛出。

```php
use Yuxin\Feishu\Exceptions\HttpException;

try {
    $message->send('user_id', 'text', 'Hello');
} catch (HttpException $e) {
    echo "API错误: " . $e->getMessage();
    echo "错误代码: " . $e->getCode();
}
```

### InvalidArgumentException

参数无效异常，当传入的参数不符合要求时抛出。

```php
use Yuxin\Feishu\Exceptions\InvalidArgumentException;

try {
    $message->send('user_id', 'invalid_type', 'Hello');
} catch (InvalidArgumentException $e) {
    echo "参数错误: " . $e->getMessage();
}
```

### GroupNotFoundException

群组未找到异常，当搜索群组失败时抛出。

```php
use Yuxin\Feishu\Exceptions\GroupNotFoundException;

try {
    $group->search('不存在的群组');
} catch (GroupNotFoundException $e) {
    echo "群组未找到: " . $e->getMessage();
}
```
