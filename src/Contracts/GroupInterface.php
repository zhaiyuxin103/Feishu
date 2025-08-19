<?php

declare(strict_types=1);

namespace Yuxin\Feishu\Contracts;

use Yuxin\Feishu\Enums\UserIDTypeEnum;

interface GroupInterface
{
    /**
     * 搜索群组
     *
     * @param  string  $query  搜索关键词，群组名称
     * @param  string  $userIdType  返回 owner 的 ID 类型
     * @return string 群组 ID
     */
    public function search(string $query, string $userIdType = UserIDTypeEnum::OpenID->value): string;
}
