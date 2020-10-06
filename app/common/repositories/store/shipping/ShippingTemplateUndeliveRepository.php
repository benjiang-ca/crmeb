<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/6
 * @Time: 14:24
 */
namespace app\common\repositories\store\shipping;

use app\common\repositories\BaseRepository;
use app\common\dao\store\shipping\ShippingTemplateUndeliveryDao as dao;

class ShippingTemplateUndeliveRepository extends BaseRepository
{

    /**
     * ShippingTemplateUndeliveRepository constructor.
     * @param dao $dao
     */
    public function __construct(dao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/8
     * @param $merId
     * @param $id
     * @return bool
     */
    public function merExists($merId , $id)
    {
        $result = $this->dao->get($id);
        $make = app()->make(ShippingTemplateRepository::class);
        if ($result)
            return $make->merExists($merId,$result['temp_id']);
        return false;
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/8
     * @param $id
     * @param $data
     */
    public function update($id,$data)
    {
        if(isset($data['shipping_template_undelivery_id']) && $data['shipping_template_undelivery_id']){
            $data['city_id'] = implode('/',$data['city_id']);
            $this->dao->update($data['shipping_template_undelivery_id'],$data);
        }else{
            $data['temp_id'] = $id;
            $this->dao->create($data);
        }
    }


}