<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-20
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\repositories\article;


use app\common\dao\article\ArticleContentDao;
use app\common\repositories\BaseRepository;

class ArticleContentRepository extends BaseRepository
{
    public function __construct(ArticleContentDao $dao)
    {
        $this->dao = $dao;
    }
}