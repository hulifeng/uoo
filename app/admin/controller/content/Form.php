<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/9 0009
 * Time: 0:36
 */

namespace app\admin\controller\content;

use app\admin\service\FormService;
use app\BaseController;

class Form extends BaseController
{
    public function __construct(FormService $service)
    {
        $this->service = $service;
    }

    public function list($pageNo, $pageSize)
    {
        $send = $this->service->getList((int) $pageNo, (int) $pageSize);

        return $this->sendSuccess($send);
    }

    public function show($id)
    {
        return $this->sendSuccess($this->service->show($id));
    }

    public function save($id)
    {
        if ($this->service->renew($id) === false) {
            return $this->sendError($this->service->getError());
        }

        return $this->sendSuccess();
    }
}
