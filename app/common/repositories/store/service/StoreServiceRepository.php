<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/5/29
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\repositories\store\service;


use app\common\dao\store\service\StoreServiceDao;
use app\common\repositories\BaseRepository;
use FormBuilder\Exception\FormBuilderException;
use FormBuilder\Factory\Elm;
use FormBuilder\Form;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Route;

/**
 * Class StoreServiceRepository
 * @package app\common\repositories\store\service
 * @author xaboy
 * @day 2020/5/29
 * @mixin StoreServiceDao
 */
class StoreServiceRepository extends BaseRepository
{
    /**
     * StoreServiceRepository constructor.
     * @param StoreServiceDao $dao
     */
    public function __construct(StoreServiceDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param array $where
     * @param $page
     * @param $limit
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/5/29
     */
    public function getList(array $where, $page, $limit)
    {
        $query = $this->dao->search($where)->with(['user' => function ($query) {
            $query->field('nickname,avatar,uid');
        }]);
        $count = $query->count();
        $list = $query->page($page, $limit)->select();
        return compact('count', 'list');
    }

    /**
     * @return Form
     * @throws FormBuilderException
     * @author xaboy
     * @day 2020/5/29
     */
    public function form()
    {
        return Elm::createForm(Route::buildUrl('merchantServiceCreate')->build(), [
            Elm::frameImage('uid', '用户', '/' . config('admin.merchant_prefix') . '/setting/userList?field=uid&type=1')->prop('srcKey', 'src')->width('675px')->height('500px')->modal(['modal' => false]),
            Elm::frameImage('avatar', '客服头像', '/' . config('admin.merchant_prefix') . '/setting/uploadPicture?field=avatar&type=1')->width('896px')->height('480px')->props(['footer' => false])->modal(['modal' => false]),
            Elm::input('nickname', '客服昵称')->required(),
            Elm::switches('status', '客服状态', 1)->activeValue(1)->inactiveValue(0)->inactiveText('关闭')->activeText('开启'),
            Elm::switches('notify', '订单通知', 1)->activeValue(1)->inactiveValue(0)->inactiveText('关闭')->activeText('开启')->control([
                [
                    'value' => 1,
                    'rule' => [
                        Elm::input('phone', '通知电话')
                    ]
                ]
            ]),
            Elm::switches('customer', '手机端订单管理', 1)->activeValue(1)->inactiveValue(0)->inactiveText('关闭')->activeText('开启'),
            Elm::number('sort', '排序', 0),
            Elm::switches('is_verify', '开启核销', 1)->activeValue(1)->inactiveValue(0)->inactiveText('关闭')->activeText('开启'),
        ])->setTitle('添加客服');
    }

    /**
     * @param $id
     * @return Form
     * @throws FormBuilderException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/5/29
     */
    public function updateForm($id)
    {
        $service = $this->dao->getWith($id, ['user' => function ($query) {
            $query->field('avatar,uid');
        }])->toArray();
        $service['uid'] = ['id' => $service['uid'], 'src' => $service['user']['avatar']];
        unset($service['user']);
        return $this->form()->formData($service)->setTitle('编辑表单')->setAction(Route::buildUrl('merchantServiceUpdate', compact('id'))->build());
    }

    /**
     * @param $merId
     * @param $uid
     * @return array|mixed|\think\Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/5/29
     */
    public function getChatService($merId, $uid)
    {
        $logRepository = app()->make(StoreServiceLogRepository::class);
        $lastServiceId = $logRepository->getLastServiceId($merId, $uid);
        $service = null;
        if ($lastServiceId)
            $service = $this->getValidServiceInfo($lastServiceId);
        if ($service) return $service;
        $service = $this->dao->getChatService($merId);
        if ($service) return $service;
    }
}
