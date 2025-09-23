<?php

declare(strict_types=1);

namespace Yuxin\Feishu\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly string $to,
        public readonly string $messageType,
        public readonly ?string $messageId = null
    ) {
        //
    }

    public function getMessageId(): ?string
    {
        return $this->messageId;
    }
}
