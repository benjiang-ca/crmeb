<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/8/14
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\controller\admin\system\merchant;


use app\common\repositories\store\ExcelRepository;
use app\common\repositories\system\merchant\FinancialRecordRepository;
use crmeb\basic\BaseController;
use think\App;

class FinancialRecord extends BaseController
{
    protected $repository;

    public function __construct(App $app, FinancialRecordRepository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
    }

    public function lst()
    {
        [$page, $limit] = $this->getPage();
        $where = $this->request->params(['keyword', 'date', 'mer_id']);
        $merId = $this->request->merId();
        if ($merId) {
            $where['mer_id'] = $merId;
            $where['financial_type'] = ['order', 'mer_accoubts', 'brokerage_one', 'brokerage_two', 'refund_brokerage_one', 'refund_brokerage_two', 'refund_order'];
        } else {
            $where['financial_type'] = ['order', 'sys_accoubts', 'brokerage_one', 'brokerage_two', 'refund_brokerage_one', 'refund_brokerage_two', 'refund_order'];
        }
        return app('json')->success($this->repository->getList($where, $page, $limit));
    }

    public function export()
    {
        $where = $this->request->params(['keyword', 'date', 'mer_id']);
        $merId = $this->request->merId();
        if ($merId) {
            $where['mer_id'] = $merId;
            $where['financial_type'] = ['order', 'mer_accoubts', 'brokerage_one', 'brokerage_two', 'refund_brokerage_one', 'refund_brokerage_two', 'refund_order'];
        } else {
            $where['financial_type'] = ['order', 'sys_accoubts', 'brokerage_one', 'brokerage_two', 'refund_brokerage_one', 'refund_brokerage_two', 'refund_order'];
        }
        app()->make(ExcelRepository::class)->create($where, $this->request->adminId(), 'financial',$merId);
        return app('json')->success('开始导出数据');
    }
}
