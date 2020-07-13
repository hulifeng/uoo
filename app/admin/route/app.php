<?php

declare(strict_types=1);

use think\facade\Route;
use xiaodi\JWTAuth\Middleware\Jwt;
use app\admin\middleware\Permission;

Route::get('/', function () {
    return 'Hello,ThinkPHP6!';
});

Route::get('/hello', function () {
    return 'Hello,ThinkPHP6!';
});

Route::group('/auth', function () {
    Route::post('/login', 'auth/login');
    Route::post('/logout', 'auth/logout');
    Route::get('/refresh_token', 'auth/refreshToken');
});

// 规则
Route::group('/permission', function () {
    Route::rule('/', 'system.permission/list', 'GET')->middleware(Permission::class, 'FetchPermission');
    Route::rule('/', 'system.permission/create', 'POST')->middleware(Permission::class, 'CreatePermission');
    Route::rule('/:id', 'system.permission/update', 'PUT')->middleware(Permission::class, 'UpdatePermission');
    Route::rule('/:id', 'system.permission/delete', 'DELETE')->middleware(Permission::class, 'DeletePermission');
})->middleware(Jwt::class);

// 角色
Route::group('/role', function () {
    Route::rule('/', 'system.role/list', 'GET')->middleware(Permission::class, 'FetchRole');
    Route::rule('/', 'system.role/create', 'POST')->middleware(Permission::class, 'CreateRole');
    Route::rule('/all$', 'system.role/all', 'GET');
    Route::rule('/:id$', 'system.role/update', 'PUT')->middleware(Permission::class, 'UpdateRoleUpdate');
    Route::rule('/:id$', 'system.role/delete', 'DELETE')->middleware(Permission::class, 'DeleteRole');
    Route::rule('/:id/mode', 'system.role/mode', 'PUT')->middleware(Permission::class, 'UpdateRoleAccess');
})->middleware(Jwt::class);

// 用户
Route::group('/user', function () {
    Route::rule('/data', 'system.user/data', 'GET');
    //获取 个人信息
    Route::rule('/current$', 'system.user/current', 'GET');
    //更新 个人信息
    Route::rule('/current$', 'system.user/updateCurrent', 'PUT');
    //更新 头像
    Route::rule('/avatar$', 'system.user/avatar', 'POST');
    //更新 密码
    Route::rule('/reset-password$', 'system.user/resetPassword', 'PUT');
    Route::rule('/', 'system.user/list', 'GET')->middleware(Permission::class, 'FetchAccount');
    Route::rule('/', 'system.user/create', 'POST')->middleware(Permission::class, 'CreateAccount');
    Route::rule('/info$', 'system.user/info', 'GET');
    Route::rule('/:id', 'system.user/update', 'PUT')->middleware(Permission::class, 'UpdateAccount');
    Route::rule('/:id', 'system.user/delete', 'DELETE')->middleware(Permission::class, 'DeleteAccount');
})->middleware(Jwt::class);

// 日志
Route::group('/log', function () {
    Route::rule('/acount', 'log.AccountLog/list', 'GET')->middleware(Permission::class, 'FetchLogAccountGet');
    Route::rule('/acount', 'log.AccountLog/delete', 'DELETE')->middleware(Permission::class, 'DeleteLogAccount');
    Route::rule('/db', 'log.DataBaseLog/list', 'GET')->middleware(Permission::class, 'FetchLogDb');
    Route::rule('/db', 'log.DataBaseLog/delete', 'DELETE')->middleware(Permission::class, 'DeleteLogDb');
})->middleware(Jwt::class);

