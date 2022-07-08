<?php

declare(strict_types=1);

namespace Yurun\Nacos\Provider\Operator\Model;

use Yurun\Nacos\Model\BaseModel;

class Server extends BaseModel
{
    protected string $ip = '';

    protected int $port = -1;

    protected string $state = '';

    /**
     * @var array<string, array>
     */
    protected array $extendInfo = [];

    protected string $address = '';

    protected int $failAccessCnt = 0;

    protected ?ServerAbilities $abilities = null;

    public function __construct(array $data = [])
    {
        if ($data) {
            foreach ($data as $k => $v) {
                if ('abilities' === $k) {
                    $v = new ServerAbilities($v);
                }
                $this->$k = $v;
            }
        }
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @return array<string, array>
     */
    public function getExtendInfo(): array
    {
        return $this->extendInfo;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getFailAccessCnt(): int
    {
        return $this->failAccessCnt;
    }

    public function getAbilities(): ?ServerAbilities
    {
        return $this->abilities;
    }
}
