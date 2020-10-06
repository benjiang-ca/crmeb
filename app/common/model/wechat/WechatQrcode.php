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

/**
 * Class WechatQrcode
 * @package app\common\model\wechat
 * @author xaboy
 * @day 2020-04-28
 */
class WechatQrcode extends BaseModel
{

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tablePk(): string
    {
        return 'wechat_qrcode_id';
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tableName(): string
    {
        return 'wechat_qrcode';
    }

    /**
     * @return mixed
     * @author xaboy
     * @day 2020-04-28
     */
    public function incTicket()
    {
        return self::getDB()->where('wechat_qrcode_id', $this->wechat_qrcode_id)->inc('scan')->update();
    }
}