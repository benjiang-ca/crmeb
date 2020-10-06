<?php
/**
 * User: Qinii
 * Date: 2020-06-05
 * Time: 09:41
 */
namespace app\controller\api\article;

use think\App;
use app\common\repositories\article\ArticleRepository as repository;
use crmeb\basic\BaseController;

class Article extends BaseController
{
    /**
     * @var repository
     */
    protected $repository;

    /**
     * StoreBrand constructor.
     * @param App $app
     * @param repository $repository
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
    public function lst($cid)
    {
        [$page, $limit] = $this->getPage();
        $where = ['status' => 1,'cid' => $cid];
        return app('json')->success($this->repository->search(0,$where, $page, $limit));
    }

    public function detail($id)
    {
        if (!$this->repository->merApiExists($id))
            return app('json')->fail('文章不存在');

        return app('json')->success($this->repository->get($id,0));
    }
}
