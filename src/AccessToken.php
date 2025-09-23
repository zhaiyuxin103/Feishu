<?php

declare(strict_types=1);

namespace Yuxin\Feishu;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Yuxin\Feishu\Contracts\AccessTokenInterface;
use Yuxin\Feishu\Exceptions\HttpException;

use function json_decode;

class AccessToken implements AccessTokenInterface
{
    protected const CACHE_PREFIX = 'feishu';

    protected array $guzzleOptions = [];

    protected CacheInterface $cache;

    public function __construct(
        protected string $appId,
        protected string $appSecret,
        ?CacheInterface $cache = null
    ) {
        $this->cache = $cache ?? new Psr16Cache(new FilesystemAdapter(namespace: 'feishu', defaultLifetime: 3600));
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

    public function getKey(): string
    {
        return sprintf('%s.access_token.%s.%s', self::CACHE_PREFIX, $this->appId, $this->appSecret);
    }

    public function getToken(): string
    {
        $token = $this->cache->get($this->getKey());
        if ($token && is_string($token)) {
            return $token;
        }

        return $this->getAccessToken();
    }

    /**
     * @throws HttpException
     * @throws GuzzleException
     */
    public function getAccessToken(): string
    {
        // Get new token from API
        $response = json_decode($this->getHttpClient()->post('auth/v3/tenant_access_token/internal', [
            'form_params' => [
                'app_id'     => $this->appId,
                'app_secret' => $this->appSecret,
            ],
        ])->getBody()->getContents(), true);

        if (empty($response['tenant_access_token'])) {
            throw new HttpException('Failed to get access token');
        }
        $token = $response['tenant_access_token'];

        $this->cache->set($this->getKey(), $token, (int) $response['expire']);

        return $token;
    }
}
