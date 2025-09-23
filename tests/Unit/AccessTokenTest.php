<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Yuxin\Feishu\AccessToken;
use Yuxin\Feishu\Exceptions\HttpException;

beforeEach(function (): void {
    $this->appId     = 'app_id';
    $this->appSecret = 'app_secret';
});

test('get http client', function (): void {
    $user = new AccessToken($this->appId, $this->appSecret);

    expect($user->getHttpClient())->toBeInstanceOf(Client::class);
});

test('set guzzle options', function (): void {
    $user = new AccessToken($this->appId, $this->appSecret);

    // 设置参数前，timeout 为 null
    expect($user->getHttpClient()->getConfig('timeout'))->toBeNull();

    // 设置参数
    $user->setGuzzleOptions(['timeout' => 5000]);

    expect($user->getHttpClient()->getConfig('timeout'))->toBe(5000);
});

test('get access token', function (): void {
    // 创建模拟接口响应值
    $response = new Response(200, [], json_encode(['tenant_access_token' => 'mock_access_token', 'expire' => 7200]));

    // 创建模拟的 Guzzle HTTP 客户端
    $mock = Mockery::mock(Client::class);

    // 配置客户端的预期行为
    $mock->shouldReceive('post')->andReturn($response);

    // 将 `getHttpClient` 方法替换为使用模拟客户端
    $legacyMock = Mockery::mock(AccessToken::class, [$this->appId, $this->appSecret])->makePartial();
    // $client 为上面创建的模拟实例
    $legacyMock->shouldReceive('getHttpClient')->andReturn($mock);

    // 然后调用 getToken 方法，并断言返回值为模拟的返回值
    expect($legacyMock->getToken())->toBe('mock_access_token');
});

test('http exception', function (): void {
    expect(function (): void {
        $mock = Mockery::mock(Client::class);
        $mock->shouldReceive('post')->andReturn(new Response(200, [], '{}'));

        $cacheAdapter = new ArrayAdapter;
        $cache        = new Psr16Cache($cacheAdapter);

        $legacyMock = Mockery::mock(AccessToken::class, [$this->appId, $this->appSecret, $cache])->makePartial();
        $legacyMock->shouldReceive('getHttpClient')->andReturn($mock);
        $legacyMock->getToken();
    })->toThrow(HttpException::class, 'Failed to get access token');
});

test('cache functionality', function (): void {
    $cacheAdapter = new ArrayAdapter;
    $cache        = new Psr16Cache($cacheAdapter);

    // Pre-populate cache
    $cache->set('feishu.access_token.' . $this->appId . '.' . $this->appSecret, 'cached_token', 7200);

    $accessToken = new AccessToken($this->appId, $this->appSecret, $cache);

    expect($accessToken->getToken())->toBe('cached_token');
});

test('cache miss and store', function (): void {
    $response = new Response(200, [], json_encode(['tenant_access_token' => 'new_token', 'expire' => 7200]));

    $mock = Mockery::mock(Client::class);
    $mock->shouldReceive('post')->andReturn($response);

    $cacheAdapter = new ArrayAdapter;
    $cache        = new Psr16Cache($cacheAdapter);

    $legacyMock = Mockery::mock(AccessToken::class, [$this->appId, $this->appSecret, $cache])->makePartial();
    $legacyMock->shouldReceive('getHttpClient')->andReturn($mock);

    expect($legacyMock->getToken())->toBe('new_token');

    // Verify token was cached
    expect($cache->get('feishu.access_token.' . $this->appId . '.' . $this->appSecret))->toBe('new_token');
});

test('cache key generation', function (): void {
    $accessToken = new AccessToken($this->appId, $this->appSecret);

    expect($accessToken->getKey())->toBe('feishu.access_token.' . $this->appId . '.' . $this->appSecret);
});
