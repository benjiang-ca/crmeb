<?php
declare (strict_types=1);

namespace app\command;

use app\common\repositories\system\auth\MenuRepository;
use think\console\Command;
use think\console\Input;
use think\console\Output;

/**
 * Class FormatMenuPath
 * @package app\command
 * @author xaboy
 * @day 2020/8/26
 */
class FormatMenuPath extends Command
{
    /**
     * @author xaboy
     * @day 2020/8/26
     */
    protected function configure()
    {
        // 指令配置
        $this->setName('menu:format')
            ->setDescription('the format menu command');
    }


    /**
     * @param Input $input
     * @param Output $output
     * @return int|void|null
     * @author xaboy
     * @day 2020/8/26
     */
    protected function execute(Input $input, Output $output)
    {
        $output->writeln('开启修复');
        $menuRepository = app()->make(MenuRepository::class);
        $menuRepository->formatPath(0);
        $menuRepository->formatPath(1);
        $output->writeln('执行成功');
    }

}
