<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Mockery\Matcher\AnyArgs;
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
    $response = new Response(200, [], json_encode(['tenant_access_token' => 'mock_access_token']));

    // 创建模拟的 Guzzle HTTP 客户端
    $mock = Mockery::mock(Client::class);

    // 配置客户端的预期行为
    $mock->allows()->post(new AnyArgs)->andReturn($response);

    // 将 `getHttpClient` 方法替换为使用模拟客户端
    $legacyMock = Mockery::mock(AccessToken::class, [$this->appId, $this->appSecret])->makePartial();
    // $client 为上面创建的模拟实例
    $legacyMock->allows()->getHttpClient()->andReturn($mock);

    // 然后调用 getAccessToken 方法，并断言返回值为模拟的返回值
    expect($legacyMock->getAccessToken())->toBe('mock_access_token');
});

test('http exception', function (): void {
    expect(function (): void {
        $mock = Mockery::mock(Client::class);
        $mock->allows()->post(new AnyArgs)->andReturn([]);

        $accessToken = new AccessToken($this->appId, $this->appSecret);
        $accessToken->getAccessToken();
    })->toThrow(HttpException::class, 'Failed to get access token');
});
