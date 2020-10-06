<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-07-31
 *
 * Copyright (c) http://crmeb.net
 */
namespace app\controller\merchant\store\product;

use app\common\dao\store\StoreSeckillActiveDao;
use app\common\repositories\store\StoreSeckillActiveRepository;
use app\common\repositories\store\StoreSeckillTimeRepository;
use think\App;
use crmeb\basic\BaseController;
use app\validate\merchant\StoreSeckillProductValidate as validate;
use app\common\repositories\store\product\ProductRepository as repository;
use think\exception\ValidateException;

class SeckillProduct extends BaseController
{
    protected  $repository ;

    /**
     * Product constructor.
     * @param App $app
     * @param repository $repository
     */
    public function __construct(App $app ,repository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/18
     * @return mixed
     */
    public function lst()
    {
        [$page, $limit] = $this->getPage();
        $where = $this->request->params(['cate_id','keyword',['type',1],'mer_cate_id','seckill_status']);
        $where = array_merge($where,$this->repository->switchType($where['type'],$this->request->merId(),1));
        return app('json')->success($this->repository->getSeckillList($this->request->merId(),$where, $page, $limit));
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/18
     * @param $id
     * @return mixed
     */
    public function detail($id)
    {
        if(!$this->repository->merExists($this->request->merId(),$id))
            return app('json')->fail('数据不存在');
        return app('json')->success($this->repository->get($id));
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/18
     * @param validate $validate
     * @return mixed
     */
    public function create(validate $validate)
    {
        $data = $this->checkParams($validate);
        $this->check($data);
        $merchant = $this->request->merchant();
        $data['product_type'] = 1;
        $data['mer_id'] = $this->request->merId();
        $data['status'] = 0;
        $data['is_gift_bag'] = 0;
        $data['mer_status'] = ($merchant['is_del'] || !$merchant['mer_state'] || !$merchant['status']) ? 0 : 1;
        $this->repository->create($data,1);
        return app('json')->success('添加成功');
    }


    /**
     * TODO 商品验证
     * @param $data
     * @author Qinii
     * @day 2020-08-01
     */
    public function check($data)
    {
        if(!$this->repository->merBrandExists($data['brand_id']))
            throw new ValidateException('品牌不存在');
        if(!$this->repository->CatExists($data['cate_id']))
            throw new ValidateException('平台分类不存在');
        if(isset($data['mer_cate_id']) && !$this->repository->merCatExists($data['mer_cate_id'],$this->request->merId()))
            throw new ValidateException('不存在的商户分类');
        if(!$this->repository->merShippingExists($this->request->merId(),$data['temp_id']))
            throw new ValidateException('运费模板不存在');
        if(!app()->make(StoreSeckillTimeRepository::class)->getWhereCount([
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time']])
        ) throw new ValidateException('时间段未开放活动');
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/18
     * @param $id
     * @param validate $validate
     * @return mixed
     */
    public function update($id,validate $validate)
    {
        $data = $this->checkParams($validate);
        $merchant = $this->request->merchant();
        if(!$this->repository->merExists($this->request->merId(),$id)) return app('json')->fail('数据不存在');
        $this->check($data);
        $data['status'] = 0;
        $data['mer_status'] = ($merchant['is_del'] || !$merchant['mer_state'] || !$merchant['status']) ? 0 : 1;
        unset($data['is_gift_bag'],$data['old_product_id']);
        $this->repository->edit($id,$data,$this->request->merId(),1);
        return app('json')->success('编辑成功');
    }

    /**
     * TODO
     * @param $id
     * @return mixed
     * @author Qinii
     * @day 2020-08-07
     */
    public function delete($id)
    {
        if(!$this->repository->merExists($this->request->merId(),$id))
            return app('json')->fail('数据不存在');
        if($this->repository->getWhereCount(['product_id' => $id,'is_show' => 1,'status' => 1]))
            return app('json')->fail('商品上架中');
        $this->repository->delete($id);
        return app('json')->success('转入回收站');
    }


    public function destory($id)
    {
        if(!$this->repository->merDeleteExists($this->request->merId(),$id))
            return app('json')->fail('只能删除回收站的商品');
        $this->repository->destory($id);
        return app('json')->success('删除成功');
    }
    /**
     * @Author:Qinii
     * @Date: 2020/5/18
     * @param int $id
     * @return mixed
     */
    public function switchStatus($id)
    {
        $status = $this->request->param('status', 0) == 1 ? 1 : 0;
        if(!$this->repository->merExists($this->request->merId(),$id))
            return app('json')->fail('数据不存在');
        $this->repository->switchStatus([$id], ['is_show' => $status]);
        return app('json')->success('修改成功');
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/18
     * @return mixed
     */
    public function getStatusFilter()
    {
        return app('json')->success($this->repository->getFilter($this->request->merId(),'秒杀商品',1));
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/8
     * @Time: 14:39
     * @param validate $validate
     * @return array
     */
    public function checkParams(validate $validate)
    {
        $params = [
            "image", "slider_image", "store_name", "store_info", "keyword", "bar_code", "brand_id","start_day","end_day",
            "start_time","end_time","old_product_id", "cate_id", "mer_cate_id", "unit_name", "sort" , "is_show", "is_good",
            'is_gift_bag', "video_link", "temp_id", "content", "spec_type","extension_type", "attr", "attrValue","all_pay_count",
            "once_pay_count",['give_coupon_ids',[]]
        ];
        $data = $this->request->params($params);
        $validate->check($data);
        return $data;
    }


    /**
     * TODO
     * @param $id
     * @return mixed
     * @author Qinii
     * @day 2020-07-03
     */
    public function restore($id)
    {
        if(!$this->repository->merDeleteExists($this->request->merId(),$id))
            return app('json')->fail('只能删除回收站的商品');
        $this->repository->restore($id);
        return app('json')->success('商品已恢复');
    }


    /**
     * TODO 获取可用时间段
     * @return mixed
     * @author Qinii
     * @day 2020-08-03
     */
    public function lst_time()
    {
        return app('json')->success(app()->make(StoreSeckillTimeRepository::class)->select());
    }
}
