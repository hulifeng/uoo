<?php

declare(strict_types=1);

namespace app\common\service;

use app\BaseService;
use app\common\model\Member;

class MemberService extends BaseService
{
    public function __construct(Member $model)
    {
        $this->model = $model;
    }

    /**
     * 处理用户信息更新与创建.
     *
     * @param [type] $user
     *
     * @return void
     */
    public function handleWechatCallback($user)
    {
        $member = $this->model->where('openid', $user->id)->find();
        $user = $user->toArray();
        $user['openid'] = $user['id'];
        unset($user['id']);

        if (!$member) {
            $this->model->save($user);
            $member = $this->model;
        } else {
            $member->save($user);
        }

        return $member;
    }
}
