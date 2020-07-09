<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/25 0025
 * Time: 20:46
 */

namespace app\admin\service;

use app\BaseService;
use app\common\model\Share;
use app\common\model\ShareLog;
use app\common\model\UserBehavior;
use app\common\model\UserBehaviorLog;
use app\Request;
use think\facade\Db;
use app\common\model\UooUser;

class StatisticsService extends BaseService
{
    protected $time;

    public function __construct(UserBehavior $behavior, TimeService $time)
    {
        $this->model = $behavior;
        $this->time = $time;
    }

    // 首页数据
    public function index()
    {
        // 用户数
        $today = $this->totalStatistics('today');
        $yesterday = $this->totalStatistics('yesterday');
        $data = $this->twoHourStatistics('today');
        $chart = [];
        if (sizeof($data)) {
            foreach ($data as $k => $value) {
                if (array_key_exists('is_new', $value)) {
                    $chart[] = [
                        'trend' => '新增人数',
                        'time' => date("H:i", strtotime($value['date_str'])),
                        'value' => (int)$value['is_new']
                    ];
                }
                if (array_key_exists('visit_user_num', $value)) {
                    $chart[] = [
                        'trend' => '访问人数',
                        'time' => date("H:i", strtotime($value['date_str'])),
                        'value' => (int)$value['visit_user_num']
                    ];
                }
                if (array_key_exists('visit_num', $value)) {
                    $chart[] = [
                        'trend' => '发访次数',
                        'time' => date("H:i", strtotime($value['date_str'])),
                        'value' => (int)$value['visit_num']
                    ];
                }
                if (array_key_exists('open_num', $value)) {
                    $chart[] = [
                        'trend' => '打开次数',
                        'time' => date("H:i", strtotime($value['date_str'])),
                        'value' => (int)$value['open_num']
                    ];
                }
                if (array_key_exists('avg_stay_time', $value)) {
                    $chart[] = [
                        'trend' => '停留时长',
                        'time' => date("H:i", strtotime($value['date_str'])),
                        'value' => (int)$value['avg_stay_time']
                    ];
                }
                if (array_key_exists('break_num', $value)) {
                    $chart[] = [
                        'trend' => '跳出率',
                        'time' => date("H:i", strtotime($value['date_str'])),
                        'value' => (int)$value['break_num']
                    ];
                }
            }
        }
        $source = UooUser::field(['COUNT(DISTINCT appname)' => 'count', 'appname' => 'item'])->group("appname")->select();
        return [
            'today' => $today,
            'yesterday' => $yesterday,
            'pages' => $this->topTenPages(),
            'todayData' => $chart,
            'source' => $source
        ];
    }

