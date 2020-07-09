<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/20 0020
 * Time: 10:25
 */

namespace app\admin\service;

use app\BaseController;
use app\common\model\City;
use GuzzleHttp\Client;

class CityService extends BaseController
{
    private $url;
    public function __construct(City $city)
    {
        $this->model = $city;
        $this->url = config('uoolu.url')['city_url'];
    }

    public function getList()
    {
        $client = new Client();

        $response = $client->get($this->url);

        $responseJson = json_decode($response->getBody()->getContents(), true);

        return $this->getTree($responseJson['data']);

    }

    public function getTree($data)
    {
        // 伪造数据
        foreach ($data as &$value) {
            array_unshift($value['city'], [
                'name_en' => '',
                'name' => '全国',
                'id' => 0,
                'url_param' => '',
                'lng' => '',
                'lat' => ''
            ]);
        }

        $top = [
            'name_en' => '',
            'name' => '不限',
            'id' => 0,
            'url_param' => '',
            'lng' => '',
            'lat' => '',
            'city' => [
                [
                    'name_en' => '',
                    'name' => '全球',
                    'id' => 0,
                    'url_param' => '',
                    'lng' => '',
                    'lat' => ''
                ]
            ]
        ];

        array_unshift($data, $top);

        return $data;
    }
}