Route::group('/system', function () {
    Route::rule('/dept', 'system.dept/list', 'GET')->middleware(Permission::class, 'FetchDept');
    Route::rule('/dept', 'system.dept/create', 'POST')->middleware(Permission::class, 'CreateDept');
    Route::rule('/dept/:id', 'system.dept/update', 'PUT')->middleware(Permission::class, 'UpdateDept');
    Route::rule('/dept/:id', 'system.dept/delete', 'DELETE')->middleware(Permission::class, 'DeleteDept');

    Route::rule('/post', 'system.post/create', 'POST')->middleware(Permission::class, 'CreatePost');
    Route::rule('/post', 'system.post/all', 'GET')->middleware(Permission::class, 'FetchPost');
    Route::rule('/post/:id', 'system.post/update', 'PUT')->middleware(Permission::class, 'UpdatePost');
    Route::rule('/post/:id', 'system.post/delete', 'DELETE')->middleware(Permission::class, 'DeletePost');
})->middleware(Jwt::class);

// 轮播
Route::group('/article', function () {
    Route::rule('/', 'content.article/list', 'GET')->middleware(Permission::class, 'ArticleGet');
    Route::rule('/', 'content.article/create', 'POST')->middleware(Permission::class, 'ArticlePost');
    Route::rule('/', 'content.article/delete', 'DELETE')->middleware(Permission::class, 'ArticleDELETE');
})->middleware(Jwt::class);

// 房源
Route::group('/house', function () {
    Route::rule('/', 'content.house/list', 'GET')->middleware(Permission::class, 'HouseGet');
    Route::rule('/link', 'content.house/link', 'GET')->middleware(Permission::class, 'HouseLink');
    Route::rule('/', 'content.house/create', 'POST')->middleware(Permission::class, 'HousePost');
    Route::rule('/', 'content.house/delete', 'DELETE')->middleware(Permission::class, 'HouseDelete');
})->middleware(Jwt::class);

// 文章
Route::group('/article', function () {
    Route::rule('/', 'content.article/list', 'GET')->middleware(Permission::class, 'ArticleGet');
    Route::rule('/link', 'content.article/link', 'GET')->middleware(Permission::class, 'ArticleLink');
	Route::rule('/isTop', 'content.article/isTop', 'POST')->middleware(Permission::class, 'ArticleIsTop');
    Route::rule('/', 'content.article/create', 'POST')->middleware(Permission::class, 'ArticlePost');
    Route::rule('/', 'content.article/delete', 'DELETE')->middleware(Permission::class, 'ArticleDelete');
})->middleware(Jwt::class);

// 城市
Route::group('/city', function () {
    Route::rule('/', 'content.city/list', 'GET')->middleware(Permission::class, 'CityGet');
})->middleware(Jwt::class);

// 数据分析
Route::group('/statistics', function () {
    Route::rule('/trend', 'content.statistics/trend', 'GET')->middleware(Permission::class, 'TrendGet');
    Route::rule('/liveness', 'content.statistics/liveness', 'GET')->middleware(Permission::class, 'LivenessGet');
    Route::rule('/pageres', 'content.statistics/pageres', 'GET')->middleware(Permission::class, 'PageresGet');
    Route::rule('/dashboard', 'content.statistics/dashboard', 'GET')->middleware(Permission::class, 'DashboardGet');
    Route::rule('/user$', 'content.statistics/user', 'GET')->middleware(Permission::class, 'UooLuUserGet');
    Route::rule('/user/:id/info', 'content.statistics/userInfo', 'GET')->middleware(Permission::class, 'UooLuUserInfoGet');
    Route::rule('/user/:id/house', 'content.statistics/historyHouse', 'GET')->middleware(Permission::class, 'UooLuUserHistoryHouseGet');
    Route::rule('/user/:id/article', 'content.statistics/historyArticle', 'GET')->middleware(Permission::class, 'UooLuUserHistoryArticleGet');
    Route::rule('/share', 'content.statistics/share', 'GET')->middleware(Permission::class, 'UooLuUserShare');
})->middleware(Jwt::class);

// 表单验证
Route::group('form', function () {
    Route::rule('/', 'content.form/list', 'GET')->middleware(Permission::class, 'FormList');
    Route::rule('/:id$', 'content.form/show', 'GET')->middleware(Permission::class, 'FormShow');
    Route::rule('/:id$', 'content.form/save', 'POST')->middleware(Permission::class, 'FormSave');
})->middleware(Jwt::class);

