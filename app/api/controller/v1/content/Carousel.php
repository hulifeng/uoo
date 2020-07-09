<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/17 0017
 * Time: 12:21
 */

namespace app\api\controller\v1\content;

use app\BaseController;

class Carousel extends BaseController
{
    public function list()
    {
        $data = [
            [
                'id' => 1, 'title' => '一级标题', 'sub_title' => '二级标题',
                'img' => 'https://main-uoolu.uoolu.com/images/dd0d907bccc11767e47c02710180395a.png',
                'content' => '',
                'created_at' => '2020-06-17'
            ],
            [
                'id' => 2, 'title' => '一级标题2', 'sub_title' => '二级标题2',
                'img' => 'https://main-uoolu.uoolu.com/images/35717301046f5fccd0d1d9cae9b84e83.jpg',
                'content' => '',
                'created_at' => '2020-06-17'
            ],
            [
                'id' => 3, 'title' => '一级标题3', 'sub_title' => '二级标题3',
                'img' => 'https://main-uoolu.uoolu.com/images/590696e47cce73a98e9179e2b6d2a93d.jpg',
                'content' => '',
                'created_at' => '2020-06-17'
            ]
        ];
        return $this->sendSuccess($data);
    }
}
