<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/27
 */
namespace app\controller\api\store\product;

use think\App;
use crmeb\basic\BaseController;
use app\common\repositories\store\StoreCategoryRepository as repository;

class StoreCategory extends BaseController
{
    protected $repository;

    /**
     * ProductCategory constructor.
     * @param App $app
     * @param repository $repository
     */
    public function __construct(App $app, repository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/27
     * @return mixed
     */
    public function lst()
    {
        return app('json')->success($this->repository->getApiFormatList(0,1));
    }

    public function children()
    {
        $pid = (int)$this->request->param('pid');
        return app('json')->success($this->repository->children($pid));
    }
}