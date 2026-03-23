<?php

declare(strict_types=1);

use Yuxin\Feishu\AccessToken;
use Yuxin\Feishu\Facades\AccessToken as AccessTokenFacade;
use Yuxin\Feishu\Facades\Group as GroupFacade;
use Yuxin\Feishu\Facades\Message as MessageFacade;
use Yuxin\Feishu\Facades\User as UserFacade;
use Yuxin\Feishu\Feishu;
use Yuxin\Feishu\Group;
use Yuxin\Feishu\Message;
use Yuxin\Feishu\User;

beforeEach(function (): void {
    $this->app->register(Yuxin\Feishu\ServiceProvider::class);

    config([
        'feishu.app_id'     => 'test_app_id',
        'feishu.app_secret' => 'test_app_secret',
    ]);
});

describe('service container bindings', function (): void {
    test('feishu manager is bound as singleton', function (): void {
        $feishu = app('feishu');

        expect($feishu)->toBeInstanceOf(Feishu::class);
        expect(app('feishu'))->toBe($feishu);
    });

    test('feishu.message resolves to Message instance', function (): void {
        expect(app('feishu.message'))->toBeInstanceOf(Message::class);
    });

    test('feishu.group resolves to Group instance', function (): void {
        expect(app('feishu.group'))->toBeInstanceOf(Group::class);
    });

    test('feishu.user resolves to User instance', function (): void {
        expect(app('feishu.user'))->toBeInstanceOf(User::class);
    });

    test('feishu.access_token resolves to AccessToken instance', function (): void {
        expect(app('feishu.access_token'))->toBeInstanceOf(AccessToken::class);
    });
});

describe('facades', function (): void {
    test('Message facade resolves to Message instance', function (): void {
        expect(MessageFacade::getFacadeRoot())->toBeInstanceOf(Message::class);
    });

    test('Group facade resolves to Group instance', function (): void {
        expect(GroupFacade::getFacadeRoot())->toBeInstanceOf(Group::class);
    });

    test('User facade resolves to User instance', function (): void {
        expect(UserFacade::getFacadeRoot())->toBeInstanceOf(User::class);
    });

    test('AccessToken facade resolves to AccessToken instance', function (): void {
        expect(AccessTokenFacade::getFacadeRoot())->toBeInstanceOf(AccessToken::class);
    });
});

describe('class type aliases', function (): void {
    test('Feishu class resolves via type hint', function (): void {
        expect(app(Feishu::class))->toBeInstanceOf(Feishu::class);
        expect(app(Feishu::class))->toBe(app('feishu'));
    });

    test('Message class resolves via type hint', function (): void {
        expect(app(Message::class))->toBeInstanceOf(Message::class);
        expect(app(Message::class))->toBe(app('feishu.message'));
    });

    test('Group class resolves via type hint', function (): void {
        expect(app(Group::class))->toBeInstanceOf(Group::class);
        expect(app(Group::class))->toBe(app('feishu.group'));
    });

    test('User class resolves via type hint', function (): void {
        expect(app(User::class))->toBeInstanceOf(User::class);
        expect(app(User::class))->toBe(app('feishu.user'));
    });

    test('AccessToken class resolves via type hint', function (): void {
        expect(app(AccessToken::class))->toBeInstanceOf(AccessToken::class);
        expect(app(AccessToken::class))->toBe(app('feishu.access_token'));
    });
});

describe('lazy loading cache', function (): void {
    test('Feishu manager returns same instance on repeated calls', function (): void {
        $feishu = app('feishu');

        expect($feishu->message())->toBe($feishu->message());
        expect($feishu->group())->toBe($feishu->group());
        expect($feishu->user())->toBe($feishu->user());
        expect($feishu->accessToken())->toBe($feishu->accessToken());
    });
});
