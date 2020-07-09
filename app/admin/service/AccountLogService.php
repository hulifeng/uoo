<?php

declare(strict_types=1);

namespace app\admin\service;

use app\BaseService;
use app\common\model\AccountLog;

class AccountLogService extends BaseService
{
    public function __construct(AccountLog $model)
    {
        $this->model = $model;
    }

    /**
     * 获取日志列表.
     */
    public function list(int $pageNo, int $pageSize)
    {
        $data = $this->model->alias('l')
            ->join('user u', 'u.id = l.user_id')
            ->field('l.*,u.nickname')
            ->order('create_time desc')
            ->paginate([
                'list_rows' => $pageSize,
                'page'      => $pageNo,
            ]);

        return [
            'data'       => $data->items(),
            'pageSize'   => $pageSize,
            'pageNo'     => $pageNo,
            'totalPage'  => count($data->items()),
            'totalCount' => $data->total(),
        ];
    }

    /**
     * 删除日志.
     *
     * @param mixed $id
     */
    public function remove($id)
    {
        $ids = explode(',', $id);

        if (empty($ids)) {
            return false;
        }

        $this->model->whereIn('id', $ids)->delete();

        return true;
    }
}
