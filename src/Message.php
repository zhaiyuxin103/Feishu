<?php

declare(strict_types=1);

namespace Yuxin\Feishu;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Yuxin\Feishu\Contracts\AccessTokenInterface;
use Yuxin\Feishu\Contracts\UserInterface;
use Yuxin\Feishu\Enums\MessageTypeEnum;
use Yuxin\Feishu\Enums\ReceiveIDTypeEnum;
use Yuxin\Feishu\Enums\UserIDTypeEnum;
use Yuxin\Feishu\Exceptions\HttpException;
use Yuxin\Feishu\Exceptions\InvalidArgumentException;

class Message
{
    protected array $guzzleOptions = [];

    public function __construct(
        protected string $appId,
        protected string $appSecret,
        protected ?AccessTokenInterface $accessTokenInstance = null,
        protected ?UserInterface $userInstance = null,
    ) {
        $this->accessTokenInstance = $accessTokenInstance ?: new AccessToken($this->appId, $this->appSecret);
        $this->userInstance        = $userInstance ?: new User($this->appId, $this->appSecret);
    }

    public function getHttpClient(): Client
    {
        return new Client(array_merge($this->guzzleOptions, [
            'base_uri' => 'https://open.feishu.cn/open-apis/',
        ]));
    }

    public function setGuzzleOptions(array $options): void
    {
        $this->guzzleOptions = $options;
    }

    /**
     * 发送消息
     *
     * 根据飞书官方文档，向指定用户或群组发送消息。
     *
     * 支持的消息类型：
     * - text: 文本消息
     * - image: 图片消息
     * - file: 文件消息
     * - post: 富文本消息
     * - audio: 音频消息
     * - media: 视频消息
     * - sticker: 表情消息
     * - interactive: 卡片消息
     * - share_chat: 分享群组消息
     * - share_user: 分享用户消息
     * - system: 系统消息
     *
     * 支持的接收者ID类型：
     * - union_id: 用户的 union_id
     * - open_id: 用户的 open_id
     * - email: 用户的邮箱
     * - chat_id: 群组ID
     * - user_id: 用户的 user_id
     *
     * 支持的用户ID类型：
     * - union_id: 用户的 union_id
     * - open_id: 用户的 open_id
     * - user_id: 用户的 user_id
     *
     * 如何构建消息内容，请参考：
     *
     * @link https://open.feishu.cn/document/server-docs/im-v1/message-content-description/create_json
     *
     * @param  string  $to  接收者 ID，根据 receiveIdType 参数确定类型
     * @param  string  $messageType  消息类型，使用 MessageTypeEnum 中的值
     * @param  string  $content  消息内容，JSON 格式的字符串
     * @param  string  $userIdType  用户ID类型，使用 UserIDTypeEnum 中的值，默认为 open_id
     * @param  string  $receiveIdType  接收者ID类型，使用 ReceiveIDTypeEnum 中的值，默认为 open_id
     * @return bool 发送成功返回 true，失败抛出异常
     *
     * @throws InvalidArgumentException 当消息类型、用户 ID 类型或接收者 ID 类型无效时
     * @throws HttpException 当 API 调用失败时
     * @throws GuzzleException
     *
     * @see MessageTypeEnum
     * @see ReceiveIDTypeEnum
     * @see UserIDTypeEnum
     */
    public function send(
        string $to,
        string $messageType,
        string|array $content,
        string $userIdType = UserIDTypeEnum::OpenID->value,
        string $receiveIdType = ReceiveIDTypeEnum::OpenID->value,
    ): bool {
        $this->validateParameters($messageType, $userIdType, $receiveIdType);

        $response = json_decode($this->getHttpClient()->post('im/v1/messages', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessTokenInstance->getAccessToken(),
            ],
            'query' => [
                'receive_id_type' => $receiveIdType,
            ],
            'body' => json_encode([
                'receive_id' => $this->getReceiveId($to, $userIdType, $receiveIdType),
                'msg_type'   => $messageType,
                'content'    => json_encode([
                    $messageType => $this->formatContent($content),
                ]),
            ]),
        ])->getBody()->getContents(), true);

        if ($response['code'] !== 0) {
            throw new HttpException($response['msg'] ?? 'Unknown error', $response['code'] ?? 0);
        }

        return true;
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function validateParameters(string $messageType, string $userIdType, string $receiveIdType): void
    {
        if (! in_array($messageType, array_column(MessageTypeEnum::cases(), 'value'))) {
            throw new InvalidArgumentException('Invalid message type: ' . $messageType);
        }

        if (! in_array($userIdType, array_column(UserIDTypeEnum::cases(), 'value'))) {
            throw new InvalidArgumentException('Invalid user id type: ' . $userIdType);
        }

        if (! in_array($receiveIdType, array_column(ReceiveIDTypeEnum::cases(), 'value'))) {
            throw new InvalidArgumentException('Invalid receive id type: ' . $receiveIdType);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function getReceiveId(string $to, string $userIdType, string $receiveIdType): string
    {
        return match ($receiveIdType) {
            ReceiveIDTypeEnum::ChatID->value => $to,
            ReceiveIDTypeEnum::OpenID->value => $this->userInstance->getId($to, $userIdType),
            default                          => throw new InvalidArgumentException('Invalid receive id type'),
        };
    }

    private function formatContent(string|array $content): string|array
    {
        if (is_array($content)) {
            return $content;
        }
        $decoded = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $content;
        }

        return $decoded;
    }
}
