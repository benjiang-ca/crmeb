<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-03-26
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\repositories\system\config;


use app\common\dao\system\config\SystemConfigDao;
use app\common\model\system\config\SystemConfigClassify;
use app\common\repositories\BaseRepository;
use app\common\repositories\store\product\ProductRepository;
use crmeb\jobs\CheckProductExtensionJob;
use FormBuilder\Exception\FormBuilderException;
use FormBuilder\Factory\Elm;
use FormBuilder\Form;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Db;
use think\facade\Queue;
use think\facade\Route;

/**
 * Class ConfigRepository
 * @package crmeb\repositories\system\config
 * @mixin SystemConfigDao
 */
class ConfigRepository extends BaseRepository
{
    const TYPES = ['input' => '文本框', 'number' => '数字框', 'textarea' => '多行文本框', 'radio' => '单选框', 'checkbox' => '多选框', 'select' => '下拉框', 'file' => '文件上传', 'image' => '图片上传', 'color' => '颜色选择框'];

    /**
     * ConfigRepository constructor.
     * @param SystemConfigDao $dao
     */
    public function __construct(SystemConfigDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param int $merId
     * @param SystemConfigClassify $configClassify
     * @param array $configs
     * @param array $formData
     * @return Form
     * @throws FormBuilderException
     * @author xaboy
     * @day 2020-04-23
     */
    public function formRule(int $merId, SystemConfigClassify $configClassify, array $configs, array $formData = [])
    {
        $components = $this->getRule($configs, $merId);

        $form = Elm::createForm(Route::buildUrl($merId ? 'merchantConfigSave' : 'configSave', ['key' => $configClassify->classify_key])->build(), $components);
        return $form->setTitle($configClassify->classify_name)->formData($formData);
    }

    public function getRule(array $configs, $merId)
    {
        $components = [];
        foreach ($configs as $config) {
            $component = $this->getComponent($config, $merId);
            $components[] = $component;
        }
        return $components;
    }

    public function getComponent($config, $merId)
    {
        if ($config['config_type'] == 'image')
            $component = Elm::frameImage($config['config_key'], $config['config_name'], '/' . config('admin.' . ($merId ? 'merchant' : 'admin') . '_prefix') . '/setting/uploadPicture?field=' . $config['config_key'] . '&type=1')->modal(['modal' => false])->width('896px')->height('480px')->props(['footer' => false]);
        else if ($config['config_type'] == 'file') {
            $component = Elm::uploadFile($config['config_key'], $config['config_name'], Route::buildUrl('configUpload', ['field' => 'file'])->build())->headers(['X-Token' => request()->token()]);
        } else if (in_array($config['config_type'], ['select', 'checkbox', 'radio'])) {
            $options = array_map(function ($val) {
                [$value, $label] = explode(':', $val, 2);
                return compact('value', 'label');
            }, explode("\n", $config['config_rule']));
            $component = Elm::{$config['config_type']}($config['config_key'], $config['config_name'])->options($options);
        } else
            $component = Elm::{$config['config_type']}($config['config_key'], $config['config_name']);
        if ($config['required']) $component->required();
        $component->info($config['info']);
        return $component;
    }

    /**
     * @param int $id
     * @param int $status
     * @return int
     * @throws DbException
     * @author xaboy
     * @day 2020-03-31
     */
    public function switchStatus(int $id, int $status)
    {
        return $this->dao->update($id, compact('status'));
    }

    /**
     * @param SystemConfigClassify $configClassify
     * @param int $merId
     * @return Form
     * @throws DataNotFoundException
     * @throws DbException
     * @throws FormBuilderException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-04-22
     */
    public function cidByFormRule(SystemConfigClassify $configClassify, int $merId)
    {
        $config = $this->dao->cidByConfig($configClassify->config_classify_id, $merId == 0 ? 0 : 1);
        $keys = $config->column('config_key');
        return $this->formRule($merId, $configClassify, $config->toArray(), app()->make(ConfigValueRepository::class)->more($keys, $merId));
    }

    /**
     * @param int|null $id
     * @param array $formData
     * @return Form
     * @throws FormBuilderException
     * @author xaboy
     * @day 2020-03-31
     */
    public function form(?int $id = null, array $formData = []): Form
    {
        $form = Elm::createForm(is_null($id) ? Route::buildUrl('configSettingCreate')->build() : Route::buildUrl('configSettingUpdate', ['id' => $id])->build());
        $form->setRule([
            Elm::cascader('config_classify_id', '上级分类')->options(function () {
                $configClassifyRepository = app()->make(ConfigClassifyRepository::class);
                return array_merge([['value' => 0, 'label' => '请选择']], $configClassifyRepository->options());
            })->props(['props' => ['checkStrictly' => true, 'emitPath' => false]]),
            Elm::select('user_type', '后台类型', 0)->options([
                ['label' => '总后台配置', 'value' => 0],
                ['label' => '商户后台配置', 'value' => 1],
            ])->requiredNum(),
            Elm::input('config_name', '配置名称')->required(),
            Elm::input('config_key', '配置key')->required(),
            Elm::input('info', '说明'),
            Elm::select('config_type', '配置类型')->options(function () {
                $options = [];
                foreach (self::TYPES as $value => $label) {
                    $options[] = compact('value', 'label');
                }
                return $options;
            })->required(),
            Elm::textarea('config_rule', '规则'),
            Elm::number('sort', '排序', 0),
            Elm::switches('required', '必填', 0)->activeValue(1)->inactiveValue(0)->inactiveText('关闭')->activeText('开启'),
            Elm::switches('status', '是否显示', 1)->activeValue(1)->inactiveValue(0)->inactiveText('关闭')->activeText('开启'),
        ]);

        return $form->setTitle(is_null($id) ? '添加配置' : '编辑配置')->formData($formData);
    }

    /**
     * @param int $id
     * @return Form
     * @throws DataNotFoundException
     * @throws DbException
     * @throws FormBuilderException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-03-31
     */
    public function updateForm(int $id)
    {
        return $this->form($id, $this->dao->get($id)->toArray());
    }

    /**
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-03-31
     */
    public function lst(array $where, int $page, int $limit)
    {
        $query = $this->dao->search($where);
        $count = $query->count();
        $list = $query->page($page, $limit)->withAttr('typeName', function ($value, $data) {
            return self::TYPES[$data['config_type']];
        })->hidden(['config_classify_id'])->append(['typeName'])->select();
        return compact('count', 'list');
    }

    public function uploadForm()
    {
        $config = $this->getWhere(['config_key' => 'upload_type']);
        $rule = $this->getComponent($config, 0)->value(systemConfig('upload_type'));
        $make = app()->make(ConfigClassifyRepository::class);
        $rule->control([
            [
                'value' => '2',
                'rule' => $this->cidByFormRule($make->keyByData('qiniuyun'), 0)->formRule()
            ],
            [
                'value' => '3',
                'rule' => $this->cidByFormRule($make->keyByData('aliyun_oss'), 0)->formRule()
            ],
            [
                'value' => '4',
                'rule' => $this->cidByFormRule($make->keyByData('tengxun'), 0)->formRule()
            ],
        ]);
        return Elm::createForm(Route::buildUrl('systemSaveUploadConfig')->build(), [$rule])->setTitle('上传配置');
    }

    public function saveUpload($data)
    {
        $configValueRepository = app()->make(ConfigValueRepository::class);
        $uploadType = $data['upload_type'] ?? '1';
        $key = '';
        if ($uploadType == 2) {
            $key = 'qiniuyun';
        } else if ($uploadType == 3) {
            $key = 'aliyun_oss';
        } else if ($uploadType == 4) {
            $key = 'tengxun';
        }

        Db::transaction(function () use ($data, $key, $uploadType, $configValueRepository) {
            $configValueRepository->setFormData([
                'upload_type' => $uploadType
            ], 0);
            if ($key) {
                $make = app()->make(ConfigClassifyRepository::class);
                if (!($cid = $make->keyById($key))) return app('json')->fail('保存失败');
                $configValueRepository->save($cid, $data, 0);
            }
        });
    }
}
