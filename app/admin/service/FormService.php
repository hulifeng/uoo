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
        if (!empty($form)) {
            $form = $form->toArray();

            $form['extra'] = json_decode($form['extra'], true);
        }
        return $form;
    }

    public function renew($id)
    {
        return $this->model->find($id)->save(['status' => 1]);
    }
}
