<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/7/29
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\repositories\store\broadcast;


use app\common\dao\store\broadcast\BroadcastRoomDao;
use app\common\model\store\broadcast\BroadcastRoom;
use app\common\repositories\BaseRepository;
use crmeb\jobs\SendSmsJob;
use crmeb\services\DownloadImageService;
use crmeb\services\MiniProgramService;
use EasyWeChat\Core\Exceptions\HttpException;
use Exception;
use FormBuilder\Exception\FormBuilderException;
use FormBuilder\Factory\Elm;
use FormBuilder\Form;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\Queue;
use think\facade\Route;

/**
 * Class BroadcastRoomRepository
 * @package app\common\repositories\store\broadcast
 * @author xaboy
 * @day 2020/7/29
 * @mixin BroadcastRoomDao
 */
class BroadcastRoomRepository extends BaseRepository
{
    /**
     * @var BroadcastRoomDao
     */
    protected $dao;

    /**
     * BroadcastRoomRepository constructor.
     * @param BroadcastRoomDao $dao
     */
    public function __construct(BroadcastRoomDao $dao)
    {
        $this->dao = $dao;
    }

    public function getList($merId, array $where, $page, $limit)
    {
        $where['mer_id'] = $merId;
        $query = $this->dao->search($where)->order('create_time DESC');
        $count = $query->count();
        $list = $query->page($page, $limit)->select();
        return compact('count', 'list');
    }

    public function userList(array $where, $page, $limit)
    {
        $where['show_tag'] = 1;
        $query = $this->dao->search($where)->with('broadcast.goods')->where('room_id', '>', 0)->whereNotIn('live_status', [107])->order('sort DESC, create_time DESC');
        $count = $query->count();
        $list = $query->page($page, $limit)->select();
        foreach ($list as $item) {
            $item->show_time = date('m/d H:i', strtotime($item->start_time));
        }
        return compact('count', 'list');
    }

    public function adminList(array $where, $page, $limit)
    {
        $query = $this->dao->search($where)->with(['merchant' => function ($query) {
            $query->field('mer_name,mer_id,is_trader');
        }])->order('BroadcastRoom.sort DESC, BroadcastRoom.create_time DESC');
        $count = $query->count();
        $list = $query->page($page, $limit)->select();
        return compact('count', 'list');
    }

    /**
     * @return Form
     * @throws FormBuilderException
     * @author xaboy
     * @day 2020/7/29
     */
    public function createForm()
    {
        return Elm::createForm(Route::buildUrl('merchantBroadcastRoomCreate')->build(), [
            Elm::input('name', '直播间名字')->required(),
            Elm::frameImage('cover_img', '背景图', '/' . config('admin.merchant_prefix') . '/setting/uploadPicture?field=cover_img&type=1')
                ->info('建议像素1080*1920，大小不超过2M')->modal(['modal' => false])->width('896px')->height('480px')->props(['footer' => false]),
            Elm::frameImage('share_img', '分享图', '/' . config('admin.merchant_prefix') . '/setting/uploadPicture?field=share_img&type=1')
                ->info('建议像素800*640，大小不超过1M')->modal(['modal' => false])->width('896px')->height('480px')->props(['footer' => false]),
            Elm::input('anchor_name', '主播昵称')->required()->placeholder('请输入主播昵称，主播需通过小程序直播认证，否则会提交失败。'),
            Elm::input('anchor_wechat', '主播微信号')->required()->placeholder('请输入主播微信号，主播需通过小程序直播认证，否则会提交失败。'),
            Elm::input('phone', '联系电话')->required(),
            Elm::dateTimeRange('start_time', '直播时间')->value([]),
            Elm::radio('type', '直播间类型', 0)->options([['value' => 0, 'label' => '手机直播']])->disabled(true),
            Elm::radio('screen_type', '显示样式', 0)->options([['value' => 0, 'label' => '竖屏'], ['value' => 1, 'label' => '横屏']]),
            Elm::switches('close_like', '是否开启点赞', 0)->activeValue(0)->inactiveValue(1)->inactiveText('关闭')->activeText('开启'),
            Elm::switches('close_goods', '是否开启货架', 0)->activeValue(0)->inactiveValue(1)->inactiveText('关闭')->activeText('开启'),
            Elm::switches('close_comment', '是否开启评论', 0)->activeValue(0)->inactiveValue(1)->inactiveText('关闭')->activeText('开启'),
            Elm::switches('replay_status', '是否开启回放', 0)->activeValue(1)->inactiveValue(0)->inactiveText('关闭')->activeText('开启'),
            Elm::switches('close_share', '是否开启分享', 0)->activeValue(0)->inactiveValue(1)->inactiveText('关闭')->activeText('开启'),
            Elm::switches('close_kf', '是否开启客服', 0)->activeValue(0)->inactiveValue(1)->inactiveText('关闭')->activeText('开启'),
        ])->setTitle('创建直播间');
    }

    public function create($merId, array $data)
    {
        $data['status'] = request()->merchant()->is_bro_room == 1 ? 0 : 1;
        $data['mer_id'] = $merId;
        return Db::transaction(function () use ($data) {
            $room = $this->dao->create($data);
            if ($data['status'] == 1) {
                $room->room_id = $this->wxCreate($room);
                $room->status = 2;
                $room->save();
            }
            return $room;
        });
    }

