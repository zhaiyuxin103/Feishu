<?php

declare(strict_types=1);

namespace Workbench\App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Yuxin\Feishu\Events\AccessTokenGenerated;
use Yuxin\Feishu\Events\GroupSearched;
use Yuxin\Feishu\Events\MessageSent;
use Yuxin\Feishu\Events\UserSearched;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register event listeners for testing
        Event::listen(MessageSent::class, function (MessageSent $messageSent): void {
            Log::info('MessageSent event triggered', [
                'to'           => $messageSent->to,
                'message_type' => $messageSent->messageType,
                'message_id'   => $messageSent->messageId,
            ]);
        });

        Event::listen(UserSearched::class, function (UserSearched $userSearched): void {
            Log::info('UserSearched event triggered', [
                'username' => $userSearched->username,
                'user_id'  => $userSearched->getUserId(),
            ]);
        });

        Event::listen(GroupSearched::class, function (GroupSearched $groupSearched): void {
            Log::info('GroupSearched event triggered', [
                'query'   => $groupSearched->query,
                'chat_id' => $groupSearched->getChatId(),
            ]);
        });

        Event::listen(AccessTokenGenerated::class, function (AccessTokenGenerated $accessTokenGenerated): void {
            Log::info('AccessTokenGenerated event triggered', [
                'token'      => substr($accessTokenGenerated->getToken(), 0, 10) . '...',
                'from_cache' => $accessTokenGenerated->isFromCache(),
            ]);
        });
    }
}
