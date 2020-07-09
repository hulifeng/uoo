<?php

declare(strict_types=1);

use think\facade\Route;

Route::get('/', 'index/index');

Route::get('/think', 'index/hello');
