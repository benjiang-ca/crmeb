<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-28
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\wechat;


use app\common\model\BaseModel;

class WechatUser extends BaseModel
{

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tablePk(): string
    {
        return 'wechat_user_id';
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tableName(): string
    {
        return 'wechat_user';
    }
}