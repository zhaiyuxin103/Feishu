<?php

declare(strict_types=1);

namespace Yuxin\Feishu\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupSearched
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly string $query,
        public readonly ?string $chatId = null
    ) {
        //
    }

    public function getChatId(): ?string
    {
        return $this->chatId;
    }
}
