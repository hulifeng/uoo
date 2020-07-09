<?php

declare(strict_types=1);

namespace Tant\Traits;

trait Error
{
    protected $error;

    public function getError()
    {
        return $this->error;
    }
}
