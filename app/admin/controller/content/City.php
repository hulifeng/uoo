<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/20 0020
 * Time: 10:24
 */

namespace app\admin\controller\content;

use app\admin\service\CityService;
use app\BaseController;

class City extends BaseController
{
    public function __construct(CityService $service)
    {
        $this->service = $service;
    }

    public function list()
    {
        $send = $this->service->getList();

        return $this->sendSuccess($send);
    }
}
