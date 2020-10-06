<?php
/**
 * @author: liaofei<136327134@qq.com>
 * @day: 2020/5/26
 */

namespace crmeb\services;

use crmeb\services\printer\AccessToken;
use crmeb\basic\BaseStorage;

/**
 * Class BasePrinter
 * @package crmeb\basic
 */
abstract class PrinterService extends BaseStorage
{

    /**
     * token句柄
     * @var AccessToken
     */
    protected $accessToken;

    /**
     * 打印内容
     * @var string
     */
    protected $printerContent;

    /**
     * BasePrinter constructor.
     * @param string $name
     * @param AccessToken $accessToken
     * @param string $configFile
     */
    public function __construct(string $name, AccessToken $accessToken, string $configFile)
    {
        $this->accessToken = $accessToken;
        $this->initialize([]);
    }

    /**
     * 开始打印
     * @param array|null $systemConfig
     * @return mixed
     */
    abstract public function startPrinter();

    /**
     * 设置打印内容
     * @param array $config
     * @return mixed
     */
    abstract public function setPrinterContent(array $config);

}
