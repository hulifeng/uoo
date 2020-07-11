<?php
/**
 * Created by PhpStorm.
 * User: Mac
 * Date: 2020/6/21
 * Time: 13:57
 */

namespace app\api\controller\v1;

use app\admin\service\CityService;
use app\BaseController;
use app\common\model\Article;
use app\common\model\City;
use app\common\model\Collection;
use app\common\model\House;
use app\common\model\Search;
use app\common\model\Share;
use app\common\model\ShareLog;
use app\common\model\Form;
use app\common\model\UooUser;
use app\common\model\UserBehavior;
use app\common\model\UserBehaviorLog;
use app\Request;
use GuzzleHttp\Client;
use think\Db;
use think\Exception;

class Index extends BaseController
{
    private $config;
    protected $log;

    public function __construct()
    {
        header("Access-Control-Allow-Origin: * ");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
        $this->config = config('uoolu');
    }

    // 首页展示列表
    public function list(int $pageNo)
    {
        try {
            // 轮播
            $send = [];
            $pageSize = 10;
            $carousel = [];
            if ($pageNo == 1) $carousel = Article::field(['id', 'title', 'sub_title' => 'desc', 'cover' => 'image'])->limit(5)->order('is_top desc, update_time desc, create_time desc')->select();
            $send['carousel'] = $carousel;

            // 数据
            $houseTotal = House::field('id')->count();
            $houses = House::field(['id', 'name' => 'title', 'desc', 'price', 'location' => 'city', 'first_image' => 'image'])->page($pageNo)->limit($pageSize)->order('create_time desc')->select();
            $houseTotalPage = ceil($houseTotal / $pageSize);
            $send['house'] = ['data' => $houses, 'pageNo' => $pageNo, 'totalPage' => $houseTotalPage, 'currentCount' => count($houses), 'totalCount' => $houseTotal];
            return $this->sendSuccess($send);
        } catch (\Exception $exception) {
            return $this->sendError('服务器异常');
        }
    }

    // 文章详情页
    public function article($id)
    {
        try {
            if (!$id) return $this->sendError('缺省参数！');
            $article = Article::field(['title', 'sub_title', 'content', 'create_time'])->find($id)->toArray();
            $article['content'] = str_replace('<p><br/></p>', '', $article['content']);
            $article['is_collection'] = 0;
            $article['is_like'] = 0;
            if (request()->param('user_id')) {
                $is_collection = Collection::where("house_id", $id)->where("user_id", request()->param('user_id'))->where('type', 1)->value('id');
                $article['is_collection'] = isset($is_collection) && $is_collection ? 1 : 0;
                $is_like = Collection::where("house_id", $id)->where("user_id", request()->param('user_id'))->where('type', 3)->value('id');
                $article['is_like'] = isset($is_like) && $is_like ? 1 : 0;
            }
            return $this->sendSuccess($article);
        } catch (\Exception $exception) {
            return $this->sendError('服务器异常');
        }
    }

    // 房产详情页
    public function house($id)
    {
        try {
            if (!$id) return $this->sendError('缺省参数！');
            $field = ['name', 'desc', 'location', 'country', 'keywords', 'price', 'rise', 'recent', 'down_payment', 'basic_info', 'group_pictures', 'recent_information', 'graphic_information'];
            $item = House::field($field)->find($id);
            if (empty($item)) {
                return $this->sendError('房源不存在');
            } else {
                $house = $item->toArray();
                $house['keywords'] = explode(',', $house['keywords']);
                $house['basic_info'] = json_decode($house['basic_info'], true);
                $house['group_pictures'] = array_slice(explode(',', $house['group_pictures']), 0, 10);
                $house['recent_information'] = json_decode($house['recent_information'], true);
                $house['graphic_information'] = str_replace('<p><br></p>', '', $house['graphic_information']);
                $house['is_collection'] = 0;
                $house['is_like'] = 0;
                if (request()->param('user_id')) {
                    $is_collection = Collection::where("house_id", $id)->where("user_id", request()->param('user_id'))->where('type', 1)->value('id');
                    $house['is_collection'] = isset($is_collection) && $is_collection ? 1 : 0;
                    $is_like = Collection::where("house_id", $id)->where("user_id", request()->param('user_id'))->where('type', 3)->value('id');
                    $house['is_like'] = isset($is_like) && $is_like ? 1 : 0;
                }
                $link = House::where("country", $house['country'])->where('id', '<>', $id)->field(['id', 'name', 'desc', 'location', 'keywords', 'price', 'recent', 'down_payment', 'first_image' => 'image'])->limit(4)->select();
                if (sizeof($link)) {
                    foreach ($link as &$link_item) {
                        $link_item['keywords'] = explode(',', $link_item['keywords']);
                    }
                }
                $house['link'] = $link;
                return $this->sendSuccess($house);
            }
        } catch (\Exception $exception) {
            return $this->sendError('服务器异常');
        }
    }

