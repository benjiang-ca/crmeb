<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/2
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\user;


use app\common\model\BaseModel;

/**
 * Class UserRecharge
 * @package app\common\model\user
 * @author xaboy
 * @day 2020/6/2
 */
class UserRecharge extends BaseModel
{

    /**
     * @return string|null
     * @author xaboy
     * @day 2020/6/2
     */
    public static function tablePk(): ?string
    {
        return 'recharge_id';
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020/6/2
     */
    public static function tableName(): string
    {
        return 'user_recharge';
    }

    public function user()
    {
        return $this->hasOne(User::class, 'uid', 'uid');
    }

}