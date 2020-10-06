<?php
/**
 * User: Qinii
 * Date: 2020-06-05
 * Time: 09:41
 */
namespace app\controller\api\article;

use think\App;
use app\common\repositories\article\ArticleCategoryRepository as repository;
use crmeb\basic\BaseController;

class ArticleCategory extends BaseController
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
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author Qinii
     */
    public function lst()
    {
        return app('json')->success($this->repository->apiGetArticleCategory());
    }
}
