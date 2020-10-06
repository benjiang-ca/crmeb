<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy<xaboy2005@qq.com>
 * @day 2020/5/28
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\controller\api\user;

use crmeb\basic\BaseController;
use app\common\repositories\user\FeedbackRepository;
use app\validate\api\FeedbackValidate;
use think\App;

class Feedback extends BaseController
{
    protected $repository;

    public function __construct(App $app, FeedbackRepository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
    }

    /**
     * @param FeedbackValidate $validate
     * @param FeedbackRepository $repository
     * @return mixed
     * @author xaboy
     * @day 2020/5/28
     */
    public function feedback(FeedbackValidate $validate)
    {
        $data = $this->request->params(['type', 'content', ['images', []], 'realname', 'contact']);
        $validate->check($data);
        $data['uid'] = $this->request->uid();
        $this->repository->create($data);
        return app('json')->success('反馈成功');
    }

    /**
     * @return mixed
     * @author xaboy
     * @day 2020/5/28
     */
    public function feedbackList()
    {
        [$page, $limit] = $this->getPage();
        return app('json')->success($this->repository->getList(['uid' => $this->request->uid(),'is_del' => 0], $page, $limit));
    }

    public function detail($id)
    {
        if (!$this->repository->uidExists($id, $this->request->uid()))
            return app('json')->fail('数据不存在');
        $feedback = $this->repository->get($id);
        return app('json')->success($feedback);
    }
}
