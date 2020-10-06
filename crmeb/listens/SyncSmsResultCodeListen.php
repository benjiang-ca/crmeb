<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/7/14
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\listens;


use app\common\repositories\system\sms\SmsRecordRepository;
use crmeb\interfaces\ListenerInterface;
use crmeb\services\YunxinSmsService;
use Swoole\Timer;

class SyncSmsResultCodeListen implements ListenerInterface
{

    public function handle($event): void
    {
        $smsRecordRepository = app()->make(SmsRecordRepository::class);
        Timer::tick(1000 * 60 * 5, function () use ($smsRecordRepository) {
            $time = date('Y-m-d H:i:s', strtotime("- 10 minutes"));
            $ids = $smsRecordRepository->getTimeOutIds($time);
            if (count($ids)) return;
            $list = (array)YunxinSmsService::create()->getStatus($ids);
            foreach ($list as $item) {
                if (isset($item['id'])) {
                    if ($item['resultcode'] == '' || $item['resultcode'] == null) $item['resultcode'] = 134;
                    $smsRecordRepository->updateRecordStatus($item['id'], $item['resultcode']);
                }
            }
        });
    }
}
