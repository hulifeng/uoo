<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/17 0017
 * Time: 20:32
 */

namespace app\admin\service;


use app\BaseService;
use app\common\model\Config;
use think\Model;

class ConfigService extends BaseService
{
    public function __construct(Config $config)
    {
        $this->model = $config;
    }

    public function getData()
    {
        return $this->model->field(['name', 'title', 'value'])->select();
    }

    public function renew(array $input)
    {
        $value = $input['value'] ? 1 : 0;

        return $this->model->where('name', $input['name'])->save(['value' => $value]);
    }
}
