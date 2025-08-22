<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Workbench\App\Http\Controllers\MessageController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('message', [MessageController::class, 'store']);
