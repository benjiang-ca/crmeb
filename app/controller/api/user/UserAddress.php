<?php
/**
 * User: Qinii
 * Date: 2020-06-03
 * Time: 09:42
 */

namespace app\controller\api\user;

use think\App;
use crmeb\basic\BaseController;
use app\validate\api\UserAddressValidate as validate;
use app\common\repositories\user\UserAddressRepository as repository;

class UserAddress extends BaseController
{
    /**
     * @var repository
     */
    protected $repository;

    /**
     * UserAddress constructor.
     * @param App $app
     * @param repository $repository
     */
    public function __construct(App $app, repository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
    }


    public function lst()
    {
        return app('json')->success($this->repository->getList($this->request->uid()));
    }

    public function detail($id)
    {
        return app('json')->success($this->repository->get($id,$this->request->uid()));
    }
    /**
     * @param validate $validate
     * @return mixed
     * @author Qinii
     */
    public function create(validate $validate)
    {
        $data = $this->checkParams($validate);
        if($data['is_default']){
            $this->repository->changeDefault($this->request->uid());
        } else {
            if(!$this->repository->defaultExists($this->request->uid()))$data['is_default'] = 1;
        }
        $city_id = $this->repository->getCityId($data['province'],$data['city']);
        if(!$city_id)return app('json')->fail('未查询到城市ID');
        $data['city_id'] = $city_id;
        if($data['address_id']){
            if(!$this->repository->fieldExists($data['address_id'],$this->request->uid()))
                return app('json')->fail('信息不存在');
            $this->repository->update($data['address_id'],$data);
            return app('json')->success('编辑成功');
        };
        $data['uid'] = $this->request->uid();
        $address = $this->repository->create($data);
        return app('json')->success('添加成功', $address->toArray());
    }

    /**
     * @param $id
     * @param validate $validate
     * @return mixed
     * @author Qinii
     */
    public function update($id,validate $validate)
    {
        if(!$this->repository->fieldExists($id,$this->request->uid()))
            return app('json')->fail('信息不存在');
        $data = $this->checkParams($validate);
        if($data['is_default']) $this->repository->changeDefault($this->request->uid());
        $this->repository->update($id,$data);
        return app('json')->success('编辑成功');
    }

    /**
     * @param $id
     * @return mixed
     * @author Qinii
     */
    public function delete($id)
    {
        if(!$this->repository->fieldExists($id,$this->request->uid()))
            return app('json')->fail('信息不存在');
        if($this->repository->checkDefault($id))
            return app('json')->fail('默认地址不能删除');
        $this->repository->delete($id);
        return app('json')->success('删除成功');
    }

    public function editDefault($id)
    {
        if(!$this->repository->fieldExists($id,$this->request->uid()))
            return app('json')->fail('信息不存在');
        $this->repository->changeDefault($this->request->uid());
        $this->repository->update($id,['is_default' => 1]);
        return app('json')->success('修改成功');
    }

    /**
     * @param validate $validate
     * @return array
     * @author Qinii
     */
    public function checkParams(validate $validate)
    {
        $data = $this->request->params(['address_id','real_name','phone','province','city','city_id','district','detail','post_code','is_default']);
        $validate->check($data);
        return $data;
    }
}
