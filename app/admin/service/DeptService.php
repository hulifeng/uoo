<?php

declare(strict_types=1);

/*
 * This file is part of TAnt.
 * @link     https://github.com/edenleung/think-admin
 * @document https://www.kancloud.cn/manual/thinkphp6_0
 * @contact  QQ Group 996887666
 * @author   Eden Leung 758861884@qq.com
 * @copyright 2019 Eden Leung
 * @license  https://github.com/edenleung/think-admin/blob/6.0/LICENSE.txt
 */

namespace app\admin\service;

use app\BaseService;
use app\common\model\Dept;

class DeptService extends BaseService
{
    public function __construct(Dept $dept)
    {
        $this->model = $dept;
    }

    /**
     * 获取树状结构.
     */
    public function getTree()
    {
        $data = $this->model->select();
        $category = new \Tant\Util\Category(['id', 'pid', 'name', 'cname']);

        return $category->formatTree($data->toArray());
    }

    /**
     * 获取子部门.
     *
     * @param [type] $deptPid
     */
    public function getChildrenDepts($deptPid)
    {
        $data = $this->model->select();
        $category = new \Tant\Util\Category(['id', 'pid', 'name', 'cname']);

        return $category->getTree($data->toArray(), $deptPid);
    }
}
