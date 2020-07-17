<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/17 0017
 * Time: 20:31
 */

namespace app\admin\controller\content;


use app\admin\service\ConfigService;
use app\BaseController;
use app\Request;

class Config extends BaseController
{
    public function __construct(ConfigService $service)
    {
        $this->service = $service;
    }

    public function list()
    {
        $send = $this->service->getData();

        return $this->sendSuccess($send);
    }

    public function update(Request $request)
    {
        if ($this->service->renew($request->param()) === false) {
            return $this->sendError($this->service->getError());
        }

        return $this->sendSuccess();
    }
}
