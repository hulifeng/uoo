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
        $field = ['id', 'title', 'sub_title', 'cover', 'user_name', 'user_avatar', 'is_top'];
        $articles = $this->model->field($field)->where('is_entering', 1)->page($pageNo)->limit($pageSize)->order('is_top desc, update_time desc, create_time desc')->select();

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
        $articleData['content'] = str_replace("section", "div", $articleData['content']);

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

	public function reTop($id, array $input)
	{
	    $input['update_time'] = time();
		return $this->find($id)->save($input);
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
