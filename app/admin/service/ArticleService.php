<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/18 0018
 * Time: 0:19
 */

namespace app\admin\service;

use app\BaseService;
use app\common\model\Article;
use GuzzleHttp\Client;

class ArticleService extends BaseService
{
    public function __construct(Article $article)
    {
        $this->model = $article;
    }

    public function getList(int $pageNo, int $pageSize)
    {
        $total = $this->model->where('is_entering', 1)->count();
        $totalPage = ceil($total / $pageSize);
        $articles = $this->model->field(['id', 'title', 'sub_title', 'cover', 'user_name', 'user_avatar'])->where('is_entering', 1)->page($pageNo)->limit($pageSize)->order('create_time desc')->select();

        return [
            'data'       => $articles,
            'pageSize'   => $pageSize,
            'pageNo'     => $pageNo,
            'totalPage'  => $totalPage,
            'totalCount' => $total,
        ];
    }

    public function getLinkData($id, $uid)
    {
        $client = new Client();

        $config = config('uoolu');

        $url = $config['url']['article_url'] . $id;

        $response = $client->get($url);

        $responseJson = json_decode($response->getBody()->getContents(), true);

        if ($responseJson['code'] != 100 || empty($responseJson['data'])) return false;

        $articleData = $responseJson['data'];
        $articleData['link_id'] = $articleData['id'];
        $articleData['entering_user_id'] = $uid;
        $articleData['content'] = str_replace("<img ", "<img width='100%'", $articleData['content']);
        unset($articleData['id']);

        $articleId = $this->model->where('link_id', $id)->value('id');
        if (!$articleId) {
            $article = $this->model->create($articleData);
            $articleId = $article->id;
        }

        $returnData = $articleData;
        $returnData['id'] = $articleId;

        return $returnData;
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

        return $this->model->whereIn('id', $ids)->select()->delete();
    }
}
