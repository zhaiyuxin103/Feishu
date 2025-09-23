<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Mockery\Matcher\AnyArgs;
use Yuxin\Feishu\Contracts\AccessTokenInterface;
use Yuxin\Feishu\Contracts\UserInterface;
use Yuxin\Feishu\Enums\UserIDTypeEnum;
use Yuxin\Feishu\Exceptions\GroupNotFoundException;
use Yuxin\Feishu\Exceptions\HttpException;
use Yuxin\Feishu\Group;

use function json_encode;

beforeEach(function (): void {
    $this->appId       = 'app_id';
    $this->appSecret   = 'app_secret';
    $this->accessToken = 'mock_access_token';

    // Mock AccessToken
    $this->accessToken = Mockery::mock(AccessTokenInterface::class);
    $this->accessToken->allows()->getAccessToken()->andReturn($this->accessToken);

    // Mock User
    $this->user = Mockery::mock(UserInterface::class);
});

afterEach(function (): void {
    Mockery::close();
});

test('get http client', function (): void {
    $group = new Group($this->appId, $this->appSecret);

    expect($group->getHttpClient())->toBeInstanceOf(Client::class);
});

test('set guzzle options', function (): void {
    $group = new Group($this->appId, $this->appSecret);

    // 设置参数前，timeout 为 null
    expect($group->getHttpClient()->getConfig('timeout'))->toBeNull();

    // 设置参数
    $group->setGuzzleOptions(['timeout' => 5000]);

    expect($group->getHttpClient()->getConfig('timeout'))->toBe(5000);
});

describe('instance', function (): void {
    test('with injected', function (): void {
        $group = new Group($this->appId, $this->appSecret, $this->accessToken, $this->user);

        expect($group)->toBeInstanceOf(Group::class);
    });

    test('without injected', function (): void {
        $group = new Group($this->appId, $this->appSecret);

        expect($group)->toBeInstanceOf(Group::class);
    });
});

describe('search', function (): void {
    test('success', function (): void {
        $response = new Response(200, [], json_encode([
            'code' => 0,
            'msg'  => 'success',
            'data' => [
                'items' => [
                    [
                        'chat_id'  => 'mock_chat_id',
                        'name'     => 'mock_name',
                        'owner_id' => 'mock_owner_id',
                    ],
                ],
            ],
        ]));

        $mock = Mockery::mock(Client::class);
        $mock->shouldReceive('get')
            ->with('im/v1/chats/search', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                ],
                'query' => [
                    'user_id_type' => UserIDTypeEnum::OpenID->value,
                    'query'        => 'mock_query',
                ],
            ])
            ->once()
            ->andReturn($response);

        $legacyMock = Mockery::mock(Group::class, [$this->appId, $this->appSecret, $this->accessToken])->makePartial();
        $legacyMock->allows()->getHttpClient()->andReturn($mock);

        $data = $legacyMock->search('mock_query');

        expect($data)->toBe('mock_chat_id');
    });

    // 测试传参 user id type
    test('with user id type', function (): void {
        $response = new Response(200, [], json_encode([
            'code' => 0,
            'msg'  => 'success',
            'data' => [
                'items' => [
                    [
                        'chat_id'  => 'mock_chat_id',
                        'name'     => 'mock_name',
                        'owner_id' => 'mock_owner_id',
                    ],
                ],
            ],
        ]));

        $mock = Mockery::mock(Client::class);
        $mock->shouldReceive('get')
            ->with('im/v1/chats/search', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                ],
                'query' => [
                    'user_id_type' => UserIDTypeEnum::UnionID->value,
                    'query'        => 'mock_query',
                ],
            ])
            ->once()
            ->andReturn($response);

        $legacyMock = Mockery::mock(Group::class, [$this->appId, $this->appSecret, $this->accessToken])->makePartial();
        $legacyMock->allows()->getHttpClient()->andReturn($mock);

        $data = $legacyMock->search('mock_query', UserIDTypeEnum::UnionID->value);

        expect($data)->toBe('mock_chat_id');
    });
});

describe('exception', function (): void {
    test('group not found', function (): void {
        $response = new Response(200, [], json_encode([
            'code' => 0,
            'msg'  => 'success',
            'data' => [
                'items' => [],
            ],
        ]));

        $mock = Mockery::mock(Client::class);
        $mock->allows()->get(new AnyArgs)->andReturn($response);

        $legacyMock = Mockery::mock(Group::class, [$this->appId, $this->appSecret, $this->accessToken])->makePartial();
        $legacyMock->allows()->getHttpClient()->andReturn($mock);

        expect(fn () => $legacyMock->search('mock_query'))->toThrow(GroupNotFoundException::class, 'Group not found with query: mock_query');
    });

    test('http', function (): void {
        $response = new Response(200, [], json_encode([
            'code' => 400,
            'msg'  => 'Bad Request',
        ]));

        $mock = Mockery::mock(Client::class);
        $mock->allows()->get(new AnyArgs)->andReturn($response);

        $legacyMock = Mockery::mock(Group::class, [$this->appId, $this->appSecret, $this->accessToken])->makePartial();
        $legacyMock->allows()->getHttpClient()->andReturn($mock);

        expect(fn () => $legacyMock->search('mock_query'))->toThrow(HttpException::class, 'Failed to search groups: Bad Request');
    });
});
