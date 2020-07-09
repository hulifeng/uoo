<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/20 0020
 * Time: 13:39
 */

namespace app\admin\service;

use app\BaseService;
use app\common\model\House;
use GuzzleHttp\Client;

class HouseService extends BaseService
{
    public function __construct(House $house)
    {
        $this->model = $house;
    }

    public function getList(int $pageNo, int $pageSize)
    {
        $total = $this->model->where('is_entering', 1)->count();
        $total_page = ceil($total/$pageSize);

        $houses = $this->model->where('is_entering', 1)->limit($pageSize)->page($pageNo)->order('create_time desc')->select();

        return [
            'data'       => $houses,
            'pageSize'   => $pageSize,
            'pageNo'     => $pageNo,
            'totalPage'  => $total_page,
            'totalCount' => $total,
        ];
    }

    public function getLinkData($id, $uid, $type)
    {
        $client = new Client();

        $config = config('uoolu');

        $url = $config['url']['house_url'] . $id;

        $response = $client->get($url);

        $responseJson = json_decode($response->getBody()->getContents(), true);

        if ($responseJson['code'] != 100 || empty($responseJson['data'])) return false;

        $houseData = $responseJson['data'];
        $houseData['link_id'] = $houseData['id'];
        $houseData['type'] = $type;
        unset($houseData['id']);
        $houseData['keywords'] = implode(',', $houseData['keywords']);
        $houseData['model'] = implode(',', $houseData['model']);
        $houseData['group_pictures'] = implode(',', $houseData['group_pictures']);
        $houseData['basic_info'] = json_encode($houseData['basic_info']);
        $houseData['entering_user_id'] = $uid;
        $houseData['graphic_information'] = str_replace("<img ", "<img width='100%'", $houseData['graphic_information']);
        $houseData['recent_information'] = json_encode($houseData['recent_information'], JSON_UNESCAPED_SLASHES);

        $houseId = $this->model->where('link_id', $id)->value('id');
        if (!$houseId) {
            $house = $this->model->create($houseData);
            $houseId = $house->id;
        }

        $returnHouseData = [
            'id' => $houseId ? $houseId : 0,
            'name' => $houseData['name'],
            'desc' => $houseData['desc'],
            'keywords' => $responseJson['data']['keywords'],
            'first_image' => $houseData['first_image'],
            'price' => $houseData['price'],
            'recent' => $houseData['recent'],
            'rise' => $houseData['rise'],
            'down_payment' => $houseData['down_payment'],
            'recent_information' => $responseJson['data']['recent_information'],
        ];

        return $returnHouseData;
    }

    public function add($id)
    {
        return $this->model->find($id)->save(['is_entering' => 1]);
    }

    public function remove($id)
    {
        $ids = explode(',', $id);

        if (empty($ids)) {
            return false;
        }

        $this->model->whereIn('id', $ids)->select()->delete();

        return true;
    }
}
