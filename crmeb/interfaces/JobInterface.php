<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-08
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\interfaces;


interface JobInterface
{
    public function fire($job, $data);

    public function failed($data);
}