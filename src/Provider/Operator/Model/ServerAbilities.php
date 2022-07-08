<?php

declare(strict_types=1);

namespace Yurunsoft\Nacos\Provider\Operator\Model;

use Yurunsoft\Nacos\Model\BaseModel;

class ServerAbilities extends BaseModel
{
    protected ?ServerRemoteAbility $remoteAbility = null;

    protected ?ServerRemoteAbility $configAbility = null;

    protected ?ServerRemoteAbility $namingAbility = null;

    public function __construct(array $data = [])
    {
        if ($data) {
            foreach ($data as $k => $v) {
                $this->$k = new ServerRemoteAbility($v);
            }
        }
    }

    public function getRemoteAbility(): ?ServerRemoteAbility
    {
        return $this->remoteAbility;
    }

    public function getConfigAbility(): ?ServerRemoteAbility
    {
        return $this->configAbility;
    }

    public function getNamingAbility(): ?ServerRemoteAbility
    {
        return $this->namingAbility;
    }
}
