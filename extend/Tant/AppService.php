<?php

declare(strict_types=1);

namespace Tant;

use think\Service;
use Tant\Command\Backup\Backup;
use Tant\Command\Install\Install;

class AppService extends Service
{
    public function register()
    {
        $this->registerCommand();
    }

    /**
     * 注册命令行.
     *
     * @return void
     */
    protected function registerCommand()
    {
        $this->commands([
            Install::class,
            Backup::class,
        ]);
    }
}
