<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-03-26
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\middleware;


use app\Request;
use crmeb\interfaces\MiddlewareInterface;
use think\Response;

abstract class BaseMiddleware implements MiddlewareInterface
{

    /**
     * @var Request
     */
    protected $request;

    protected $args = [];

    abstract public function before(Request $request);

    /**
     * @param int $num
     * @param mixed $default
     * @return mixed
     * @author xaboy
     * @day 2020-04-10
     */
    public function getArg($num, $default = null)
    {
        return isset($this->args[$num]) ? $this->args[$num] : $default;
    }

    final public function handle(Request $request, \Closure $next, ...$args): Response
    {
        $this->args = $args;
        $this->request = $request;
        $this->before($request);
        $response = $next($request);
        $this->after($response);
        return $response;
    }

    abstract public function after(Response $response);
}