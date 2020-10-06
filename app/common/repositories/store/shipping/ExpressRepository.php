<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/6
 */
namespace app\common\repositories\store\shipping;

use app\common\repositories\BaseRepository;
use app\common\dao\store\shipping\ExpressDao as dao;
use FormBuilder\Factory\Elm;
use think\facade\Route;

/**
 * Class ExpressRepository
 * @package app\common\repositories\store\shipping
 * @day 2020/6/13
 * @mixin dao
 */
class ExpressRepository extends BaseRepository
{

    /**
     * ExpressRepository constructor.
     * @param dao $dao
     */
    public function __construct(dao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/13
     * @param string $value
     * @param string $name
     * @return bool
     */
    public function nameExists(string $value,?int $id)
    {
        return $this->dao->merFieldExists('name',$value,null,$id);
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/13
     * @param string $value
     * @param $code
     * @return bool
     */
    public function codeExists(string $value,?int $id)
    {
        return $this->dao->merFieldExists('code',$value,null,$id);
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/13
     * @param int $value
     * @return bool
     */
    public function fieldExists(int $value)
    {
        return $this->dao->merFieldExists($this->dao->getPk(),$value);
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/13
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function search(array $where,int $page, int $limit)
    {
        $query = $this->dao->search($where);
        $count = $query->count($this->dao->getPk());
        $list = $query->page($page, $limit)->select();
        return compact('count', 'list');
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/22
     * @param int $merId
     * @param int|null $id
     * @param array $formData
     * @return \FormBuilder\Form
     */
    public function form(int $merId, ?int $id = null, array $formData = [])
    {
        $form = Elm::createForm(is_null($id) ? Route::buildUrl('systemExpressCreate')->build() : Route::buildUrl('systemExpressUpdate', ['id' => $id])->build());
        $form->setRule([
            Elm::input('name', '快递公司名称')->required(),
            Elm::input('code', '快递公司编码')->required(),
            Elm::switches('is_show', '是否显示', 1)->activeValue(1)->inactiveValue(0)->inactiveText('关闭')->activeText('开启'),
            Elm::number('sort', '排序', 0),
        ]);

        return $form->setTitle(is_null($id) ? '添加快递公司' : '编辑快递公司')->formData($formData);
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/22
     * @param int|null $merId
     * @param $id
     * @return \FormBuilder\Form
     */
    public function updateForm(?int$merId,$id)
    {
        return $this->form($merId, $id, $this->dao->get($id, $merId)->toArray());
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/22
     * @param $id
     * @param $data
     * @return int
     */
    public function switchStatus($id,$data)
    {
        return $this->dao->update($id,$data);
    }

    public function options()
    {
        return $this->dao->selectWhere(['is_show' => 1], 'name label,id value')->toArray();
    }

    /**
     * @param $id
     * @return \FormBuilder\Form
     * @author Qinii
     */
    public function sendProductForm($id)
    {
        $form = Elm::createForm(Route::buildUrl('merchantStoreOrderDelivery',['id' => $id])->build());
        $form->setRule([
            Elm::radio('delivery_type', '发货类型', 1)
                ->setOptions([
                    ['value' => 1, 'label' => '发货'],
                    ['value' => 2, 'label' => '送货'],
                    ['value' => 3, 'label' => '虚拟'],
                ])->control([
                    [
                        'value' => 1,
                        'rule'=> [
                            Elm::select('delivery_name', '快递名称')->options(function () {
                                return $this->options();
                            }),
                            Elm::input('delivery_id', '快递单号')->required(),
                            ]
                    ],
                    [
                        'value' => 2,
                        'rule'=> [
                            Elm::input('delivery_name', '送货人姓名')->required(),
                            Elm::input('delivery_id', '手机号')->required(),
                            ]
                    ],
                    [
                        'value' => 3,
                        'rule'=> []
                    ],

                ]),
        ]);
        return $form->setTitle('添加发货信息');
    }
}