    // 趋势
    public function getTrend($type)
    {
        $timeService = new TimeService();

        $total = [];
        $data = [];
        switch ($type) {
            case "today":
                $total = $this->totalStatistics('today');
                $data = $this->twoHourStatistics('today');
                break;
            case "yesterday":
                $total = $this->totalStatistics('yesterday');
                $data = $this->twoHourStatistics('yesterday');
                break;
            case "latest_seven_week":
                $between_week = $timeService::last_seven_day();
                $total = $this->totalStatistics('latest_seven_week', $between_week[0], $between_week[1]);
                $data = $this->dayStatistics('latest_seven_week', $between_week[0], $between_week[1]);
                break;
            case "latest_thirty":
                $between_thirty = $timeService::last_thirty_day();
                $total = $this->totalStatistics('latest_thirty', $between_thirty[0], $between_thirty[1]);
                $data = $this->dayStatistics('latest_thirty', $between_thirty[0], $between_thirty[1]);
                break;
            case "filter_search":
                $total = $this->totalStatistics('filter_search', request()->param('start'), request()->param('end'));
                $data = $this->dayStatistics('filter_search', request()->param('start'), request()->param('end'));
                break;
        }

        $chart = [];
        if (sizeof($data)) {
            foreach ($data as $k => $value) {
                if (array_key_exists('is_new', $value)) {
                    $chart[] = [
                        'trend' => '新增人数',
                        'time' => in_array($type, ['today', 'yesterday']) ? date("H:i", strtotime($value['date_str'])) : date("m/d", strtotime($value['date_str'])),
                        'value' => (int)$value['is_new']
                    ];
                }
                if (array_key_exists('visit_user_num', $value)) {
                    $chart[] = [
                        'trend' => '访问人数',
                        'time' => in_array($type, ['today', 'yesterday']) ? date("H:i", strtotime($value['date_str'])) : date("m/d", strtotime($value['date_str'])),
                        'value' => (int)$value['visit_user_num']
                    ];
                }
                if (array_key_exists('visit_num', $value)) {
                    $chart[] = [
                        'trend' => '发访次数',
                        'time' => in_array($type, ['today', 'yesterday']) ? date("H:i", strtotime($value['date_str'])) : date("m/d", strtotime($value['date_str'])),
                        'value' => (int)$value['visit_num']
                    ];
                }
                if (array_key_exists('open_num', $value)) {
                    $chart[] = [
                        'trend' => '打开次数',
                        'time' => in_array($type, ['today', 'yesterday']) ? date("H:i", strtotime($value['date_str'])) : date("m/d", strtotime($value['date_str'])),
                        'value' => (int)$value['open_num']
                    ];
                }
                if (array_key_exists('avg_stay_time', $value)) {
                    $chart[] = [
                        'trend' => '停留时长',
                        'time' => in_array($type, ['today', 'yesterday']) ? date("H:i", strtotime($value['date_str'])) : date("m/d", strtotime($value['date_str'])),
                        'value' => (int)$value['avg_stay_time']
                    ];
                }
                if (array_key_exists('break_num', $value)) {
                    $chart[] = [
                        'trend' => '跳出率',
                        'time' => in_array($type, ['today', 'yesterday']) ? date("H:i", strtotime($value['date_str'])) : date("m/d", strtotime($value['date_str'])),
                        'value' => (int)$value['break_num']
                    ];
                }
            }
        }

        return [
            'total' => $total,
            'list' => $data,
            'chart' => $chart,
            'date' => [
                'today' => [
                    'start' => date("Y-m-d", time()),
                    'end' => date("Y-m-d", time())
                ],
                'yesterday' => [
                    'start' => date("Y-m-d", strtotime('-1 day')),
                    'end' => date("Y-m-d", strtotime('-1 day')),
                ],
                'last_week' => [
                    'start' => date("Y-m-d", strtotime('-7 day')),
                    'end' => date("Y-m-d", strtotime('-1 day'))
                ],
                'last_thirty' => [
                    'start' => date("Y-m-d", strtotime('-30 day')),
                    'end' => date("Y-m-d", strtotime('-1 day'))
                ],
            ]
        ];
    }

    // 每两小时为间隔统计
    public function twoHourStatistics($type)
    {
        $cdate = $type == 'today' ? '@cdate := DATE_ADD(CURDATE(), INTERVAL + 1 DAY)' : '@cdate := CURDATE()';
        $precise_day = $type == 'today' ? 'UNIX_TIMESTAMP(CURDATE())' : 'UNIX_TIMESTAMP(CURDATE()-1)';

        $sql = "SELECT 
                    x.`date_str`,
                    IFNULL(d.is_new, 0) AS is_new,
                    IFNULL(d.visit_user_num, 0) AS visit_user_num,
                    IFNULL(d.visit_num, 0) AS visit_num,
                    IFNULL(d.open_num, 0) AS open_num,
                    IFNULL(d.avg_stay_time, 0) AS avg_stay_time,
                    IFNULL(d.break_num, '0.00%') AS break_num 
                  FROM
                    (SELECT 
                      @cdate := DATE_ADD(@cdate, INTERVAL - 2 HOUR) AS date_str 
                    FROM
                      (SELECT 
                        $cdate
                      FROM
                        city 
                      LIMIT 12) tmp1) `x`
                    LEFT JOIN 
                      (SELECT 
                        FROM_UNIXTIME(FLOOR(precise_time / 7200) * 7200) AS house_time,
                        SUM(is_new) AS is_new,
                        COUNT(DISTINCT user_id) AS visit_user_num,
                        SUM(visit_num) AS visit_num,
                        SUM(open_num) AS open_num,
                        FLOOR((SUM(stay_time) / SUM(open_num))) AS avg_stay_time,
                        CONCAT(ROUND(SUM(break_num)/SUM(visit_num)*100, 2), '%') AS break_num
                      FROM
                        user_behavior 
                      WHERE precise_day = $precise_day
                      GROUP BY house_time 
                      ORDER BY house_time) d 
                      ON x.`date_str` = d.house_time 
                  ORDER BY x.`date_str`";

        $data = Db::query($sql);

        return $data;
    }

