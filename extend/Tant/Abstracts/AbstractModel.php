<?php

declare(strict_types=1);

namespace Tant\Abstracts;

use think\Model;
use Tant\DataScope\Scope;

abstract class AbstractModel extends Model
{
    public $sortBy = 'create_time';

    public $sortOrder = 'asc';

    protected $autoWriteTimestamp = true;

    protected $createTime = 'create_time';

    protected $updateTime = 'update_time';

    /**
     * 获取所有.
     */
    public function all()
    {
        return $this->order($this->sortBy, $this->sortOrder)
            ->select();
    }

    /**
     * 数据权限 (数据范围).
     *
     * @param [type] $query
     * @param [type] $alias
     */
    public function scopeDataAccess($query, $alias)
    {
        $dataScope = new Scope();
        $sql = $dataScope->handle($alias);

        $query->where($sql);
    }
}
