<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/18
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\controller\api\user;


use crmeb\basic\BaseController;
use app\common\repositories\store\order\StoreOrderRepository;
use app\common\repositories\user\UserBillRepository;
use app\common\repositories\user\UserRepository;
use app\common\repositories\user\UserVisitRepository;
use crmeb\services\YunxinSmsService;
use think\App;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Cache;

class User extends BaseController
{
    public $repository;

    public function __construct(App $app, UserRepository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
    }

    /**
     * @return mixed
     * @author xaboy
     * @day 2020/6/22
     */
    public function spread_image()
    {
        $type = $this->request->param('type');
        $res = $type == 'routine'
            ? $this->repository->routineSpreadImage($this->request->userInfo())
            : $this->repository->wxSpreadImage($this->request->userInfo());
        return app('json')->success($res);
    }

    public function spread_info()
    {
        $user = $this->request->userInfo();
        $user->append(['one_level_count', 'lock_brokerage', 'two_level_count', 'spread_total', 'yesterday_brokerage', 'total_extract', 'total_brokerage', 'total_brokerage_price']);
        $data = [
            'total_brokerage_price' => $user->total_brokerage_price,
            'lock_brokerage' => $user->lock_brokerage,
            'one_level_count' => $user->one_level_count,
            'two_level_count' => $user->two_level_count,
            'spread_total' => $user->spread_total,
            'yesterday_brokerage' => $user->yesterday_brokerage,
            'total_extract' => $user->total_extract,
            'total_brokerage' => $user->total_brokerage,
            'brokerage_price' => $user->brokerage_price,
            'now_money' => $user->now_money,
            'broken_day' => (int)systemConfig('lock_brokerage_timer'),
            'user_extract_min' => (int)systemConfig('user_extract_min'),
        ];
        return app('json')->success($data);
    }

    /**
     * @param UserBillRepository $billRepository
     * @return mixed
     * @author xaboy
     * @day 2020/6/22
     */
    public function bill(UserBillRepository $billRepository)
    {
        [$page, $limit] = $this->getPage();
        return app('json')->success($billRepository->userList([
            'now_money' => $this->request->param('type', 0),
            'status' => 1,
        ], $this->request->uid(), $page, $limit));
    }

    /**
     * @param UserBillRepository $billRepository
     * @return mixed
     * @author xaboy
     * @day 2020/6/22
     */
    public function brokerage_list(UserBillRepository $billRepository)
    {
        [$page, $limit] = $this->getPage();
        return app('json')->success($billRepository->userList([
            'category' => 'brokerage',
        ], $this->request->uid(), $page, $limit));
    }

    /**
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/6/22
     */
    public function spread_order()
    {
        [$page, $limit] = $this->getPage();
        return app('json')->success($this->repository->subOrder($this->request->uid(), $page, $limit));
    }

    /**
     * TODO
     * @return mixed
     * @author Qinii
     * @day 2020-06-18
     */
    public function binding()
    {
        $data = $this->request->params(['phone', 'sms_code']);
        if (!$data['sms_code'] || !(YunxinSmsService::create())->checkSmsCode($data['phone'], $data['sms_code'],'binding')) return app('json')->fail('验证码不正确');
        $user = $this->repository->accountByUser($data['phone']);
        if ($user) {
            $data = ['phone' => $data['phone']];
        } else {
            $data = ['account' => $data['phone'], 'phone' => $data['phone']];
        }
        $this->repository->update($this->request->uid(), $data);
        return app('json')->success('绑定成功');
    }

    /**
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/6/22
     */
    public function spread_list()
    {
        [$level, $sort, $nickname] = $this->request->params(['level', 'sort', 'keyword'], true);
        $uid = $this->request->uid();
        [$page, $limit] = $this->getPage();
        return app('json')->success($level == 2
            ? $this->repository->getTwoLevelList($uid, $nickname, $sort, $page, $limit)
            : $this->repository->getOneLevelList($uid, $nickname, $sort, $page, $limit));
    }

    /**
     * @return mixed
     * @author xaboy
     * @day 2020/6/22
     */
    public function spread_top()
    {
        [$page, $limit] = $this->getPage();
        $type = $this->request->param('type', 0);
        return app('json')->success($type == 1
            ? $this->repository->spreadMonthTop($page, $limit)
            : $this->repository->spreadWeekTop($page, $limit));
    }

    /**
     * @return mixed
     * @author xaboy
     * @day 2020/6/22
     */
    public function brokerage_top()
    {
        [$page, $limit] = $this->getPage();
        $type = $this->request->param('type', 0);
        return app('json')->success($type == 1
            ? $this->repository->brokerageMonthTop($page, $limit)
            : $this->repository->brokerageWeekTop($page, $limit));
    }

    public function history(UserVisitRepository $repository)
    {
        $uid = $this->request->uid();
        [$page, $limit] = $this->getPage();
        return app('json')->success($repository->getHistory($uid, $page, $limit));
    }

    public function deleteHistory($id, UserVisitRepository $repository)
    {
        $uid = $this->request->uid();

        if (!$repository->getWhereCount(['user_visit_id' => $id, 'uid' => $uid]))
            return app('json')->fail('数据不存在');
        $repository->delete($id);
        return app('json')->success('删除成功');
    }

    public function account()
    {
        $user = $this->request->userInfo();
        if (!$user->phone) return app('json')->fail('请绑定手机号');
        return app('json')->success($this->repository->selfUserList($user->phone));
    }

    public function switchUser()
    {
        $uid = (int)$this->request->param('uid');
        if (!$uid) return app('json')->fail('用户不存在');
        $userInfo = $this->request->userInfo();
        if (!$userInfo->phone) return app('json')->fail('请绑定手机号');
        $user = $this->repository->switchUser($userInfo, $uid);
        $tokenInfo = $this->repository->createToken($user);
        $this->repository->loginAfter($user);
        return app('json')->success($this->repository->returnToken($user, $tokenInfo));
    }
}
