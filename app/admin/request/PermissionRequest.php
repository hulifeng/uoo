<?php

declare(strict_types=1);

namespace app\admin\request;

use app\BaseRequest;

class PermissionRequest extends BaseRequest
{
    protected $rule = [
        'name'  => 'require|unique:permission',
        'title' => 'require',
        'pid'   => 'require',
    ];

    protected $message = [
        'pid.require'   => '父级必须',
        'title.require' => '名称必须',
        'name.require'  => '规则必须',
        'name.unique'   => '规则重复',
    ];

    protected $scene = [
        'create' => ['name', 'title', 'pid'],
        'update' => ['title', 'pid'],
    ];
}
