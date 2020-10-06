<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-07-18
 *
 * Copyright (c) http://crmeb.net
 */
namespace app\common\repositories\system\merchant;

use app\common\repositories\BaseRepository;
use FormBuilder\Factory\Elm;
use app\common\dao\system\merchant\MerchantIntentionDao;
use think\facade\Route;

class MerchantIntentionRepository extends BaseRepository
{

    public function __construct(MerchantIntentionDao $dao)
    {
        $this->dao = $dao;
    }

    public function getList(array $where,$page,$limit)
    {
        $query = $this->dao->search($where);
        $count = $query->count();
        $list = $query->page($page,$limit)->order('create_time DESC , status ASC')->select();

        return compact('count','list');
    }

    public function markForm($id)
    {
        $data = $this->dao->get($id);
        $form = Elm::createForm(Route::buildUrl('systemMerchantIntentionMark', ['id' => $id])->build());
        $form->setRule([
            Elm::textarea('mark', '备注',$data['remark'])->required(),
        ]);
        return $form->setTitle('修改备注');
    }

    public function statusForm($id)
    {
        $form = Elm::createForm(Route::buildUrl('systemMerchantIntentionStatus', ['id' => $id])->build());
        $form->setRule([
            Elm::select('status', '审核状态', 1)->options([
                ['value' => 1, 'label' => '同意'],
                ['value' => 2, 'label' => '拒绝'],
            ]),
        ]);
        return $form->setTitle('修改审核状态');
    }
}
