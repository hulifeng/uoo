<?php

declare(strict_types=1);

namespace app\common\model;

use app\BaseModel;
use app\common\traits\Log;
use xiaodi\Permission\Contract\PermissionContract;

class Permission extends BaseModel implements PermissionContract
{
    use Log;
    use \xiaodi\Permission\Traits\Permission;
}
