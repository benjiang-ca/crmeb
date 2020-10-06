<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/30
 */
namespace app\controller\api\user;


use crmeb\basic\BaseController;
use app\common\repositories\user\UserRelationRepository as repository;
use think\App;

class UserRelation extends BaseController
{
    /**
     * @var repository
     */
    protected $repository;

    /**
     * UserRelation constructor.
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
    public function create()
    {
        $params = $this->request->params(['type_id','type']);

        if(!$params['type_id'] || !$params['type'])
            return app('json')->fail('参数丢失');
        if(!$this->repository->fieldExists($params))
            return app('json')->fail('信息丢失');
        if($this->repository->getUserRelation($params,$this->request->uid()))
            return app('json')->fail('您已经关注过了');
        $params['uid'] = $this->request->uid();
        $this->repository->create($params);
        return app('json')->success('关注成功');
    }

    /**
     * @return mixed
     * @author Qinii
     */
    public function productList()
    {
        [$page, $limit] = $this->getPage();
        $where = ['uid'=>$this->request->uid(),'type'=>1];
        return app('json')->success($this->repository->search($where, $page,$limit));
    }

    /**
     * @return mixed
     * @author Qinii
     */
    public function merchantList()
    {
        [$page, $limit] = $this->getPage();
        $where = ['uid'=>$this->request->uid(),'type'=>10];
        return app('json')->success($this->repository->search($where, $page,$limit));
    }

    /**
     * @return mixed
     * @author Qinii
     */
    public function delete()
    {
        $params = $this->request->params(['type_id','type']);
        if(!$this->repository->getUserRelation($params,$this->request->uid()))
            return app('json')->fail('信息不存在');
        $params['uid'] = $this->request->uid();
        $this->repository->destory($params);
        return app('json')->success('已取消收藏');
    }

    /**
     * @return mixed
     * @author Qinii
     */
    public function batchCreate()
    {
        $params = $this->request->params(['type_id','type']);
        if(!count($params['type_id']) || !$params['type'])
            return app('json')->fail('缺少必备参数');
        $this->repository->batchCreate($this->request->uid(),$params);
        return app('json')->success('收藏成功');
    }
}
