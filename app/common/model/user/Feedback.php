<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/5/28
 *
 * Copyright (c) http://crmeb.net
 */
namespace app\common\model\user;

use app\common\model\BaseModel;

class Feedback extends BaseModel
{

    public static function tablePk(): ?string
    {
        return 'feedback_id';
    }

    public static function tableName(): string
    {
        return 'feedback';
    }

    public function getImagesAttr($val)
    {
        return $val ? json_decode($val, true) : [];
    }

    public function setImagesAttr($val)
    {
        return json_encode($val ?: []);
    }

    public function user()
    {
        return $this->hasOne(User::class,'uid','uid');
    }

    public function type()
    {
        return $this->hasOne(FeedBackCategory::class,'feedback_category_id','type');
    }
}
