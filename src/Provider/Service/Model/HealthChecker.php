<?php

declare(strict_types=1);

namespace Yurunsoft\Nacos\Provider\Service\Model;

use Yurunsoft\Nacos\Model\BaseModel;

class HealthChecker extends BaseModel
{
    protected string $type = '';

    public function getType(): string
    {
        return $this->type;
    }
}
