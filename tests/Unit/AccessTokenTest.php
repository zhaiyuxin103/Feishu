<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Mockery\Matcher\AnyArgs;
use Yuxin\Feishu\AccessToken;
use Yuxin\Feishu\Exceptions\HttpException;

beforeEach(function () {
    $this->appId     = 'app_id';
    $this->appSecret = 'app_secret';
});

test('get access token', function () {
    // 创建模拟接口响应值
    $response = new Response(200, [], json_encode(['tenant_access_token' => 'mock_access_token']));

    // 创建模拟的 Guzzle HTTP 客户端
    $client = Mockery::mock(Client::class);

    // 配置客户端的预期行为
    $client->allows()->post(new AnyArgs)->andReturn($response);

    // 将 `getHttpClient` 方法替换为使用模拟客户端
    $accessToken = Mockery::mock(AccessToken::class, [$this->appId, $this->appSecret])->makePartial();
    // $client 为上面创建的模拟实例
    $accessToken->allows()->getHttpClient()->andReturn($client);

    // 然后调用 getAccessToken 方法，并断言返回值为模拟的返回值
    expect($accessToken->getAccessToken())->toBe('mock_access_token');
});

test('http exception', function () {
    expect(function () {
        $client = Mockery::mock(Client::class);
        $client->allows()->post(new AnyArgs)->andReturn([]);

        $accessToken = new AccessToken($this->appId, $this->appSecret);
        $accessToken->getAccessToken();
    })->toThrow(HttpException::class, 'Failed to get access token');
});
