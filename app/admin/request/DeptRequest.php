<?php

declare(strict_types=1);

namespace app\admin\request;

use app\BaseRequest;

class DeptRequest extends BaseRequest
{
    protected $rule = [
        'name' => 'require',
        'pid'  => 'require',
    ];

    protected $message = [
        'pid.require'  => '父级必须',
        'name.require' => '名称必须',
    ];
}
