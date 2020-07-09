<?php

declare(strict_types=1);

namespace app\admin\controller\system;

use app\BaseController;
use app\admin\request\PermissionRequest;
use app\admin\service\PermissionService;

class Permission extends BaseController
{
    public function __construct(PermissionService $service)
    {
        $this->service = $service;
    }

    /**
     * 规则列表.
     *
     * @param [type] $pageSize
     * @param [type] $pageNo
     *
     * @return \think\Response
     */
    public function list($pageSize, $pageNo)
    {
        $result = $this->service->list((int) $pageNo, (int) $pageSize);

        return $this->sendSuccess($result);
    }

    /**
     * 添加规则.
     *
     * @return \think\Response
     */
    public function create(PermissionRequest $request)
    {
        if (!$request->scene('create')->validate()) {
            return $this->sendError($request->getError());
        }

        if ($this->service->create($request->param()) === false) {
            return $this->sendError();
        }

        return $this->sendSuccess();
    }

    /**
     * 更新规则.
     *
     * @param [type] $id
     *
     * @return \think\Response
     */
    public function update($id, PermissionRequest $request)
    {
        if (!$request->scene('update')->validate()) {
            return $this->sendError($request->getError());
        }

        if ($this->service->update($id, $request->param()) === false) {
            return $this->sendError();
        }

        return $this->sendSuccess();
    }

    /**
     * 删除规则.
     *
     * @param [type] $id
     *
     * @return \think\Response
     */
    public function delete($id)
    {
        if ($this->service->delete($id) === false) {
            return $this->sendError();
        }

        return $this->sendSuccess();
    }
}
