<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/22
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\jobs;


use app\common\repositories\user\UserRepository;
use crmeb\interfaces\JobInterface;

class AutoUserPosterJob implements JobInterface
{
    public function fire($job, $uid)
    {
        $userRepository = app()->make(UserRepository::class);
        $user = $userRepository->get($uid);
        if (!$user)
            $job->delete();
        try {
            $userRepository->routineSpreadImage($user);
        } catch (\Exception $e) {
        };
        try {
            $userRepository->wxSpreadImage($user);
        } catch (\Exception $e) {
        };
        $job->delete();
    }

    public function failed($data)
    {
        // TODO: Implement failed() method.
    }
}
