<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/11/23
 */
namespace crmeb\services;

use stdClass;
use think\facade\Cache;
/**
 * 复制主流商城商品
 * Class CopyProductService
 * @package crmeb\services
 */
class CopyProductService
{
    //接口地址
    protected static $api = [
        'taobao' => 'https://api03.6bqb.com/taobao/detail', //https://api03.6bqb.com/app/taobao/detail
        'tmall' => 'https://api03.6bqb.com/tmall/detail',
        'jd' => 'https://api03.6bqb.com/jd/detail',
        'pdd' => 'https://api03.6bqb.com/pdd/detail',
        'suning' => 'https://api03.6bqb.com/suning/detail',
    ];
    protected static $apiKey = '';
    //商品默认字段
    protected static $productInfo = [
        'cate_id' => '',
        'store_name' => '',
        'store_info' => '',
        'unit_name' => '件',
        'price' => 0,
        'keyword' => '',
        'ficti' => 0,
        'ot_price' => 0,
        'give_integral' => 0,
        'postage' => 0,
        'cost' => 0,
        'image' => '',
        'slider_image' => '',
        'video_link' => '',
        'add_time' => 0,
        'stock' => 0,
        'description' => '',
        'description_images' => [],
        'soure_link' => '',
        'temp_id' => '',
        'items' => [],
        'attrs' => [],
        'info' => [],
    ];

    /**
     * 整合
     * @param $url
     * @param $method
     * @param $data
     * @return string
     */
    public static function makeUrl(string $url, string $method, array $data)
    {
        $param = '';
        if (strtolower($method) == 'get' && $data) {
            foreach ($data as $key => $value) {
                $param .= '&' . $key . '=' . $value;
            }
        }
        return $url . '?apikey=' . self::$apiKey . $param;
    }

    /**
     * @param bool $status
     * @param string $msg
     * @param array $data
     */
    public static function setReturn(bool $status = true, string $msg = 'SUCCESS', array $data = [])
    {
        return ['status' => $status, 'msg' => $msg, 'data' => $data];
    }

    /**
     *
     * @param array $data
     */
    public static function getInfo(string $type = 'taobao', array $data = [], string $apikey = '')
    {
        if (!$apikey) {
            return self::setReturn(false, '请先去设置复制商品apiKey');
        }
        $url = self::$api[$type] ?? '';
        $action = $type . 'Info';
        $deal_action = $type . 'Deal';
        $method = 'get';
        self::$apiKey = $apikey;
        if (!$data || !$url || !is_callable(self::class, $action) || !is_callable(self::class, $deal_action)) {
            return self::setReturn(false, '暂不支持该平台商品复制');
        }
        switch ($type) {
            case 'taobao':
            case 'tmall':
            case 'jd':
            case 'pdd':
                $method = 'get';
                if (!isset($data['itemid']) || !$data['itemid'])
                    return self::setReturn(false, '缺少商品ID');
                break;
            case 'suning':
                $method = 'get';
                if (!isset($data['itemid']) || !$data['itemid'])
                    return self::setReturn(false, '缺少商品ID');
                if (!isset($data['shopid']) || !$data['shopid'])
                    return self::setReturn(false, '缺少商户ID');
                break;
        }
        $url = self::makeUrl($url, $method, $data);
        if ($cache_info = Cache::get(md5($url))) {
            return self::setReturn(true, 'SUCCESS', $cache_info);
        }

        /*
         *  测试 节省次数用
          if (!$info = Cache::get('info'.md5($url))) {
            $info = self::$action($url, $data);
            Cache::set('info'.md5($url),$info);
          }
         */

        $info = self::$action($url, $data);
        if (!$info) return self::setReturn(false, '获取商品失败');
        $info = json_decode($info, true);
        if (!$info || (!in_array($info['retcode'], ['0000']))) {
            return self::setReturn(false, $info['data'], $info);
        }
        $result = $info['data'];
        /*
        //可能存在下一页  但是api中没有分页参数 暂留
        if (isset($info['hasNext']) && $info['hasNext']) {
            $data['page'] = $info['page'] + 1;
        }
        */
        $result = self::$deal_action($result);
        if ($result['items']) {
            foreach ($result['items'] as $k => $item) {
                if ($item['value'] == '') unset($result['items'][$k]);
            }
            $result['info'] = self::formatAttr($result['items']);
        }else{
            $result['info'] = null;
        }
        if (!$result['image'] && $result['slider_image']) $result['image'] = $result['slider_image'][0] ?? '';
        if($result['description']){
            $result['description'] = str_replace('data-lazyload','src',$result['description']);
            $pattern  = '/<img size=(.*?)>/';
            $replacement  = '<img src="';
            $result['description'] = preg_replace($pattern,$replacement,$result['description']);
            $result['description'] = preg_replace('/<\/img>/','">',$result['description']);
        }
        Cache::set(md5($url), $result, 3600 * 24);
        return self::setReturn(true, 'SUCCESS', $result);
    }

