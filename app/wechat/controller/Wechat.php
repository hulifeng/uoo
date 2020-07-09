<?php

declare(strict_types=1);

namespace app\wechat\controller;

use app\BaseController;
use app\common\service\MemberService;
use EasyWeChat\Kernel\Messages\Message;
use app\wechat\handler\EventMessageHandler;

class Wechat extends BaseController
{
    // 信任域名
    private $access_domain = 'domain.com';

    /**
     * 微信公众平台通信
     *
     * @return void
     */
    public function index()
    {
        $app = app('wechat.official_account');

        $app->server->push(EventMessageHandler::class, Message::EVENT);

        $response = $app->server->serve();

        $response->send();

        exit;
    }

    /**
     * 微信公众号授权.
     *
     * @return void
     */
    public function oauth()
    {
        $target = request()->get('target') ?? request()->server('HTTP_REFERER');
        if ($target && !strpos($target, $this->access_domain)) {
            exit('非法请求');
        }

        $callback_url = request()->domain() . '/wechat/callback?target=' . $target;
        $response = app('wechat.official_account')->oauth->scopes(['snsapi_userinfo'])
            ->redirect($callback_url);

        $response->send();
    }

    /**
     * 微信授权回调.
     *
     * @param MemberService $member
     *
     * @return void
     */
    public function callback(MemberService $member)
    {
        // $oauth = app('wechat.official_account')->oauth;

        // // 获取 OAuth 授权结果用户信息
        // $user = $oauth->user();

        // unset($user['original']);
        // $member = $member->handleWechatCallback($user);

        // 分配 jwt token
        // $token = app('jwt')->store('wechat')->token(['uid' => $member->id, 'nickname' => $member->name, 'type' => 'wechat']);

        // $target = request()->get('target') ?? "http://{$this->access_domain}";
        // return redirect("$target?token={$token}");
    }
}
