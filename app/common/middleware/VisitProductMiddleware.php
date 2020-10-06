<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-15
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\middleware;

use app\common\repositories\user\UserVisitRepository;
use app\Request;
use crmeb\services\SwooleTaskService;
use think\Response;

class VisitProductMiddleware extends BaseMiddleware
{

    public function before(Request $request)
    {
        // TODO: Implement before() method.
    }


    public function after(Response $response)
    {
        $id = intval($this->request->param('id'));
        if ($this->request->isLogin() && $id) {
            $uid = $this->request->uid();
            $make = app()->make(UserVisitRepository::class);
            $count = $make->search($uid,'product')->where('type_id',$id)->whereTime('create_time','>',date('Y-m-d H:i:s' ,strtotime('- 300 seconds')))->count();
            if(!$count){
                SwooleTaskService::visit(intval($uid), $id, 'product');
            }
        }
    }
}
