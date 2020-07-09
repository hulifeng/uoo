<?php

declare(strict_types=1);

namespace app\common\model;

use app\BaseModel;
use app\common\traits\Log;
use think\model\relation\BelongsToMany;
use xiaodi\Permission\Contract\RoleContract;

class Role extends BaseModel implements RoleContract
{
    use Log;
    use \xiaodi\Permission\Traits\Role;

    /**
     * 获取角色下部门.
     */
    public function depts(): BelongsToMany
    {
        return $this->belongsToMany(
            Dept::class,
            RoleDeptAccess::class,
            'dept_id',
            'role_id'
        );
    }
}
