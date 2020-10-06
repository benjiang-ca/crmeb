<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-20
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\article;


use app\common\model\BaseModel;

class Article extends BaseModel
{

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tablePk(): string
    {
        return 'article_id';
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tableName(): string
    {
        return 'article';
    }

    /**
     * @return \think\model\relation\HasOne
     * @author Qinii
     */
    public function content()
    {
        return $this->hasOne(ArticleContent::class,'article_content_id','article_id');
    }

    /**
     * @return \think\model\relation\HasOne
     * @author Qinii
     */
    public function articleCategory()
    {
        return $this->hasOne(ArticleCategory::class ,'article_category_id','cid')
            ->field('article_category_id,title');
    }
}
