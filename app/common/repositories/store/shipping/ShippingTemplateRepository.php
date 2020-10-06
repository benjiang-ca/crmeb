<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/6
 * @Time: 14:24
 */
namespace app\common\repositories\store\shipping;

use app\common\repositories\BaseRepository;
use app\common\dao\store\shipping\ShippingTemplateDao as dao;
use app\common\repositories\store\product\ProductRepository;
use think\facade\Db;

class ShippingTemplateRepository extends BaseRepository
{

    /**
     * ShippingTemplateRepository constructor.
     * @param dao $dao
     */
    public function __construct(dao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/13
     * @param int $merId
     * @return mixed
     */
    public function getList(int $merId)
    {
        return $this->dao->getList($merId);
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/8
     * @param int $merId
     * @param $id
     * @return bool
     */
    public function merExists(int $merId,$id)
    {
        return $this->dao->merFieldExists($merId,$this->getPk(),$id);
    }


    public function merDefaultExists(int $merId,$id)
    {
        $where = ['mer_id' => $merId,'is_default' => 1, $this->dao->getPk() => $id];
        return $this->dao->getWhere($where) ? true : false;
    }

    public function getProductUse(int $merId ,int $id)
    {
        return app()->make(ProductRepository::class)->merTempExists($merId,$id);
    }

    /**
     * @param int $id
     * @return mixed
     * @author Qinii
     */
    public function getOne(int $id)
    {
        $with = ['free','region','undelives'];
        $result = $this->dao->getWhere([$this->dao->getPk() => $id],'*',$with);
        $result->append(['free.city_ids','region.city_ids','undelives.city_ids']);
        return $result;

    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/8
     * @param int $merId
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function search(int $merId,array $where, int $page, int $limit)
    {
        $query = $this->dao->search($merId, $where);
        $count = $query->count($this->dao->getPk());
        $list = $query->page($page, $limit)->select();
        return compact('count', 'list');
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/8
     * @param int $id
     * @param array $data
     */
    public function update(int $id,array $data)
    {
        Db::transaction(function()use ($id,$data) {
            $region = $data['region'];
            $free = $data['free'] ?? '';
            $undelives = $data['undelives']??'';

            unset($data['region'],$data['free'],$data['undelives'],$data['city_ids']);
            $this->dao->update($id, $data);
            (app()->make(ShippingTemplateRegionRepository::class))->batchRemove([], [$id]);
            (app()->make(ShippingTemplateFreeRepository::class))->batchRemove([], [$id]);
            (app()->make(ShippingTemplateUndeliveRepository::class))->batchRemove([], [$id]);

            if($data['appoint']) {
                $settlefree = $this->settleFree($free, $id);
                (app()->make(ShippingTemplateFreeRepository::class)->insertAll($settlefree));
            }
            $settleRegion = $this->settleRegion($region,$id);
            (app()->make(ShippingTemplateRegionRepository::class)->insertAll($settleRegion));
            if($data['undelivery']){
                $settleUndelives = $this->settleUndelives($undelives,$id);
                (app()->make(ShippingTemplateUndeliveRepository::class))->create($settleUndelives);
            }

        });
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/13
     * @param array $data
     */
    public function create(array $data)
    {
        Db::transaction(function()use ($data) {
            $region = $data['region'];
            $free = $data['free'] ?? '';
            $undelives = $data['undelives'] ?? '';
            unset($data['region'],$data['free'],$data['undelives'],$data['city_ids']);
            $temp = $this->dao->create($data);
            if($data['appoint']) {
                $settlefree = $this->settleFree($free, $temp['shipping_template_id']);
                (app()->make(ShippingTemplateFreeRepository::class)->insertAll($settlefree));
            }
            $settleRegion = $this->settleRegion($region, $temp['shipping_template_id']);
            (app()->make(ShippingTemplateRegionRepository::class)->insertAll($settleRegion));
            if($data['undelivery']){
                $settleUndelives = $this->settleUndelives($undelives,$temp['shipping_template_id']);
                (app()->make(ShippingTemplateUndeliveRepository::class))->create($settleUndelives);
            }

        });
    }


    /**
     * @param $data
     * @param $id
     * @return array
     * @author Qinii
     */
    public function settleFree($data,$id)
    {
        foreach ($data as $v){
            $city = '/'.implode('/',$v['city_id']).'/';
            $free[] = [
                'temp_id' => $id,
                'city_id' => $city,
                'number' => $v['number'],
                'price' => $v['price']
            ];
        }
        return $free;
    }


    /**
     * @param $data
     * @param $id
     * @return array
     * @author Qinii
     */
    public function settleUndelives($data,$id)
    {
        return ['temp_id' => $id, 'city_id' => $data['city_id']];
    }


    /**
     * @Author:Qinii
     * @Date: 2020/5/13
     * @param $data
     * @param $id
     * @return array
     */
    public function settleRegion($data,$id)
    {
        $result = [];
        foreach ($data as $k => $v){
            $result[] = [
                'city_id' => ($k > 0 ) ? '/'.implode('/',$v['city_id']).'/' : 0,
                'temp_id' => $id,
                'first' => $v['first'],
                'first_price'=> $v['first_price'],
                'continue' => $v['continue'],
                'continue_price'=> $v['continue_price'],
            ];
        }
        return $result;
    }

    /**
     * @param int $merId
     * @author Qinii
     */
    public function createDefault(int $merId)
    {
        $data = [
            "name" => "默认模板",
            "type" => 1,
            "appoint" => 0,
            "undelivery" => 0,
            'mer_id' => $merId,
            "region" => [[
                "first" => 0,
                "first_price" => 0,
                "continue" => 0,
                "continue_price" => 0,
                "city_id" => 0
              ]]
        ];
        return $this->create($data);
    }

}
