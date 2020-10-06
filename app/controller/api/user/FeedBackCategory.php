<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-06-09
 *
 * Copyright (c) http://crmeb.net
 */
namespace app\controller\api\user;

use think\App;
use crmeb\basic\BaseController;
use app\common\repositories\user\FeedBackCategoryRepository as repository;

class FeedBackCategory extends BaseController
{

    protected $repository;

    public function __construct(App $app, repository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
    }

    public function lst()
    {
        return app('json')->success($this->repository->getFormatList(0,1));
    }
}
