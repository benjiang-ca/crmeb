<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/8
 */

namespace app\common\repositories\store\product;

use app\common\model\store\order\StoreOrder;
use app\common\model\store\product\Product;
use app\common\model\store\product\ProductContent;
use app\common\model\user\User;
use app\common\repositories\store\coupon\StoreCouponRepository;
use app\common\repositories\store\order\StoreOrderRepository;
use app\common\repositories\store\order\StoreRefundOrderRepository;
use app\common\repositories\store\StoreSeckillActiveRepository;
use app\common\repositories\store\StoreSeckillTimeRepository;
use app\common\repositories\system\attachment\AttachmentRepository;
use app\common\repositories\user\UserRelationRepository;
use app\common\repositories\user\UserVisitRepository;
use app\common\repositories\wechat\RoutineQrcodeRepository;
use app\common\repositories\wechat\WechatQrcodeRepository;
use crmeb\services\DownloadImageService;
use crmeb\services\QrcodeService;
use crmeb\services\SwooleTaskService;
use FormBuilder\Factory\Elm;
use think\exception\ValidateException;
use think\facade\Db;
use app\common\repositories\BaseRepository;
use app\common\dao\store\product\ProductDao as dao;
use app\common\repositories\store\StoreCategoryRepository;
use app\common\repositories\store\shipping\ShippingTemplateRepository;
use app\common\repositories\store\StoreBrandRepository;
use think\facade\Route;

/**
 * Class ProductRepository
 * @package app\common\repositories\store\product
 * @author xaboy
 * @mixin dao
 */
class ProductRepository extends BaseRepository
{

    protected $dao;
    protected $filed = 'product_id,mer_id,brand_id,unit_name,mer_status,rate,reply_count,store_info,cate_id,ficti,image,slider_image,store_name,keyword,sort,rank,is_show,sales,price,extension_type,refusal,cost,ot_price,stock,is_gift_bag,care_count,status,is_used,create_time';

