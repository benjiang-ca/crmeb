<?php

namespace app;

use crmeb\traits\Macro;

class Request extends \think\Request
{
    use Macro;

    public function __construct()
    {
        parent::__construct();
        $this->filter[] = function ($val) {
            return is_string($val) ? trim($val) : $val;
        };
    }

    public function ip(): string
    {
        return $this->header('remote-host') ?? parent::ip();
    }

    public function params(array $names, $filter = '')
    {
        $data = [];
        $flag = false;
        if ($filter === true) {
            $filter = '';
            $flag = true;
        }
        foreach ($names as $name) {
            if (!is_array($name))
                $data[$name] = $this->param($name, '', $filter);
            else
                $data[$name[0]] = $this->param($name[0], $name[1], $filter);
        }

        return $flag ? array_values($data) : $data;
    }

    public function merId()
    {
        return intval($this->hasMacro('merchantId') ? $this->merchantId() : 0);
    }
}
