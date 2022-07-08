<?php

declare(strict_types=1);

namespace Yurun\Nacos\Provider\Service\Model;

use Yurun\Nacos\Model\BaseModel;

class HealthChecker extends BaseModel
{
    protected string $type = '';

    public function getType(): string
    {
        return $this->type;
    }
}
