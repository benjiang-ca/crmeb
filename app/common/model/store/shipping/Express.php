<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/13
 */
namespace app\common\model\store\shipping;

use app\common\model\BaseModel;

class Express extends BaseModel
{
    /**
     * @Author:Qinii
     * @return string
     */
    public  static function tablePk():string
    {
        return 'id';
    }

    /**
     * @Author:Qinii
     * @return string
     */
    public static function tableName():string
    {
        return 'express';
    }
}