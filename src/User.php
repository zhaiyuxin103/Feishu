<?php

declare(strict_types=1);

namespace Yuxin\Feishu;

use GuzzleHttp\Exception\GuzzleException;
use Yuxin\Feishu\Contracts\AccessTokenInterface;
use Yuxin\Feishu\Contracts\UserInterface;
use Yuxin\Feishu\Enums\UserIDTypeEnum;
use Yuxin\Feishu\Exceptions\HttpException;
use Yuxin\Feishu\Exceptions\InvalidArgumentException;

use function array_column;
use function filter_var;
use function in_array;
use function json_decode;
use function json_encode;

class User implements UserInterface
{
    protected HttpClient $httpClient;

    public function __construct(
        protected string $appId,
        protected string $appSecret,
        protected ?AccessTokenInterface $accessTokenInstance = null,
        ?HttpClient $httpClient = null
    ) {
        $this->accessTokenInstance = $accessTokenInstance ?: new AccessToken($this->appId, $this->appSecret);
        $this->httpClient          = $httpClient ?? new HttpClient;
    }

    public function getHttpClient(): HttpClient
    {
        return $this->httpClient;
    }

    public function setGuzzleOptions(array $options): void
    {
        $this->httpClient->setOptions($options);
    }

    /**
     * @throws HttpException
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function getId(string $username, string $type = 'union_id'): string
    {
        if (! in_array($type, array_column(UserIDTypeEnum::cases(), 'value'))) {
            throw new InvalidArgumentException('Invalid user id type');
        }

        $response = json_decode($this->getHttpClient()->getClient()->post('contact/v3/users/batch_get_id', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessTokenInstance->getToken(),
            ],
            'query' => [
                'user_id_type' => $type,
            ],
            'body' => json_encode([
                filter_var($username, FILTER_VALIDATE_EMAIL)
                    ? 'emails'
                    : 'mobiles'    => (array) $username,
                'include_resigned' => true,
            ]),
        ])->getBody()->getContents(), true);

        if (empty($response['data']['user_list'][0]['user_id'])) {
            throw new HttpException('User not found');
        }

        return $response['data']['user_list'][0]['user_id'];
    }
}
