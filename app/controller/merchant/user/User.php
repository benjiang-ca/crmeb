<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/5/29
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\controller\merchant\user;


use crmeb\basic\BaseController;
use app\common\repositories\user\UserRepository;
use think\App;
use think\facade\Queue;
use crmeb\jobs\SendTemplateMessageJob;
use crmeb\services\WechatTemplateService;
class User extends BaseController
{
    protected $repository;

    public function __construct(App $app, UserRepository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
    }

    public function getUserList()
    {
        $keyword = $this->request->param('keyword', '');
        if (!$keyword)
            return app('json')->fail('请输入关键字');
        [$page, $limit] = $this->getPage();
        return app('json')->success($this->repository->merList($keyword, $page, $limit));
    }
}
