<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-07-30
 *
 * Copyright (c) http://crmeb.net
 */
namespace app\common\repositories\store\product;

use app\common\repositories\BaseRepository;
use app\common\repositories\system\merchant\MerchantRepository;
use crmeb\services\CopyProductService;
use think\exception\ValidateException;
use app\common\dao\store\product\ProductCopyDao;
use think\facade\Db;

class ProductCopyRepository extends BaseRepository
{
    protected $host = ['taobao', 'tmall', 'jd', 'pinduoduo', 'suning', 'yangkeduo'];

    protected $dao;

    /**
     * ProductRepository constructor.
     * @param dao $dao
     */
    public function __construct(ProductCopyDao $dao)
    {
        $this->dao = $dao;
    }

    public function copyProduct(array $data,?int $merId)
    {
        $type = $data['type'];
        $id = $data['id'];
        $shopid = $data['shopid'];
        $url = $data['url'];

        $apikey = systemConfig('copy_product_apikey');
        if ((!$type || !$id) && $url) {
            $url_arr = parse_url($url);
            if (isset($url_arr['host'])) {
                foreach ($this->host as $name) {
                    if (strpos($url_arr['host'], $name) !== false) {
                        $type = $name;
                    }
                }
            }
            $type = ($type == 'pinduoduo' || $type == 'yangkeduo') ? 'pdd' : $type;
            switch ($type) {
                case 'taobao':
                case 'tmall':
                    $params = [];
                    if (isset($url_arr['query']) && $url_arr['query']) {
                        $queryParts = explode('&', $url_arr['query']);
                        foreach ($queryParts as $param) {
                            $item = explode('=', $param);
                            if (isset($item[0]) && $item[1]) $params[$item[0]] = $item[1];
                        }
                    }
                    $id = $params['id'] ?? '';
                    break;
                case 'jd':
                    $params = [];
                    if (isset($url_arr['path']) && $url_arr['path']) {
                        $path = str_replace('.html', '', $url_arr['path']);
                        $params = explode('/', $path);
                    }
                    $id = $params[1] ?? '';
                    break;
                case 'pdd':
                    $params = [];
                    if (isset($url_arr['query']) && $url_arr['query']) {
                        $queryParts = explode('&', $url_arr['query']);
                        foreach ($queryParts as $param) {
                            $item = explode('=', $param);
                            if (isset($item[0]) && $item[1]) $params[$item[0]] = $item[1];
                        }
                    }
                    $id = $params['goods_id'] ?? '';
                    break;
                case 'suning':
                    $params = [];
                    if (isset($url_arr['path']) && $url_arr['path']) {
                        $path = str_replace('.html', '', $url_arr['path']);
                        $params = explode('/', $path);
                    }
                    $id = $params[2] ?? '';
                    $shopid = $params[1] ?? '';
                    break;

            }
        }
        $result = CopyProductService::getInfo($type, ['itemid' => $id, 'shopid' => $shopid], $apikey);

        if ($result['status']) {
            $this->add([
                'type'  => $data['type'],
                'num'   => -1,
                'url'   => $data['url'],
                'message' => '复制商品「'.$result['data']['store_name'] .'」'
            ],$merId);
            return ['info' => $result['data']];
        } else {
            throw new ValidateException($result['msg']);
        }
    }

    /**
     * TODO 添加记录并修改数据
     * @param $data
     * @param $merId
     * @author Qinii
     * @day 2020-08-06
     */
    public function add($data,$merId)
    {
        $make = app()->make(MerchantRepository::class);
        $copy_product_num = $make->getCopyNum($merId);
        $copy_num = $copy_product_num + $data['num'];
        $arr = [
            'type'  => $data['type'] ?? '',
            'num'   => $data['num'],
            'url'   => $data['url'] ??'',
            'mer_id'=> $merId,
            'message' => $data['message'],
            'copy_product_num' => ($copy_num < 0) ? 0 : $copy_num,
        ];
        Db::transaction(function()use($arr,$make){
            $this->dao->create($arr);
            $make->changeCopyNum($arr['mer_id'],$arr['num']);
        });
    }

    /**
     * TODO 默认赠送复制次数
     * @param $merId
     * @author Qinii
     * @day 2020-08-06
     */
    public function defaulCopyNum($merId)
    {
        if(systemConfig('copy_product_status')){
            $data = [
                'type' => 'sys',
                'num' => systemConfig('copy_product_defaul'),
                'message' => '赠送次数',
            ];
            $this->add($data,$merId);
        }
    }

    public function getList(array $where,int $page, int $limit)
    {
        $query = $this->dao->search($where);
        $count = $query->count();
        $list = $query->page($page,$limit)->select();
        return compact('count','list');
    }

}
