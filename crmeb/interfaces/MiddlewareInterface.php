<?php


namespace crmeb\interfaces;


use app\Request;
use think\Response;

interface MiddlewareInterface
{
    public function handle(Request $request, \Closure $next): Response;
}