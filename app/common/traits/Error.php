<?php

declare(strict_types=1);

namespace app\common\traits;

trait Error
{
    protected $error;

    public function getError()
    {
        return $this->error;
    }
}
