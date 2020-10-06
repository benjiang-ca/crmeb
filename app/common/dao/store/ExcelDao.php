<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-07-30
 *
 * Copyright (c) http://crmeb.net
 */
namespace app\common\dao\store;

use app\common\model\store\Excel;
use app\common\dao\BaseDao;

class ExcelDao extends BaseDao
{

    /**
     * TODO
     * @return string
     * @author Qinii
     * @day 2020-07-30
     */
    protected function getModel(): string
    {
        return Excel::class;
    }


    public function search(array $where)
    {
        $query = $this->getModel()::getDB()
            ->when(isset($where['type']) && $where['type'] !== '',function($query) use($where){
                $query->where('type',$where['type']);
            })
            ->when(isset($where['mer_id']) && $where['mer_id'] !== '',function($query) use($where){
                $query->where('mer_id',$where['mer_id']);
            })
            ->when(isset($where['admin_id']) && $where['admin_id'] !== '',function($query) use($where){
                $query->where('admin_id',$where['admin_id']);
            });
        $query->order('create_time DESC');
        return $query;
    }

    /**
     * TODO 获取小于某个时间的文件
     * @param $time
     * @return mixed
     * @author Qinii
     * @day 2020-08-15
     */
    public function getDelByTime($time)
    {
        return $this->getModel()::getDB()->whereTime('create_time','<',$time)->column('path','excel_id');
    }
}
