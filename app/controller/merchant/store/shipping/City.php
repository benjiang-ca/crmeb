<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/6
 * @Time: 14:37
 */
namespace app\controller\merchant\store\shipping;

use think\App;
use crmeb\basic\BaseController;
use app\common\repositories\store\shipping\CityRepository as repository;

class City extends BaseController
{
    protected $repository;

    /**
     * City constructor.
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
     * @Date: 2020/5/8
     * @Time: 14:40
     * @return mixed
     */
    public function lst()
    {
        return app('json')->success($this->repository->getFormatList([['is_show', '=', 1],['level','<',2]]));
    }


    /**
     * @return mixed
     * @author Qinii
     */
    public function getlist()
    {
        return app('json')->success($this->repository->getFormatList(['is_show' => 1]));
    }
}
