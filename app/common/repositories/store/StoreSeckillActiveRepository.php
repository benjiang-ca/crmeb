<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-08-03
 *
 * Copyright (c) http://crmeb.net
 */
namespace app\common\repositories\store;

use app\common\dao\store\StoreSeckillActiveDao;
use app\common\repositories\BaseRepository;
use FormBuilder\Factory\Elm;
use think\facade\Route;

class StoreSeckillActiveRepository extends BaseRepository
{

    /**
     * @var StoreSeckillActiveDao
     */
    protected $dao;

    /**
     * StoreSeckillTimeRepository constructor.
     * @param StoreSeckillActiveDao $dao
     */
    public function __construct(StoreSeckillActiveDao $dao)
    {
        $this->dao = $dao;
    }
}
