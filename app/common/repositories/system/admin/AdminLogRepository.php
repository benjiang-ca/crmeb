<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-15
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\repositories\system\admin;


use app\common\dao\BaseDao;
use app\common\dao\system\admin\LogDao;
use app\common\repositories\BaseRepository;
use app\Request;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Model;

/**
 * Class AdminLogRepository
 * @package app\common\repositories\system\admin
 * @author xaboy
 * @day 2020-04-16
 */
class AdminLogRepository extends BaseRepository
{
    /**
     * AdminLogRepository constructor.
     * @param LogDao $dao
     */
    public function __construct(LogDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param $merId
     * @param array $where
     * @param $page
     * @param $limit
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-04-16
     */
    public function lst($merId, array $where, $page, $limit)
    {
        $query = $this->dao->search($where, $merId);
        $count = $query->count($this->dao->getPk());
        $list = $query->setOption('field', [])->field(['create_time', 'log_id', 'admin_name', 'route', 'method', 'url', 'ip', 'admin_id'])
            ->page($page, $limit)->order('create_time DESC')->select();
        return compact('count', 'list');
    }

    /**
     * @param Request $request
     * @param int $merId
     * @return BaseDao|Model
     * @author xaboy
     * @day 2020-04-15
     */
    public function addLog(Request $request, int $merId = 0)
    {
        return $this->create($merId, self::parse($request));
    }

    /**
     * @param int $merId
     * @param array $data
     * @return BaseDao|Model
     * @author xaboy
     * @day 2020-04-16
     */
    public function create(int $merId, array $data)
    {
        $data['mer_id'] = $merId;
        return $this->dao->create($data);
    }

    /**
     * @param Request $request
     * @return array
     * @author xaboy
     * @day 2020-04-16
     */
    public static function parse(Request $request)
    {
        return [
            'admin_id' => $request->adminId(),
            'admin_name' => $request->adminInfo()->real_name ?: '未定义',
            'route' => $request->rule()->getName(),
            'ip' => $request->ip(),
            'url' => $request->url(true),
            'method' => $request->method()
        ];
    }
}