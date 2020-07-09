<?php

declare(strict_types=1);

namespace app;

use Tant\Abstracts\AbstractModel;
use think\model\concern\SoftDelete;

class BaseModel extends AbstractModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
}
