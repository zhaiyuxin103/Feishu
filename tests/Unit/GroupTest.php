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

beforeEach(function () {
    $this->appId       = 'app_id';
    $this->appSecret   = 'app_secret';
    $this->accessToken = 'mock_access_token';

    // Mock AccessToken
    $this->accessToken = Mockery::mock(AccessTokenInterface::class);
    $this->accessToken->allows()->getAccessToken()->andReturn($this->accessToken);

    // Mock User
    $this->user = Mockery::mock(UserInterface::class);
});

afterEach(function () {
    Mockery::close();
});

test('get http client', function () {
    $group = new Group($this->appId, $this->appSecret);

    expect($group->getHttpClient())->toBeInstanceOf(Client::class);
});

test('set guzzle options', function () {
    $group = new Group($this->appId, $this->appSecret);

    // 设置参数前，timeout 为 null
    expect($group->getHttpClient()->getConfig('timeout'))->toBeNull();

    // 设置参数
    $group->setGuzzleOptions(['timeout' => 5000]);

    expect($group->getHttpClient()->getConfig('timeout'))->toBe(5000);
});

describe('instance', function () {
    test('with injected', function () {
        $group = new Group($this->appId, $this->appSecret, $this->accessToken, $this->user);

        expect($group)->toBeInstanceOf(Group::class);
    });

    test('without injected', function () {
        $group = new Group($this->appId, $this->appSecret);

        expect($group)->toBeInstanceOf(Group::class);
    });
});

describe('search', function () {
    test('success', function () {
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

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('get')
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

        $group = Mockery::mock(Group::class, [$this->appId, $this->appSecret, $this->accessToken])->makePartial();
        $group->allows()->getHttpClient()->andReturn($client);

        $data = $group->search('mock_query');

        expect($data)->toBe('mock_chat_id');
    });

    // 测试传参 user id type
    test('with user id type', function () {
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

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('get')
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

        $group = Mockery::mock(Group::class, [$this->appId, $this->appSecret, $this->accessToken])->makePartial();
        $group->allows()->getHttpClient()->andReturn($client);

        $data = $group->search('mock_query', UserIDTypeEnum::UnionID->value);

        expect($data)->toBe('mock_chat_id');
    });
});

describe('exception', function () {
    test('group not found', function () {
        $response = new Response(200, [], json_encode([
            'code' => 0,
            'msg'  => 'success',
            'data' => [
                'items' => [],
            ],
        ]));

        $client = Mockery::mock(Client::class);
        $client->allows()->get(new AnyArgs)->andReturn($response);

        $group = Mockery::mock(Group::class, [$this->appId, $this->appSecret, $this->accessToken])->makePartial();
        $group->allows()->getHttpClient()->andReturn($client);

        expect(fn () => $group->search('mock_query'))->toThrow(GroupNotFoundException::class, 'Group not found with query: mock_query');
    });

    test('http', function () {
        $response = new Response(200, [], json_encode([
            'code' => 400,
            'msg'  => 'Bad Request',
        ]));

        $client = Mockery::mock(Client::class);
        $client->allows()->get(new AnyArgs)->andReturn($response);

        $group = Mockery::mock(Group::class, [$this->appId, $this->appSecret, $this->accessToken])->makePartial();
        $group->allows()->getHttpClient()->andReturn($client);

        expect(fn () => $group->search('mock_query'))->toThrow(HttpException::class, 'Failed to search groups: Bad Request');
    });
});