    /**
     * 获取淘宝商品
     * @param $url
     * @param $data
     * @param string $method
     * @return bool|string
     */
    public static function taobaoInfo(string $url, array $data, string $method = 'get')
    {
        $info = HttpService::request($url, $method, $data);
        $result = false;
        if ($info) {
            $result = $info;
        }
        return $result;
    }

    /**
     * 处理获取淘宝的商品
     * @param $data
     * @return mixed
     */
    public static function taobaoDeal(array $data)
    {
        $info = $data['item'] ?? [];
        $result = self::$productInfo;
        if ($info) {
            $result['store_name'] = $info['title'] ?? '';
            $result['store_info'] = $info['subTitle'] ?? '';
            $result['slider_image'] = $info['images'] ?? '';
            $result['description'] = $info['desc'] ?? '';
            $result['description_images'] = $info['descImgs'] ?? [];
            $items = [];
            if (isset($info['props']) && $info['props']) {
                foreach ($info['props'] as $key => $prop) {
                    $item['value'] = $prop['name'];
                    $item['detail'] = [];
                    foreach ($prop['values'] as $name) {
                        $item['detail'][] = $name['name'];
                    }
                    $items[] = $item;
                }
            }
            $result['items'] = $items;
        }
        return $result;
    }


    /**
     * 获取天猫商品
     * @param $url
     * @param $data
     * @param string $method
     * @return bool|string
     */
    public static function tmallInfo(string $url, array $data, string $method = 'get')
    {
        $info = HttpService::request($url, $method, $data);
        $result = false;
        if ($info) {
            $result = $info;
        }
        return $result;
    }

    /**
     * 处理天猫商品
     * @param $data
     * @return mixed
     */
    public static function tmallDeal(array $data)
    {
        $info = $data['item'] ?? [];
        $result = self::$productInfo;
        if ($info) {
            $result['store_name'] = $info['title'] ?? '';
            $result['store_info'] = $info['subTitle'] ?? '';
            $result['slider_image'] = $info['images'] ?? '';
            $result['description'] = $info['desc'] ?? '';
            $result['description_images'] = $info['descImgs'] ?? [];
            $items = [];
            if (isset($info['props']) && $info['props']) {
                foreach ($info['props'] as $key => $prop) {
                    $item['value'] = $prop['name'];
                    $item['detail'] = [];
                    foreach ($prop['values'] as $name) {
                        $item['detail'][] = $name['name'];
                    }
                    $items[] = $item;
                }
            }
            $result['items'] = $items;
        }
        return $result;
    }

    /**
     * 获取京东商品
     * @param $url
     * @param $data
     * @param string $method
     * @return bool|string
     */
    public static function jdInfo(string $url, array $data, string $method = 'get')
    {
        $info = HttpService::request($url, $method, $data);
        $result = false;
        if ($info) {
            $result = $info;
        }
        return $result;
    }

