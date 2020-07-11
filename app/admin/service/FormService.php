<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/9 0009
 * Time: 0:37
 */

namespace app\admin\service;

use app\BaseService;
use app\common\model\Form;

class FormService extends BaseService
{
    public function __construct(Form $form)
    {
        $this->model = $form;
    }

    public function getList($pageNo, $pageSize)
    {
        $total = $this->model->count();
        $total_page = ceil($total/ $pageSize);

        $houses = $this->model->limit($pageSize)->page($pageNo)->order('create_time desc')->select();

        return [
            'data'       => $houses,
            'pageSize'   => $pageSize,
            'pageNo'     => $pageNo,
            'totalPage'  => $total_page,
            'totalCount' => $total,
        ];
    }

    public function show($id)
    {
        $form = $this->find($id);
        $data = [];
        $form = $form->toArray();
        if ($form['type'] == 1) {
            foreach (json_decode($form['simple_extra'], true) as $k => $value) {
                if ($k == 'country') $data[] = [ 'label' => '国家', 'value' => implode('、', $value) ];
                if ($k == 'purposes') $data[] = [ 'label' => '目的', 'value' => $value ];
                if ($k == 'price_label') $data[] = [ 'label' => '预算', 'value' => $value ];
            }
            $data[] = [ 'label' => '手机号', 'value' => $form['phone'] ];
            $data[] = [ 'label' => '提交时间', 'value' => $form['create_time'] ];
        } else {
            $form['extra'] = json_decode($form['extra'], true);
            $data[] = [ 'label' => '手机号', 'value' => $form['extra']['phone'] ];
            $data[] = [ 'label' => '提交时间', 'value' => $form['create_time'] ];
        }
        return $data;
    }

    public function renew($id)
    {
        return $this->model->find($id)->save(['status' => 1]);
    }
}
