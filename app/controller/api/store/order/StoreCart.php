<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/27
 */
namespace app\controller\api\store\order;

use app\common\repositories\store\order\StoreOrderRepository;
use app\common\repositories\store\product\ProductAttrValueRepository;
use app\common\repositories\store\product\ProductRepository;
use app\common\repositories\store\StoreSeckillActiveRepository;
use MongoDB\BSON\MaxKey;
use think\App;
use crmeb\basic\BaseController;
use app\validate\api\StoreCartValidate as validate;
use app\common\repositories\store\order\StoreCartRepository as repository;
use think\exception\ValidateException;

class StoreCart extends BaseController
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
        return app('json')->success($this->repository->getList($this->request->uid()));
    }

    /**
     * @param validate $validate
     * @return mixed
     * @author Qinii
     */
    public function create(validate $validate)
    {
        $data = $this->checkParams($validate);
        $result = $this->checkCart($data);

        [$source, $sourceId, $pid] = explode(':', $this->request->param('source', '0'), 3) + ['', '', ''];
        $data['source'] = (in_array($source, [0, 1]) && $pid == $data['product_id']) ? $source : 0;
        if ($data['source'] > 0)
            $data['source_id'] = intval($sourceId);

        $cart = $this->repository->getCartByProductSku($data['product_attr_unique'], $this->request->uid());
        $data['product_type'] = $result['product']['product_type'];
        //不是礼包，不是立即购买，不是秒杀商品，购物车存在
        if (!$result['product']['is_gift_bag'] && !$data['is_new'] && !$result['product']['product_type'] && $cart) {
            if ($result['sku']['stock'] < ($cart['cart_num'] + $data['cart_num']))
                return app('json')->fail('库存不足');
            $this->repository->update($cart['cart_id'], ['cart_num' => ($cart['cart_num'] + $data['cart_num'])]);
        } else {
            if ($result['sku']['stock'] < $data['cart_num']) return app('json')->fail('库存不足');
            $data['uid'] = $this->request->uid();
            $data['mer_id'] = $result['product']['mer_id'];
            $cart = $this->repository->create($data);
        }
        return app('json')->success(['cart_id' => $cart['cart_id']]);
    }

    /**
     * TODO 检测
     * @param $data
     * @return array
     * @author Qinii
     * @day 2020-08-05
     */
    public function checkCart($data)
    {

        $product_make = app()->make(ProductRepository::class);
        $value_make = app()->make(ProductAttrValueRepository::class);
        if( $data['cart_num'] < 0 ) throw new ValidateException('数量必须大于0');
        $product = $product_make->getWhere(['product_id' => $data['product_id']],'*');
        if(!$product) throw new ValidateException('商品不存在');
        $res = $value_make->getOptionByUnique($data['product_attr_unique']);
        if(!$res) throw new ValidateException('SKU不存在');
        if($res['product_id'] != $data['product_id']) throw new ValidateException('数据不一致');

        if($product['is_gift_bag']){
            if(!$data['is_new']) throw new ValidateException('礼包商品不可加入购物车');
            if($data['cart_num'] !== 1) throw new ValidateException('礼包商品只能购买一个');
            if($this->request->userInfo()->is_promoter) throw new ValidateException('您已经是分销员了');
        }

        if($product['product_type'] == 1){
            if($product->end_time < time()) throw new ValidateException('秒杀活动已结束');
            $order_make = app()->make(StoreOrderRepository::class);
            if($data['is_new'] !== 1) throw new ValidateException('秒杀商品不能加入购物车');
            if($data['cart_num'] !== 1) throw new ValidateException('秒杀商品只能购买一个');
            $count = $order_make->seckillOrderCounut($data['product_id']);
            if($res['stock'] <= $count)  throw new ValidateException('限购数量不足');
            $sku = $value_make->getWhere(['sku' => $res['sku'], 'product_id' => $product['old_product_id']]);
            if(!$sku) throw new ValidateException('原商品SKU不存在');
            if($sku['stock'] <= 0) throw new ValidateException('原库存不足');
            if(!$order_make->getDayPayCount($this->request->uid(),$data['product_id']))
                throw new ValidateException('您的本次活动购买数量上限');
            if(!$order_make->getPayCount($this->request->uid(),$data['product_id']))
                throw new ValidateException('您的该商品购买数量上限');
        }
        return ['product' => $product,'sku' => $res];
    }


    /**
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DbException
     * @author Qinii
     */
    public function change($id)
    {
        $where = $this->request->params(['cart_num']);
        if( intval($where['cart_num']) < 0 )
            return app('json')->fail('数量必须大于0');
        if(!$cart = $this->repository->getOne($id,$this->request->uid()))
            return app('json')->fail('购物车信息不存在');
        if(!$res= app()->make(ProductAttrValueRepository::class)->getOptionByUnique($cart['product_attr_unique']))
            return app('json')->fail('SKU不存在');
        if($res['stock'] < $where['cart_num'])
            return app('json')->fail('库存不足');
        $this->repository->update($id,$where);
        return app('json')->success('修改成功');

    }

    /**
     * @return mixed
     * @author Qinii
     */
    public function batchDelete()
    {
        $ids = $this->request->param('cart_id');
        if(!count($ids))return app('json')->fail('参数错误');
        $this->repository->batchDelete($ids,$this->request->uid());
        return app('json')->success('删除成功');
    }


    /**
     * @return mixed
     * @author Qinii
     */
    public function cartCount()
    {
        return app('json')->success($this->repository->getCartCount($this->request->uid()));
    }

    /**
     * @param $data
     * @return mixed
     * @author Qinii
     * @day 2020-06-11
     */
    public function check($data)
    {
        $product = app()->make(ProductRepository::class)->get($data['product_id']);
        if(!$product)
           throw new ValidateException('商品不存在');
        if( $data['cart_num'] < 0 )
            throw new ValidateException('数量必须大于0');
        if(!$res= app()->make(ProductAttrValueRepository::class)->getOptionByUnique($data['product_attr_unique']))
            throw new ValidateException('SKU不存在');
        if($res['product_id'] != $data['product_id'])
            throw new ValidateException('数据不一致');
        if($res['stock'] < $data['cart_num'])
            throw new ValidateException('库存不足');
        $data['is_new'] = 1;
        $data['uid'] = $this->request->uid();
        $data['mer_id'] = $product['mer_id'];
        return $data;
    }


    /**
     * @param validate $validate
     * @return mixed
     * @author Qinii
     * @day 2020-06-11
     */
    public function again(validate $validate)
    {
        $param = $this->request->param('data',[]);
        foreach ($param as $data){
            $validate->check($data);
            $item[] = $this->check($data);
        }

        foreach ($item as $it){
            $it__id = $this->repository->create($it);
            $ids[] = $it__id['cart_id'];
        }
        return app('json')->success(['cart_id' => $ids]);
    }


    /**
     * @param validate $validate
     * @return array
     * @author Qinii
     * @day 2020-06-11
     */
    public function checkParams(validate $validate)
    {
        $data = $this->request->params(['product_id','product_attr_unique','cart_num','is_new']);
        $validate->check($data);
        return $data;
    }
}
