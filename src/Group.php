<?php

declare(strict_types=1);

namespace Yuxin\Feishu;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Yuxin\Feishu\Contracts\AccessTokenInterface;
use Yuxin\Feishu\Contracts\GroupInterface;
use Yuxin\Feishu\Contracts\UserInterface;
use Yuxin\Feishu\Enums\UserIDTypeEnum;
use Yuxin\Feishu\Exceptions\GroupNotFoundException;
use Yuxin\Feishu\Exceptions\HttpException;

class Group implements GroupInterface
{
    protected array $guzzleOptions = [];

    public function __construct(
        protected string $appId,
        protected string $appSecret,
        protected ?AccessTokenInterface $accessTokenInstance = null,
        protected ?UserInterface $userInstance = null,
    ) {
        $this->accessTokenInstance = $accessTokenInstance ?? new AccessToken($this->appId, $this->appSecret);
        $this->userInstance        = $userInstance        ?? new User($this->appId, $this->appSecret);
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
     * 搜索群组
     *
     * @param  string  $query  搜索关键词，群组名称
     * @param  string  $userIdType  返回 owner 的 ID 类型
     * @return string 群组 ID
     *
     * @throws GuzzleException
     * @throws HttpException
     * @throws GroupNotFoundException
     */
    public function search(string $query, string $userIdType = UserIDTypeEnum::OpenID->value): string
    {
        $response = json_decode($this->getHttpClient()->get('im/v1/chats/search', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessTokenInstance->getToken(),
            ],
            'query' => [
                'user_id_type' => $userIdType,
                'query'        => $query,
            ],
        ])->getBody()->getContents(), true);

        if ($response['code'] !== 0) {
            throw new HttpException("Failed to search groups: {$response['msg']}", $response['code']);
        }

        if (count($response['data']['items']) === 0) {
            throw new GroupNotFoundException("Group not found with query: {$query}");
        }

        return $response['data']['items'][0]['chat_id'];
    }
}