    // 选房 || 收租
    public function choose(Request $request)
    {
        try {
            if (!$request->param('type')) return $this->sendError('缺省参数！');
            $type = $request->param('type');
            $order = 'create_time desc';
            $price = request()->param('price');
            $area = request()->param('area');
            $choose_type = request()->param('choose_type');
            if ($choose_type) {
                $between = explode(':', $choose_type);
                $order = "{$between[0]} {$between[1]}";
            }
            $field = ['id', 'name', 'desc', 'keywords' => 'tags', 'first_image' => 'image', 'recommend', 'price', 'recent', 'location' => 'city'];
            $total = House::where(function ($query) use ($type) {
                $query->where('type', '=', $type);
            })->where(function ($query) use ($price) {
                if ($price) {
                    $between = explode('_', $price);
                    $query->where('min_price_rmb', '>=', $between[0])->where('max_price_rmb', '<=', $between[1]);
                }
            })->where(function ($query) use ($area) {
                if ($area && !in_array($area, [0])) {
                    $query->where('country', '=', $area)->whereOr('city', '=', $area);
                }
            })->count();
            $houses = House::where(function ($query) use ($type) {
                $query->where('type', '=', $type);
            })->where(function ($query) use ($price) {
                if ($price) {
                    $between = explode('_', $price);
                    $query->where('min_price_rmb', '>=', $between[0])->where('max_price_rmb', '<=', $between[1]);
                }
            })->where(function ($query) use ($area) {
                if ($area && !in_array($area, [0])) {
                    $query->where('country', '=', $area)->whereOr('city', '=', $area);
                }
            })->field($field)->page($request->param('page'))->limit(10)->order($order)->select();
            foreach ($houses as &$value) {
                $value['tags'] = explode(',', $value['tags']);
            }
            $houseTotalPage = ceil($total / 10);
            $return = ['data' => $houses, 'pageNo' => $request->param('page'), 'totalPage' => $houseTotalPage, 'currentCount' => count($houses), 'totalCount' => $total];
            return $this->sendSuccess($return);
        } catch (\Exception $exception) {
            return $this->sendError('服务器异常');
        }
    }

    // 搜索页
    public function search($keywords, $pageNo = 1)
    {
        try {
            if (!$keywords) return $this->sendError('缺省参数！');
            $order = 'create_time desc';
            $price = request()->param('price');
            $choose_type = request()->param('choose_type');
            if ($choose_type) {
                $between = explode(':', $choose_type);
                $order = "{$between[0]} {$between[1]}";
            }
            $area = request()->param('area');
            $field = ['id', 'name', 'desc', 'keywords' => 'tags', 'first_image' => 'image', 'recommend', 'price', 'recent', 'location' => 'city'];
            $total = House::where(function ($query) use ($keywords) {
                $query->where('name', 'like', "%$keywords%")->whereOr('location', 'like', "%$keywords%");
            })->where(function ($query) use ($price) {
                if ($price) {
                    $between = explode('_', $price);
                    $query->where('min_price_rmb', '>=', $between[0])->where('max_price_rmb', '<=', $between[1]);
                }
            })->where(function ($query) use ($area) {
                if ($area && !in_array($area, [0])) {
                    $query->where('country', '=', $area)->whereOr('city', '=', $area);
                }
            })->count();
            $houses = House::where(function ($query) use ($keywords) {
                $query->where('name', 'like', "%$keywords%")->whereOr('location', 'like', "%$keywords%");
            })->where(function ($query) use ($price) {
                if ($price) {
                    $between = explode('_', $price);
                    $query->where('min_price_rmb', '>=', $between[0])->where('max_price_rmb', '<=', $between[1]);
                }
            })->where(function ($query) use ($area) {
                if ($area && !in_array($area, [0])) {
                    $query->where('country', '=', $area)->whereOr('city', '=', $area);
                }
            })->page($pageNo)->limit(10)->field($field)->order($order)->select();
            foreach ($houses as &$value) {
                $value['tags'] = explode(',', $value['tags']);
            }
            $houseTotalPage = ceil($total / 10);
            $return = ['data' => $houses, 'pageNo' => $pageNo, 'totalPage' => $houseTotalPage, 'currentCount' => count($houses), 'totalCount' => $total];
            return $this->sendSuccess($return);
        } catch (\Exception $exception) {
            return $this->sendError('服务器异常');
        }
    }

