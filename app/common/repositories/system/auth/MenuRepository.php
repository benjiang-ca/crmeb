<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-03-24
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\repositories\system\auth;


//附件
use app\common\dao\BaseDao;
use app\common\dao\system\menu\MenuDao;
use app\common\repositories\BaseRepository;
use FormBuilder\Exception\FormBuilderException;
use FormBuilder\Factory\Elm;
use FormBuilder\Form;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Db;
use think\facade\Route;
use think\Model;

/**
 * Class BaseRepository
 * @package common\repositories
 * @mixin MenuDao
 */
class MenuRepository extends BaseRepository
{
    /**
     * MenuRepository constructor.
     * @param MenuDao $dao
     */
    public function __construct(MenuDao $dao)
    {
        /**
         * @var MenuDao
         */
        $this->dao = $dao;
    }


    /**
     * @param array $where
     * @param int $merId
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-04-16
     */
    public function getList(array $where, $merId = 0)
    {
        $query = $this->dao->search($where, $merId);
        $count = $query->count();
        $list = $query->hidden(['update_time', 'path'])->select()->toArray();
        return compact('count', 'list');
    }

    /**
     * @param array $data
     * @return BaseDao|Model
     * @author xaboy
     * @day 2020-04-09
     */
    public function create(array $data)
    {
        $data['path'] = '/';
        if ($data['pid']) {
            $data['path'] = $this->getPath($data['pid']) . $data['pid'] . '/';
        }
        return $this->dao->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return int
     * @throws DbException
     * @author xaboy
     * @day 2020-04-09
     */
    public function update(int $id, array $data)
    {
        $menu = $this->dao->get($id);
        if ($menu->pid != $data['pid']) {
            Db::transaction(function () use ($menu, $data) {
                $data['path'] = '/';
                if ($data['pid']) {
                    $data['path'] = $this->getPath($data['pid']) . $data['pid'] . '/';
                }
                $this->dao->updatePath($menu->path . $menu->menu_id . '/', $data['path'] . $menu->menu_id . '/');
                $menu->save($data);
            });
        } else {
            unset($data['path']);
            $this->dao->update($id, $data);
        }
    }

    /**
     * @param bool $is_mer
     * @return array
     * @author xaboy
     * @day 2020-04-18
     */
    public function getTree($is_mer = false)
    {
        $options = $this->dao->getAllOptions($is_mer);
        return formatTree($options, 'menu_name');
    }

    /**
     * @param int $isMer
     * @param int|null $id
     * @param array $formData
     * @return Form
     * @throws FormBuilderException
     * @author xaboy
     * @day 2020-04-16
     */
    public function menuForm(int $isMer = 0, ?int $id = null, array $formData = []): Form
    {
        $action = $isMer == 0 ? (is_null($id) ? Route::buildUrl('systemMenuCreate')->build() : Route::buildUrl('systemMenuUpdate', ['id' => $id])->build())
            : (is_null($id) ? Route::buildUrl('systemMerchantMenuCreate')->build() : Route::buildUrl('systemMerchantMenuUpdate', ['id' => $id])->build());

        $form = Elm::createForm($action);
        $form->setRule([
            Elm::cascader('pid', '父级分类')->options(function () use ($id, $isMer) {
                $menus = $this->dao->getAllOptions($isMer);
                if ($id && isset($menus[$id])) unset($menus[$id]);
                $menus = formatCascaderData($menus, 'menu_name');
                array_unshift($menus, ['label' => '顶级分类', 'value' => 0]);
                return $menus;
            })->props(['props' => ['checkStrictly' => true, 'emitPath' => false]]),
            Elm::select('is_menu', '权限类型', 1)->options([
                ['value' => 1, 'label' => '菜单'],
                ['value' => 0, 'label' => '权限'],
            ])->control([
                [
                    'value' => 0,
                    'rule' => [
                        Elm::input('menu_name', '路由名称')->required(),
                        Elm::textarea('params', '参数')->placeholder("路由参数:\r\nkey1:value1\r\nkey2:value2"),
                    ]
                ], [
                    'value' => 1,
                    'rule' => [
                        Elm::switches('is_show', '是否显示', 1)->inactiveValue(0)->activeValue(1)->inactiveText('关闭')->activeText('开启'),
                        Elm::frameInput('icon', '菜单图标', '/' . config('admin.admin_prefix') . '/setting/icons?field=icon')->icon('el-icon-circle-plus-outline')->height('338px')->width('700px')->modal(['modal' => false]),
                        Elm::input('menu_name', '菜单名称')->required(),
                    ]
                ]
            ]),
            Elm::input('route', '路由'),
            Elm::number('sort', '排序', 0)
        ]);

        return $form->setTitle(is_null($id) ? '添加菜单' : '编辑菜单')->formData($formData);
    }


    /**
     * @param int $id
     * @param int $merId
     * @return Form
     * @throws DataNotFoundException
     * @throws DbException
     * @throws FormBuilderException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-04-16
     */
    public function updateMenuForm(int $id, $merId = 0)
    {
        return $this->menuForm($merId, $id, $this->dao->get($id)->toArray());
    }


    /**
     * @param string $params
     * @return array
     * @author xaboy
     * @day 2020-04-22
     */
    public function tidyParams(?string $params)
    {
        return $params ? array_reduce(explode('|', $params), function ($initial, $val) {
            $data = explode(':', $val, 2);
            if (count($data) != 2) return $initial;
            $initial[$data[0]] = $data[1];
            return $initial;
        }, []) : [];
    }

    /**
     * @param array $params
     * @param array $routeParams
     * @return bool
     * @author xaboy
     * @day 2020-04-23
     */
    public function checkParams(array $params, array $routeParams)
    {
        foreach ($routeParams as $k => $param) {
            if (isset($params[$k]) && $params[$k] != $param)
                return false;
        }
        return true;
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/26
     * @param array $data
     */
    public function commandCreate(array $data)
    {
        $this->chekcAndCreate($data['sys'],0,'sys');
        $this->chekcAndCreate($data['mer'],0,'mer');
    }

    /**
     * 入库操作
     * @Author:Qinii
     * @Date: 2020/9/8
     * @param array $data
     * @param int $isMer
     * @param string $name
     * @param $authName
     */
    public function chekcAndCreate(array $data, int $isMer, string $group)
    {
        $slit = [];
        foreach ($data as $key => $v)
        {
            $result = $this->dao->getMenuPid($v);
            if(!empty($result)){
                $res = $this->createSlit($isMer,$result[0]['menu_id'],$result[0]['path'],$v);
            }else{
                $auth = $this->createAncestors($isMer,$group,$key);
                $res = $this->createSlit($isMer,$auth['menu_id'],$auth['path'],$v);
            }
            if(!empty($res)) $slit = array_merge($slit,$res[0]);
        }
        if (!empty($slit)) $this->dao->insertAll($slit);
        return true;
    }

    /**
     * @Author:Qinii
     * @Date: 2020/9/8
     * @param $isMer
     * @param $group
     * @param $key
     * @return array|Model|null
     */
    public function createAncestors($isMer,$group,$key)
    {
        $res = $this->dao->findOrCreate(['menu_name' => '新增权限', 'path' => '/','is_mer' => $isMer]);
        $auth = $this->dao->findOrCreate(['menu_name' => $group.'/'.$key, 'path' => $res['path'].$res['menu_id'].'/','is_mer' => $isMer]);
        return $auth;
    }

    /**
     * 新增权限数据整理
     * @Author:Qinii
     * @Date: 2020/9/8
     * @param int $isMer
     * @param int $menuId
     * @param string $path
     * @param array $data
     * @return array
     */
    public function createSlit(int $isMer,int $menuId, string  $path,array $data)
    {
        $arr = [];
        foreach ($data as $item){
            $result = $this->dao->getFieldExists('route', $item);
            if (!$result) {
                $arr[] = [
                    'pid' => $menuId,
                    'path' => $path . $menuId . '/',
                    'menu_name' => $this->getMenuName($item),
                    'route' => $item,
                    'is_mer' => $isMer,
                    'is_menu' => 0
                ];
            }
        }
        return $arr;
    }

    public function getMenuName($item)
    {
        if (strpos($item, 'CreateForm') != false) return '添加表单';
        if (strpos($item, 'UpdateForm') != false) return '编辑表单';
        if (strpos($item, 'Create') != false) return '添加';
        if (strpos($item, 'Update') != false) return '编辑';
        if (strpos($item, 'Detail') != false) return '详情';
        if (strpos($item, 'Status') != false) return '编辑状态';
        if (strpos($item, 'Lst') != false) return '列表';
        if (strpos($item, 'Delete') != false) return '删除';
        return $item;
    }

    public function formatPath($is_mer = 0)
    {
        $options = $this->getAll($is_mer);
        $options = formatCategory($options, 'menu_id');
        Db::transaction(function () use ($options) {
            foreach ($options as $option) {
                $this->_formatPath($option);
            }
        });
    }

    protected function _formatPath($parent, $path = '/')
    {
        $this->dao->update($parent['menu_id'], ['path' => $path]);
        foreach ($parent['children'] ?? [] as $item) {
            $itemPath = $path . $item['pid'] . '/';
            $this->_formatPath($item, $itemPath);
        }
    }
    /**
     *   v1.1.2 已弃用
     * @Author:Qinii
     * @Date: 2020/5/26
     * @param array $data
     * @param int $isMer
     * @param string $name
     */
    public function add(array $data, int $isMer, string $name, $authName)
    {
        //创建顶级分类
        $auth = $this->dao->getFieldExists('menu_name', $authName);
        if ($auth) {
            $auth_id = $auth['menu_id'];
        } else {
            $auth = $this->dao->create([
                'pid' => 0,
                'path' => '/',
                'menu_name' => $authName,
                'route' => '',
                'is_mer' => $isMer,
                'is_menu' => 0
            ]);
            $auth_id = $auth['menu_id'];
        }

        //权限分组
        foreach ($data as $k => $v) {
            $menuName = $name . '/' . $k;
            $res = $this->dao->getFieldExists('route', $menuName);
            if ($res) {
                $menu_id = $res['menu_id'];
            } else {
                $res = $this->dao->create([
                    'pid' => $auth_id,
                    'path' => $auth['path'] . $auth_id . '/',
                    'menu_name' => $menuName,
                    'route' => $menuName,
                    'is_mer' => $isMer,
                    'is_menu' => 0
                ]);
                $menu_id = $res['menu_id'];
            }
            $arr = [];
            //各个权限
            foreach ($v as $item) {
                $result = $this->dao->getFieldExists('route', $item);
                if (!$result) {
                    $arr[] = [
                        'pid' => $menu_id,
                        'path' => $res['path'] . $menu_id . '/',
                        'menu_name' => $this->getMenuName($item),
                        'route' => $item,
                        'is_mer' => $isMer,
                        'is_menu' => 0
                    ];
                }
            }
            if (!empty($arr))
                $this->dao->insertAll($arr);
        }
        return;
    }

}
