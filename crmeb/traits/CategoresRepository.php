<?php
/**
 * User: Qinii
 * Date: 2020-04-28
 * Time: 18:09
 */
namespace crmeb\traits;

trait CategoresRepository
{

    /**
     * 获得分级 列表
     * @param $merID
     * @return array
     * @author Qinii
     */
    public function getFormatList($merID,$status = null)
    {
        return formatCategory($this->dao->getAll($merID,$status)->hidden(['path'])->toArray(), $this->dao->getPk());
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/28
     * @param $merID
     * @param null $status
     * @return array
     */
    public function getApiFormatList($merID,$status = null)
    {
        return formatCategory($this->dao->getAll($merID,$status)->hidden(['path','level','mer_id','create_time'])->toArray(), $this->dao->getPk());
    }

    /**
     * 筛选用
     * @Author:Qinii
     * @Date: 2020/5/16
     * @param int $merId
     * @return array
     */
    public function getTreeList(int $merId = 0)
    {
        $data = $this->dao->getAllOptions($merId);
        return formatCascaderData($data,'cate_name');
    }

    /**
     * 提交的pid 是否等于当前pid
     * @param int $id
     * @param int $pid
     * @author Qinii
     */
    public function checkUpdate(int $id,int $pid)
    {
       return  (($this->dao->get($id))[$this->dao->getParentId()] == $pid) ? true : false;
    }

    /**
     * 检测是否超过最低等级限制
     * @param int $id
     * @param int $level
     * @return bool
     * @author Qinii
     */
    public function checkLevel(int $id,int $level = 0)
    {

        $check = $this->getLevelById($id);
        if($level)
            $check = $level;
        return ($check < $this->dao->getMaxLevel()) ? true : false;
    }


    /**
     * 根据ID 查询是否存在
     * @param int $merId
     * @param int $id
     * @param null $except
     * @return mixed
     * @author Qinii
     */
    public function merExists(int $merId, int $id, $except = null)
    {
        return ($this->dao->merFieldExists($merId, $this->getPk(), $id, $except));
    }

    /**
     * 通过id获取 path
     * @param int $id
     * @return string
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author Qinii
     */
    public function getPathById(int $id)
    {
        if ($this->dao->getPathById($id))
            $path = $this->dao->getPathById($id).$id.$this->dao->getPathTag();
        return $path ?? $this->dao->getPathTag();
    }

    /**
     * 通过id获取 + 1等级
     * @param int $id
     * @return int|mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author Qinii
     */
    public function getLevelById(int $id)
    {
        $level = 0;
        if(($parentLevel = $this->dao->getLevelById($id)) !== null)
            $level = $parentLevel + 1;
        return $level;
    }


    /**
     * 编辑时 子集是否 超过最低限制
     * @param int $id
     * @param int $pid
     * @return bool
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author Qinii
     */
    public function checkChildLevel(int $id,int $pid)
    {
        $childLevel = max($this->dao->getChildLevelById($id)); //1
        $changLevel = $childLevel + ($this->changeLevel($id,$pid)); //2
        return $this->checkLevel(0, $changLevel);
    }

    /**
     * 变动等级差
     * @param int $id
     * @param int $pid
     * @return int|mixed
     * @author Qinii
     *  0->1  1-> 2  2-> 3
     *  3->2  2-> 1  1-> 0
     */
    public  function  changeLevel(int $id,int $pid)
    {
        return ($this->dao->getLevelById($pid) + 1 ) - ($this->dao->getLevelById($id));
    }

    /**
     * 检测 是否修改到 子集
     * @param int $id
     * @param int $pid
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author Qinii
     */
    public function checkChangeToChild(int $id, int $pid)
    {
        return ($this->dao->checkChangeToChild($id,$pid) > 0) ? false : true;
    }

    /**
     * 子集是否存在
     * @param int $id
     * @return bool
     * @author Qinii
     */
    public function hasChild(int $id)
    {
        return (($this->dao->hasChild($id)) > 0) ? true : false ;
    }

    /**
     * 添加
     * @param array $data
     * @return \app\common\dao\BaseDao|\think\Model
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author Qinii
     */
    public function create(array $data)
    {
        $data[$this->dao->getPath()] = $this->getPathById($data[$this->dao->getParentId()]);
        $data[$this->dao->getLevel()] = $this->getLevelById($data[$this->dao->getParentId()]);
        return ($this->dao->create($data));
    }

    /**
     * 修改
     * @param int $id
     * @param array $data
     * @author Qinii
     */
    public function update(int $id,array $data)
    {

        if($this->checkUpdate($id,$data['pid'])){
            $this->dao->update($id,$data);
        } else {
            $data[$this->dao->getPath()] = $this->getPathById($data[$this->dao->getParentId()]);
            $data[$this->dao->getLevel()] = $this->getLevelById($data[$this->dao->getParentId()]);

            $data['change'] = [
                'oldPath' => $this->dao->getPathById($id).$id.$this->getPathTag(),
                'newPath' => $data[$this->dao->getPath()].$id.$this->getPathTag(),
                'changeLevel' => $this->changeLevel($id,$data[$this->getParentId()]),
            ];
            $this->dao->updateParent($id,$data);
        }
    }
}
