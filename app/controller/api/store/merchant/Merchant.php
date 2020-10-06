<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/28
 */
namespace app\controller\api\store\merchant;

use think\App;
use crmeb\basic\BaseController;
use app\common\repositories\system\merchant\MerchantRepository as repository;

class Merchant extends BaseController
{
    protected $repository;
    protected $userInfo;

    /**
     * ProductCategory constructor.
     * @param App $app
     * @param repository $repository
     */
    public function __construct(App $app, repository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
        $this->userInfo =$this->request->isLogin() ? $this->request->userInfo():null;
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/27
     * @return mixed
     */
    public function lst()
    {
        [$page, $limit] = $this->getPage();
        $where = $this->request->params(['keyword','order']);
        return app('json')->success($this->repository->getList($where, $page,$limit,$this->userInfo));
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/29
     * @param $id
     * @return mixed
     */
    public function detail($id)
    {
        if(!$this->repository->apiGetOne($id))
            return app('json')->fail(' 店铺已打烊');
        return app('json')->success($this->repository->detail($id,$this->userInfo));
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/29
     * @param $id
     * @return mixed
     */
    public function productList($id)
    {
        [$page, $limit] = $this->getPage();
        $where = $this->request->params(['keyword','order','mer_cate_id','cate_id', 'order', 'price_on', 'price_off', 'brand_id', 'pid']);
        if(!$this->repository->apiGetOne($id)) return app('json')->fail(' 店铺已打烊');
        return app('json')->success($this->repository->productList($id,$where, $page, $limit,$this->userInfo));
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/29
     * @param int $id
     * @return mixed
     */
    public function categoryList($id)
    {
        if(!$this->repository->merExists($id))
            return app('json')->fail('店铺已打烊');
        return app('json')->success($this->repository->categoryList($id));
    }

    public function qrcode($id)
    {
        if(!$this->repository->merExists($id))
            return app('json')->fail('店铺已打烊');
        $url = $this->request->param('type') == 'routine' ? $this->repository->routineQrcode(intval($id)) : $this->repository->wxQrcode(intval($id));
        return app('json')->success(compact('url'));
    }

}
