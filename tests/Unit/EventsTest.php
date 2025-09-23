<?php

declare(strict_types=1);

use Yuxin\Feishu\Events\AccessTokenGenerated;
use Yuxin\Feishu\Events\GroupSearched;
use Yuxin\Feishu\Events\MessageSent;
use Yuxin\Feishu\Events\UserSearched;

test('message sent event creation', function (): void {
    $event = new MessageSent(
        to: 'test_user',
        messageType: 'text',
        messageId: 'test_message_id'
    );

    expect($event->to)->toBe('test_user');
    expect($event->messageType)->toBe('text');
    expect($event->messageId)->toBe('test_message_id');
});

test('user searched event creation', function (): void {
    $event = new UserSearched(
        username: 'test_user',
        userId: 'test_user_id'
    );

    expect($event->username)->toBe('test_user');
    expect($event->getUserId())->toBe('test_user_id');
});

test('group searched event creation', function (): void {
    $event = new GroupSearched(
        query: 'test_group',
        chatId: 'test_chat_id'
    );

    expect($event->query)->toBe('test_group');
    expect($event->getChatId())->toBe('test_chat_id');
});

test('access token generated event creation', function (): void {
    $event = new AccessTokenGenerated(
        token: 'test_token',
        fromCache: false
    );

    expect($event->getToken())->toBe('test_token');
    expect($event->isFromCache())->toBeFalse();
});

test('access token generated event from cache', function (): void {
    $event = new AccessTokenGenerated(
        token: 'cached_token',
        fromCache: true
    );

    expect($event->getToken())->toBe('cached_token');
    expect($event->isFromCache())->toBeTrue();
});
