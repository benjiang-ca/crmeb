<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-03-24
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\dao\system\menu;

use app\common\dao\BaseDao;
use app\common\model\BaseModel;
use app\common\model\system\auth\Menu;
use think\db\BaseQuery;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Model;

/**
 * Class MenuDao
 * @package app\common\dao\system\menu
 * @author xaboy
 * @day 2020-04-08
 */
class MenuDao extends BaseDao
{
    /**
     * @return BaseModel
     * @author xaboy
     * @day 2020-03-30
     */
    protected function getModel(): string
    {
        return Menu::class;
    }

    /**
     * @param array $where
     * @param int $is_mer
     * @return BaseQuery
     * @author xaboy
     * @day 2020-04-08
     */
    public function search(array $where, int $is_mer = 0)
    {
        $query = Menu::getDB()->where('is_mer', $is_mer)->order('sort DESC,menu_id ASC');
        if (isset($where['pid'])) $query->where('pid', (int)$where['pid']);
        if (isset($where['keyword'])) $query->whereLike('menu_name|route', "%{$where['keyword']}%");
        if (isset($where['is_menu'])) $query->where('is_menu', (int)$where['is_menu']);
        return $query;
    }


    /**
     * @param int $is_mer
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-04-08
     */
    public function getAllMenu($is_mer = 0)
    {
        return Menu::getDB()->where('is_mer', $is_mer)->where('is_menu', 1)->order('sort DESC,menu_id ASC')->select()->toArray();
    }

    /**
     * @param int $is_mer
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-04-08
     */
    public function getAll($is_mer = 0)
    {
        return Menu::getInstance()->where('is_mer', $is_mer)->order('sort DESC,menu_id ASC')->select()->toArray();
    }

    /**
     * @param int $id
     * @param int $is_mer
     * @return bool
     * @author xaboy
     * @day 2020-04-08
     */
    public function menuExists(int $id, $is_mer = 0)
    {
        return Menu::getDB()->where($this->getPk(), $id)->where('is_menu', 1)->where('is_mer', $is_mer)->count() > 0;
    }

    /**
     * @param int $id
     * @param int $is_mer
     * @return bool
     * @author xaboy
     * @day 2020-04-16
     */
    public function merExists(int $id, $is_mer = 0)
    {
        return Menu::getDB()->where($this->getPk(), $id)->where('is_mer', $is_mer)->count() > 0;
    }

    /**
     * @param int $id
     * @param int $is_mer
     * @return bool
     * @author xaboy
     * @day 2020-04-08
     */
    public function authExists(int $id, $is_mer = 0)
    {
        return Menu::getDB()->where($this->getPk(), $id)->where('is_menu', 0)->where('is_mer', $is_mer)->count() > 0;
    }

    /**
     * @param string $route
     * @param int $is_mer
     * @return bool
     * @author xaboy
     * @day 2020-04-08
     */
    public function routeExists(string $route, $is_mer = 0)
    {
        return Menu::getDB()->where('route', $route)->where('is_menu', 0)->where('is_mer', $is_mer)->count() > 0;
    }

    /**
     * @param int $is_mer
     * @return array
     * @author xaboy
     * @day 2020-04-08
     */
    public function getAllMenuOptions($is_mer = 0)
    {
        return Menu::getDB()->where('is_menu', 1)->where('is_mer', $is_mer)->order('sort DESC,menu_id ASC')->column('menu_name,pid', 'menu_id');
    }

    /**
     * @param array $rule
     * @param int $is_mer
     * @return array
     * @author xaboy
     * @day 2020-04-10
     */
    public function ruleByMenuList(array $rule, $is_mer = 0)
    {
        $paths = Menu::getDB()->whereIn($this->getPk(), $rule)->column('path', 'menu_id');
        $ids = [];
        foreach ($paths as $id => $path) {
            $ids = array_merge($ids, explode('/', trim($path, '/')));
            array_push($ids, $id);
        }
        return Menu::getDB()->where('is_menu', 1)->where('is_show', 1)->order('sort DESC,menu_id ASC')->where('is_mer', $is_mer)
            ->whereIn('menu_id', array_unique(array_filter($ids)))
            ->column('menu_name,route,params,icon,pid,menu_id');
    }

