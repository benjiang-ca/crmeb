<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-18
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\repositories\system\sms;


use app\common\dao\system\sms\SmsRecordDao;
use app\common\repositories\BaseRepository;
use crmeb\services\YunxinSmsService;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * Class SmsRecordRepository
 * @package app\common\repositories\system\sms
 * @author xaboy
 * @day 2020-05-18
 * @mixin SmsRecordDao
 */
class SmsRecordRepository extends BaseRepository
{
    /**
     * @var SmsRecordDao
     */
    protected $dao;

    /**
     * 短信状态
     * @var array
     */
    protected static $resultcode = ['100' => '成功', '130' => '失败', '131' => '空号', '132' => '停机', '133' => '关机', '134' => '无状态'];


    /**
     * SmsRecordRepository constructor.
     * @param SmsRecordDao $dao
     */
    public function __construct(SmsRecordDao $dao)
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
     * @day 2020-05-18
     */
    public function getList(array $where, $page, $limit)
    {
        $query = $this->dao->search($where);
        $count = $query->count($this->dao->getPk());
        $list = $query->page($page, $limit)->select()->toArray();

        foreach ($list as $k => $item) {
            $list[$k]['_resultcode'] = self::$resultcode[$item['resultcode']] ?? '无状态';
        }
        return compact('list', 'count');
    }

}