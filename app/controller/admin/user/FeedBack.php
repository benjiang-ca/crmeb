<?php
/**
 *
 */
namespace app\controller\admin\user;


use crmeb\basic\BaseController;
use think\App;
use app\common\repositories\user\FeedbackRepository as repository;

class FeedBack extends BaseController
{
    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * User constructor.
     * @param App $app
     * @param  $repository
     */
    public function __construct(App $app, repository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
    }

    /**
     * @return mixed
     * @author Qinii
     */
    public function lst()
    {
        $where = $this->request->params(['keyword', 'type', 'status','realname',['is_del',0]]);
        [$page, $limit] = $this->getPage();
        return app('json')->success($this->repository->getList($where, $page, $limit));
    }


    /**
     * @param $id
     * @return mixed
     * @author Qinii
     */
    public function detail($id)
    {
        if (!$this->repository->fieldExists('feedback_id',$id))
            return app('json')->fail('数据不存在');
        $feedback = $this->repository->get($id)->toArray();
        [$feedback['category'], $feedback['type']] = explode('/', $feedback['type'], 2);
        return app('json')->success($feedback);
    }

    /**
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DbException
     * @author Qinii
     */
    public function reply($id)
    {
        if (!$this->repository->fieldExists('feedback_id',$id))
            return app('json')->fail('数据不存在');
        $data = $this->request->params(['reply', 'remake', ['status',0]]);
        $this->repository->update($id,$data);
        return app('json')->success('回复成功');
    }

    /**
     * @param $id
     * @return mixed
     * @author Qinii
     */
    public function delete($id)
    {
        if (!$this->repository->fieldExists('feedback_id',$id))
            return app('json')->fail('数据不存在');
        $this->repository->update($id,['is_del' => 1]);
        return app('json')->success('删除成功');
    }
}
