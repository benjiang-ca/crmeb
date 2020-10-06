<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/27
 */
namespace app\controller\api\store\product;

use think\App;
use crmeb\basic\BaseController;
use app\common\repositories\store\StoreBrandRepository as repository;

class StoreBrand extends BaseController
{
    /**
     * @var repository
     */
    protected $repository;

    /**
     * StoreBrand constructor.
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
     * @Date: 2020/5/28
     * @return mixed
     */
    public function lst()
    {
        [$page, $limit] = $this->getPage();
        $where = $this->request->params(['keyword', 'cate_id']);
        return app('json')->success($this->repository->getCategorySearch($where, $page, $limit));
    }

}