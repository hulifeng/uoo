<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/18 0018
 * Time: 15:03
 */

namespace app\admin\controller\content;

use app\admin\service\HouseService;
use app\BaseController;
use think\Request;
use app\common\model\House as HouseModel;

class House extends BaseController
{
    public function __construct(HouseService $service)
    {
        $this->service = $service;
    }

    public function list($pageNo = 1, $pageSize = 10)
    {
        $send = $this->service->getList((int) $pageNo, (int) $pageSize);

        return $this->sendSuccess($send);
    }

    public function link(Request $request)
    {
        if (!$request->param('id')) return $this->sendError('请输入房源ID');

        // 校验房源是否录入
        $isExists = HouseModel::where('link_id', $request->param('id'))->where("is_entering", 1)->value('id');
        if ($isExists) return $this->sendError('房源已存在');

        $data = $this->service->getLinkData($request->param('id'), $request->user->id, $request->param('type'));

        if ($data === false) return $this->sendError('房源查找失败');

        return $this->sendSuccess($data);
    }

    public function create(Request $request)
    {
        $id = $request->param('id');

        if ($this->service->add($id) === false) {
            return $this->sendError($this->service->getError());
        }

        return $this->sendSuccess($request->param());
    }

    public function isTop(Request $request)
    {
        if ($this->service->reTop($request->param('id'), ['is_top' => $request->param('is_top')]) === false) {
            return $this->sendError($this->service->getError());
        }

        return $this->sendSuccess();
    }

    public function delete($id)
    {
        if ($this->service->remove($id) === false) {
            return $this->sendError('操作失败！');
        }

        return $this->sendSuccess();
    }
}
