<?php

declare(strict_types=1);

namespace Yurunsoft\Nacos\Provider\Operator\Model;

use Yurunsoft\Nacos\Model\BaseModel;

class ServerRemoteAbility extends BaseModel
{
    protected bool $supportRemoteConnection = false;

    public function getSupportRemoteConnection(): bool
    {
        return $this->supportRemoteConnection;
    }
}
