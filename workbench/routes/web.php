<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Workbench\App\Http\Controllers\MessageController;
use Yuxin\Feishu\Enums\MessageTypeEnum;
use Yuxin\Feishu\Facades\Feishu;

Route::get('/', fn () => view('welcome'));

Route::get('message', [MessageController::class, 'store']);

Route::get('facade', fn () => Feishu::message()->send('18816545428', MessageTypeEnum::Text->value, 'Hello, world!'));
