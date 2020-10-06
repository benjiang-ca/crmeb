<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-06-18
 *
 * Copyright (c) http://crmeb.net
 */
namespace app\common\repositories\wechat;

use app\common\dao\wechat\TemplateMessageDao;
use app\common\repositories\BaseRepository;
use FormBuilder\Factory\Elm;
use think\facade\Config;
use think\facade\Route;

/**
 * Class TemplateMessageRepository
 * @package app\common\repositories\wechat
 * @mixin TemplateMessageDao
 */
class TemplateMessageRepository extends BaseRepository
{

    /**
     * @var TemplateMessageDao
     */
    public $dao;

    /**
     * TemplateMessageRepository constructor.
     * @param TemplateMessageDao $dao
     */
    public function __construct(TemplateMessageDao $dao)
    {
        $this->dao = $dao;
    }


    public function getList($wereh,$page,$limit)
    {
        $query = $this->dao->search($wereh);
        $count = $query->count();
        $list = $query->page($page,$limit)->select();
        return compact('count','list');
    }

    /**
     * TODO
     * @param int|null $id
     * @param int $type
     * @return \FormBuilder\Form
     * @author Qinii
     * @day 2020-06-19
     */
    public function form(?int $id = null,$type = 0)
    {
        $form = Elm::createForm(Route::buildUrl('systemTemplateMessageCreate')->build());
        $form->setRule([
            Elm::hidden('type',$type),
            Elm::input('tempkey','模板编号'),
            Elm::input('name','模板名'),
            Elm::input('tempid','模板ID'),
            Elm::input('content','回复内容'),
            Elm::switches('status','状态',1)->activeValue(1)->inactiveValue(0)->inactiveText('关闭')->activeText('开启'),
        ]);
        return $form->setTitle(is_null($id) ? '添加' : '编辑');
    }

    /**
     * TODO
     * @param $id
     * @return \FormBuilder\Form
     * @author Qinii
     * @day 2020-06-19
     */
    public function updateForm($id)
    {
        $tem = $this->dao->get($id);
        $form = Elm::createForm(Route::buildUrl('systemTemplateMessageUpdate',['id' => $id])->build());
        $form->setRule([
            Elm::hidden('type',$tem['type']),
            Elm::input('tempkey','模板编号',$tem['tempkey'])->disabled(1),
            Elm::input('name','模板名',$tem['name'])->disabled(1),
            Elm::input('tempid','模板ID',$tem['tempid']),
            Elm::switches('status','状态',$tem['status'])->activeValue(1)->inactiveValue(0)->inactiveText('关闭')->activeText('开启'),
        ]);
        return $form->setTitle('编辑');
    }

    public function getSubscribe()
    {
        $res = [];
        $data = $this->dao->search(['type' => 0])->column('tempid','tempkey');
        $arr = Config::get('template.stores.subscribe.template_id');
        foreach ($arr as $k => $v){
            $res[$k] = $data[$v];
        }
        return $res;
    }
}
