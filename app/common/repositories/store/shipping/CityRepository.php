<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/6
 */
namespace app\common\repositories\store\shipping;

use app\common\repositories\BaseRepository;
use app\common\dao\store\shipping\CityDao as dao;

class CityRepository extends BaseRepository
{

    public function __construct(dao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/8
     * @return array
     */
    public function getFormatList( array $where)
    {
        return  formatCategory($this->dao->getAll($where)->toArray(), 'city_id','parent_id');
    }


}
