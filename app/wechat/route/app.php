<?php

declare(strict_types=1);

use think\facade\Route;

Route::rule('/', 'wechat/index', 'GET|POST');
