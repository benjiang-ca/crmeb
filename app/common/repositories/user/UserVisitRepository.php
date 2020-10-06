<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/5/27
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\repositories\user;


use app\common\dao\user\UserVisitDao;
use app\common\repositories\BaseRepository;

/**
 * Class UserVisitRepository
 * @package app\common\repositories\user
 * @author xaboy
 * @day 2020/5/27
 * @mixin UserVisitDao
 */
class UserVisitRepository extends BaseRepository
{
    /**
     * @var UserVisitDao
     */
    protected $dao;

    /**
     * UserVisitRepository constructor.
     * @param UserVisitDao $dao
     */
    public function __construct(UserVisitDao $dao)
    {
        $this->dao = $dao;
    }

    public function getRecommend(?int $uid)
    {
        $data = $this->dao->search($uid,'product')->with(['product'=>function($query){
            $query->field('product_id,cate_id');
        }])->limit(7)->select();
        $i = [];
        if(is_array($data)){
            foreach ($data as $item){
                $i[] = $item['product']['cate_id'];
            }
        }
        return $i;
    }

    public function getHistory($uid,$page, $limit)
    {
        $query = $this->dao->search($uid,'product');
        $query->with(['product'=>function($query){
            $query->field('product_id,image,store_name,slider_image,price,is_show,status,sales,ficti');
        }]);
        $count = $query->count();
        $list = $query->page($page,$limit)->select()->each(function ($item) {
            $item['product']['sales'] = $item['product']['sales'] + $item['product']['ficti'];
            unset($item['product']['ficti']);
            return $item;
        });
        return compact('count','list');
    }

}