    // 以天为间隔统计
    public function dayStatistics($type, $start = '', $end = '')
    {
        $cdate = "CURDATE()";
        switch ($type) {
            case 'latest_seven_week':
                $limit = 7;
                break;
            case 'latest_thirty':
                $limit = 30;
                break;
            default:
                $cdate = "DATE_ADD('{$end}', INTERVAL + 1 DAY)";
                $start = strtotime(date("Y-m-d 00:00:00", strtotime(request()->param('start'))));
                $end = strtotime(date("Y-m-d 23:59:59", strtotime(request()->param('end'))));
                $limit = TimeService::getBetweenTwoDate(request()->param('start'), request()->param('end'));
                break;
        }

        $where = "where precise_time between '{$start}' and '{$end}'";

        $sql = "SELECT 
                  x.`date_str`,
                  IFNULL(d.is_new, 0) AS is_new,
                  IFNULL(d.visit_user_num, 0) AS visit_user_num,
                  IFNULL(d.visit_num, 0) AS visit_num,
                  IFNULL(d.open_num, 0) AS open_num,
                  IFNULL(d.stay_time, 0) AS stay_time,
                  IFNULL(d.avg_stay_time, 0) AS avg_stay_time,
                  IFNULL(d.break_num, '0.00%') AS break_num 
                FROM
                  (SELECT 
                    @cdate := DATE_ADD(@cdate, INTERVAL - 1 DAY) AS date_str 
                  FROM
                    (SELECT 
                      @cdate := $cdate
                    FROM
                      city 
                    LIMIT $limit) tmp1) `x`
                  LEFT JOIN 
                    (SELECT 
                      DATE(
                        FROM_UNIXTIME(precise_day, '%Y%m%d')
                      ) AS house_time,
                      SUM(is_new) AS is_new,
                      COUNT(DISTINCT user_id) AS visit_user_num,
                      SUM(visit_num) AS visit_num,
                      SUM(open_num) AS open_num,
                      SUM(stay_time) AS stay_time,
                      FLOOR((SUM(stay_time) / SUM(open_num))) AS avg_stay_time,
                      CONCAT(ROUND(SUM(break_num)/SUM(visit_num)*100, 2), '%') AS break_num 
                    FROM
                      user_behavior $where
                    GROUP BY house_time 
                    ORDER BY house_time) d 
                    ON x.`date_str` = d.house_time 
                ORDER BY x.`date_str` DESC ";

        $data = Db::query($sql);

        return $data;
    }

    // 统计所有
    public function totalStatistics($type, $start = '', $end = '')
    {
        $field = [
            "SUM(is_new)" => 'is_new', "COUNT(DISTINCT user_id)" => "visit_user_num", "SUM(visit_num)" => 'visit_num', "SUM(open_num)" => 'open_num',
            "FLOOR(SUM(stay_time)/SUM(visit_num))" => 'stay_time', "CONCAT(ROUND(SUM(break_num)/SUM(visit_num) * 100, 2), '%')" => 'break_num',
        ];

        $where = [];

        $group = 'precise_day';

        switch ($type) {
            case 'today':
                $where = ['precise_day' => strtotime(date("Y-m-d", time()))];
                break;
            case 'yesterday':
                $where = ['precise_day' => strtotime(date("Y-m-d", strtotime('-1 day')))];
                break;
            case 'filter_search':
                $where[] = ['precise_time', 'between', [strtotime($start . '00:00:00'), strtotime($end . '23:59:59')]];
                break;
            default:
                $where[] = ['precise_time', 'between', [$start, $end]];
                break;
        }

        $res = $this->model->field($field)->where($where)->group($group)->find();

        return [
            'is_new' => isset($res->is_new) ? $res->is_new : "0",
            'visit_user_num' => isset($res->visit_user_num) ? $res->visit_user_num : "0",
            'visit_num' => isset($res->visit_num) ? $res->visit_num : "0",
            'open_num' => isset($res->open_num) ? $res->open_num : "0",
            'stay_time' => isset($res->stay_time) ? $res->stay_time : "0",
            'break_num' => isset($res->break_num) ? $res->break_num : "0.00%",
        ];
    }