    /**
     * ProductRepository constructor.
     * @param dao $dao
     */
    public function __construct(dao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/11
     * @param int $id
     * @param int $merId
     * @return mixed
     */
    public function CatExists(int $id)
    {
        return (app()->make(StoreCategoryRepository::class))->merExists(0, $id);
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/20
     * @param $ids
     * @param int $merId
     * @return bool
     */
    public function merCatExists($ids, int $merId)
    {
        if (!is_array($ids ?? '')) return true;
        foreach ($ids as $id) {
            if (!(app()->make(StoreCategoryRepository::class))->merExists($merId, $id))
                return false;
        }
        return true;
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/11
     * @param int $merId
     * @param int $id
     * @return mixed
     */
    public function merShippingExists(int $merId, int $id)
    {
        $make = app()->make(ShippingTemplateRepository::class);
        return $make->merExists($merId, $id);
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/11
     * @param int $id
     * @return mixed
     */
    public function merBrandExists(int $id)
    {
        $make = app()->make(StoreBrandRepository::class);
        return $make->meExists($id);
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/11
     * @param int $merId
     * @param int $id
     * @return bool
     */
    public function merExists(?int $merId, int $id)
    {
        return $this->dao->merFieldExists($merId, $this->getPk(), $id);
    }

    public function merDeleteExists(int $merId, int $id)
    {
        return $this->dao->getDeleteExists($merId, $id);
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/30
     * @param int $merId
     * @param int $id
     * @return bool
     */
    public function apiExists(?int $merId, int $id)
    {
        return $this->dao->apiFieldExists($merId, $this->getPk(), $id);
    }

    /**
     * @param int $merId
     * @param int $tempId
     * @return bool
     * @author Qinii
     */
    public function merTempExists(int $merId, int $tempId)
    {
        return $this->dao->merFieldExists($merId, 'temp_id', $tempId);
    }

    /**
     * 复制商品，图片下载
     * @Author:Qinii
     * @Date: 2020/9/28
     * @param array $data
     * @param int $productType
     */
    public function copySave(array $data, int $productType = 0)
    {
        $serve = app()->make(DownloadImageService::class);
        $url = $data['image'];
        if (strpos('http', $data['image']) == false) $url = 'http:' . $data['image'];
        $image = $serve->downloadImage($url);
        $data['image'] = systemConfig("site_url") . $image['path'];

        $this->create($data, $productType);
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/11
     * @param array $data
     */
    public function create(array $data, int $productType = 0)
    {
        return Db::transaction(function () use ($data, $productType) {
            $product = $this->setProduct($data);
            $result = $this->dao->create($product);
            $settleParams = $this->setAttrValue($data['attrValue'], $result->product_id, $productType, 0);
            $settleParams['cate'] = $this->setMerCate($data['mer_cate_id'], $result->product_id, $data['mer_id']);
            $settleParams['attr'] = $this->setAttr($data['attr'], $result->product_id);
            $this->save($result->product_id, $settleParams, $data['content']);
            if ($productType == 1) { //秒杀商品
                $dat = $this->setSeckillProduct($data);
                $dat['product_id'] = $result->product_id;
                app()->make(StoreSeckillActiveRepository::class)->create($dat);
            }
            return $result->product_id;
        });
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/11
     * @param int $id
     * @param array $data
     */
    public function edit(int $id, array $data, int $merId, int $productType)
    {
        Db::transaction(function () use ($id, $data, $merId, $productType) {
            $product = $this->setProduct($data);
            $settleParams = $this->setAttrValue($data['attrValue'], $id, $productType, 1);
            $settleParams['cate'] = $this->setMerCate($data['mer_cate_id'], $id, $merId);
            $settleParams['attr'] = $this->setAttr($data['attr'], $id);
            $this->save($id, $settleParams, $data['content'], $product);
            if ($productType == 1) {//秒杀商品
                $dat = $this->setSeckillProduct($data);
                app()->make(StoreSeckillActiveRepository::class)->updateByProduct($id, $dat);
            }
        });
    }


    /**
     * @Author:Qinii
     * @Date: 2020/5/11
     * @param $id
     */
    public function destory($id)
    {
        (app()->make(ProductAttrRepository::class))->clearAttr($id);
        (app()->make(ProductAttrValueRepository::class))->clearAttr($id);
        (app()->make(ProductContentRepository::class))->clearAttr($id);
        (app()->make(ProductCateRepository::class))->clearAttr($id);
        $this->dao->delete($id, true);
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/20
     * @param $id
     * @param $spec_type
     * @param $settleParams
     * @param $content
     * @return int
     */
    public function save($id, $settleParams, $content, $data = [])
    {
        (app()->make(ProductAttrRepository::class))->clearAttr($id);
        (app()->make(ProductAttrValueRepository::class))->clearAttr($id);
        (app()->make(ProductContentRepository::class))->clearAttr($id);
        (app()->make(ProductCateRepository::class))->clearAttr($id);

        if (isset($settleParams['cate']))
            (app()->make(ProductCateRepository::class)->insert($settleParams['cate']));
        if (isset($settleParams['attr']))
            (app()->make(ProductAttrRepository::class))->insert($settleParams['attr']);
        if (isset($settleParams['attrValue'])) {
            $arr = array_chunk($settleParams['attrValue'], 30);
            foreach ($arr as $item) {
                (app()->make(ProductAttrValueRepository::class))->insert($item);
            }
        }
        $this->dao->createContent($id, ['content' => $content]);
        $data['price'] = $settleParams['data']['price'];
        $data['ot_price'] = $settleParams['data']['ot_price'];
        $data['cost'] = $settleParams['data']['cost'];
        $data['stock'] = $settleParams['data']['stock'];
        return $this->dao->update($id, $data);
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/18
     * @param int $id
     * @param array $data
     * @return int
     */
    public function adminUpdate(int $id, array $data)
    {
        Db::transaction(function () use ($id, $data) {
            (app()->make(ProductContentRepository::class))->clearAttr($id);
            $this->dao->createContent($id, ['content' => $data['content']]);
            unset($data['content']);
            return $this->dao->update($id, $data);
        });
    }

    /**
     *  格式化秒杀商品活动时间
     * @Author:Qinii
     * @Date: 2020/9/15
     * @param array $data
     * @return array
     */
    public function setSeckillProduct(array $data)
    {
        $dat = [
            'start_day' => $data['start_day'],
            'end_day' => $data['end_day'],
            'start_time' => $data['start_time'] . ':00:00',
            'end_time' => $data['end_time'] . ':00:00',
            'status' => 1,
            'once_pay_count' => $data['once_pay_count'],
            'all_pay_count' => $data['all_pay_count'],
        ];
        if (isset($data['mer_id'])) $dat['mer_id'] = $data['mer_id'];
        return $dat;
    }

    /**
     *  格式商品主体信息
     * @Author:Qinii
     * @Date: 2020/9/15
     * @param array $data
     * @return array
     */
    public function setProduct(array $data)
    {
        $give_coupon_ids = '';
        if (isset($data['give_coupon_ids']) && !empty($data['give_coupon_ids'])) {
            $gcoupon_ids = array_unique($data['give_coupon_ids']);
            $give_coupon_ids = implode(',', $gcoupon_ids);
        }
        $result = [
            'store_name' => $data['store_name'],
            'image' => $data['image'],
            'slider_image' => implode(',', $data['slider_image']),
            'store_info' => $data['store_info'],
            'keyword' => $data['keyword'],
            'brand_id' => $data['brand_id'],
            'cate_id' => $data['cate_id'],
            'unit_name' => $data['unit_name'],
            'sort' => $data['sort'],
            'is_show' => $data['is_show'] ?? 0,
            'is_good' => $data['is_good'],
            'video_link' => $data['video_link'],
            'temp_id' => $data['temp_id'],
            'extension_type' => $data['extension_type'],
            'spec_type' => $data['spec_type'],
            'status' => $data['status'],
            'give_coupon_ids' => $give_coupon_ids,
            'mer_status' => $data['mer_status'],
        ];
        if (isset($data['is_gift_bag'])) $result['is_gift_bag'] = $data['is_gift_bag'];
        if (isset($data['mer_id'])) $result['mer_id'] = $data['mer_id'];
        if (isset($data['old_product_id'])) $result['old_product_id'] = $data['old_product_id'];
        if (isset($data['product_type'])) $result['product_type'] = $data['product_type'];
        return $result;
    }

    /**
     *  格式商品商户分类
     * @Author:Qinii
     * @Date: 2020/9/15
     * @param array $data
     * @param int $productId
     * @param int $merId
     * @return array
     */
    public function setMerCate(array $data, int $productId, int $merId)
    {
        $result = [];
        foreach ($data as $value) {
            $result[] = [
                'product_id' => $productId,
                'mer_cate_id' => $value,
                'mer_id' => $merId,
            ];
        }
        return $result;
    }

    /**
     *  格式商品规格
     * @Author:Qinii
     * @Date: 2020/9/15
     * @param array $data
     * @param int $productId
     * @return array
     */
    public function setAttr(array $data, int $productId)
    {
        $result = [];
        foreach ($data as $value) {
            $result[] = [
                'type' => 0,
                'product_id' => $productId,
                "attr_name" => $value['value'],
                'attr_values' => implode('-!-', $value['detail']),
            ];
        }
        return $result;
    }

    /**
     *  格式商品SKU
     * @Author:Qinii
     * @Date: 2020/9/15
     * @param array $data
     * @param int $productId
     * @return mixed
     */
    public function setAttrValue(array $data, int $productId, int $productType, int $isUpdate = 0)
    {
        $extension_status = systemConfig('extension_status');
        if ($isUpdate) {
            $product = app()->make(ProductAttrValueRepository::class)->search(['product_id' => $productId])->select()->toArray();
            $oldSku = $this->detailAttrValue($product, null);
        }
        // halt($oldSku);
        $pric = $stock = $ot_price = $cost = 0;
        foreach ($data as $value) {
            $sku = '';
            if (isset($value['detail']) && is_array($value['detail'])) {
                $sku = implode(',', $value['detail']);
            }
            $unique = $this->setUnique($productId, $sku, $productType);
            $result['attrValue'][] = [
                'detail' => json_encode($value['detail'] ?? ''),
                "bar_code" => $value["bar_code"] ?? '',
                "image" => $value["image"],
                "cost" => $value['cost'] ? (($value['cost'] < 0) ? 0 : $value['cost']) : 0,
                "price" => $value['price'] ? (($value['price'] < 0) ? 0 : $value['price']) : 0,
                "volume" => $value['volume'] ? (($value['volume'] < 0) ? 0 : $value['volume']) : 0,
                "weight" => $value['weight'] ? (($value['weight'] < 0) ? 0 : $value['weight']) : 0,
                "stock" => $value['stock'] ? (($value['stock'] < 0) ? 0 : $value['stock']) : 0,
                "ot_price" => $value['ot_price'] ? (($value['ot_price'] < 0) ? 0 : $value['ot_price']) : 0,
                "extension_one" => $extension_status ? ($value['extension_one'] ?? 0) : 0,
                "extension_two" => $extension_status ? ($value['extension_two'] ?? 0) : 0,
                "product_id" => $productId,
                "type" => 0,
                "sku" => $sku,
                "unique" => $unique,
                'sales' => $isUpdate ? ($oldSku[$sku]['sales'] ?? 0) : 0,
            ];

            if (!$pric) $pric = $value['price'];
            if (!$ot_price) $ot_price = $value['ot_price'];
            if (!$cost) $cost = $value['cost'];
            $ot_price = ($ot_price > $value['ot_price']) ? $value['ot_price'] : $ot_price;
            $pric = ($pric > $value['price']) ? $value['price'] : $pric;
            $cost = ($cost > $value['cost']) ? $value['cost'] : $cost;

            $stock = $stock + $value['stock'];
        }
        $result['data'] = ['price' => $pric, 'stock' => $stock, 'ot_price' => $ot_price, 'cost' => $cost];
        return $result;
    }


    /**
     * @Author:Qinii
     * @Date: 2020/5/11
     * @param int $id
     * @param string $sku
     * @param int $type
     * @return string
     */
    public function setUnique(int $id, $sku, int $type)
    {
        return $unique = substr(md5($sku . $id), 12, 11) . $type;
//        $has = (app()->make(ProductAttrValueRepository::class))->merUniqueExists(null, $unique);
//        return $has ? false : $unique;
    }


    /**
     * admin 获取商品详情
     * @Author:Qinii
     * @Date: 2020/5/18
     * @param $id
     * @return array
     */
    public function get($id)
    {
        $with = ['attr', 'attrValue', 'content', 'merCateId.category', 'storeCategory', 'brand', 'temp', 'seckillActive'];
        $data = $this->dao->getWhere(['product_id' => $id], '*', $with)->append(['seckill_status']);
        $where = [['coupon_id', 'in', $data['give_coupon_ids']]];
        $data['coupon'] = app()->make(StoreCouponRepository::class)->selectWhere($where, 'coupon_id,title')->toArray();
        $arr = [];
        if (isset($data['merCateId'])) {
            foreach ($data['merCateId'] as $i) {
                $arr[] = $i['mer_cate_id'];
            }
        }
        $data['mer_cate_id'] = $arr;
        foreach ($data['attr'] as $k => $v) {
            $data['attr'][$k] = [
                'value' => $v['attr_name'],
                'detail' => $v['attr_values']
            ];
        }
        foreach ($data['attrValue'] as $key => $item) {
            $sku = explode(',', $item['sku']);
            $item['old_stock'] = $item['stock'];
            foreach ($sku as $k => $v) {
                $item['value' . $k] = $v;
            }
            $data['attrValue'][$key] = $item;
        }
        $content = $data['content']['content'];
        unset($data['content']);
        $data['content'] = $content;
        return $data;
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/18
     * @param $type
     * @param int|null $merId
     * @return array
     */
    public function switchType($type, ?int $merId = 0, $productType = 0)
    {
        $stock = 0;
        if ($merId) $stock = merchantConfig($merId, 'mer_store_stock');
        switch ($type) {
            case 1:
                $where = ['is_show' => 1, 'status' => 1,];
                break;
            case 2:
                $where = ['is_show' => 0, 'status' => 1];
                break;
            case 3:
                $where = ['is_show' => 1, 'stock' => 0, 'status' => 1];
                break;
            case 4:
                $where = ['stock' => $stock ? $stock : 0, 'status' => 1];
                break;
            case 5:
                $where = ['soft' => true];
                break;
            case 6:
                $where = ['status' => 0];
                break;
            case 7:
                $where = ['status' => -1];
                break;
            default:
                $where = ['is_show' => 1, 'status' => 1];
                break;
        }
        if ($productType == 0) {
            $where['product_type'] = $productType;
            if (!$merId) $where['is_gift_bag'] = 0;
        }
        if ($productType == 1) {
            $where['product_type'] = $productType;
        }
        if ($productType == 3) {
            $where['is_gift_bag'] = 1;
        }
        return $where;
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/18
     * @param int|null $merId
     * @return array
     */
    public function getFilter(?int $merId, $name = '商品', $productType = 0)
    {
        $result = [];
        $result[] = [
            'type' => 1,
            'name' => '出售中' . $name,
            'count' => $this->dao->search($merId, $this->switchType(1, $merId, $productType))->count()
        ];
        $result[] = [
            'type' => 2,
            'name' => '仓库中' . $name,
            'count' => $this->dao->search($merId, $this->switchType(2, $merId, $productType))->count()
        ];
        if ($merId) {
            $result[] = [
                'type' => 3,
                'name' => '已售罄' . $name,
                'count' => $this->dao->search($merId, $this->switchType(3, $merId, $productType))->count()
            ];
            $result[] = [
                'type' => 4,
                'name' => '警戒库存',
                'count' => $this->dao->search($merId, $this->switchType(4, $merId, $productType))->count()
            ];
            $result[] = [
                'type' => 5,
                'name' => '回收站' . $name,
                'count' => $this->dao->search($merId, $this->switchType(5, $merId, $productType))->count()
            ];
        }
        $result[] = [
            'type' => 6,
            'name' => '待审核' . $name,
            'count' => $this->dao->search($merId, $this->switchType(6, $merId, $productType))->count()
        ];
        $result[] = [
            'type' => 7,
            'name' => '审核未通过' . $name,
            'count' => $this->dao->search($merId, $this->switchType(7, $merId, $productType))->count()
        ];
        return $result;
    }

    /**
     * TODO 商户商品列表
     * @Author:Qinii
     * @Date: 2020/5/11
     * @param int $merId
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getList(?int $merId, array $where, int $page, int $limit)
    {
        $query = $this->dao->search($merId, $where)->with(['merCateId.category', 'storeCategory', 'brand']);
        $count = $query->count();
        $list = $query->page($page, $limit)->setOption('field', [])->field($this->filed)->select();
        $list->append(['max_extension', 'min_extension']);
        return compact('count', 'list');
    }

    /**
     * TODO 商户秒杀商品列表
     * @param int|null $merId
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @author Qinii
     * @day 2020-08-04
     */
    public function getSeckillList(?int $merId, array $where, int $page, int $limit)
    {
        $make = app()->make(StoreOrderRepository::class);
        $query = $this->dao->search($merId, $where)->with(['merCateId.category', 'storeCategory', 'brand', 'seckillActive']);
        $count = $query->count($this->dao->getPk());
        $list = $query->page($page, $limit)->setOption('field', [])->field($this->filed)->select()
            ->each(function ($item) use ($make) {
                $item['sales'] = $make->seckillOrderCounut($item['product_id']);
                return $item;
            });
        $list->append(['seckill_status']);
        return compact('count', 'list');
    }

    /**
     * TODO 平台商品列表
     * @Author:Qinii
     * @Date: 2020/5/11
     * @param int $merId
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getAdminList(?int $merId, array $where, int $page, int $limit)
    {
        $query = $this->dao->search($merId, $where)->with(['merCateId.category', 'storeCategory', 'brand', 'merchant']);
        $count = $query->count($this->dao->getPk());
        $list = $query->page($page, $limit)->select();
        $list->append(['max_extension', 'min_extension']);
        return compact('count', 'list');
    }

    /**
     * TODO 平台秒杀商品列表
     * @param int|null $merId
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @author Qinii
     * @day 2020-08-04
     */
    public function getAdminSeckillList(?int $merId, array $where, int $page, int $limit)
    {
        $make = app()->make(StoreOrderRepository::class);
        $query = $this->dao->search($merId, $where)->with(['merCateId.category', 'storeCategory', 'brand', 'merchant', 'seckillActive']);
        $count = $query->count();
        $list = $query->page($page, $limit)
            ->select()
            ->each(function ($item) use ($make) {
                $item['sales'] = $make->seckillOrderCounut($item['product_id']);
                return $item;
            });
        $list->append(['seckill_status']);
        return compact('count', 'list');
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/28
     * @param array $where
     * @param int $page
     * @param int $limit
     * @param $userInfo
     * @return array
     */
    public function getApiSearch($merId, array $where, int $page, int $limit, $userInfo)
    {
        if (isset($where['price_on']) && $where['price_on'] !== '') $where['price'] = [$where['price_on'], $where['price_off']];
        if (isset($where['brand_id']) && $where['brand_id'] !== '') $where['brand_id'] = explode(',', $where['brand_id']);
        unset($where['price_on'], $where['price_off']);

        $where = array_merge($where, $this->dao->productShow());
        //搜索记录
        if ($userInfo && isset($where['keyword']) && !empty($where['keyword']))
            app()->make(UserVisitRepository::class)->searchProduct($userInfo['uid'], $where['keyword']);

        $query = $this->dao->search($merId, $where);
        $count = $query->count($this->dao->getPk());
        $list = $query->page($page, $limit)->setOption('field', [])->field($this->filed)->with(['merchant', 'issetCoupon'])->select();

        $list->each(function ($item) {
            $item['sales'] = $item['sales'] + $item['ficti'];
            unset($item['ficti']);
            return $item;
        });

        if ($this->getUserIsPromoter($userInfo)) $list = $list->each(function ($item) {
            return $item['max_extension'] = $item->max_extension;
        });


        return compact('count', 'list');
    }

    /**
     * TODO 秒杀列表
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @author Qinii
     * @day 2020-08-04
     */
    public function getApiSeckill(array $where, int $page, int $limit)
    {
        $field = 'Product.product_id,Product.mer_id,is_new,keyword,brand_id,image,store_name,sort,rate,reply_count,sales,price,cost,ficti,ot_price,stock,extension_type,care_count,unit_name,create_time';

        $make = app()->make(StoreOrderRepository::class);
        $res = app()->make(StoreSeckillTimeRepository::class)->getBginTime($where);
        $count = 0;
        $list = [];

        if ($res) {
            $where = [
                'start_time' => $res['start_time'],
                'end_time' => $res['end_time'],
                'day' => date('Y-m-d', time())
            ];
            $query = $this->dao->seckillSearch($where)->with(['seckillActive']);
            $count = $query->count();
            $list = $query->page($page, $limit)->setOption('field', [])->field($field)->select()
                ->each(function ($item) use ($make) {
                    $item['sales'] = $make->seckillOrderCounut($item['product_id']);
                    $item['stop'] = $item->end_time;
                    //unset($item['seckillActive']);
                    unset($item['ficti']);
                    return $item;
                });
        }
        return compact('count', 'list');
    }

    /**
     * TODO 平台礼包列表
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @author Qinii
     * @day 2020-06-01
     */
    public function getBagList(array $where, int $page, int $limit)
    {
        $query = $this->dao->search(null, $where)->with(['merCateId.category', 'storeCategory', 'brand', 'merchant' => function ($query) {
            $query->field('mer_id,mer_avatar,mer_name,product_score,service_score,postage_score,status,care_count,is_trader');
        }]);
        $count = $query->count($this->dao->getPk());
        $list = $query->page($page, $limit)->setOption('field', [])->field($this->filed)->select();

        return compact('count', 'list');
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/28
     * @param array $where
     * @return mixed
     */
    public function getBrandByCategory(array $where)
    {
        $query = $this->dao->search(null, $where);
        return $query->group('brand_id')->column('brand_id');
    }

    /**
     * api 获取商品详情
     * @Author:Qinii
     * @Date: 2020/5/30
     * @param $id
     * @param $userInfo
     */
    public function detail(int $id, $userInfo)
    {
        $where = [
            'is_show' => 1,
            'status' => 1,
            'is_used' => 1,
            'mer_status' => 1,
            'product_id' => $id
        ];
        $field = 'product_id,mer_id,image,slider_image,store_name,store_info,unit_name,price,cost,ot_price,stock,sales,ficti,video_link,product_type,extension_type';
        $with = ['attr', 'attrValue', 'content', 'topReply.orderProduct' => function ($query) {
            $query->field('reply_id,uid,nickname,merchant_reply_content,avatar,order_product_id,product_id,product_score,service_score,postage_score,comment,pics,rate,create_time');
        }, 'merchant.recommend'];
        $res = $this->dao->getWhere($where, $field, $with);
        if (!$res) return null;
        if ($userInfo) {
            $isRelation = app()->make(UserRelationRepository::class)->getUserRelation(['type_id' => $res['product_id'], 'type' => 1], $userInfo['uid']);
            if ($this->getUserIsPromoter($userInfo)) $res->append(['max_extension', 'min_extension']);
        }
        $res['isRelation'] = $isRelation ?? false;
        $res['sku'] = $this->detailAttrValue($res['attrValue'], $userInfo, $res['product_type']);
        $res['sales'] = $res['sales'] + $res['ficti'];
        $res['replayData'] = app()->make(ProductReplyRepository::class)->getReplyRate($res['product_id']);
        if ($res['topReply']) {
            $res['topReply']['sku'] = $res['topReply']['orderProduct']['cart_info']['productAttr']['sku'];
            unset($res['topReply']['orderProduct']);
        }
        $attr = $this->detailAttr($res['attr']);
        unset($res['attrValue']);
        unset($res['ficti']);
        unset($res['attr']);
        $res['attr'] = $attr;
        return $res;
    }

    /**
     * TODO 单商品属性
     * @param $data
     * @return array
     * @author Qinii
     * @day 2020-08-05
     */
    public function detailAttr($data)
    {
        $attr = [];
        foreach ($data as $key => $item) {
            $attr[$key] = $item;
            $arr = [];
            foreach ($item['attr_values'] as $i => $value) {
                $arr[] = [
                    'attr' => $value,
                    'check' => false
                ];
            }
            $attr[$key]['attr_value'] = $arr;
        }
        return $attr;
    }

    /**
     * TODO 单商品sku
     * @param $data
     * @param $userInfo
     * @return array
     * @author Qinii
     * @day 2020-08-05
     */
    public function detailAttrValue($data, $userInfo, $productType = 0)
    {
        $sku = [];
        $make = app()->make(StoreOrderRepository::class);
        foreach ($data as $k => $value) {
            $sku[$value['sku']] = [
                'sku' => $value['sku'],
                'price' => $value['price'],
                'stock' => $value['stock'],
                'image' => $value['image'],
                'bar_code' => $value['bar_code'],
                'weight' => $value['weight'],
                'volume' => $value['volume'],
                'sales' => $value['sales'],
                'unique' => $value['unique'],
            ];
            if ($productType) {
                $sku[$value['sku']]['stock'] = $value['stock'] - $make->seckillSkuOrderCounut($value['unique']); //获取sku的销量
            }
            if ($this->getUserIsPromoter($userInfo)) {
                $sku[$value['sku']]['extension_one'] = $value->bc_extension_one;
                $sku[$value['sku']]['extension_two'] = $value->bc_extension_two;
            }

        }
        return $sku;
    }


    /**
     * TODO api秒杀商品详情
     * @param int $id
     * @author Qinii
     * @day 2020-08-05
     */
    public function seckillDetail(int $id)
    {
        $field = 'product_id,mer_id,image,slider_image,store_name,store_info,unit_name,price,cost,ot_price,stock,sales,video_link,old_product_id,product_type';

        $with = ['attr', 'attrValue', 'content', 'topReply' => function ($query) {
            $query->field('reply_id,uid,nickname,merchant_reply_content,avatar,order_product_id,product_id,product_score,service_score,postage_score,comment,create_time');
        }, 'merchant.recommend', 'seckillActive' => function ($query) {
            $query->field('start_day,end_day,start_time,end_time,product_id');
        }];
        $where = $this->seckillShow();
        $where['product_id'] = $id;
        $res = $this->dao->getWhere($where, $field, $with);
        if (!$res) throw new ValidateException('商品不存在');
        $res['isRelation'] = $isRelation ?? false;
        $res['sku'] = $this->detailAttrValue($res['attrValue'], null, $res['product_type']);
        $res['replayData'] = app()->make(ProductReplyRepository::class)->getReplyRate($res['product_id']);
        unset($res['attrValue'], $res['ficti'], $res['attr']);
        $res['attr'] = $this->detailAttr($res['attr']);
        //活动是否结束
        $res->append(['seckill_status']);
        $res['stop'] = strtotime(date('Y-m-d', time()) . $res['seckillActive']['end_time'] . ':00:00');
        unset($res['seckillActive']);
        $res['sales'] = app()->make(StoreOrderRepository::class)->seckillOrderCounut($id);
        $res['quota'] = $this->seckillStock($id);
        $oldPro = $this->dao->productShow();
        $oldPro['product_id'] = $res['old_product_id'];
        $old_status = 0;
        if ($this->dao->getWhere($oldPro)) $old_status = 1;
        $res['old_status'] = $old_status;
        return $res;
    }


    /**
     * TODO 秒杀商品库存检测
     * @param int $productId
     * @return bool|int
     * @author Qinii
     * @day 2020-08-05
     */
    public function seckillStock(int $productId)
    {
        $product = $this->dao->getWhere(['product_id' => $productId], '*', ['attrValue']);
        $count = app()->make(StoreOrderRepository::class)->seckillOrderCounut($productId);
        if ($product['stock'] > $count) {
            $make = app()->make(ProductAttrValueRepository::class);
            foreach ($product['attrValue'] as $item) {
                $attr = [
                    ['sku', '=', $item['sku']],
                    ['product_id', '=', $product['old_product_id']],
                    ['stock', '>', 0],
                ];
                if ($make->getWhereCount($attr)) return true;
            }
        }
        return false;
    }


    /**
     * @Author:Qinii
     * @Date: 2020/5/30
     * @param $userInfo
     * @return bool
     */
    public function getUserIsPromoter($userInfo)
    {
        return (isset($userInfo['is_promoter']) && $userInfo['is_promoter'] && systemConfig('extension_status')) ? true : false;
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/30
     * @param $userInfo
     * @param int|null $merId
     * @param $page
     * @param $limit
     * @return array
     */
    public function recommend($userInfo, ?int $merId, $page, $limit)
    {
        $where = ['order' => 'sales'];
        if (!is_null($userInfo)) {
            $cate_ids = app()->make(UserVisitRepository::class)->getRecommend($userInfo['uid']);
            if ($cate_ids) $where = ['cate_ids' => $cate_ids];
        }
        $where = array_merge($where, $this->switchType(1, $merId, 0), $this->dao->productShow());
        $query = $this->dao->search($merId, $where);
        $count = $query->count($this->dao->getPk());
        $list = $query->page($page, $limit)->setOption('field', [])->with(['issetCoupon', 'merchant'])->field('mer_id,product_id,cate_id,image,store_name,sort,sales,price,create_time')->select();

        return compact('count', 'list');
    }

    /**
     * 检测是否有效
     * @Author:Qinii
     * @Date: 2020/6/1
     * @param $id
     * @return mixed
     */
    public function getOne($id)
    {
        $data = ($this->dao->getWhere([$this->dao->getPk() => $id]));
        if (!is_null($data) && $data->check()) return $data;
        return false;
    }

    public function switchStatus(array $id, $data)
    {
        $status = $data['status'] ?? 0;
        $is_show = $data['is_show'] ?? 0;
        $make = app()->make(ProductAttrValueRepository::class);

        if (($status == 1 || $is_show == 1) && systemConfig('extension_status')) {
            foreach ($id as $i) {
                if ($this->getWhereCount(['product_id' => $i, 'extension_type' => 1]) && !$make->checkExtensionById($i))
                    throw new ValidateException('设置佣金不能低于系统比例');
            }
        }
        $this->dao->updates($id, $data);
        $res = $this->dao->get($id[0]);

        if (isset($data['status'])) {
            SwooleTaskService::merchant('notice', [
                'type' => 'product',
                'data' => [
                    'title' => '新审核结果',
                    'message' => '您有一条新的商品审核',
                    'id' => ''
                ]
            ], $res['mer_id']);
        }
    }

    /**
     * TODO 执行检测所有线上商品
     * @author Qinii
     * @day 2020-06-24
     */
    public function checkProductByExtension()
    {
        if (systemConfig('extension_status')) {
            $where = ['status' => 1, 'is_show' => 1, 'product_type' => 0];
            $make = app()->make(ProductAttrValueRepository::class);
            $query = $this->dao->search(null, $where)->where('extension_type', 1);
            $query->chunk(100, function ($prduct) use ($make) {
                foreach ($prduct as $item) {
                    if (!$make->checkExtensionById($item['product_id'])) {
                        $item->status = -2;
                        $item->refusal = '因佣金比例调整，商品佣金低于系统比例';
                        $item->save();
                    }
                }
            });
        }
    }

    public function wxQrCode(Product $product, User $user)
    {
        $name = md5('pwx' . $product->product_id . $user->uid . $user['is_promoter'] . date('Ymd')) . '.jpg';
        return app()->make(QrcodeService::class)->getWechatQrcodePath($name, '/pages/goods_details/index?id=' . $product->product_id . '&spread=' . $user['uid']);
    }

    public function routineQrCode(Product $product, User $user)
    {
        //小程序
        $name = md5('prt' . $product->product_id . $user->uid . $user['is_promoter'] . date('Ymd')) . '.jpg';
        $data = 'id=' . $product->product_id;
        if ($user['is_promoter'] || systemConfig('store_brokerage_statu') == 2) $data .= '&pid=' . $user['uid'];
        return app()->make(QrcodeService::class)->getRoutineQrcodePath($name, 'pages/goods_details/index', $data);
    }

    /**
     * TODO 礼包是否超过数量限制
     * @param $merId
     * @return bool
     * @author Qinii
     * @day 2020-06-25
     */
    public function checkMerchantBagNumber($merId)
    {
        $where = ['is_gift_bag' => 1];
        $promoter_bag_number = systemConfig('promoter_bag_number');
        $count = $this->dao->search($merId, $where)->count();
        if ($promoter_bag_number > $count) return true;
        return false;
    }

    /**
     * TODO
     * @param int $id
     * @param array $status
     * @author Qinii
     * @day 2020-07-17
     */
    public function changeUsed(int $id, array $status)
    {
        return $this->dao->update($id, $status);
    }

    public function orderProductIncStock($cart, $productNum = null)
    {
        Db::transaction(function () use ($cart, $productNum) {
            $productAttrValueRepository = app()->make(ProductAttrValueRepository::class);
            if (!$productAttrValueRepository->attrExists($cart['product_id'], $cart['cart_info']['productAttr']['unique'])) return;
            $productAttrValueRepository->incStock($cart['product_id'], $cart['cart_info']['productAttr']['unique'], $productNum ?? $cart['product_num']);
            if (!$this->exists($cart['product_id'])) return;
            $this->dao->incStock($cart['product_id'], $productNum ?? $cart['product_num']);
            if (isset($cart['cart_info']['product']['old_product_id'])) {
                $oldId = $cart['cart_info']['product']['old_product_id'];
                if (!$productAttrValueRepository->skuExists($oldId, $cart['cart_info']['productAttr']['sku'])) return;
                $productAttrValueRepository->incSkuStock($oldId, $cart['cart_info']['productAttr']['sku'], $productNum ?? $cart['product_num']);
                if ($this->exists($oldId))
                    $this->incStock($oldId, $productNum ?? $cart['product_num']);
            }
        });
    }

    /**
     * TODO 商品验证
     * @param $data
     * @author Qinii
     * @day 2020-08-01
     */
    public function check($data, $merId)
    {
        if (!$this->merBrandExists($data['brand_id']))
            throw new ValidateException('品牌不存在');
        if (!$this->CatExists($data['cate_id']))
            throw new ValidateException('平台分类不存在');
        if (isset($data['mer_cate_id']) && !$this->merCatExists($data['mer_cate_id'], $merId))
            throw new ValidateException('不存在的商户分类');
        if (!$this->merShippingExists($merId, $data['temp_id']))
            throw new ValidateException('运费模板不存在');

    }
}
