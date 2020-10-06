<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-03-24
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\controller\admin\system\config;


use crmeb\basic\BaseController;
use app\common\repositories\system\config\ConfigClassifyRepository;
use app\common\repositories\system\config\ConfigRepository;
use app\validate\admin\ConfigValidate;
use crmeb\services\UploadService;
use FormBuilder\Exception\FormBuilderException;
use think\App;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * Class Config
 * @package app\controller\admin\system\config
 * @author xaboy
 * @day 2020-03-27
 */
class Config extends BaseController
{
    /**
     * @var ConfigRepository
     */
    protected $repository;

    /**
     * Config constructor.
     * @param App $app
     * @param ConfigRepository $repository
     */
    public function __construct(App $app, ConfigRepository $repository)
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
     * @day 2020-03-31
     */
    public function lst()
    {
        $where = $this->request->params(['keyword']);
        [$page, $limit] = $this->getPage();
        $lst = $this->repository->lst($where, $page, $limit);

        return app('json')->success($lst);
    }

    /**
     * @return mixed
     * @throws FormBuilderException
     * @author xaboy
     * @day 2020-03-31
     */
    public function createTable()
    {
        $form = $this->repository->form();
        return app('json')->success(formToData($form));
    }

    /**
     * @param int $id
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws FormBuilderException
     * @author xaboy
     * @day 2020-03-31
     */
    public function updateTable($id)
    {
        if (!$this->repository->exists($id)) app('json')->fail('数据不存在');
        $form = $this->repository->updateForm($id);
        return app('json')->success(formToData($form));
    }

    /**
     * @param int $id
     * @return mixed
     * @throws DbException
     * @author xaboy
     * @day 2020-03-31
     */
    public function switchStatus($id)
    {
        $status = $this->request->param('status', 0);
        if (!$this->repository->exists($id))
            return app('json')->fail('分类不存在');
        $this->repository->switchStatus($id, $status == 1 ? 1 : 0);
        return app('json')->success('修改成功');
    }

    /**
     * @param int $id
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-03-27
     */
    public function get($id)
    {
        $data = $this->repository->get($id);
        if (!$data)
            return app('json')->fail('配置不存在');
        else
            return app('json')->success($data->hidden(['mer_id', 'value']));
    }

    /**
     * @param ConfigValidate $validate
     * @param ConfigClassifyRepository $configClassifyRepository
     * @return mixed
     * @author xaboy
     * @day 2020-03-27
     */
    public function create(ConfigValidate $validate, ConfigClassifyRepository $configClassifyRepository)
    {
        $data = $this->request->params(['user_type', 'config_classify_id', 'config_name', 'config_key', 'config_type', 'config_rule', 'required', 'info', 'sort', 'status']);
        $validate->check($data);
        if (!$configClassifyRepository->exists($data['config_classify_id']))
            return app('json')->fail('配置分类不已存在');
        if ($this->repository->keyExists($data['config_key']))
            return app('json')->fail('配置key已存在');

        $this->repository->create($data);
        return app('json')->success('添加成功');
    }

    /**
     * @param int $id
     * @param ConfigValidate $validate
     * @param ConfigClassifyRepository $configClassifyRepository
     * @return mixed
     * @throws DbException
     * @author xaboy
     * @day 2020-03-27
     */
    public function update($id, ConfigValidate $validate, ConfigClassifyRepository $configClassifyRepository)
    {
        $data = $this->request->params(['user_type', 'config_classify_id', 'config_name', 'config_key', 'config_type', 'config_rule', 'required', 'info', 'sort', 'status']);
        $validate->check($data);

        if (!$this->repository->exists($id))
            return app('json')->fail('分类不存在');
        if (!$configClassifyRepository->exists($data['config_classify_id']))
            return app('json')->fail('配置分类不已存在');
        if ($this->repository->keyExists($data['config_key'], $id))
            return app('json')->fail('配置key已存在');
        $this->repository->update($id, $data);
        return app('json')->success('修改成功');
    }

    /**
     * @param int $id
     * @return mixed
     * @throws DbException
     * @author xaboy
     * @day 2020-03-27
     */
    public function delete($id)
    {
        $this->repository->delete($id);
        return app('json')->success('删除成功');
    }

    /**
     * @param string $key
     * @param ConfigClassifyRepository $configClassifyRepository
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws FormBuilderException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-04-22
     */
    public function form($key, ConfigClassifyRepository $configClassifyRepository)
    {
        if (!$configClassifyRepository->keyExists($key) || !($configClassfiy = $configClassifyRepository->keyByData($key)))
            return app('json')->fail('配置分类不存在');
        $form = $this->repository->cidByFormRule($configClassfiy, $this->request->merId());
        return app('json')->success(formToData($form));
    }

    public function upload($field)
    {
        $file = $this->request->file($field);
        if (!$file)
            return app('json')->fail('请上传附件');
        $upload = UploadService::create(1);
        $data = $upload->to('attach')->validate()->move($field);
        if ($data === false) {
            return app('json')->fail($upload->getError());
        }
        $res = $upload->getUploadInfo();
        $res['dir'] = path_to_url($res['dir']);;
        return app('json')->success(['src' => $res['dir']]);
    }

}
