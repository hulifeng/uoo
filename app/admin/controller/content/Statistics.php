<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/25 0025
 * Time: 20:45
 */

namespace app\admin\controller\content;

use app\admin\service\StatisticsService;
use app\BaseController;

class Statistics extends BaseController
{
    public function __construct(StatisticsService $service)
    {
        $this->service = $service;
    }

    // 趋势分析
    public function trend($type = 'today', $pageNo = 1, $pageSize = 20)
    {
        $send = $this->service->getTrend($type, $pageNo, $pageSize);

        return $this->sendSuccess($send);
    }

    // 用户活跃度
    public function liveness($type = 'today', $pageNo = 1, $pageSize = 20)
    {
        $send = $this->service->getLiveNess($type, $pageNo, $pageSize);

        return $this->sendSuccess($send);
    }

    // 受访页面
    public function pageres($type = 'today', $pageNo = 1, $pageSize = 20)
    {
        $send = $this->service->getPageRes($type, $pageNo, $pageSize);

        return $this->sendSuccess($send);
    }

    // 仪表盘
    public function dashboard()
    {
        $send = $this->service->index();

        return $this->sendSuccess($send);
    }

    // 用户列表
    public function user($pageNo, $pageSize)
    {
        $send = $this->service->getUserList($pageNo, $pageSize);

        return $this->sendSuccess($send);
    }

    // 分享
    public function share()
    {
        $send = $this->service->getShare($type = 'today');

        return $this->sendSuccess($send);
    }
}
