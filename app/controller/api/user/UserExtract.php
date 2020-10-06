<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-06-16
 *
 * Copyright (c) http://crmeb.net
 */
namespace app\controller\api\user;

use crmeb\basic\BaseController;
use app\common\repositories\system\groupData\GroupDataRepository;
use think\App;
use app\validate\api\UserExtractValidate as validate;
use app\common\repositories\user\UserExtractRepository as repository;

class UserExtract extends BaseController
{
    /**
     * @var repository
     */
    public $repository;

    /**
     * UserExtract constructor.
     * @param App $app
     * @param repository $repository
     */
    public function __construct(App $app,repository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
    }


    public function lst()
    {
        [$page,$limit] = $this->getPage();
        $where = $this->request->params(['status']);
        $where['uid'] = $this->request->uid();
        return app('json')->success($this->repository->search($where,$page,$limit));
    }


    public function create(validate $validate)
    {
        $data = $this->checkParams($validate);
        $user = $this->request->userInfo();
        if($user['brokerage_price'] < (systemConfig('user_extract_min')))
            return app('json')->fail('可提现金额不足');
        if($data['extract_price'] < (systemConfig('user_extract_min')))
            return app('json')->fail('提现金额不得小于最低额度');
        if($user['brokerage_price'] < $data['extract_price'])
            return app('json')->fail('提现金额不足');
        $this->repository->create($user,$data);
        return app('json')->success('申请已提交');
    }

    public function checkParams(validate $validate)
    {
        $data = $this->request->params(['extract_type','bank_code','bank_address','alipay_code','wechat','extract_pic','extract_price','real_name']);
        $validate->check($data);
        return $data;
    }

    public function bankLst()
    {
        [$page,$limit] = $this->getPage();
        $data = app()->make(GroupDataRepository::class)->groupData('bank_list',0,$page,100);
        return app('json')->success($data);
    }
}
