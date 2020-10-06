<?php
/**
 *
 * User: Qinii
 * Date: 2020-04-27
 * Time: 11:33
 */
namespace app\controller\admin\store;

use think\App;
use crmeb\basic\BaseController;
use app\validate\admin\StoreBrandValidate as validate;
use app\common\repositories\store\StoreBrandRepository as repository;

class StoreBrand extends BaseController
{

    protected $repository;

    /**
     * ArticleCategory constructor.
     * @param App $app
     * @param StoreBrandCategoryRepository $repository
     */
    public function __construct(App $app, repository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
    }

    /**
     * 列表
     * @return mixed
     * @author Qinii
     */
    public function lst()
    {
        [$page , $limit] = $this->getPage();
        $where = $this->request->params(['brand_category_id','brand_name']);

        return app('json')->success($this->repository->getList($where, $page, $limit));
    }

    public function create(validate $validate)
    {
        $data = $this->checkParams($validate);
        if (!$this->repository->parentExists($data['brand_category_id']))
            return app('json')->fail('上级分类不存在');
        $this->repository->create($data);
        return app('json')->success('添加成功');
    }

    public function update($id,validate $validate)
    {
        $data = $this->checkParams($validate);
        if(!$this->repository->meExists($id))
            return app('json')->fail('数据不存在');
        if (!$this->repository->parentExists($data['brand_category_id']))
            return app('json')->fail('上级分类不存在');
        $this->repository->update($id,$data);
        return app('json')->success('编辑成功');
    }


    public function delete($id)
    {
        if(!$this->repository->meExists($id))
            return app('json')->fail('数据不存在');
        $this->repository->delete($id);
        return app('json')->success('删除成功');
    }

    public function detail($id)
    {
        if (!$this->repository->meExists($id))
            return app('json')->fail('数据不存在');
        return app('json')->success($this->repository->get($id));
    }
    /**
     * 验证
     * @param  validate $validate
     * @param bool $isCreate
     * @return array
     * @author Qinii
     */
    public function checkParams(validate $validate)
    {
        $data = $this->request->params(['brand_category_id','brand_name','is_show','sort','pic']);
        $validate->check($data);
        return $data;
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/27
     * @return mixed
     */
    public function createForm()
    {
        return app('json')->success(formToData($this->repository->form()));
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/27
     * @param $id
     * @return mixed
     */
    public function updateForm($id)
    {
        if (!$this->repository->meExists($id))
            return app('json')->fail('数据不存在');
        return app('json')->success(formToData($this->repository->updateForm($id)));
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/27
     * @param int $id
     * @return mixed
     */
    public function switchStatus($id)
    {
        $status = $this->request->param('status', 0) == 1 ? 1 : 0;
        if (!$this->repository->meExists($id))
            return app('json')->fail('数据不存在');

        $this->repository->update($id, ['is_show' => $status]);
        return app('json')->success('修改成功');
    }
}
