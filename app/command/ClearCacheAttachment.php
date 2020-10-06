<?php
declare (strict_types=1);

namespace app\command;

use app\common\repositories\system\attachment\AttachmentRepository;
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
class ClearCacheAttachment extends Command
{
    /**
     * @author xaboy
     * @day 2020/9/21
     */
    protected function configure()
    {
        // 指令配置
        $this->setName('clear:attachment')
            ->setDescription('clear cache attachment');
    }


    /**
     * @param Input $input
     * @param Output $output
     * @return int|void|null
     * @author xaboy
     * @day 2020/9/21
     */
    protected function execute(Input $input, Output $output)
    {
        $output->writeln('开始清理');
        app()->make(AttachmentRepository::class)->clearCache();
        $output->writeln('开始完毕');
    }

}
