<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Mockery\Matcher\AnyArgs;
use Yuxin\Feishu\Contracts\AccessTokenInterface;
use Yuxin\Feishu\Exceptions\HttpException;
use Yuxin\Feishu\Exceptions\InvalidArgumentException;
use Yuxin\Feishu\HttpClient;
use Yuxin\Feishu\User;

beforeEach(function (): void {
    $this->appId     = 'app_id';
    $this->appSecret = 'app_secret';
});

test('get http client', function (): void {
    $user = new User($this->appId, $this->appSecret);

    expect($user->getHttpClient())->toBeInstanceOf(HttpClient::class);
    expect($user->getHttpClient()->getClient())->toBeInstanceOf(Client::class);
});

test('set guzzle options', function (): void {
    $user = new User($this->appId, $this->appSecret);

    // 设置参数前，timeout 为 null
    expect($user->getHttpClient()->getClient()->getConfig('timeout'))->toBeNull();

    // 设置参数
    $user->setGuzzleOptions(['timeout' => 5000]);

    expect($user->getHttpClient()->getClient()->getConfig('timeout'))->toBe(5000);
});

test('http exception', function (): void {
    expect(function (): void {
        // 创建一个模拟的响应对象，模拟空的用户列表
        $response = new Response(200, [], json_encode(['data' => ['user_list' => []]]));

        $mockGuzzle = Mockery::mock(Client::class);
        $mockGuzzle->allows()->post(new AnyArgs)->andReturn($response);

        $mockHttpClient = Mockery::mock(HttpClient::class);
        $mockHttpClient->shouldReceive('getClient')->andReturn($mockGuzzle);

        // mock 接口而不是具体类
        $mock = Mockery::mock(AccessTokenInterface::class);
        $mock->allows()->getToken()->andReturn('mock_access_token');

        $legacyMock = Mockery::mock(User::class, [$this->appId, $this->appSecret, $mock])->makePartial();
        $legacyMock->allows()->getHttpClient()->andReturn($mockHttpClient);

        $legacyMock->getId('mock-id');
    })->toThrow(HttpException::class, 'User not found');
});

test('invalid user id type', function (): void {
    $user = new User($this->appId, $this->appSecret);

    expect(function () use ($user): void {
        $user->getId('mock-id', 'invalid-type');
    })->toThrow(InvalidArgumentException::class, 'Invalid user id type');
});