    /**
     * 处理京东商品
     * @param $data
     * @return mixed
     */
    public static function jdDeal(array $data)
    {
        $info = $data['item'] ?? [];
        $result = self::$productInfo;
        if ($info) {
            $result['store_name'] = $info['name'] ?? '';
            $result['store_info'] = $result['store_name'];
            $result['price'] = $info['price'] ?? 0;
            $result['ot_price'] = $info['originalPrice'] ?? 0;
            $result['slider_image'] = $info['images'] ?? [];
            $result['description'] = $info['desc'] ?? '';
            $result['description_images'] = $info['descImgs'] ?? [];
            $items = [];
            if (isset($info['skuProps']) && $info['skuProps']) {
                foreach ($info['skuProps'] as $key => $prop) {
                    $item['value'] = $info['saleProp'][$key] ?? '';
                    $item['detail'] = $prop;
                    $items[] = $item;
                }
            }
            $result['items'] = $items;
        }
        return $result;
    }

    /**
     * 获取拼多多商品
     * @param $url
     * @param $data
     * @param string $method
     * @return bool|string
     */
    public static function pddInfo(string $url, array $data, string $method = 'get')
    {
        $info = HttpService::request($url, $method, $data);
        $result = false;
        if ($info) {
            $result = $info;
        }
        return $result;
    }

    /**
     * 处理拼多多商品
     * @param $data
     * @return mixed
     */
    public static function pddDeal(array $data)
    {
        $info = $data['item'] ?? [];
        $result = self::$productInfo;
        if ($info) {
            $result['store_name'] = $info['goodsName'] ?? '';
            $result['store_info'] = $info['goodsDesc'] ?? '';
            $result['image'] = $info['thumbUrl'] ?? '';
            $result['slider_image'] = $info['banner'] ?? [];
            $result['video_link'] = $info['video']['videoUrl'] ?? '';
            $result['price'] = $info['maxNormalPrice'] ?? 0;
            $result['ot_price'] = $info['marketPrice'] ?? 0;
            $descImgs = [];
            if (isset($info['detail']) && $info['detail']) {
                foreach ($info['detail'] as $img) {
                    if (isset($img['url']) && $img['url']) $descImgs[] = $img['url'];
                }
            }
            $result['description_images'] = $descImgs;
            $items = [];
            if (isset($info['skus']) && $info['skus']) {
                $i = $y = 0;
                foreach ($info['skus'] as $sku) {
                    foreach ($sku['specs'] as $key => $spec) {
                        if ($i == 0) $items[$y]['value'] = $spec['spec_key'];
                        $items[$y]['detail'][] = $spec['spec_value'];
                        $y++;
                    }
                    $i++;
                }
            }
            foreach ($items as $k => $item) {
                $items[$k]['detail'] = array_unique($item['detail']);
            }
            $result['items'] = $items;
        }
        return $result;
    }

    /**
     * 获取苏宁商品
     * @param $url
     * @param $data
     * @param string $method
     * @return bool|string
     */
    public static function suningInfo(string $url, array $data, string $method = 'get')
    {
        $info = HttpService::request($url, $method, $data);
        $result = false;
        if ($info) {
            $result = $info;
        }
        return $result;
    }

    /**
     * 处理苏宁商品
     * @param $data
     * @return mixed
     */
    public static function suningDeal(array $data)
    {
        $result = self::$productInfo;
        if ($data) {
            $result['store_name'] = $data['title'] ?? '';
            $result['store_info'] = $result['store_name'];
            $result['slider_image'] = $data['images'] ?? [];
            $result['price'] = $data['price'] ?? 0;
            $result['desc'] = $data['desc'] ?? '';
            $items = [];
            if (isset($data['passSubList']) && $data['passSubList']) {
                $i = 0;
                foreach ($data['passSubList'] as $passSUb) {
                    $j = 0;
                    foreach ($passSUb as $key => $sub) {
                        if ($i == 0) $items[$j]['value'] = $key;
                        foreach ($sub as $value) {
                            if (isset($value['characterValueDisplayName']) && $value['characterValueDisplayName'])
                                $items[$j]['detail'][] = $value['characterValueDisplayName'];
                        }
                        $j++;
                    }
                    $i++;
                }
            }
            foreach ($items as $k => $item) {
                $items[$k]['detail'] = array_unique($item['detail']);
            }
            $result['items'] = $items;
        }
        return $result;
    }

