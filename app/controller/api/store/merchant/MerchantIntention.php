<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-07-18
 *
 * Copyright (c) http://crmeb.net
 */
namespace app\controller\api\store\merchant;

use app\common\repositories\system\merchant\MerchantCategoryRepository;
use crmeb\services\YunxinSmsService;
use think\App;
use crmeb\basic\BaseController;
use app\common\repositories\system\merchant\MerchantIntentionRepository as repository;

class MerchantIntention extends BaseController
{
    protected $repository;
    protected $userInfo;

    public function __construct(App $app, repository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
        $this->userInfo = $this->request->isLogin() ? $this->request->userInfo() : null;
    }

    public function create()
    {
        $data = $this->request->params(['phone', 'mer_name', 'name', 'code','images','merchant_category_id']);
        $check = (YunxinSmsService::create())->checkSmsCode($data['phone'], $data['code'],'intention');
        if (!$check) return app('json')->fail('验证码不正确');
        $categ = app()->make(MerchantCategoryRepository::class)->get($data['merchant_category_id']);
        if(!$categ) return app('json')->fail('商户分类不存在');
        if ($this->userInfo) $data['uid'] = $this->userInfo->uid;
        $this->repository->create($data);
        return app('json')->success('提交成功');
    }

    /**
     * 商户分类
     * @Author:Qinii
     * @Date: 2020/9/15
     * @return mixed
     */
    public function cateLst()
    {
        $lst = app()->make(MerchantCategoryRepository::class)->getSelect();
        return app('json')->success($lst);
    }
}

