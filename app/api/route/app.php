<?php

declare(strict_types=1);

/*
 * @contact  QQ Group 9265959
 * @author   Eden Leung codedge@163.com
 * @copyright 2020 Mason
 */

use think\facade\Route;

Route::group('/', function () {
    Route::group('/upload', function () {
        Route::post('/file', 'upload/file');
    });
});

Route::group('/:v/', function () {
    Route::rule('index', '/:v.Index/list', 'GET');
    Route::rule('city', '/:v.Index/city', 'GET');
    Route::rule('article/:id', '/:v.Index/article', 'GET');
    Route::rule('house/:id', '/:v.Index/house', 'GET');
    Route::rule('choose', '/:v.Index/choose', 'GET');
    Route::rule('search', '/:v.Index/search', 'GET');
    Route::rule('collection', '/:v.Index/collection', 'POST');
    Route::rule('collection', '/:v.Index/collection_list', 'GET');
    Route::rule('view', '/:v.Index/view', 'GET');
    Route::rule('my', '/:v.Index/my', 'GET');
    Route::rule('register', '/:v.Index/register', 'POST');
    Route::rule('updateUserInfo', '/:v.Index/updateUserInfo', 'POST');
    Route::rule('share', '/:v.Index/share', 'POST');
    Route::rule('share', '/:v.Index/share_list', 'GET');
    Route::rule('like', '/:v.Index/like', 'POST');
    Route::rule('bindPhone', '/:v.Index/bindPhone', 'POST');
    Route::rule('like', '/:v.Index/like_list', 'GET');
    Route::rule('user_behavior', '/:v.Index/user_behavior', 'POST');
    Route::rule('captcha', '/:v.Index/captcha', 'get');
    Route::rule('checkCode', '/:v.Index/checkCode', 'get');
    Route::rule('mapping', '/:v.Index/mapping', 'get');
    Route::rule('config', '/:v.Index/config', 'get');
    Route::rule('record_search', '/:v.Index/recordSearch', 'get');
})->allowCrossDomain();
