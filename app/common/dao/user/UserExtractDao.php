<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-06-16
 *
 * Copyright (c) http://crmeb.net
 */
namespace app\common\dao\user;

use app\common\dao\BaseDao;
use app\common\model\user\UserExtract as model;

class UserExtractDao extends  BaseDao
{
    protected function getModel(): string
    {
        return model::class;
    }

    public function search(array $where)
    {
        if(isset($where['wechat']) && $where['wechat'] != '') {
            $query = model::hasWhere('user',function ($query)use($where){
                $query->where('nickname',"%{$where['wechat']}%");
            });
        }else{
            $query = model::alias('UserExtract');
        }
       $query->when(isset($where['uid']) && $where['uid'] != '',function($query)use($where){
            $query->where('uid',$where['uid']);
        })->when(isset($where['extract_type']) && $where['extract_type'] != '',function($query)use($where){
            $query->where('extract_type',$where['extract_type']);
        })->when(isset($where['keyword']) && $where['keyword'] != '',function($query)use($where){
           $query->whereLike('UserExtract.real_name|UserExtract.uid|bank_code|alipay_code|wechat',"%{$where['keyword']}%");
        })->when(isset($where['status']) && $where['status'] != '',function($query)use($where){
            $query->where('UserExtract.status',$where['status']);
        })->when(isset($where['real_name']) && $where['real_name'] != '',function($query)use($where){
            $query->where('UserExtract.real_name','%'.$where['real_name'].'%');
        })->when(isset($where['date']) && $where['date'] != '',function($query)use($where){
            getModelTime($query, $where['date']);
        })->order('UserExtract.create_time DESC');

        return $query;
    }
}
