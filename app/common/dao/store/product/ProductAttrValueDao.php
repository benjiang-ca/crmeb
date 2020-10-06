<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/8
 */

namespace app\common\dao\store\product;

use app\common\dao\BaseDao;
use app\common\model\store\product\ProductAttrValue as model;
use think\db\exception\DbException;
use think\facade\Db;

/**
 * Class ProductAttrValueDao
 * @package app\common\dao\store\product
 * @author xaboy
 * @day 2020/6/9
 */
class ProductAttrValueDao extends BaseDao
{
    /**
     * @return string
     * @author xaboy
     * @day 2020/6/9
     */
    protected function getModel(): string
    {
        return model::class;
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/9
     * @param int $productId
     * @return mixed
     */
    public function clearAttr(int $productId)
    {
        return ($this->getModel())::where('product_id', $productId)->delete();
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/9
     * @param int $merId
     * @param $field
     * @param $value
     * @param null $except
     * @return mixed
     */
    public function getFieldColumnt($key, $value, $field, $except = null)
    {
        return ($this->getModel()::getDB())->when($except, function ($query, $except) use ($field) {
            $query->where($field, '<>', $except);
        })->where($key, $value)->column($field);
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/11
     * @param $key
     * @param $value
     * @param $field
     * @param null $except
     * @return mixed
     */
    public function getFieldSum($key, $value, $field, $except = null)
    {
        return ($this->getModel()::getDB())->when($except, function ($query, $except) use ($field) {
            $query->where($field, '<>', $except);
        })->where($key, $value)->sum($field);
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/11
     * @param array $data
     * @return mixed
     */
    public function insert(array $data)
    {
        return ($this->getModel()::getDB())->insertAll($data);
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/11
     * @param int|null $merId
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

    /**
     * @param $id
     * @return mixed
     * @author xaboy
     * @day 2020/6/9
     */
    public function getSku($id)
    {
        return ($this->getModel())::where('product_id', $id);
    }

    /**
     * @param int|null $merId
     * @param $field
     * @param $value
     * @param null $except
     * @return mixed
     * @author xaboy
     * @day 2020/6/9
     */
    public function getFieldExists(?int $merId, $field, $value, $except = null)
    {
        return ($this->getModel())::getDB()->when($except, function ($query, $except) use ($field) {
            $query->where($field, '<>', $except);
        })->when($merId, function ($query, $merId) {
            $query->where('mer_id', $merId);
        })->where($field, $value);
    }

    /**
     * @param int $productId
     * @param string $unique
     * @param int $desc
     * @return int
     * @throws DbException
     * @author xaboy
     * @day 2020/6/8
     */
    public function descStock(int $productId, string $unique, int $desc)
    {
        return model::getDB()->where('product_id', $productId)->where('unique', $unique)->update([
            'stock' => Db::raw('stock-' . $desc),
            'sales' => Db::raw('sales+' . $desc)
        ]);
    }

    /**
     * @param int $productId
     * @param string $sku
     * @param int $desc
     * @return int
     * @throws DbException
     * @author xaboy
     * @day 2020/6/8
     */
    public function descSkuStock(int $productId, string $sku, int $desc)
    {
        return model::getDB()->where('product_id', $productId)->where('sku', $sku)->update([
            'stock' => Db::raw('stock-' . $desc),
            'sales' => Db::raw('sales+' . $desc)
        ]);
    }

    /**
     * @param int $productId
     * @param string $unique
     * @param int $inc
     * @throws DbException
     * @author xaboy
     * @day 2020/6/8
     */
    public function incStock(int $productId, string $unique, int $inc)
    {
        model::getDB()->where('product_id', $productId)->where('unique', $unique)->inc('stock', $inc)->update();
        model::getDB()->where('product_id', $productId)->where('unique', $unique)->where('sales', '>', $inc)->dec('sales', $inc)->update();
    }

    /**
     * @param int $productId
     * @param string $sku
     * @param int $inc
     * @throws DbException
     * @author xaboy
     * @day 2020/6/8
     */
    public function incSkuStock(int $productId, string $sku, int $inc)
    {
        model::getDB()->where('product_id', $productId)->where('sku', $sku)->inc('stock', $inc)->update();
        model::getDB()->where('product_id', $productId)->where('sku', $sku)->where('sales', '>', $inc)->dec('sales', $inc)->update();
    }

    /**
     * @param int $productId
     * @param string $unique
     * @return bool
     * @author xaboy
     * @day 2020/6/9
     */
    public function attrExists(int $productId, string $unique): bool
    {
        return model::getDB()->where('product_id', $productId)->where('unique', $unique)->count() > 0;
    }

    /**
     * @param int $productId
     * @param string $sku
     * @return bool
     * @author xaboy
     * @day 2020/6/9
     */
    public function skuExists(int $productId, string $sku): bool
    {
        return model::getDB()->where('product_id', $productId)->where('sku', $sku)->count() > 0;
    }

    /**
     * TODO 商品佣金是否大于设置佣金比例
     * @param $productId
     * @return bool
     * @author Qinii
     * @day 2020-06-25
     */
    public function checkExtensionById($productId)
    {
        $extension_one_rate = systemConfig('extension_one_rate');
        $extension_two_rate = systemConfig('extension_two_rate');

        $count = ($this->getModel()::getDb())->where(function($query)use($productId,$extension_one_rate){
            $query->where('product_id',$productId)->whereRaw('price * '.$extension_one_rate.' > extension_one');
        })->whereOr(function($query)use($productId,$extension_two_rate){
            $query->where('product_id',$productId)->whereRaw('price * '.$extension_two_rate.' > extension_two');
        })->count();
        return $count ? false : true;
    }

    public function search(array $where)
    {
        $query = ($this->getModel()::getDb())
            ->when(isset($where['product_id']) && $where['product_id'] !== '',function($query)use($where){
                $query->where('product_id',$where['product_id']);
            })
            ->when(isset($where['sku']) && $where['sku'] !== '',function($query)use($where){
                $query->where('sku',$where['sku']);
            })
            ->when(isset($where['unique']) && $where['unique'] !== '',function($query)use($where){
                $query->where('unique',$where['unique']);
            });
        return $query;
    }
}
