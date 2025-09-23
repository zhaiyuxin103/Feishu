<?php

declare(strict_types=1);

namespace Yuxin\Feishu\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccessTokenGenerated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly string $token,
        public readonly bool $fromCache
    ) {
        //
    }

    public function isFromCache(): bool
    {
        return $this->fromCache;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
