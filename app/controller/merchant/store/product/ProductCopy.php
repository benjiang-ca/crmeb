<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-07-30
 *
 * Copyright (c) http://crmeb.net
 */
namespace app\controller\merchant\store\product;

use app\common\repositories\system\merchant\MerchantRepository;
use think\App;
use crmeb\basic\BaseController;
use app\common\repositories\store\product\ProductCopyRepository as repository;

class ProductCopy extends BaseController
{
    /**
     * @var repository
     */
    protected $repository;

    /**
     * ProductCopy constructor.
     * @param App $app
     * @param repository $repository
     */
    public function __construct(App $app ,repository $repository)
    {
        $this->repository = $repository;
        parent::__construct($app);
    }

    /**
     * TODO 列表
     * @return mixed
     * @author Qinii
     * @day 2020-08-14
     */
    public function lst()
    {
        [$page, $limit] = $this->getPage();
        $where['mer_id'] = $this->request->merId();
        return app('json')->success($this->repository->getList($where,$page, $limit));
    }

    /**
     * TODO
     * @return mixed
     * @author Qinii
     * @day 2020-08-07
     */
    public function count()
    {
        $count = $this->request->merchant()->copy_product_num;
        return app('json')->success(['count' => $count]);
    }



    /**
     * TODO 复制商品
     * @return mixed
     * @author Qinii
     * @day 2020-08-06
     */
    public function get()
    {
        if(!systemConfig('copy_product_status')) return app('json')->fail('复制商品功能未开启');
        $num = app()->make(MerchantRepository::class)->getCopyNum($this->request->merId());
        if($num <= 0) return app('json')->fail('复制商品次数已用完');
        $data = $this->request->params(['type','id','shopid','url']);
        $res = $this->repository->copyProduct($data,$this->request->merId());
        return app('json')->success($res);
    }
}