    // 日活
    public function getLiveNess($type, int $pageNo, int $pageSize)
    {
        $cdate = "CURDATE()";
        switch ($type) {
            case "today":
                $limit = 1;
                $cdate = "CURDATE() + 1";
                $timestamp = strtotime(date('Y-m-d', time()));
                $where = "precise_day = '{$timestamp}'";
                break;
            case "yesterday":
                $limit = 1;
                $yesterday = strtotime(date('Y-m-d', strtotime('-1 day')));
                $where = "precise_day = '{$yesterday}'";
                break;
            case "latest_seven_week":
                $limit = 7;
                $latest_seven = $this->time::last_seven_day();
                $where = "precise_day between '{$latest_seven[0]}' and '{$latest_seven[1]}'";
                break;
            case "latest_thirty":
                $limit = 30;
                $latest_thirty = $this->time::last_thirty_day();
                $where = "precise_day between '{$latest_thirty[0]}' and '{$latest_thirty[1]}'";
                break;
            case "filter_search":
                $cdate = "CURDATE() + 1";
                $start = strtotime(request()->param('start') . ' 00:00:00');
                $end = strtotime(request()->param('end') . ' 23:59:59');
                $limit = $this->time::getBetweenTwoDate($start, $end);
                $where = "precise_day between '{$start}' and '{$end}'";
                break;
        }

        $data = $this->liveNessStatistics($where, $limit, $cdate);

        return [
            'data' => $data,
            'chat' => $data,
            'date' => [
                'today' => [
                    'start' => date("Y-m-d", time()),
                    'end' => date("Y-m-d", time())
                ],
                'yesterday' => [
                    'start' => date("Y-m-d", strtotime('-1 day')),
                    'end' => date("Y-m-d", strtotime('-1 day')),
                ],
                'last_week' => [
                    'start' => date("Y-m-d", strtotime('-7 day')),
                    'end' => date("Y-m-d", strtotime('-1 day'))
                ],
                'last_thirty' => [
                    'start' => date("Y-m-d", strtotime('-30 day')),
                    'end' => date("Y-m-d", strtotime('-1 day'))
                ],
            ]
        ];
    }

    // 统计日活
    public function liveNessStatistics($where, $limit, $cdate)
    {

        $sql = "SELECT 
                  DATE_FORMAT(x.`date_str`, '%m-%d') as live_date,
                  IFNULL(d.live, 0) AS live
                FROM
                  (SELECT 
                    @cdate := DATE_ADD(@cdate, INTERVAL - 1 DAY) AS date_str 
                  FROM
                    (SELECT 
                      @cdate := $cdate
                    FROM
                      city 
                    LIMIT $limit) tmp1) `x`
                  LEFT JOIN 
                    (SELECT 
                      count(distinct `user_id`) as live,
                      DATE(
                        FROM_UNIXTIME(precise_day, '%Y%m%d')
                      ) AS house_time
                    FROM
                      user_behavior where $where
                    GROUP BY precise_day 
                    ORDER BY precise_day) d 
                    ON x.`date_str` = d.house_time 
                ORDER BY x.`date_str` DESC ";

        $data = Db::query($sql);

        return $data;
    }

