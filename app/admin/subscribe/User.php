<?php

declare(strict_types=1);

namespace app\admin\subscribe;

use app\common\model\AccountLog;
use app\admin\event\UserLogin as Event;

class User
{
    /**
     * 用户登录事件监听处理.
     *
     * @param UserLogin $event
     */
    public function onUserLogin(Event $event)
    {
        AccountLog::create([
            'user_id'    => $event->user->id,
            'action'     => '登录',
            'url'        => request()->url(),
            'ip'         => request()->ip(),
            'user_agent' => request()->header('USER_AGENT'),
        ]);
    }
}