    /**
     * @param int $is_mer
     * @return array
     * @author xaboy
     * @day 2020-04-10
     */
    public function getValidMenuList($is_mer = 0)
    {
        return Menu::getDB()->where('is_menu', 1)->where('is_show', 1)->order('sort DESC,menu_id ASC')->where('is_mer', $is_mer)
            ->column('menu_name,route,params,icon,pid,menu_id');
    }

    /**
     * @param int $is_mer
     * @return array
     * @author xaboy
     * @day 2020-04-08
     */
    public function getAllOptions($is_mer = 0)
    {
        return Menu::getDB()->where('is_mer', $is_mer)->order('sort DESC,menu_id ASC')->column('menu_name,pid', 'menu_id');
    }

    /**
     * @param $id
     * @param int $is_mer
     * @return mixed
     * @author xaboy
     * @day 2020-04-09
     */
    public function getPath($id, $is_mer = 0)
    {
        return Menu::getDB()->where('is_mer', $is_mer)->where('menu_id', $id)->value('path');
    }

    /**
     * @param int $id
     * @param int $is_mer
     * @return array|Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-04-08
     */
    public function getMenu(int $id, $is_mer = 0)
    {
        return Menu::getDB()->where('is_mer', $is_mer)->where('is_menu', 1)->where($this->getPk(), $id)->find();
    }

    /**
     * @param int $id
     * @param int $is_mer
     * @return array|Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-04-08
     */
    public function getAuth(int $id, $is_mer = 0)
    {
        return Menu::getDB()->where('is_mer', $is_mer)->where('is_menu', 0)->where($this->getPk(), $id)->find();
    }

    /**
     * @param int $id
     * @param int $is_mer
     * @return int
     * @throws DbException
     * @author xaboy
     * @day 2020-04-08
     */
    public function delete(int $id, $is_mer = 0)
    {
        return Menu::getDB()->where('is_mer', $is_mer)->delete($id);
    }

    /**
     * @param int $id
     * @return bool
     * @author xaboy
     * @day 2020-04-08
     */
    public function pidExists(int $id)
    {
        return $this->fieldExists('pid', $id);
    }

    /**
     * @param array $ids
     * @return array
     * @author xaboy
     * @day 2020-04-10
     */
    public function idsByRoutes(array $ids)
    {
        return Menu::getDB()->where('is_menu', 0)->whereIn($this->getPk(), $ids)->column('params,route');
    }

    /**
     * @param string $oldPath
     * @param string $path
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-04-30
     */
    public function updatePath(string $oldPath, string $path)
    {
        Menu::getDB()->whereLike('path', $oldPath . '%')->field('menu_id,path')->select()->each(function ($val) use ($oldPath, $path) {
            $newPath = str_replace($oldPath, $path, $val['path']);
            Menu::getDB()->where('menu_id', $val['menu_id'])->update(['path' => $newPath]);
        });
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/26
     * @param $field
     * @param $value
     * @return array|Model|null
     */
    public function getFieldExists($field,$value)
    {
        return (($this->getModel()::getDB())->where($field,$value)->find());
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/26
     * @param array $data
     * @return int
     */
    public function insertAll(array $data)
    {
        return ($this->getModel()::getDB())->insertAll($data);
    }

    public function deleteCommandMenu($where)
    {
        $this->getModel()::getDB()->where($where)->delete();
    }

    public function all()
    {
        return ($this->getModel()::getDB())->select();
    }

    /**
     *  根据每个路由分组获取是否存在父级
     * @Author:Qinii
     * @Date: 2020/9/8
     * @param array $data
     * @return mixed
     */
    public function getMenuPid(array $data)
    {
        return ($this->getModel()::getDB())->where('route','in',$data)->field('menu_id,pid,path')->order('path DESC')->select()->toArray();
    }
}