    // 受访页面
    public function getPageRes($type, int $pageNo, int $pageSize)
    {
        $where = [];
        switch ($type) {
            case "today":
                $where = ['precise_day' => strtotime(date("Y-m-d", time()))];
                break;
            case "yesterday":
                $where = ['precise_day' => strtotime(date("Y-m-d", strtotime('-1 day')))];
                break;
            case "latest_seven_week":
                $seven_between = $this->time::last_seven_day();
                $where[] = ['precise_day', 'between', [$seven_between[0], $seven_between[1]]];
                break;
            case "latest_thirty":
                $thirty_between = $this->time::last_thirty_day();
                $where[] = ['precise_day', 'between', [$thirty_between[0], $thirty_between[1]]];
                break;
            case "filter_search":
                $start = strtotime(request()->param('start') . ' 00:00:00');
                $end = strtotime(request()->param('end') . '23:59:59');
                $where[] = ['precise_day', 'between', [$start, $end]];
                break;
        }

        $field = [
            'page',
            'COUNT(DISTINCT user_id)' => 'visit_user_num',
            'COUNT( type = 996 or null )' => 'visit_num',
            'SUM(IF(`is_logout` = 1, 1, 0))' => 'logout_num',
            'CONCAT(ROUND( SUM(IF(`is_logout` = 1, 1, 0)) / COUNT(type = 996 or null) * 100, 2 ), "%")' => 'logout_precent',
            'SUM(IF(`type` = 2, 1, 0))' => 'share_num',
            'CEIL(SUM(IF(`type` = 996, stay_time, 0)) / COUNT( type = 996 or null ))' => 'avg_stay_time',
        ];

        $data = UserBehaviorLog::where($where)->group('page')->field($field)->select()->toArray();

        unset($field[0]);
        $total = UserBehaviorLog::where($where)->field($field)->find()->toArray();

        return [
            'total' => $total,
            'data' => $data,
            'date' => [
                'today' => [
                    'start' => date("Y-m-d", time()),
                    'end' => date("Y-m-d", time())
                ],
                'yesterday' => [
                    'start' => date("Y-m-d", strtotime('-1 day')),
                    'end' => date("Y-m-d", strtotime('-1 day')),
                ],
                'last_week' => [
                    'start' => date("Y-m-d", strtotime('-7 day')),
                    'end' => date("Y-m-d", strtotime('-1 day'))
                ],
                'last_thirty' => [
                    'start' => date("Y-m-d", strtotime('-30 day')),
                    'end' => date("Y-m-d", strtotime('-1 day'))
                ],
            ]
        ];
    }

    // 统计TOP10受访页面
    public function topTenPages()
    {
        $page_config = config('uoolu.pages');
        $pages = UserBehaviorLog::where('type', 996)->field(['COUNT(id)' => 'total', 'page' => 'url'])->group('page')->order('total desc')->limit(10)->select();
        foreach ($pages as &$value) {
            $value['name'] = $page_config[$value['url']];
        }
        return $pages;
    }

    // 用户列表
    public function getUserList($pageNo, $pageSize)
    {

        $field = ['user_number', 'nickname', 'avatar', 'gender', 'province', 'inviter_id', 'city', 'appname', 'system_info'];
        $total = UooUser::count();
        $data = UooUser::field($field)->page($pageNo)->limit($pageSize)->order('create_time desc')->select();

        return [
            'data' => $data,
            'pageSize' => $pageSize,
            'pageNo' => $pageNo,
            'totalPage' => ceil($total / $pageSize),
            'totalCount' => $total
        ];
    }

