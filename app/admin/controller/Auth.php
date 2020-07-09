<?php

declare(strict_types=1);

namespace app\admin\controller;

use think\Response;
use app\BaseController;
use xiaodi\JWTAuth\Facade\Jwt;
use app\admin\service\UserService;

class Auth extends BaseController
{
    /**
     * 用户登录.
     *
     * @return Response
     */
    public function login(UserService $service)
    {
        $username = $this->request->param('username');
        $password = $this->request->param('password');

        $user = $service->login($username, $password);
        if ($user === false) {
            return $this->sendError('登录失败');
        }

        $token = (string) $service->makeToken($user);

        return $this->sendSuccess([
            'token'      => $token,
            'token_type' => Jwt::type(),
            'expires_in' => Jwt::ttl(),
            'refresh_in' => Jwt::refreshTTL(),
        ]);
    }

    /**
     * 刷新Token.
     *
     * @return Response
     */
    public function refreshToken()
    {
        return $this->sendSuccess([
            'token'      => (string) Jwt::refresh(),
            'token_type' => Jwt::type(),
            'expires_in' => Jwt::ttl(),
            'refresh_in' => Jwt::refreshTTL(),
        ]);
    }

    /**
     * 退出登录.
     *
     * @return Response
     */
    public function logout()
    {
        Jwt::logout();

        return $this->sendSuccess();
    }
}
