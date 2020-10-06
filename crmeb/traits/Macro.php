<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-10
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\traits;


use BadMethodCallException;
use Closure;

trait Macro
{
    protected $macroList = [];

    /**
     * @param string $name
     * @param $macro
     * @author xaboy
     * @day 2020-04-10
     */
    public function macro(string $name, $macro)
    {
        $this->macroList[$name] = $macro;
    }

    /**
     * @param array $names
     * @param $macro
     * @author xaboy
     * @day 2020-04-10
     */
    public function macros(array $names, $macro)
    {
        foreach ($names as $name) {
            $this->macro($name, $macro);
        }
    }

    /**
     * @param string $name
     * @return bool
     * @author xaboy
     * @day 2020-04-10
     */
    public function hasMacro(string $name): bool
    {
        return isset($this->macroList[$name]);
    }

    public function __call($method, $parameters)
    {
        if (!$this->hasMacro($method)) {
            throw new BadMethodCallException("Method {$method} does not exist.");
        }

        $macro = $this->macroList[$method];

        if ($macro instanceof Closure) {
            return call_user_func_array($macro->bindTo($this, static::class), $parameters);
        }

        return call_user_func_array($macro, $parameters);
    }
}