    // 分享页面
    public function getShare()
    {
        $timeService = new TimeService();

        $total = [];
        $data = [];
        $type = \request()->param('type');
        switch ($type) {
            case "today":
                $total = $this->totalStatisticsShare('today');
                $data = $this->twoHourStatisticsShare('today');
                break;
            case "yesterday":
                $total = $this->totalStatisticsShare('yesterday');
                $data = $this->twoHourStatisticsShare('yesterday');
                break;
            case "latest_seven_week":
                $between_week = $timeService::last_seven_day();
                $total = $this->totalStatisticsShare('latest_seven_week', $between_week[0], $between_week[1]);
                $data = $this->dayStatisticsShare('latest_seven_week', $between_week[0], $between_week[1]);
                break;
            case "latest_thirty":
                $between_thirty = $timeService::last_thirty_day();
                $total = $this->totalStatistics('latest_thirty', $between_thirty[0], $between_thirty[1]);
                $data = $this->dayStatisticsShare('latest_thirty', $between_thirty[0], $between_thirty[1]);
                break;
            case "filter_search":
                $total = $this->totalStatistics('filter_search', request()->param('start'), request()->param('end'));
                $data = $this->dayStatisticsShare('filter_search', request()->param('start'), request()->param('end'));
                break;
        }

        $chart = [];
        if (sizeof($data)) {
            foreach ($data as $k => $value) {
                if (array_key_exists('share_user_num', $value)) {
                    $chart[] = [
                        'share' => '分享人数',
                        'time' => in_array($type, ['today', 'yesterday']) ? date("H:i", strtotime($value['date_str'])) : date("m/d", strtotime($value['date_str'])),
                        'value' => (int)$value['share_user_num']
                    ];
                }
                if (array_key_exists('share_num', $value)) {
                    $chart[] = [
                        'share' => '分享次数',
                        'time' => in_array($type, ['today', 'yesterday']) ? date("H:i", strtotime($value['date_str'])) : date("m/d", strtotime($value['date_str'])),
                        'value' => (int)$value['share_num']
                    ];
                }
                if (array_key_exists('return_num', $value)) {
                    $chart[] = [
                        'share' => '回流量',
                        'time' => in_array($type, ['today', 'yesterday']) ? date("H:i", strtotime($value['date_str'])) : date("m/d", strtotime($value['date_str'])),
                        'value' => (int)$value['return_num']
                    ];
                }
                if (array_key_exists('return_percent', $value)) {
                    $chart[] = [
                        'share' => '回流比',
                        'time' => in_array($type, ['today', 'yesterday']) ? date("H:i", strtotime($value['date_str'])) : date("m/d", strtotime($value['date_str'])),
                        'value' => (int)$value['return_percent']
                    ];
                }
                if (array_key_exists('new_user_num', $value)) {
                    $chart[] = [
                        'share' => '分享新增',
                        'time' => in_array($type, ['today', 'yesterday']) ? date("H:i", strtotime($value['date_str'])) : date("m/d", strtotime($value['date_str'])),
                        'value' => (int)$value['new_user_num']
                    ];
                }
            }
        }

        ksort($chart);

        return [
            'total' => $total,
            'list' => $data,
            'chart' => $chart,
            'date' => [
                'today' => [
                    'start' => date("Y-m-d", time()),
                    'end' => date("Y-m-d", time())
                ],
                'yesterday' => [
                    'start' => date("Y-m-d", strtotime('-1 day')),
                    'end' => date("Y-m-d", strtotime('-1 day')),
                ],
                'last_week' => [
                    'start' => date("Y-m-d", strtotime('-7 day')),
                    'end' => date("Y-m-d", strtotime('-1 day'))
                ],
                'last_thirty' => [
                    'start' => date("Y-m-d", strtotime('-30 day')),
                    'end' => date("Y-m-d", strtotime('-1 day'))
                ],
            ]
        ];
    }

    // 总的分享统计
    public function totalStatisticsShare($type, $start = '', $end = '')
    {
        if (!$start) {
            $yesterday = $this->time::yesterday();
            $start = $yesterday[0];
            $end = $yesterday[1];
            if ($type == 'today') {
                $today = $this->time::today();
                $start = $today[0];
                $end = $today[1];
            }
        }

        $where = [];

        $where[] = ['create_time', 'between', [$start, $end]];

        // 查询分享次数和分享人数
        $data = Share::field(['COUNT(id)' => 'share_num', 'COUNT(DISTINCT user_id)' => 'share_user_num'])->where($where)->find()->toArray();

        $data2 = ShareLog::field(['COUNT(id)' => 'return_num', 'SUM(is_reg)' => 'new_user_num'])->where($where)->find()->toArray();

        return [
            'share_num' => $data['share_num'],
            'share_user_num' => $data['share_user_num'],
            'return_num' => $data2['return_num'],
            'new_user_num' => $data2['new_user_num'] ? $data2['new_user_num'] : 0,
            'return_percent' => $data['share_num'] > 0 ? sprintf("%.2f", $data2['return_num'] / $data['share_num'] * 100) . '%' : '0.00%'
        ];
    }

