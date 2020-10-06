<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-06-08
 *
 * Copyright (c) http://crmeb.net
 */
namespace app\common\model\user;

use app\common\model\BaseModel;

class FeedBackCategory extends BaseModel
{

    public static function tablePk(): ?string
    {
        return 'feedback_category_id';
    }

    public static function tableName(): string
    {
        return 'feedback_category';
    }

}