    public static function  attrChange(array $attr)
    {
        $count = count($attr);
        $arr = $attr[$count - 2];
        unset($attr[$count-2]);
        array_push($attr,$arr);
        return $attr;
    }
    /**
     * 格式化规格
     * @param $attr
     * @return array
     */
    public static function formatAttr(array $attr)
    {
        $value = attr_format($attr)[1];
        $attr = self::attrChange($attr);
        $valueNew = [];
        $count = 0;
        foreach ($value as $key => $item) {
            $detail = $item['detail'];
            sort($item['detail'], SORT_STRING);
            $suk = implode(',', $item['detail']);
            $sukValue[$suk]['image'] = '';
            $sukValue[$suk]['price'] = 0;
            $sukValue[$suk]['cost'] = 0;
            $sukValue[$suk]['ot_price'] = 0;
            $sukValue[$suk]['stock'] = 0;
            $sukValue[$suk]['bar_code'] = '';
            $sukValue[$suk]['weight'] = 0;
            $sukValue[$suk]['volume'] = 0;
            $sukValue[$suk]['brokerage'] = 0;
            $sukValue[$suk]['brokerage_two'] = 0;

            foreach (array_keys($detail) as $k => $title) {
                if ($title == '') continue;
                $header[$k]['title'] = $title;
                $header[$k]['align'] = 'center';
                $header[$k]['minWidth'] = 120;
            }
            foreach (array_values($detail) as $k => $v) {
                if ($v == '') continue;
                $valueNew[$count]['value' . ($k + 1)] = $v;
                $header[$k]['key'] = 'value' . ($k + 1);
            }
            $valueNew[$count]['detail'] = $detail;
            $valueNew[$count]['image'] = $sukValue[$suk]['image'] ?? '';
            $valueNew[$count]['price'] = $sukValue[$suk]['price'] ? floatval($sukValue[$suk]['price']) : 0;
            $valueNew[$count]['cost'] = $sukValue[$suk]['cost'] ? floatval($sukValue[$suk]['cost']) : 0;
            $valueNew[$count]['ot_price'] = isset($sukValue[$suk]['ot_price']) ? floatval($sukValue[$suk]['ot_price']) : 0;
            $valueNew[$count]['stock'] = $sukValue[$suk]['stock'] ? intval($sukValue[$suk]['stock']) : 0;
            $valueNew[$count]['bar_code'] = $sukValue[$suk]['bar_code'] ?? '';
            $valueNew[$count]['weight'] = $sukValue[$suk]['weight'] ? floatval($sukValue[$suk]['weight']) : 0;
            $valueNew[$count]['volume'] = $sukValue[$suk]['volume'] ? floatval($sukValue[$suk]['volume']) : 0;
            $valueNew[$count]['brokerage'] = $sukValue[$suk]['brokerage'] ? floatval($sukValue[$suk]['brokerage']) : 0;
            $valueNew[$count]['brokerage_two'] = $sukValue[$suk]['brokerage_two'] ? floatval($sukValue[$suk]['brokerage_two']) : 0;
            $count++;
        }
        $header[] = ['title' => '图片', 'slot' => 'image', 'align' => 'center', 'minWidth' => 80];
        $header[] = ['title' => '售价', 'slot' => 'price', 'align' => 'center', 'minWidth' => 95];
        $header[] = ['title' => '成本价', 'slot' => 'cost', 'align' => 'center', 'minWidth' => 95];
        $header[] = ['title' => '原价', 'slot' => 'ot_price', 'align' => 'center', 'minWidth' => 95];
        $header[] = ['title' => '库存', 'slot' => 'stock', 'align' => 'center', 'minWidth' => 95];
        $header[] = ['title' => '商品编号', 'slot' => 'bar_code', 'align' => 'center', 'minWidth' => 120];
        $header[] = ['title' => '重量(KG)', 'slot' => 'weight', 'align' => 'center', 'minWidth' => 95];
        $header[] = ['title' => '体积(m³)', 'slot' => 'volume', 'align' => 'center', 'minWidth' => 95];
        $header[] = ['title' => '操作', 'slot' => 'action', 'align' => 'center', 'minWidth' => 70];
        $info = ['attr' => $attr, 'value' => $valueNew, 'header' => $header];
        return $info;
    }
}
