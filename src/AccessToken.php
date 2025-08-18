<?php

declare(strict_types=1);

namespace Yuxin\Feishu;

use GuzzleHttp\Client;
use Yuxin\Feishu\Exceptions\HttpException;

use function json_decode;

class AccessToken
{
    protected array $guzzleOptions = [];

    public function __construct(
        protected string $appId,
        protected string $appSecret
    ) {
        //
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

    public function getAccessToken()
    {
        $response = json_decode($this->getHttpClient()->post('auth/v3/tenant_access_token/internal', [
            'form_params' => [
                'app_id'     => $this->appId,
                'app_secret' => $this->appSecret,
            ],
        ])->getBody()->getContents(), true);

        if (empty($response['tenant_access_token'])) {
            throw new HttpException('Failed to get access token');
        }

        return $response['tenant_access_token'];
    }
}
