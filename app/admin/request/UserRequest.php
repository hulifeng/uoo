<?php

declare(strict_types=1);

namespace app\admin\request;

use app\BaseRequest;

class UserRequest extends BaseRequest
{
    protected $rule = [
        'name'     => 'require',
        'nickname' => 'require',
        'password' => 'require',
        'roles'    => 'require',
    ];

    protected $message = [
        'name.require'     => '登录账号必须',
        'nickname.require' => '名称必须',
        'password.require' => '密码必须',
        'roles.require'    => '角色必须',
    ];

    protected $scene = [
        'create' => ['name', 'nickname', 'password'],
        'update' => ['name', 'nickname'],
    ];
}