    // 收藏
    public function collection()
    {
        try {
            if (request()->param('type') == 2) {
                Collection::create(request()->only(['article_id', 'house_id', 'user_id', 'type']));
                if (request()->param('house_id')) House::where('id', request()->param('house_id'))->inc('view_count')->update();
                if (request()->param('article_id')) Article::where('id', request()->param('article_id'))->inc('view_count')->update();
            } else {
                if (!request()->param('status')) {
                    if (request()->param('house_id')) Collection::where('house_id', request()->param('house_id'))->where('type', request()->param('type'))->where('user_id', request()->param('user_id'))->select()->delete();
                    if (request()->param('article_id')) Collection::where('article_id', request()->param('article_id'))->where('type', request()->param('type'))->where('user_id', request()->param('user_id'))->select()->delete();
                } else {
                    Collection::create(request()->only(['article_id', 'house_id', 'user_id', 'type']));
                }
            }
            return $this->sendSuccess();
        } catch (\Exception $exception) {
            return $this->sendError('服务器异常');
        }
    }

    // 点赞
    public function like()
    {
        try {
            if (request()->param('type') == 3) {
                if (!request()->param('status')) {
                    Collection::where('house_id', request()->param('house_id'))->where('type', 3)->where('user_id', request()->param('user_id'))->select()->delete();
                } else {
                    Collection::create(request()->only(['house_id', 'user_id', 'type']));
                }
            }
            return $this->sendSuccess();
        } catch (\Exception $exception) {
            return $this->sendError('服务器异常');
        }
    }

    // 点赞列表
    public function like_list()
    {
        try {
            $field = ['id', 'name', 'desc', 'keywords' => 'tags', 'price', 'recommend', 'location' => 'city', 'first_image' => 'image'];
            $list = Collection::where('user_id', request()->param('user_id'))->where('type', 3)->order("create_time desc")->column('house_id');
            $res = [];
            if (!empty($list)) {
                $count = House::where('id', 'in', $list)->count();
                $data = House::field($field)->page(request()->param('page'))->limit(10)->where('id', 'in', $list)->select();
                if (sizeof($data)) {
                    foreach ($data as &$value) {
                        $value['tags'] = explode(',', $value['tags']);
                    }
                }
                $houseTotalPage = ceil($count / 10);
                $res = ['data' => $data, 'pageNo' => request()->param('page'), 'totalPage' => $houseTotalPage, 'currentCount' => count($data), 'totalCount' => $count];
            }
            return $this->sendSuccess($res);
        } catch (\Exception $exception) {
            return $this->sendError('服务器异常');
        }
    }

