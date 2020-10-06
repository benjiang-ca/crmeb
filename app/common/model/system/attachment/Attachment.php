<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-03-24
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\system\attachment;

use app\common\model\BaseModel;

class Attachment extends BaseModel
{
    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tablePk(): string
    {
        return 'attachment_id';
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tableName(): string
    {
        return 'system_attachment';
    }

    public function parent()
    {
        return $this->hasOne(self::class, 'config_classify_id', 'pid');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'pid', 'config_classify_id');
    }
}
