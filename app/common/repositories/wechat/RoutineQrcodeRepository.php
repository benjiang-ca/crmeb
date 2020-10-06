<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/18
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\repositories\wechat;


use app\common\dao\wechat\RoutineQrcodeDao;
use app\common\repositories\BaseRepository;
use crmeb\services\MiniProgramService;

/**
 * Class RoutineQrcodeRepository
 * @package app\common\repositories\wechat
 * @author xaboy
 * @day 2020/6/18
 * @mixin RoutineQrcodeDao
 */
class RoutineQrcodeRepository extends BaseRepository
{
    /**
     * RoutineQrcodeRepository constructor.
     * @param RoutineQrcodeDao $dao
     */
    public function __construct(RoutineQrcodeDao $dao)
    {
        $this->dao = $dao;
    }


    /**
     * TODO 获取小程序二维码
     * @param $thirdId
     * @param $thirdType
     * @param $page
     * @param $imgUrl
     * @return array|bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getShareCode($thirdId, $thirdType, $page, $imgUrl)
    {
        $res = $this->dao->routineQrCodeForever($thirdId, $thirdType, $page, $imgUrl);
        $resCode = MiniProgramService::create()->qrcodeService()->appCodeUnlimit($res->routine_qrcode_id, '', 280);
        if ($resCode)
            return ['res' => $resCode, 'id' => $res->routine_qrcode_id];
        else return false;
    }

    /**
     * TODO 获取小程序页面带参数二维码不保存数据库
     * @param string $page
     * @param string $pramam
     * @param int $width
     * @return mixed
     */
    public function getPageCode($page = '', $pramam = "", $width = 280)
    {
        return MiniProgramService::create()->qrcodeService()->appCodeUnlimit($pramam, $page, $width);
    }
}