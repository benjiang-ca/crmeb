<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-07-30
 *
 * Copyright (c) http://crmeb.net
 */
namespace app\common\repositories\store;

use app\common\dao\store\ExcelDao;
use app\common\repositories\BaseRepository;
use app\common\repositories\system\admin\AdminRepository;
use app\common\repositories\system\merchant\MerchantAdminRepository;
use crmeb\services\ExcelService;
use think\facade\Db;
use think\facade\Queue;
use crmeb\jobs\SpreadsheetExcelJob;

class ExcelRepository extends BaseRepository
{
    /**
     * @var ExcelDao
     */
    protected $dao;


    /**
     * StoreAttrTemplateRepository constructor.
     * @param ExcelDao $dao
     */
    public function __construct(ExcelDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * TODO
     * @param array $where
     * @param int $admin_id
     * @param string $type
     * @author Qinii
     * @day 2020-07-30
     */
    public function create(array $where ,int $admin_id, string $type,int $merId)
    {
        //app()->make(ExcelService::class)->order($where,1);
        $excel = $this->dao->create([
            'mer_id'    => $merId,
            'admin_id'  => $admin_id,
            'type'      => $type
        ]);
        $data = ['where' => $where,'type' => $type,'excel_id' => $excel->excel_id];
        Queue::push(SpreadsheetExcelJob::class,$data);
    }

    /**
     * TODO
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @author Qinii
     * @day 2020-07-30
     */
    public function getList(array $where,int $page, int $limit)
    {
        $mer_make = app()->make(MerchantAdminRepository::class);
        $sys_make = app()->make(AdminRepository::class);
        $query = $this->dao->search($where);
        $count = $query->count();
        $list = $query->page($page,$limit)->hidden(['path'])->select()
            ->each(function($item) use ($mer_make,$sys_make){
                if($item['mer_id']){
                    $admin = $mer_make->get($item['admin_id']);
                }else{
                    $admin = $sys_make->get($item['admin_id']);
                }
                $real_name = $admin['real_name'];
                return $item['real_name'] = $real_name;
            });

        return compact('count','list');
    }

    /**
     * TODO 删除文件
     * @param int $id
     * @param string $path
     * @author Qinii
     * @day 2020-08-15
     */
    public function del(int $id,?string $path)
    {
        Db::transaction(function()use($id,$path){
            $this->dao->delete($id);
            if(!is_null($path)){
                $path = app()->getRootPath().'public'.$path;
                if(file_exists($path))unlink($path);
            }
        });
    }
}

