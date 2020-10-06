<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-24
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\wechat;


use app\common\model\BaseModel;

/**
 * Class WechatReply
 * @package app\common\model\wechat
 * @author xaboy
 * @day 2020-04-24
 */
class WechatReply extends BaseModel
{

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tablePk(): string
    {
        return 'wechat_reply_id';
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tableName(): string
    {
        return 'wechat_reply';
    }

    /**
     * @param $val
     * @return mixed
     * @author xaboy
     * @day 2020-04-24
     */
    public function getDataAttr($val)
    {
        return json_decode($val, true);
    }

    /**
     * @param $val
     * @return false|string
     * @author xaboy
     * @day 2020-04-24
     */
    public function setDataAttr($val)
    {
        return json_encode($val);
    }
}