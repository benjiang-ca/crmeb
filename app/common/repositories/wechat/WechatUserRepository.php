<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-28
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\repositories\wechat;


use app\common\dao\wechat\WechatUserDao;
use app\common\repositories\article\ArticleRepository;
use app\common\repositories\BaseRepository;
use app\common\repositories\user\UserRepository;
use crmeb\jobs\SendNewsJob;
use crmeb\services\WechatUserGroupService;
use crmeb\services\WechatUserTagService;
use FormBuilder\Exception\FormBuilderException;
use FormBuilder\Factory\Elm;
use FormBuilder\Form;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Db;
use think\facade\Queue;
use think\facade\Route;

/**
 * Class WechatUserRepository
 * @package app\common\repositories\wechat
 * @author xaboy
 * @day 2020-04-28
 * @mixin WechatUserDao
 */
class WechatUserRepository extends BaseRepository
{
    /**
     * WechatUserRepository constructor.
     * @param WechatUserDao $dao
     */
    public function __construct(WechatUserDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param string $openId
     * @param array $userInfo
     * @param bool $mode
     * @return mixed|void
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-04-28
     */
    public function syncUser(string $openId, array $userInfo, bool $mode = false)
    {
        if (($mode && (!isset($userInfo['subscribe']) || !$userInfo['subscribe'])) || !isset($userInfo['openid']))
            return;
        $wechatUser = null;
        $userInfo['nickname'] = filter_emoji($userInfo['nickname']);
        if (isset($userInfo['unionid']))
            $wechatUser = $this->dao->unionIdByWechatUser($userInfo['unionid']);
        if (!$wechatUser)
            $wechatUser = $this->dao->openIdByWechatUser($openId);

        unset($userInfo['qr_scene'], $userInfo['qr_scene_str'], $userInfo['qr_scene_str'], $userInfo['subscribe_scene']);

        return Db::transaction(function () use ($userInfo, $wechatUser) {
            if ($wechatUser) {
                $wechatUser->save($userInfo);
            } else {
                $wechatUser = $this->dao->create($userInfo);
            }
            /** @var UserRepository $userRepository */
            $userRepository = app()->make(UserRepository::class);
            $user = $userRepository->syncWechatUser($wechatUser);
            return [$wechatUser, $user];
        });
    }

    /**
     * @param string $routineOpenid
     * @param array $routine
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-05-11
     */
    public function syncRoutineUser(string $routineOpenid, array $routine)
    {
        $routineInfo = [];
        $routineInfo['nickname'] = filter_emoji($routine['nickName']);//姓名
        $routineInfo['sex'] = $routine['gender'];//性别
        $routineInfo['language'] = $routine['language'];//语言
        $routineInfo['city'] = $routine['city'];//城市
        $routineInfo['province'] = $routine['province'];//省份
        $routineInfo['country'] = $routine['country'];//国家
        $routineInfo['headimgurl'] = $routine['avatarUrl'];//头像
        $routineInfo['routine_openid'] = $routineOpenid;//openid
        $routineInfo['session_key'] = $routine['session_key'] ?? '';//会话密匙
        $routineInfo['unionid'] = $routine['unionId'];//用户在开放平台的唯一标识符
        $routineInfo['user_type'] = 'routine';//用户类型
        $wechatUser = null;
        if ($routineInfo['unionid'])
            $wechatUser = $this->dao->unionIdByWechatUser($routineInfo['unionid']);
        if (!$wechatUser)
            $wechatUser = $this->dao->routineIdByWechatUser($routineOpenid);

        return Db::transaction(function () use ($routineInfo, $wechatUser) {
            if ($wechatUser) {
                $wechatUser->save($routineInfo);
            } else {
                $wechatUser = $this->dao->create($routineInfo);
            }
            /** @var UserRepository $userRepository */
            $userRepository = app()->make(UserRepository::class);
            $user = $userRepository->syncWechatUser($wechatUser, 'routine');
            return [$wechatUser, $user];
        });
    }

    /**
     * @param array $where
     * @param $page
     * @param $limit
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-04-29
     */
    public function getList(array $where, $page, $limit)
    {
        $query = $this->dao->search($where);
        $count = $query->count($this->dao->getPk());
        $list = $query->setOption('field', [])->field('uid,openid,nickname,headimgurl,sex,country,province,city,subscribe')
            ->page($page, $limit)->select()->each(function ($item) {
                $item['subscribe_time'] = $item['subscribe_time'] ? date('Y-m-d H:i', $item['subscribe_time']) : '';
                return $item;
            });
        return compact('count', 'list');
    }


    /**
     * @param $id
     * @return Form
     * @throws DataNotFoundException
     * @throws DbException
     * @throws FormBuilderException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-04-29
     */
    public function updateUserTagForm($id)
    {
        $wechatUserTagService = new WechatUserTagService();
        $lst = $wechatUserTagService->lst();
        $user = $this->dao->get($id);
        return Elm::createForm(Route::buildUrl('wechat/user/tag', ['id' => $id]), [
            Elm::select('tag_id', '用户标签', explode(',', $user->tagid_list))->options(function () use ($lst) {
                $options = [];
                foreach ($lst as $item) {
                    $options[] = ['value' => $item['id'], 'label' => $item['name']];
                }
                return $options;
            })->multiple(true)
        ])->setTitle('编辑用户标签');
    }

    /**
     * @param $id
     * @param array $tags
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-04-29
     */
    public function updateTag($id, array $tags)
    {
        $user = $this->dao->get($id);
        $oTags = explode(',', $user->tagid_list);
        $user->save(['tagid_list' => implode(',', $tags)]);
        $wechatUserTagService = (new WechatUserTagService())->userTag();
        foreach ($oTags as $tag) {
            $wechatUserTagService->batchUntagUsers([$user->openid], $tag);
        }
        foreach ($tags as $tag) {
            $wechatUserTagService->batchTagUsers([$user->openid], $tag);
        }
    }


    /**
     * @param $id
     * @return Form
     * @throws DataNotFoundException
     * @throws DbException
     * @throws FormBuilderException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-04-29
     */
    public function updateUserGroupForm($id)
    {
        $wechatUserGroupService = new WechatUserGroupService();
        $lst = $wechatUserGroupService->lst();
        $user = $this->dao->get($id);
        return Elm::createForm(Route::buildUrl('wechat/user/group', ['id' => $id]), [
            Elm::select('group_id', '用户标签', (string)$user->groupid)->options(function () use ($lst) {
                $options = [];
                foreach ($lst as $item) {
                    $options[] = ['value' => $item['id'], 'label' => $item['name']];
                }
                return $options;
            })
        ])->setTitle('编辑用户分组');
    }

    /**
     * @param $id
     * @param $groupid
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-04-29
     */
    public function updateGroup($id, $groupid)
    {
        $user = $this->dao->get($id);
        $user->save(['groupid' => $groupid]);
        $wechatUserGroupService = (new WechatUserGroupService())->userGroup();
        $wechatUserGroupService->moveUser($user->openid, $groupid);
    }


    /**
     * @param $id
     * @param array $ids
     * @author xaboy
     * @day 2020-05-11
     */
    public function sendNews($id, array $ids)
    {
        if (!count($ids)) return;
        /** @var ArticleRepository $make */
        $make = app()->make(ArticleRepository::class);
        $articles = $make->wechatNewIdByData($id);
        $news = [];
        foreach ($articles as $article) {
            $news[] = [
                'title' => $article['title'],
                'image' => $article['image_input'],
                'date' => $article['create_time'],
                'description' => $article['synopsis'],
                'id' => $article['article_id']
            ];
        }

        foreach ($ids as $_id) {
            if ($this->dao->isSubscribeWechatUser($_id)) {
                Queue::push(SendNewsJob::class, [$_id, $news]);
            }
        }
    }

}