    // 分享列表
    public function share_list()
    {
        try {
            $field = ['id', 'name', 'desc', 'keywords' => 'tags', 'price', 'location' => 'city', 'first_image' => 'image'];
            if (request()->param('column') == 'house_id') {
                $list = Share::where('user_id', request()->param('user_id'))
                    ->where('house_id', '>', 0)
                    ->where('article_id', '=', 0)
                    ->order("create_time desc")
                    ->column('house_id');
            } else {
                $list = Share::where('user_id', request()->param('user_id'))
                    ->where('article_id', '>', 0)
                    ->where('house_id', '=', 0)
                    ->order("create_time desc")
                    ->column('article_id');
            }
            $res = [];
            if (!empty($list)) {
                if (request()->param('column') == 'house_id') {
                    $count = House::where('id', 'in', $list)->count();
                    $data = House::field($field)->page(request()->param('page'))->limit(10)->where('id', 'in', $list)->select();
                    if (sizeof($data)) {
                        foreach ($data as &$value) {
                            $value['tags'] = explode(',', $value['tags']);
                        }
                    }
                } else {
                    $articleField = ['id', 'title', 'cover', 'sub_title', 'user_name', 'user_avatar'];
                    $count = Article::where('id', 'in', $list)->count();
                    $data = Article::field($articleField)->page(request()->param('page'))->limit(10)->where('id', 'in', $list)->select();
                }

                $totalPage = ceil($count / 10);
                $res = ['data' => $data, 'pageNo' => request()->param('page'), 'totalPage' => $totalPage, 'currentCount' => count($data), 'totalCount' => $count];
            } else {
                $res = ['data' => [], 'pageNo' => request()->param('page'), 'totalPage' => 1, 'currentCount' => 0, 'totalCount' => 0];
            }
            return $this->sendSuccess($res);
        } catch (\Exception $exception) {
            return $this->sendError('服务器异常');
        }
    }

    // 收藏列表
    public function collection_list()
    {
        try {
            $field = ['id', 'name', 'desc', 'keywords' => 'tags', 'price', 'recommend', 'location' => 'city', 'first_image' => 'image'];
            $limit = 5;
            $type = request()->param('type');
            $column = request()->param('column');
            $list = Collection::where('user_id', request()->param('user_id'))
                ->where(function ($query) use ($type, $column) {
                    $query->where('type', $type);
                    if ($column == 'house_id') {
                        $query->where('article_id', 0);
                    } else {
                        $query->where('house_id', 0);
                    }
                })
                ->order("create_time desc")
                ->column($column);
            if (!empty($list)) {
                if (request()->param('column') == 'house_id') {
                    $count = House::where('id', 'in', $list)->count();
                    $data = House::field($field)->page(request()->param('page'))->limit($limit)->where('id', 'in', $list)->select();
                    if (sizeof($data)) {
                        foreach ($data as &$value) {
                            $value['tags'] = explode(',', $value['tags']);
                        }
                    }
                } else {
                    $articleField = ['id', 'title', 'cover', 'sub_title', 'user_name', 'user_avatar'];
                    $count = Article::where('id', 'in', $list)->count();
                    $data = Article::field($articleField)->page(request()->param('page'))->limit($limit)->where('id', 'in', $list)->select();
                }

                $totalPage = ceil($count / $limit);
                $res = ['data' => $data, 'pageNo' => request()->param('page'), 'totalPage' => $totalPage, 'currentCount' => count($data), 'totalCount' => $count];
            } else {
                $res = ['data' => [], 'pageNo' => request()->param('page'), 'totalPage' => 1, 'currentCount' => 0, 'totalCount' => 0];
            }
            return $this->sendSuccess($res);
        } catch (\Exception $exception) {
            return $this->sendError('服务器异常');
        }
    }

    // 保存资料
    public function bindPhone()
    {
        try {
            if (!request()->param('user_id')) return $this->sendError('非法请求');
            $extra = request()->param('extra', '');
            $country = '';
            $budget = '';
            $purposes = '';
            if (!empty($extra) && request()->param('type') == 1) {
                foreach ($extra as $value) {
                    if ($value['column'] == 'country') {
                        $country = implode(',', $value['choose']);
                    }
                    if ($value['column'] == 'budget') {
                        $budget = $value['choose'][0];
                    }
                    if ($value['column'] == 'purposes') {
                        $purposes = $value['choose'][0];
                    }
                }
            }
            $responseJson = save_customer(false, request()->param('phone'), request()->param('code'), request()->param('link_id', 0), $country, $budget, $purposes);

            // 验证通过
            $user_id = request()->param('user_id');

            if ($responseJson['code'] == 100) {
                $user = UooUser::where('id', $user_id)->where('mobile', request()->param('phone'))->find();
                if (empty($user)) {
                    // 更新用户表
                    UooUser::where('id', $user_id)->save(['mobile' => request()->param('phone')]);
                }
            } else {
                // 验证失败
                return $this->sendError('验证码不正确');
            }

            $data = [
                'user_id' => $user_id,
                'phone' => request()->param('phone'),
                'type' => request()->param('type'),
                'link_id' => request()->param('link_id', 0),
                'extra' => json_encode(request()->param('extra')),
                'simple_extra' => json_encode(request()->param('simple_extra'))
            ];
            $uoo_phone = Form::create($data);
            return $this->sendSuccess(['id' => isset($uoo_phone->id) ? $uoo_phone->id : 0]);
        } catch (\Exception $exception) {
            return $this->sendError('服务器异常');
        }
    }

