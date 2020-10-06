<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-13
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\controller\merchant\store\coupon;


use crmeb\basic\BaseController;
use app\common\repositories\store\coupon\StoreCouponRepository;
use app\common\repositories\store\coupon\StoreCouponUserRepository;
use app\common\repositories\user\UserRepository;
use app\validate\merchant\StoreCouponValidate;
use FormBuilder\Exception\FormBuilderException;
use think\App;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\exception\ValidateException;

/**
 * Class CouponIssue
 * @package app\controller\merchant\store\coupon
 * @author xaboy
 * @day 2020-05-13
 */
class Coupon extends BaseController
{
    /**
     * @var StoreCouponRepository
     */
    protected $repository;

    /**
     * CouponIssue constructor.
     * @param App $app
     * @param StoreCouponRepository $repository
     */
    public function __construct(App $app, StoreCouponRepository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
    }

    /**
     * @return mixed
     * @throws DbException
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-05-14
     */
    public function lst()
    {
        $where = $this->request->params(['is_full_give', 'status', 'is_give_subscribe', 'coupon_name']);
        [$page, $limit] = $this->getPage();
        return app('json')->success($this->repository->getList($this->request->merId(), $where, $page, $limit));
    }

    public function detail($id)
    {
        if (!$this->repository->merExists($this->request->merId(), $id))
            return app('json')->fail('数据不存在');
        $coupon = $this->repository->get($id)->append(['used_num', 'send_num']);
        return app('json')->success($coupon->toArray());
    }

    /**
     * @return mixed
     * @throws FormBuilderException
     * @author xaboy
     * @day 2020-05-13
     */
    public function createForm()
    {
        return app('json')->success(formToData($this->repository->form()));
    }

    /**
     * @param StoreCouponValidate $validate
     * @return mixed
     * @author xaboy
     * @day 2020/5/30
     */
    public function create(StoreCouponValidate $validate)
    {
        $merId = $this->request->merId();
        $data = $this->checkParams($validate);
        $data['mer_id'] = $merId;
        $this->repository->create($data);
        return app('json')->success('发布成功');
    }

    /**
     * @param StoreCouponValidate $validate
     * @return array
     * @author xaboy
     * @day 2020/5/20
     */
    public function checkParams(StoreCouponValidate $validate)
    {
        $data = $this->request->params(['title', 'coupon_price', 'use_min_price', 'coupon_type', 'coupon_time', ['use_start_time', []], 'sort', ['status', 0], 'type', ['product_id', []], ['range_date', ''], ['send_type', 0], ['full_reduction', 0], ['is_limited', 0], ['is_timeout', 0], ['total_count', ''], ['status', 0]]);
        $validate->check($data);
        if ($data['is_timeout']) {
            [$data['start_time'], $data['end_time']] = $data['range_date'];
        }
        if ($data['coupon_type']) {
            if (count(array_filter($data['use_start_time'])) != 2)
                throw new ValidateException('请选择有效期限');
            [$data['use_start_time'], $data['use_end_time']] = $data['use_start_time'];
        } else unset($data['use_start_time']);
        unset($data['range_date']);
        if ($data['is_limited'] == 0) $data['total_count'] = 0;
        return $data;
    }

    /**
     * @param $id
     * @return mixed
     * @throws DbException
     * @author xaboy
     * @day 2020-05-13
     */
    public function changeStatus($id)
    {
        $status = $this->request->param('status', 0) == 1 ? 1 : 0;
        if (!$this->repository->merExists($this->request->merId(), $id))
            return app('json')->fail('数据不存在');
        $this->repository->update($id, compact('status'));
        return app('json')->success('修改成功');
    }

    /**
     * @param $id
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws FormBuilderException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/5/26
     */
    public function cloneForm($id)
    {
        if (!$this->repository->merExists($this->request->merId(), $id))
            return app('json')->fail('数据不存在');
        return app('json')->success(formToData($this->repository->cloneCouponForm($id)));
    }

    /**
     * @param StoreCouponUserRepository $repository
     * @author xaboy
     * @day 2020/6/2
     */
    public function issue(StoreCouponUserRepository $repository)
    {
        [$page, $limit] = $this->getPage();
        $where = $this->request->params(['username', 'coupon', 'status','coupon_id']);
        $where = $this->request->params(['username', 'status', 'coupon_id']);
        $where['mer_id'] = $this->request->merId();
        return app('json')->success($repository->getList($where, $page, $limit));
    }


    /**
     * @return mixed
     * @author Qinii
     */
    public function select()
    {
        $where = $this->request->params(['coupon_name']);
        $where['status'] = 1;
        $where['send_type'] = 3;
        [$page, $limit] = $this->getPage();
        return app('json')->success($this->repository->getList($this->request->merId(), $where, $page, $limit));
    }

    /**
     * TODO
     * @param $id
     * @return mixed
     * @author Qinii
     * @day 2020-06-19
     */
    public function sendCoupon($id)
    {
        if (!$this->repository->merExists($this->request->merId(), $id))
            return app('json')->fail('数据不存在');
        $uid = $this->request->param('uid', []);
        if (!is_array($uid) || count($uid) < 0)
            return app('json')->fail('选择用户');
        app()->make(StoreCouponRepository::class)->sendCouponByUser($uid, $id);
        return app('json')->success('发送成功');
    }

    /**
     * @param $id
     * @return mixed
     * @throws DbException
     * @author xaboy
     * @day 2020/7/7
     */
    public function delete($id)
    {
        if (!$this->repository->merExists($this->request->merId(), $id))
            return app('json')->fail('数据不存在');
        $this->repository->delete($id);
        return app('json')->success('删除成功');
    }
}
