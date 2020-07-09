<?php

declare(strict_types=1);

namespace app\common\model;

use app\BaseModel;
use app\common\traits\Log;
use think\model\relation\BelongsToMany;
use xiaodi\Permission\Contract\UserContract;

class User extends BaseModel implements UserContract
{
    use Log;
    use \xiaodi\Permission\Traits\User;

    /**
     * 获取用户所有岗位.
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(
            Post::class,
            UserPostAccess::class,
            'post_id',
            'user_id'
        );
    }
}
