<?php
/**
 * User: Qinii
 * Date: 2020-04-27
 * Time: 11:33
 */
namespace app\controller\admin\store;

use app\common\repositories\store\product\ProductAttrValueRepository;
use app\common\repositories\system\merchant\MerchantRepository;
use app\validate\merchant\StoreProductValidate;
use crmeb\jobs\CheckProductExtensionJob;
use think\App;
use crmeb\basic\BaseController;
use app\validate\merchant\StoreProductAdminValidate as validate;
use app\common\repositories\store\product\ProductRepository as repository;
use think\facade\Queue;

class StoreProduct extends BaseController
{


    /**
     * @var repository
     */
    protected $repository;


    /**
     * StoreProduct constructor.
     * @param App $app
     * @param repository $repository
     */
    public function __construct(App $app, repository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/18
     * @return mixed
     */
    public function lst()
    {
        [$page, $limit] = $this->getPage();
        $where = $this->request->params(['cate_id', 'keyword', ['type', 1], 'mer_cate_id', 'pid','order','store_name','is_trader']);
        $mer_id = $this->request->param('mer_id','');
        $merId = $mer_id ? $mer_id : null;
        $where['is_gift_bag'] = 0;
        $where = array_merge($where, $this->repository->switchType($where['type'], null,0));
        return app('json')->success($this->repository->getAdminList($merId, $where, $page, $limit));
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/18
     * @return mixed
     */
    public function bagList()
    {
        [$page, $limit] = $this->getPage();
        $where = $this->request->params(['cate_id','keyword',['type',1],'mer_cate_id' ,'is_trader']);
        $merId = $this->request->param('mer_id') ? $this->request->param('mer_id') : null;
        $where = array_merge($where,$this->repository->switchType($where['type'],$merId,3));
        return app('json')->success($this->repository->getAdminList($merId,$where, $page, $limit));
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/18
     * @return mixed
     */
    public function getStatusFilter()
    {
        return app('json')->success($this->repository->getFilter(null,'商品',0));
    }

    /**
     * TODO 礼包表头
     * @Author:Qinii
     * @Date: 2020/5/18
     * @return mixed
     */
    public function getBagStatusFilter()
    {
        return app('json')->success($this->repository->getFilter(null,'礼包',3));
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/18
     * @param $id
     * @return mixed
     */
    public function detail($id)
    {
        if(!$this->repository->merExists(null,$id))
            return app('json')->fail('数据不存在');
        return app('json')->success($this->repository->get($id));
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/11
     * @param $id
     * @param validate $validate
     * @return mixed
     */
    public function update($id,validate $validate)
    {
        $data = $this->checkParams($validate);
        $this->repository->adminUpdate($id,$data);
        return app('json')->success('编辑成功');
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/18
     * @param int $id
     * @return mixed
     */
    public function switchStatus()
    {
        $id = $this->request->param('id');
        $data = $this->request->params(['status','refusal']);
        if($data['status'] == -1 && empty($data['refusal']))
            return app('json')->fail('请填写拒绝理由');
        if(!is_array($id)) $id = [$id];
        $this->repository->switchStatus($id,$data);
        return app('json')->success('操作成功');
    }

    /**
     * TODO 是否隐藏
     * @param $id
     * @return mixed
     * @author Qinii
     * @day 2020-07-17
     */
    public function changeUsed($id)
    {
        if(!$this->repository->merExists(null,$id))
            return app('json')->fail('数据不存在');
        $status = $this->request->param('status',0) == 1 ? 1 : 0;
        $status = ['is_used' => $status];
        $this->repository->changeUsed($id,$status);
        return app('json')->success('修改成功');
    }


    /**
     * @Author:Qinii
     * @Date: 2020/5/11
     * @param validate $validate
     * @return array
     */
    public function checkParams(validate $validate)
    {
        $data = $this->request->params(['is_hot','is_best','is_benefit','is_new','ficti','store_name','keyword','content','rank']);
        $validate->check($data);
        return $data;
    }

    /**
     * TODO
     * @author Qinii
     * @day 2020-06-24
     */
    public function checkProduct()
    {
        Queue::push(CheckProductExtensionJob::class,[]);
        return app('json')->success('后台已开始检测');
    }


    public function lists()
    {
        $make = app()->make(MerchantRepository::class);
        $data = $make->selectWhere(['status' => 1,'mer_state' => 1,'is_del' => 0],'mer_id,mer_name');
        return app('json')->success($data);
    }



}
