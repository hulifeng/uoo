<?php

declare(strict_types=1);

namespace Tant\Abstracts;

use think\Model;
use think\facade\Db;
use Tant\Traits\Error;

abstract class AbstractService
{
    use Error;

    public $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function paginate($pageNo, $pageSize)
    {
        $data = $this->model
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

    public function create(array $data)
    {
        return $this->model->save($data);
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function update($id, array $data)
    {
        return $this->model->find($id)->save($data);
    }

    public function delete($id)
    {
        return $this->model->find($id)->delete();
    }

    public function transaction($callback)
    {
        return Db::transaction($callback);
    }
}
