<?php

declare(strict_types=1);

namespace app\admin\service;

use app\BaseService;
use app\common\model\Post;

class PostService extends BaseService
{
    public function __construct(Post $post)
    {
        $this->model = $post;
    }

    /**
     * 岗位列表.
     */
    public function all()
    {
        $data = $this->model->order('sort desc')->select();

        return $data;
    }
}