    // 每两小时分享统计
    public function twoHourStatisticsShare($type)
    {
        $time = $this->time::today();
        $yesterday = $this->time::yesterday();

        $cdate = $type == 'today' ? '@cdate := DATE_ADD(CURDATE(), INTERVAL + 1 DAY)' : '@cdate := CURDATE()';
        $precise_day = $type == 'today' ? "s.create_time between {$time[0]} and {$time[1]}" : "s.create_time between {$yesterday[0]} and {$yesterday[1]}";

        $sql = "SELECT 
                    x.`date_str`,
                    IFNULL(d.share_num, 0) AS share_num,
                    IFNULL(d.share_user_num, 0) AS share_user_num,
                    IFNULL(d.return_num, '0.00%') AS return_num,
                    IFNULL(d.return_percent, 0) AS return_percent,
                    IFNULL(d.new_user_num, 0) AS new_user_num
                  FROM
                    (SELECT 
                      @cdate := DATE_ADD(@cdate, INTERVAL - 2 HOUR) AS date_str 
                    FROM
                      (SELECT 
                        $cdate
                      FROM
                        city 
                      LIMIT 12) tmp1) `x`
                    LEFT JOIN 
                      (SELECT 
                          FROM_UNIXTIME(FLOOR(s.create_time / 7200) * 7200) AS house_time,
                          COUNT(DISTINCT s.id) AS share_num,
                          COUNT(DISTINCT s.user_id) AS share_user_num,
                          COUNT(sl.id) AS return_num,
                          CONCAT(ROUND(
                            COUNT(sl.id) / COUNT(DISTINCT s.id) * 100,
                            2
                          ) ,'%')AS return_percent,
                          SUM(sl.is_reg) AS new_user_num 
                        FROM
                          `share` s 
                          LEFT JOIN share_log sl 
                            ON s.id = sl.`share_id` 
                      WHERE $precise_day 
                      GROUP BY house_time 
                      ORDER BY house_time) d
                      ON x.`date_str` = d.house_time 
                  ORDER BY x.`date_str`";

        $data = Db::query($sql);

        return $data;
    }

    // 以天为间隔分享统计
    public function dayStatisticsShare($type, $start = '', $end = '')
    {
        $cdate = "CURDATE()";
        switch ($type) {
            case 'latest_seven_week':
                $limit = 7;
                break;
            case 'latest_thirty':
                $limit = 30;
                break;
            default:
                $cdate = "DATE_ADD('{$end}', INTERVAL + 1 DAY)";
                $start = strtotime(date("Y-m-d 00:00:00", strtotime(request()->param('start'))));
                $end = strtotime(date("Y-m-d 23:59:59", strtotime(request()->param('end'))));
                $limit = TimeService::getBetweenTwoDate(request()->param('start'), request()->param('end'));
                break;
        }

        $where = "where s.create_time between '{$start}' and '{$end}'";

        $sql = "SELECT 
                  x.`date_str`,
                  IFNULL(d.share_num, 0) AS share_num,
                  IFNULL(d.share_user_num, 0) AS share_user_num,
                  IFNULL(d.return_num, 0) AS return_num,
                  IFNULL(d.return_percent, 0) AS return_percent,
                  IFNULL(d.new_user_num, 0) AS new_user_num
                FROM
                  (SELECT 
                    @cdate := DATE_ADD(@cdate, INTERVAL - 1 DAY) AS date_str 
                  FROM
                    (SELECT 
                      @cdate := $cdate
                    FROM
                      city 
                    LIMIT $limit) tmp1) `x`
                  LEFT JOIN 
                    (SELECT 
                      DATE(
                        FROM_UNIXTIME(s.precise_day, '%Y%m%d')
                      ) AS house_time,
                       COUNT(DISTINCT s.id) AS share_num,
                          COUNT(DISTINCT s.user_id) AS share_user_num,
                          COUNT(sl.id) AS return_num,
                          CONCAT(ROUND(
                            COUNT(sl.id) / COUNT(DISTINCT s.id) * 100,
                            2
                          ) ,'%')AS return_percent,
                          SUM(sl.is_reg) AS new_user_num 
                        FROM
                          `share` s 
                          LEFT JOIN share_log sl 
                            ON s.id = sl.`share_id` 
                     $where
                    GROUP BY house_time 
                    ORDER BY house_time) d 
                    ON x.`date_str` = d.house_time 
                ORDER BY x.`date_str` DESC ";

        $data = Db::query($sql);

        return $data;
    }
}