    public function applyForm($id)
    {
        return Elm::createForm(Route::buildUrl('systemBroadcastRoomApply', compact('id'))->build(), [
            Elm::radio('status', '审核状态', 1)->options([['value' => -1, 'label' => '未通过'], ['value' => 1, 'label' => '通过']])->control([
                ['value' => -1, 'rule' => [
                    Elm::textarea('msg', '未通过原因', '信息有误,请完善')->required()
                ]]
            ]),
        ])->setTitle('审核直播间');
    }

    public function apply($id, $status, $msg = '')
    {
        $room = $this->dao->get($id);
        Db::transaction(function () use ($msg, $status, $room) {
            $room->status = $status;
            if ($status == -1)
                $room->error_msg = $msg;
            else {
                $room->room_id = $this->wxCreate($room);
                $room->status = 2;
            }
            $room->save();
        });
    }

    public function wxCreate(BroadcastRoom $room)
    {
        if ($room['room_id'])
            throw new ValidateException('直播间已创建');

        $room = $room->toArray();
        $miniProgramService = MiniProgramService::create();
        $coverImg = './public' . app()->make(DownloadImageService::class)->downloadImage($room['cover_img'])['path'];
        $shareImg = './public' . app()->make(DownloadImageService::class)->downloadImage($room['share_img'])['path'];
        $data = [
            'startTime' => strtotime($room['start_time']),
            'endTime' => strtotime($room['end_time']),
            'name' => $room['name'],
            'anchorName' => $room['anchor_name'],
            'anchorWechat' => $room['anchor_wechat'],
            'screenType' => $room['screen_type'],
            'closeGoods' => $room['close_goods'],
            'closeLike' => $room['close_like'],
            'closeComment' => $room['close_comment'],
            'closeShare' => $room['close_share'],
            'closeKf' => $room['close_kf'],
            'closeReplay' => $room['replay_status'] == 1 ? 0 : 1,
            'coverImg' => $miniProgramService->material()->uploadImage($coverImg)->media_id,
            'shareImg' => $miniProgramService->material()->uploadImage($shareImg)->media_id
        ];
        @unlink($coverImg);
        @unlink($shareImg);
        try {
            $roomId = $miniProgramService->miniBroadcast()->createLiveRoom($data)->roomId;
        } catch (Exception $e) {
            throw new ValidateException($e->getMessage());
        }
        Queue::push(SendSmsJob::class, [
            'tempId' => 'BROADCAST_ROOM_CODE',
            'id' => $room['broadcast_room_id']
        ]);
        return $roomId;
    }

    public function isShow($id, $isShow, bool $admin = false)
    {
        return $this->dao->update($id, [($admin ? 'is_show' : 'is_mer_show') => $isShow]);
    }

    public function mark($id, $mark)
    {
        return $this->dao->update($id, compact('mark'));
    }

    /**
     * @param $merId
     * @param array $ids
     * @param $roomId
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/7/31
     */
    public function exportGoods($merId, array $ids, $roomId)
    {
        $broadcastGoodsRepository = app()->make(BroadcastGoodsRepository::class);
        if (count($ids) != count($goods = $broadcastGoodsRepository->goodsList($merId, $ids)))
            throw new ValidateException('请选择正确的直播商品');
        if (!$room = $this->dao->validRoom($roomId, $merId))
            throw new ValidateException('直播间状态有误');
        $broadcastRoomGoodsRepository = app()->make(BroadcastRoomGoodsRepository::class);
        $goodsId = $broadcastRoomGoodsRepository->goodsId($room->broadcast_room_id);
        $ids = [];
        $data = [];
        foreach ($goods as $item) {
            if (!in_array($item->broadcast_goods_id, $goodsId)) {
                $data[] = [
                    'broadcast_room_id' => $room->broadcast_room_id,
                    'broadcast_goods_id' => $item->broadcast_goods_id
                ];
                $ids[] = $item->goods_id;
            }
        }
        if (!count($ids)) return;
        Db::transaction(function () use ($ids, $broadcastRoomGoodsRepository, $goods, $room, $data) {
            $broadcastRoomGoodsRepository->insertAll($data);
            MiniProgramService::create()->miniBroadcast()->addGoods(['roomId' => $room->room_id, 'ids' => $ids]);
        });
    }

    /**
     * @throws HttpException
     * @throws DbException
     * @author xaboy
     * @day 2020/7/31
     */
    public function syncRoomStatus()
    {
        $start = 0;
        $limit = 50;
        $client = MiniProgramService::create()->miniBroadcast();
        do {
            $data = $client->getRooms($start, $limit)->room_info;
            $start += 50;
            $rooms = $this->getRooms(array_column($data, 'roomid'));
            foreach ($data as $room) {
                if (isset($rooms[$room['roomid']]) && $room['live_status'] != $rooms[$room['roomid']]['live_status']) {
                    $this->dao->update($rooms[$room['roomid']]['broadcast_room_id'], ['live_status' => $room['live_status']]);
                }
            }
        } while (count($data) >= $limit);
    }

    public function merDelete($id)
    {
        $room = $this->dao->get($id);
        if ($room && ($room->status == -1 || $room->status == 0 || $room->live_status == 107 || $room->live_status == 103)) {
            return $this->dao->merDelete($id);
        }
        throw new ValidateException('状态有误,删除失败');
    }

}