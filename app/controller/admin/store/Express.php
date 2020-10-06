<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/13
 */
namespace app\controller\admin\store;

use think\App;
use crmeb\basic\BaseController;
use app\common\repositories\store\shipping\ExpressRepository as repository;

class Express extends BaseController
{
    /**
     * @var repository
     */
    protected $repository;

    /**
     * City constructor.
     * @param App $app
     * @param repository $repository
     */
    public function __construct(App $app, repository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/13
     * @return mixed
     */
    public function lst()
    {
        [$page , $limit] = $this->getPage();
        $where = $this->request->params(['name','code']);
        return app('json')->success($this->repository->search($where, $page, $limit));
    }

    public function detail($id)
    {
        return app('json')->success($this->repository->get($id));
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/13
     * @return mixed
     */
    public function create()
    {
        $data = $this->request->params(['name','code','is_show','sort']);
        if(empty($data['name']))
            return app('json')->fail('名称不可为空');
        if($this->repository->codeExists($data['code'],null))
            return app('json')->fail('编码重复');
        if($this->repository->nameExists($data['name'],null))
            return app('json')->fail('名称重复');
        $this->repository->create($data);
        return app('json')->success('添加成功');
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/13
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $data = $this->request->params(['name','code','is_show','sort']);
        if(!$this->repository->fieldExists($id))
            return app('json')->fail('数据不存在');
        if(empty($data['name']))
            return app('json')->fail('名称不可为空');
        if($this->repository->codeExists($data['code'],$id))
            return app('json')->fail('编码重复');
        if($this->repository->nameExists($data['name'],$id))
            return app('json')->fail('名称重复');

        $this->repository->update($id,$data);
        return app('json')->success('编辑成功');
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/13
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        if(!$this->repository->fieldExists($id))
            return app('json')->fail('数据不存在');

        $this->repository->delete($id);
        return app('json')->success('删除成功');

    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/22
     * @return mixed
     */
    public function createForm()
    {
        return app('json')->success(formToData($this->repository->form($this->request->merId())));
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/22
     * @param $id
     * @return mixed
     */
    public function updateForm($id)
    {
        if(!$this->repository->fieldExists($id))
            return app('json')->fail('数据不存在');
        return app('json')->success(formToData($this->repository->updateForm($this->request->merId(),$id)));
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/22
     * @param int $id
     * @return mixed
     */
    public function switchStatus($id)
    {
        $status = $this->request->param('is_show', 0) == 1 ? 1 : 0;
        if(!$this->repository->fieldExists($id))
            return app('json')->fail('数据不存在');

        $this->repository->switchStatus($id, ['is_show' =>$status]);
        return app('json')->success('修改成功');
    }
}
