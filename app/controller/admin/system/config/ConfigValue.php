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
use app\common\repositories\system\config\ConfigValueRepository;
use think\App;

/**
 * Class ConfigValue
 * @package app\controller\admin\system\config
 * @author xaboy
 * @day 2020-03-27
 */
class ConfigValue extends BaseController
{
    /**
     * @var ConfigClassifyRepository
     */
    private $repository;

    /**
     * ConfigValue constructor.
     * @param App $app
     * @param ConfigValueRepository $repository
     */
    public function __construct(App $app, ConfigValueRepository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
    }

    /**
     * @param string $key
     * @return mixed
     * @author xaboy
     * @day 2020-04-22
     */
    public function save($key)
    {
        $formData = $this->request->post();
        if (!count($formData)) return app('json')->fail('保存失败');

        /** @var ConfigClassifyRepository $make */
        $make = app()->make(ConfigClassifyRepository::class);
        if (!($cid = $make->keyById($key))) return app('json')->fail('保存失败');

        $this->repository->save($cid, $formData, $this->request->merId());

        return app('json')->success('保存成功');
    }
}