    // 浏览记录
    public function view()
    {
        try {
            $type = request()->param('type', 2);
            $field = ['id', 'name', 'desc', 'keywords' => 'tags', 'price', 'recommend', 'location' => 'city', 'first_image' => 'image'];
            $user_id = request()->param('user_id');
            if (request()->param('column') == 'house_id') {
                $list = \think\facade\Db::query("SELECT 
                          a.house_id
                        FROM
                          (SELECT 
                            * 
                          FROM
                            collection 
                            WHERE `type` = {$type} AND house_id > 0 AND user_id = {$user_id} and article_id = 0
                          ORDER BY create_time DESC 
                          LIMIT 100000000) a 
                        GROUP BY a.house_id
                        ORDER BY a.id desc");
                if (sizeof($list)) {
                    $list = array_column($list, 'house_id');
                }
            }
            if (request()->param('column') == 'article_id') {
                $list = \think\facade\Db::query("SELECT 
                          a.article_id
                        FROM
                          (SELECT 
                            * 
                          FROM
                            collection 
                            WHERE `type` = {$type} AND article_id > 0 AND user_id = {$user_id} and house_id = 0
                          ORDER BY create_time DESC 
                          LIMIT 100000000) a 
                        GROUP BY a.article_id
                        ORDER BY a.id desc");
                if (sizeof($list)) {
                    $list = array_column($list, 'article_id');
                }
            }
            $res = [];
            if (!empty($list)) {
                if (request()->param('column') == 'house_id') {
                    $count = House::where('id', 'in', $list)->count();
                    $data = House::field($field)
                        ->page(request()->param('page'))
                        ->where('id', 'in', $list)
                        ->limit(10)->select();
                    if (sizeof($data)) {
                        foreach ($data as &$value) {
                            $value['tags'] = explode(',', $value['tags']);
                        }
                    }
                }
                if (request()->param('column') == 'article_id') {
                    $articleField = ['id', 'title', 'cover', 'sub_title', 'user_name', 'user_avatar'];
                    $count = Article::where('id', 'in', $list)->count();
                    $data = Article::field($articleField)->page(request()->param('page'))->limit(10)->where('id', 'in', $list)->select();
                }

                $houseTotalPage = ceil($count / 10);
                $res = ['data' => $data, 'pageNo' => request()->param('page'), 'totalPage' => $houseTotalPage, 'currentCount' => count($data), 'totalCount' => $count];
            } else {
                $res = ['data' => [], 'pageNo' => request()->param('page'), 'totalPage' => 1, 'currentCount' => 0, 'totalCount' => 0];
            }
            return $this->sendSuccess($res);
        } catch (\Exception $exception) {
            return $this->sendError('服务器异常');
        }
    }

    public function my()
    {
        try {
            $view_count = Collection::where("user_id", request()->param('user_id'))->where("type", 2)->group('user_id, house_id, article_id')->count();
            $collection_count = Collection::where("user_id", request()->param('user_id'))->where("type", 1)->count();
            $like_count = Collection::where("user_id", request()->param('user_id'))->where("type", 3)->count();
            $user_info = UooUser::where("id", request()->param('user_id'))->field(['nickname', 'avatar'])->find();
            $mapping_result = Form::where("user_id", request()->param('user_id'))->where('type', '1')->order('create_time desc')->value('id');
            return $this->sendSuccess(['view' => $view_count, 'like' => $like_count, 'collection' => $collection_count, 'user_info' => $user_info, 'mapping_id' => $mapping_result]);
        } catch (\Exception $exception) {
            return $this->sendError('服务器异常');
        }
    }

    // 注册用户
    public function register(Request $request)
    {
        try {
            $code = $request->param('code');
            $anonymous_code = $request->param('anonymous_code');
            if (empty($code) && empty($anonymous_code)) return $this->sendError('缺少必传参数');
            $res_code = $this->code_auth($code, $anonymous_code);
            if ($res_code['error'] != 0) return $this->sendError('获取code失败');
            $user_id = UooUser::where('openid', $res_code['openid'])->where('openid', '<>', '')->value('id');
            if (empty($user_id)) {
                $user_id = UooUser::where('anonymous_openid', $res_code['anonymous_openid'])->where('anonymous_openid', '<>', '')->value('id');
            }
            $is_new = 0;
            $house_id = 0;
            $article_id = 0;
            if ($user_id) {
                $user = UooUser::find($user_id);
                $user->save(['openid' => $res_code['openid'], 'anonymous_openid' => $res_code['anonymous_openid']]);
//                $user->save(['openid' => $res_code['openid'], 'anonymous_openid' => $res_code['anonymous_openid'],
//                             'province' => $request->param('province'),
//                             'city' => $request->param('city')]);
            } else {
                $data = $request->except(['v', 'code', 'anonymous_code']);
                $data['system_info'] = json_encode($data['system_info'], JSON_UNESCAPED_SLASHES);
                $data['query'] = json_encode($data['query'], JSON_UNESCAPED_SLASHES);
                $data['openid'] = $res_code['openid'];
                $data['anonymous_openid'] = $res_code['anonymous_openid'];
                $data['ip'] = get_real_ip();
                $data['create_time'] = $data['enter_time'];
                $data['precise_day'] = strtotime(date("Y-m-d", time()));
                if (isset($data['inviter_id'])) {
                    $acid = $data['acid'];
                    $house_id = $data['house_id'];
                    $article_id = $data['article_id'];
                    unset($data['acid']);
                    unset($data['house_id']);
                }
                $user = UooUser::create($data);
                $is_new = 1;
            }
            if (isset($user->id)) {
                $user->save(['user_number' => sprintf('%09d', $user->id)]);
                if (isset($data['inviter_id'])) {
                    $share_id = Share::where('acid', $acid)->value('id');
                    ShareLog::create([
                        'share_id' => $share_id,
                        'house_id' => $house_id,
                        'article_id' => $article_id,
                        'inviter_id' => $data['inviter_id'],
                        'invitee_id' => $user->id,
                        'is_reg' => $is_new ? 1 : 0
                    ]);
                }
            }
            return $this->sendSuccess(['user_id' => $user->id, 'key' => $res_code['session_key'], 'is_new' => $is_new]);
        } catch (\Exception $exception) {
            return $this->sendError('服务器异常');
        }
    }

    // 更新用户资料
    public function updateUserInfo(Request $request)
    {
        try {
            UooUser::where('id', $request->param('id'))->save($request->except(['v', 'id']));
            return $this->sendSuccess();
        } catch (\Exception $exception) {
            return $this->sendError('服务器异常');
        }
    }

    // 分享
    public function share(Request $request)
    {
        try {
            $result = Share::create($request->except(['v']));
            return $this->sendSuccess($result);
        } catch (\Exception $exception) {
            return $this->sendError('服务器异常');
        }
    }

    // 换取 openid
    public function code_auth($code, $anonymous_code)
    {
        try {
            $client = new Client();
            $data = ['appid' => config('uoolu.appid'), 'secret' => config('uoolu.secret'), 'code' => $code, 'anonymous_code' => $anonymous_code];
            $url = "https://developer.toutiao.com/api/apps/jscode2session";
            $response = $client->get($url, ['query' => $data]);
            return json_decode($response->getBody(), true);
        } catch (\Exception $exception) {
            return $this->sendError('服务器异常');
        }
    }

    // 记录用户行为日志
    public function user_behavior(Request $request)
    {
        try {
            $precise_day = strtotime(date("Y-m-d", time()));
            $precise_time = strtotime(date("Y-m-d H:i:s", time()));
            if ($request->param('init')) {
                $data = [
                    'user_id' => $request->param('user_id'),
                    'is_new' => $request->param('is_new'),
                    'enter_time' => $request->param('enter_time'),
                    'precise_day' => $precise_day,
                    'precise_time' => $precise_time
                ];
                UserBehavior::create($data);
                UooUser::where('id', $request->param('user_id'))->save(['active' => 1]);
            }
            if ($request->param('init') == 0) {
                UserBehavior::where('user_id', $request->param('user_id'))->save(['leave_time' => $request->param('leave_time')]);
                $behavior = UserBehavior::where('enter_time', $request->param('enter_time'))->where('user_id', $request->param('user_id'))->find();
                if (isset($behavior->id) && $behavior->id) {
                    $behavior->save(['leave_time' => $request->param('leave_time')]);
                    UooUser::where('id', $request->param('user_id'))->save(['active' => '0', 'last_login_time' => date("Y-m-d H:i:s", $request->param('leave_time'))]);
                    $behaviors = $request->param('behaviors');
                    $requestID = uniqid();
                    if (sizeof($behaviors)) {
                        $logs = [];
                        $total_behaviors = count($behaviors);
                        foreach ($behaviors as $k => $value) {
                            $logs[] = [
                                'user_id' => $value['user_id'],
                                'request_id' => $requestID,
                                'behavior_id' => $behavior->id,
                                'enter_time' => $value['type'] == '996' ? $value['enter_time'] : 0,
                                'leave_time' => $value['type'] == '996' ? $value['enter_time'] : 0,
                                'stay_time' => $value['type'] == '996' ? $value['leave_time'] - $value['enter_time'] : 0,
                                'type' => $value['type'],
                                'is_logout' => ($k + 1 == $total_behaviors) ? 1 : 0,
                                'eventDetail' => $value['eventDetail'],
                                'page_params' => json_encode($value['page_params'], JSON_UNESCAPED_SLASHES),
                                'page' => $value['page'],
                                'precise_day' => $precise_day,
                                'precise_time' => $precise_time
                            ];
                        }
                        $behaviorLog = new UserBehaviorLog();
                        $behaviorLog->saveAll($logs);
                        $stay_time = $request->param('leave_time') - $request->param('enter_time');
                        $visit_num = $behavior->visit_num + count($behaviors);
                        $open_num = $behavior->open_num;
                        $break_num = $behavior->break_num;
                        if (sizeof($behaviors) == 1) {
                            // 访问一页就退出
                            $break_num += 1;
                        }
                        $behavior->save([
                            'stay_time' => $stay_time,
                            'visit_num' => $visit_num,
                            'open_num' => $open_num + 1,
                            'break_num' => $break_num,
                            'leave_time' => $request->param('leave_time')
                        ]);
                    }
                }
            }
            return $this->sendSuccess();
        } catch (\Exception $exception) {
            return $this->sendError('服务器异常');
        }
    }

    // 获取城市
    public function city()
    {
        try {
            $city = new City();
            $service = new CityService($city);
            return $this->sendSuccess($service->getList());
        } catch (\Exception $exception) {
            return $this->sendError('服务器异常');
        }
    }

    // 短信测试接口
    public function captcha()
    {
        $url = 'https://api.uoolu.com/ext/send-code';

        $client = new Client();

        $config = config('uoolu.captcha');

        $response = $client->get($url, [
            'query' => [
                'grant_type' => 'toutiao',
                'appid' => $config['appid'],
                'secret' => $config['secret'],
                'area_code' => $config['area_code'],
                'mobile' => request()->param('phone')
            ]
        ]);

        $responseJson = json_decode($response->getBody()->getContents(), true);

        if ($responseJson['code'] == 100) {
            return $this->sendSuccess($responseJson);
        } else {
            return $this->sendError('验证码发送失败');
        }
    }

    //短信验证接口
    public function checkCode()
    {
        $url = 'https://api.uoolu.com/ext/check-code';

        $client = new Client();

        $config = config('uoolu.captcha');

        $response = $client->get($url, [
            'query' => [
                'area_code' => $config['area_code'],
                'mobile' => request()->param('phone'),
                'code' => request()->param('code')
            ]
        ]);

        $responseJson = json_decode($response->getBody()->getContents(), true);

        if ($responseJson['code'] == 100) {
            return $this->sendSuccess($responseJson);
        } else {
            return $this->sendError('验证码发送失败');
        }
    }

    // 智能匹配房源
    public function mapping()
    {
        try {
            file_put_contents('./log.txt', http_build_query(request()->param()));
            $mapping = Form::find(request()->param('id'));
            $simple_extra = json_decode($mapping['simple_extra'], true);
            $extra = json_decode($mapping['extra'], true);
            $country = [];
            $where = [];
            foreach ($extra as $value) {
                if ($value['column'] == 'country') {
                    $country = $value['choose'];
                    $where[] = ['country', 'in', $value['choose']];
                }
            }

            $price = explode('_', $simple_extra['price']);
            if (isset($price) && sizeof($price)) {
                $where[] = ['min_price_rmb', '>=', $price[0]];
                $where[] = ['max_price_rmb', '<=', $price[1]];
            }

            $country_str = implode(',', $country);
            $field = ["IF(country IN ($country_str), 1, 0)" => 'is_target_country', 'id', 'name', 'desc', 'price', 'keywords', 'location', 'first_image' => 'image', 'min_price_rmb', 'max_price_rmb'];
            $houses = House::field($field)->where($where)->select();
            if (!sizeof($houses)) {
                $where_country[] = ['country', 'in', $country];;
                $where_price[] = ['min_price_rmb', '>=', $price[0]];
                $where_price[] = ['max_price_rmb', '<=', $price[1]];
                $houses = House::field($field)->whereOr($where_country)->whereOr('id', 34)->order('recent desc')->limit(5)->select();
                if (!sizeof($houses)) {
                    // 还是没有随机查找5条租金最高房源
                    $houses = House::field($field)->order('recent desc')->limit(5)->select();
                }
            }
            foreach ($houses as &$value) {
                $value['keywords'] = explode(',', $value['keywords']);
                $value['is_target_price'] = 0;
                if ($price[0] < $value['min_price_rmb'] && $price[1] > $value['max_price_rmb']) {
                    $value['is_target_price'] = 1;
                }
            }
            $target_country = implode('、', $simple_extra['country']);
            if (sizeof($houses)) {
                $mapping->save(['result' => json_encode($houses, JSON_UNESCAPED_SLASHES)]);
            }
            $data = [
                'houses' => $houses,
                'target_price' => ceil($price[0] / 10000) . '-' . ceil($price[1] / 10000) . '万',
                'target_country' => $target_country
            ];
            return $this->sendSuccess($data);
        } catch (\Exception $exception) {
            return $this->sendError('服务器异常');
        }
    }

    // 房源配置
    public function config()
    {
        $client = new Client();

        $response = $client->get($this->config['url']['config']);

        $responseJson = json_decode($response->getBody()->getContents(), true);

        $newConfig = [];

        foreach ($responseJson['data'] as $key => $value) {
            if ($key == 'country') {
                $newConfig[] = [
                    'title' => '我最感兴趣的国家',
                    'sub_title' => '是哪些',
                    'desc' => '最多选3个',
                    'type' => 'checkbox',
                    'num' => 3,
                    'choose' => [],
                    'option_list' => $value,
                    'orientation' => 3,
                    'column' => 'country'
                ];
            }
            if ($key == 'price_option_list') {
                $newConfig[] = [
                    'title' => '我最能接受的房产总价',
                    'sub_title' => '在哪个价格区间',
                    'desc' => '单选',
                    'type' => 'radio',
                    'num' => 1,
                    'choose' => [],
                    'option_list' => $value,
                    'orientation' => 1,
                    'column' => 'budget'
                ];
            }
            if ($key == 'purposes') {
                $newConfig[] = [
                    'title' => '我买海外房产',
                    'sub_title' => '是为了',
                    'desc' => '单选',
                    'type' => 'radio',
                    'num' => 1,
                    'choose' => [],
                    'option_list' => $value,
                    'orientation' => 1,
                    'column' => 'purposes'
                ];
            }
        }

        array_push($newConfig, [
            'title' => '获取评估报告',
            'sub_title' => '',
            'desc' => '',
            'type' => 'form',
            'form' => [

            ],
            "column" => 'form'
        ]);

        return $this->sendSuccess($newConfig);
    }

    // 保存搜索关键词
    public function recordSearch(Request $request)
    {
        try {
            Search::create($request->only(['user_id', 'keywords']));
        } catch (Exception $exception) {
            return $this->sendError('服务器异常');
        }
    }
}
