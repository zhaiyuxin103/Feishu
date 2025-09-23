<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Mockery\Matcher\AnyArgs;
use Yuxin\Feishu\Contracts\AccessTokenInterface;
use Yuxin\Feishu\Exceptions\HttpException;
use Yuxin\Feishu\Exceptions\InvalidArgumentException;
use Yuxin\Feishu\User;

beforeEach(function (): void {
    $this->appId     = 'app_id';
    $this->appSecret = 'app_secret';
});

test('get http client', function (): void {
    $user = new User($this->appId, $this->appSecret);

    expect($user->getHttpClient())->toBeInstanceOf(Client::class);
});

test('set guzzle options', function (): void {
    $user = new User($this->appId, $this->appSecret);

    // 设置参数前，timeout 为 null
    expect($user->getHttpClient()->getConfig('timeout'))->toBeNull();

    // 设置参数
    $user->setGuzzleOptions(['timeout' => 5000]);

    expect($user->getHttpClient()->getConfig('timeout'))->toBe(5000);
});

test('http exception', function (): void {
    expect(function (): void {
        // 创建一个模拟的响应对象，模拟空的用户列表
        $response = new Response(200, [], json_encode(['data' => ['user_list' => []]]));

        $mock = Mockery::mock(Client::class);
        $mock->allows()->post(new AnyArgs)->andReturn($response);

        // mock 接口而不是具体类
        $accessToken = Mockery::mock(AccessTokenInterface::class);
        $accessToken->allows()->getToken()->andReturn('mock_access_token');

        $legacyMock = Mockery::mock(User::class, [$this->appId, $this->appSecret, $accessToken])->makePartial();
        $legacyMock->allows()->getHttpClient()->andReturn($mock);

        $legacyMock->getId('mock-id');
    })->toThrow(HttpException::class, 'User not found');
});

test('invalid user id type', function (): void {
    $user = new User($this->appId, $this->appSecret);

    expect(function () use ($user): void {
        $user->getId('mock-id', 'invalid-type');
    })->toThrow(InvalidArgumentException::class, 'Invalid user id type');
});
