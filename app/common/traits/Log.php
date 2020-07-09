<?php

declare(strict_types=1);

namespace app\common\traits;

use think\Model;
use app\common\model\DataBaseLog;

trait Log
{
    /**
     * 新增后.
     */
    public static function onAfterInsert(Model $model)
    {
        DataBaseLog::create([
            'user_id' => request()->user->id,
            'model'   => $model->getName(),
            'url'     => request()->url(),
            'action'  => 'insert',
            'sql'     => $model->getLastSql(),
        ]);
    }

    /**
     * 更新后.
     */
    public static function onAfterUpdate(Model $model)
    {
        DataBaseLog::create([
            'user_id' => request()->user->id,
            'model'   => $model->getName(),
            'url'     => request()->url(),
            'action'  => 'update',
            'sql'     => $model->getLastSql(),
        ]);
    }

    /**
     * 删除后.
     */
    public static function onAfterDelete(Model $model)
    {
        DataBaseLog::create([
            'user_id' => request()->user->id,
            'model'   => $model->getName(),
            'url'     => request()->url(),
            'action'  => 'delete',
            'sql'     => $model->getLastSql(),
        ]);
    }
}
