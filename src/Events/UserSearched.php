<?php

declare(strict_types=1);

namespace Yuxin\Feishu\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserSearched
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly string $username,
        public readonly ?string $userId = null
    ) {
        //
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }
}
