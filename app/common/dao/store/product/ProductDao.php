<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/8
 */

namespace app\common\dao\store\product;

use app\common\dao\BaseDao;
use app\common\model\store\product\Product as model;
use app\common\repositories\store\StoreCategoryRepository;
use think\db\BaseQuery;
use think\db\exception\DbException;
use think\facade\Db;

class ProductDao extends BaseDao
{
    protected function getModel(): string
    {
        return model::class;
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/9
     * @param int $id
     * @param array $data
     */
    public function createAttr(int $id, array $data)
    {
        ($this->getModel()::find($id))->attr()->saveAll($data);
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/9
     * @param int $id
     * @param array $data
     */
    public function createAttrValue(int $id, array $data)
    {
        ($this->getModel()::find($id))->attrValue()->saveAll($data);
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/9
     * @param int $id
     * @param array $data
     */
    public function createContent(int $id, array $data)
    {
        ($this->getModel()::find($id))->content()->save($data);
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/9
     * @param int $merId
     * @param $field
     * @param $value
     * @param null $except
     * @return bool
     */
    public function merFieldExists(?int $merId, $field, $value, $except = null)
    {
        return ($this->getModel())::getDB()->when($except, function ($query, $except) use ($field) {
                $query->where($field, '<>', $except);
            })->when($merId, function ($query, $merId) {
                $query->where('mer_id', $merId);
            })->where($field, $value)->count() > 0;
    }

    public function apiFieldExists(int $merId, $field, $value, $except = null)
    {
        return ($this->getModel())::getDB()->when($except, function ($query, $except) use ($field) {
                $query->where($field, '<>', $except);
            })->when($merId, function ($query, $merId) {
                $query->where('mer_id', $merId);
            })->where(['status' => 1, 'is_show' => 1])->where($field, $value)->count() > 0;
    }

    /**
     * @param int $merId
     * @param int $productId
     * @return bool
     * @author Qinii
     */
    public function getDeleteExists(int $merId, int $productId)
    {
        return ($this->getModel())::onlyTrashed()->where('mer_id', $merId)->where($this->getPk(), $productId)->count() > 0;
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/11
     * @param int $merId
     * @param array $where
     * @return mixed
     */
    public function search(?int $merId, array $where)
    {
        $keyArray = $whereArr = [];
        unset($where['type']);
        foreach ($where as $key => $item) {
            if ($item !== '' && $key != 'soft') {
                $keyArray[] = $key;
                $whereArr[$key] = $item;
            }
        }
        $query = isset($where['soft']) ? model::onlyTrashed()->alias('Product') : model::alias('Product');
        if(isset($where['is_trader']) && $where['is_trader'] !== ''){
            $query->hasWhere('merchant',function($query)use($where){
                $query->where('is_trader',$where['is_trader']);
            });
        }
        $query->withSearch($keyArray, $whereArr);
        $query->when(($merId !== null), function ($query) use ($merId) {
            $query->where('Product.mer_id', $merId);
        });
        $query->when(isset($where['hot_type']) && $where['hot_type'] !== '', function ($query) use ($where) {
            if ($where['hot_type'] == 'new')
                $query->where('is_new', 1);
            else if ($where['hot_type'] == 'hot')
                $query->where('is_hot', 1);
            else if ($where['hot_type'] == 'best')
                $query->where('is_best', 1);
            else if ($where['hot_type'] == 'good')
                $query->where('is_good', 1);
        })->when(isset($where['pid']) && $where['pid'] !== '', function ($query) use ($where) {
            $children = app()->make(StoreCategoryRepository::class)->children(intval($where['pid']));
            $ids = array_column($children, 'store_category_id');
            if (count($ids)) $query->whereIn('cate_id', $ids);
        });
      
        $query->when(isset($where['order']),function($query)use($where,$merId){
            $query->when(in_array($where['order'], ['is_new', 'price_asc', 'price_desc', 'rate', 'sales']), function ($query) use ($where) {
                if ($where['order'] == 'price_asc') {
                    $where['order'] = 'price ASC';
                } else if ($where['order'] == 'price_desc') {
                    $where['order'] = 'price DESC';
                } else {
                    $where['order'] = $where['order'] . ' DESC';
                }
                $query->order($where['order'] . ',rank DESC ,create_time DESC ');
            }, function ($query) use ($where,$merId) {
                if($merId){
                    $query->order('Product.sort DESC ,Product.create_time DESC');
                }else{
                    $query->order('Product.rank DESC ,Product.create_time DESC');
                }
            });
        },function ($query){
            $query->order('Product.sort DESC ,create_time DESC');
        });
        return $query;
    }

    /**
     * TODO
     * @param array $where
     * @return BaseQuery
     * @author Qinii
     * @day 2020-08-04
     */
    public function seckillSearch(array $where)
    {
        $query = model::hasWhere('seckillActive',function($query)use($where){
            $query->where('status',1);
            $query->whereTime('start_day','<=',$where['day'])->whereTime('end_day','>=',$where['day']);
            $query->where([['start_time','<=',$where['start_time']], ['end_time','>=',$where['end_time']]]);
        });
        $query->where([
            'Product.is_show'       => 1,
            'Product.status'        => 1,
            'Product.is_used'       => 1,
            'Product.mer_status'    => 1,
            'Product.product_type'  => 1,
            'Product.is_gift_bag'   => 0,
            ]);
        $query->order('rank DESC,Product.create_time DESC');
        return $query;
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/18
     * @param int $id
     * @param bool $soft
     * @return int|mixed
     */
    public function delete(int $id, $soft = false)
    {
        if ($soft) {
            return (($this->getModel())::onlyTrashed()->find($id))->force()->delete();
        } else {
            return $this->getModel()::where($this->getPk(),$id)->update(['is_del' => 1]);
        }
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
        $res = ($this->getModel())::onlyTrashed()->find($id);
        return $res->restore();
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/18
     * @param int $id
     * @param array $status
     * @return mixed
     */
    public function switchStatus(int $id, array $status)
    {
        return ($this->getModel()::getDB())->where($this->getPk(), $id)->update($status);
    }

    /**
     * @param int $merId
     * @param array $productIds
     * @return array
     * @author xaboy
     * @day 2020/5/26
     */
    public function productIdByImage(int $merId, array $productIds)
    {
        return model::getDB()->where('mer_id', $merId)->whereIn('product_id', $productIds)->column('product_id,image');
    }

    /**
     * @param array $ids
     * @return array
     * @author xaboy
     * @day 2020/5/30
     */
    public function intersectionKey(array $ids): array
    {
        return model::getDB()->whereIn('product_id', $ids)->column('product_id');
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/30
     * @param $id
     * @return mixed
     */
    public function productIdByMerId($id)
    {
        return model::getDB()->where('product_id', $id)->value('mer_id');
    }


    /**
     * @param int $productId
     * @param int $desc
     * @return int
     * @throws DbException
     * @author xaboy
     * @day 2020/6/8
     */
    public function descStock(int $productId, int $desc)
    {
        return model::getDB()->where('product_id', $productId)->update([
            'stock' => Db::raw('stock-' . $desc),
            'sales' => Db::raw('sales+' . $desc)
        ]);
    }

    /**
     * @param int $productId
     * @param int $inc
     * @return int
     * @throws DbException
     * @author xaboy
     * @day 2020/6/8
     */
    public function incStock(int $productId, int $inc)
    {
        return model::getDB()->where('product_id', $productId)->update([
            'stock' => Db::raw('stock+' . $inc),
            'sales' => Db::raw('sales-' . $inc)
        ]);
    }

    public function visitProductGroup($date, $merId = null, $limit = 7)
    {
        return model::getDB()->alias('A')->leftJoin('UserRelation B', 'A.product_id = B.type_id')
            ->field(Db::raw('count(B.type_id) as total,A.product_id,A.store_name,A.image'))
            ->when($date, function ($query, $date) {
                getModelTime($query, $date, 'B.create_time');
            })->when($merId, function ($query, $merId) {
                $query->where('A.mer_id', $merId);
            })->where('B.type', 1)->group('A.product_id')->limit($limit)->order('total DESC')->select();
    }

    public function cartProductGroup($date, $merId = null, $limit = 7)
    {
        return model::getDB()->alias('A')->leftJoin('StoreCart B', 'A.product_id = B.product_id')
            ->field(Db::raw('sum(B.cart_num) as total,A.product_id,A.store_name,A.image'))
            ->when($date, function ($query, $date) {
                getModelTime($query, $date, 'B.create_time');
            })->when($merId, function ($query, $merId) {
                $query->where('A.mer_id', $merId);
            })->where('B.product_type', 0)->where('B.is_pay', 0)->where('B.is_del', 0)
            ->where('B.is_new', 0)->where('B.is_fail', 0)->group('A.product_id')->limit($limit)->order('total DESC')->select();
    }

    public function changeMerchantProduct($merId,$data)
    {
        ($this->getModel()::getDB())->where('mer_id',$merId)->update($data);
    }

    /**
     * TODO
     * @param int $productId
     * @author Qinii
     * @day 2020-07-09
     */
    public function incCareCount(int $productId)
    {
        ($this->getModel()::getDB())->where($this->getPk(),$productId)->inc('care_count',1)->update();
    }

    /**
     * TODO
     * @param int $productId
     * @author Qinii
     * @day 2020-07-09
     */
    public function decCareCount(int $productId)
    {
        ($this->getModel()::getDB())->where($this->getPk(),$productId)->where('care_count','>',0)->dec('care_count',1)->update();
    }

    /**
     * TODO api展示的商品条件
     * @return array
     * @author Qinii
     * @day 2020-08-18
     */
    public function productShow()
    {
        return [
            'is_show'       => 1,
            'status'        => 1,
            'is_used'       => 1,
            'product_type'  => 0,
            'mer_status'    => 1,
            'is_gift_bag'   => 0,
        ];
    }

    /**
     * TODO api展示的礼包商品条件
     * @return array
     * @author Qinii
     * @day 2020-08-18
     */
    public function bagShow()
    {
        return [
            'is_show'       => 1,
            'status'        => 1,
            'is_used'       => 1,
            'mer_status'    => 1,
            'product_type'  => 0,
            'is_gift_bag'   => 1,
        ];
    }

    /**
     * TODO api展示的秒杀商品条件
     * @return array
     * @author Qinii
     * @day 2020-08-18
     */
    public function seckillShow()
    {
        return [
            'is_show'       => 1,
            'status'        => 1,
            'is_used'       => 1,
            'mer_status'    => 1,
            'product_type'  => 1,
            'is_gift_bag'   => 0,
        ];
    }
}
