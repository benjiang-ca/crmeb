<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-07
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\controller\admin\user;


use crmeb\basic\BaseController;
use app\common\repositories\user\UserLabelRepository;
use app\validate\admin\UserLabelValidate;
use FormBuilder\Exception\FormBuilderException;
use think\App;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * Class UserLabel
 * @package app\controller\admin\user
 * @author xaboy
 * @day 2020-05-07
 */
class UserLabel extends BaseController
{
    /**
     * @var UserLabelRepository
     */
    protected $repository;

    /**
     * UserGroup constructor.
     * @param App $app
     * @param UserLabelRepository $repository
     */
    public function __construct(App $app, UserLabelRepository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
    }

    /**
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-05-07
     */
    public function lst()
    {
        [$page, $limit] = $this->getPage();
        return app('json')->success($this->repository->getList([], $page, $limit));
    }

    /**
     * @return mixed
     * @throws FormBuilderException
     * @author xaboy
     * @day 2020-05-07
     */
    public function createForm()
    {
        return app('json')->success(formToData($this->repository->form()));
    }

    /**
     * @param UserLabelValidate $validate
     * @return mixed
     * @author xaboy
     * @day 2020-05-07
     */
    public function create(UserLabelValidate $validate)
    {
        $data = $this->checkParams($validate);
        $this->repository->create($data);

        return app('json')->success('添加成功');
    }

    /**
     * @param $id
     * @return mixed
     * @throws DbException
     * @throws FormBuilderException
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-05-07
     */
    public function updateForm($id)
    {
        if (!$this->repository->exists($id))
            return app('json')->fail('数据不存在');
        return app('json')->success(formToData($this->repository->updateForm($id)));
    }

    /**
     * @param $id
     * @param UserLabelValidate $validate
     * @return mixed
     * @throws DbException
     * @author xaboy
     * @day 2020-05-07
     */
    public function update($id, UserLabelValidate $validate)
    {
        $data = $this->checkParams($validate);
        if (!$this->repository->exists($id))
            return app('json')->fail('数据不存在');
        $this->repository->update($id, $data);

        return app('json')->success('编辑成功');
    }

    /**
     * @param $id
     * @return mixed
     * @throws DbException
     * @author xaboy
     * @day 2020-05-07
     */
    public function delete($id)
    {
        if (!$this->repository->exists($id))
            return app('json')->fail('数据不存在');
        $this->repository->delete($id);

        return app('json')->success('删除成功');
    }

    /**
     * @param UserLabelValidate $validate
     * @return array
     * @author xaboy
     * @day 2020-05-07
     */
    protected function checkParams(UserLabelValidate $validate)
    {
        $data = $this->request->params(['label_name']);
        $validate->check($data);
        return $data;
    }
}