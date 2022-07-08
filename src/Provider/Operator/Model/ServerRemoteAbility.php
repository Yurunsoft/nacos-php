<?php

declare(strict_types=1);

namespace Yurun\Nacos\Provider\Operator\Model;

use Yurun\Nacos\Model\BaseModel;

class ServerRemoteAbility extends BaseModel
{
    protected bool $supportRemoteConnection = false;

    public function getSupportRemoteConnection(): bool
    {
        return $this->supportRemoteConnection;
    }
}
