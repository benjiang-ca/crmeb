<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/27
 */

namespace app\controller\api\store\product;

use app\common\repositories\store\StoreSeckillTimeRepository;
use think\App;
use crmeb\basic\BaseController;
use app\common\repositories\store\product\ProductRepository as repository;

class StoreSeckillProduct extends BaseController
{
    /**
     * @var repository
     */
    protected $repository;
    protected $userInfo;

    /**
     * StoreProduct constructor.
     * @param App $app
     * @param repository $repository
     */
    public function __construct(App $app, repository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
        $this->userInfo = $this->request->isLogin() ? $this->request->userInfo() : null;
    }

    /**
     * TODO 秒杀列表
     * @return mixed
     * @author Qinii
     * @day 2020-08-04
     */
    public function lst()
    {
        [$page, $limit] = $this->getPage();
        $where['start_time'] = $this->request->param('start_time','');
        $where['end_time'] = $this->request->param('end_time','');
        return app('json')->success($this->repository->getApiSeckill($where,$page, $limit));
    }

    /**
     * TODO
     * @param $id
     * @return mixed
     * @author Qinii
     * @day 2020-08-04
     */
    public function detail($id)
    {
        $data = $this->repository->seckillDetail($id);
        if (!$data) return app('json')->fail('商品不存在');
        return app('json')->success($data);
    }

    /**
     * TODO 秒杀时间段
     * @return mixed
     * @author Qinii
     * @day 2020-08-04
     */
    public function select()
    {
        return  app('json')->success(app()->make(StoreSeckillTimeRepository::class)->selectTime());
    }


}
