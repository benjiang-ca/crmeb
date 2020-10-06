<?php


namespace app\common\middleware;


use app\Request;
use think\exception\HttpResponseException;
use think\facade\Config;
use think\Response;

/**
 * 跨域中间件
 * Class AllowOriginMiddleware
 * @package app\http\middleware
 */
class AllowOriginMiddleware extends BaseMiddleware
{
    /**
     * header头
     * @var array
     */
    protected $header = [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Headers' => 'X-Token, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With',
        'Access-Control-Allow-Methods' => 'GET,POST,PATCH,PUT,DELETE,OPTIONS,DELETE',
        'Access-Control-Max-Age' => '1728000'
    ];

    public function before(Request $request)
    {
        $cookieDomain = Config::get('cookie.domain', '');
        $origin = $request->header('origin');

        if ($origin && $cookieDomain && strpos($origin, $cookieDomain))
            $this->header['Access-Control-Allow-Origin'] = $origin;
        if ($request->method(true) == 'OPTIONS') {
            throw new HttpResponseException(Response::create()->code(200)->header($this->header));
        }
    }

    public function after(Response $response)
    {
        $response->header($this->header);
    }
}
