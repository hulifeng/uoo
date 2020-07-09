<?php

declare(strict_types=1);

namespace app\common\traits;

trait SortTable
{
    public $sortBy = 'createTime';

    public $sortOrder = 'asc';

    public function setSortBy($sortBy = 'createTime')
    {
        $this->sortBy = $sortBy;
    }

    public function setSortOrder($sortOrder = 'desc')
    {
        $this->sortOrder = $sortOrder;
    }
}
