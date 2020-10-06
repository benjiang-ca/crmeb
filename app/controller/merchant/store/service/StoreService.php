<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/5/29
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\controller\merchant\store\service;


use crmeb\basic\BaseController;
use app\common\repositories\store\service\StoreServiceRepository;
use app\validate\merchant\StoreServiceValidate;
use FormBuilder\Exception\FormBuilderException;
use think\App;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use app\common\repositories\store\service\StoreServiceLogRepository;

/**
 * Class StoreService
 * @package app\controller\merchant\store\service
 * @author xaboy
 * @day 2020/5/29
 */
class StoreService extends BaseController
{
    /**
     * @var StoreServiceRepository
     */
    protected $repository;
    /**
     * @var StoreServiceLogRepository
     */
    protected $logRepository;

    /**
     * StoreService constructor.
     * @param App $app
     * @param StoreServiceRepository $repository
     */
    public function __construct(App $app, StoreServiceRepository $repository, StoreServiceLogRepository $logRepository)
    {
        parent::__construct($app);
        $this->repository = $repository;
        $this->logRepository = $logRepository;
    }

    /**
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/5/29
     */
    public function lst()
    {
        $where = $this->request->params(['keyword', 'status']);
        [$page, $limit] = $this->getPage();
        $where['mer_id'] = $this->request->merId();
        return app('json')->success($this->repository->getList($where, $page, $limit));
    }

    /**
     * @return mixed
     * @throws FormBuilderException
     * @author xaboy
     * @day 2020/5/29
     */
    public function createForm()
    {
        return app('json')->success(formToData($this->repository->form()));
    }

    /**
     * @param StoreServiceValidate $validate
     * @return mixed
     * @author xaboy
     * @day 2020/5/29
     */
    public function create(StoreServiceValidate $validate)
    {
        $data = $this->checkParams($validate);
        $data['mer_id'] = $this->request->merId();
        if ($this->repository->isBindService($data['uid']) || $this->repository->issetService($data['mer_id'], $data['uid']))
            return app('json')->fail('该用户已绑定客服');
        $this->repository->create($data);
        return app('json')->success('添加成功');
    }

    /**
     * @param StoreServiceValidate $validate
     * @return array
     * @author xaboy
     * @day 2020/5/29
     */
    public function checkParams(StoreServiceValidate $validate)
    {
        $data = $this->request->params([['uid', []], 'nickname', 'status', 'customer', 'is_verify', 'notify', 'avatar', 'phone', ['sort', 0]]);
        $validate->check($data);
        if (!$data['avatar']) $data['avatar'] = $data['uid']['src'];
        $data['uid'] = $data['uid']['id'];
        return $data;
    }

    /**
     * @param $id
     * @return mixed
     * @throws FormBuilderException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/5/29
     */
    public function updateForm($id)
    {
        if (!$this->repository->merExists($this->request->merId(), $id))
            return app('json')->fail('数据不存在');
        return app('json')->success(formToData($this->repository->updateForm($id)));
    }

    /**
     * @param $id
     * @param StoreServiceValidate $validate
     * @return mixed
     * @throws DbException
     * @author xaboy
     * @day 2020/5/29
     */
    public function update($id, StoreServiceValidate $validate)
    {
        $data = $this->checkParams($validate);
        if (!$this->repository->merExists($merId = $this->request->merId(), $id))
            return app('json')->fail('数据不存在');
        if ($this->repository->isBindService($data['uid'], $id) || $this->repository->issetService($merId, $data['uid'], $id))
            return app('json')->fail('该用户已绑定客服');
        $this->repository->update($id, $data);
        return app('json')->success('修改成功');
    }


    /**
     * @param int $id
     * @return mixed
     * @throws DbException
     * @author xaboy
     * @day 2020/5/29
     */
    public function changeStatus($id)
    {
        $status = $this->request->param('status');
        if (!$this->repository->merExists($this->request->merId(), $id))
            return app('json')->fail('数据不存在');
        $this->repository->update($id, ['status' => $status == 1 ? 1 : 0]);
        return app('json')->success('修改成功');
    }

    /**
     * @param $id
     * @return mixed
     * @throws DbException
     * @author xaboy
     * @day 2020/5/29
     */
    public function delete($id)
    {
        if (!$this->repository->merExists($this->request->merId(), $id))
            return app('json')->fail('数据不存在');
        $this->repository->delete($id);
        return app('json')->success('删除成功');
    }

    /**
     * TODO 客服的全部用户
     * @param $id
     * @return mixed
     * @author Qinii
     * @day 2020-06-18
     */
    public function serviceUserList($id)
    {
        if (!$this->repository->merExists($this->request->merId(), $id))
            return app('json')->fail('数据不存在');
        [$page, $limit] = $this->getPage();
        return app('json')->success($this->logRepository->getServiceUserList($id, $page, $limit));
    }


    /**
     * TODO 商户的全部用户列表
     * @return mixed
     * @author Qinii
     * @day 2020-06-19
     */
    public function merchantUserList()
    {
        [$page, $limit] = $this->getPage();
        return app('json')->success($this->logRepository->getMerchantUserList($this->request->merId(), $page, $limit));
    }

    /**
     * TODO 用户与客服聊天记录
     * @param $id
     * @param $uid
     * @return mixed
     * @author Qinii
     * @day 2020-06-19
     */
    public function getUserMsnByService($id, $uid)
    {
        [$page, $limit] = $this->getPage();
        if (!$this->repository->getWhereCount(['service_id' => $id, 'mer_id' => $this->request->merId()]))
            return app('json')->fail('客服不存在');
        return app('json')->success($this->logRepository->getUserMsn($uid, $page, $limit, $this->request->merId(), $id));
    }

    /**
     * TODO 用户与商户聊天记录
     * @param $id
     * @return mixed
     * @author Qinii
     * @day 2020-06-19
     */
    public function getUserMsnByMerchant($id)
    {
        [$page, $limit] = $this->getPage();
        return app('json')->success($this->logRepository->getUserMsn($id, $page, $limit, $this->request->merId()));
    }
}
