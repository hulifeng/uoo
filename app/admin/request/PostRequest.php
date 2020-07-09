<?php

declare(strict_types=1);

namespace app\admin\request;

use app\BaseRequest;

class PostRequest extends BaseRequest
{
    protected $rule = [
        'name' => 'require',
        'code' => 'require',
    ];

    protected $message = [
        'name.require' => '名称必须',
        'code.require' => '标识必须',
        'code.unique'  => '标识重复',
    ];

    protected $scene = [
        'create' => ['name', 'code'],
        'update' => ['name', 'code'],
    ];
}
