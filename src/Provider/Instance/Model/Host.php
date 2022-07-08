<?php

declare(strict_types=1);

namespace Yurun\Nacos\Provider\Instance\Model;

use Yurun\Nacos\Model\BaseModel;

class Host extends BaseModel
{
    protected bool $valid = false;

    protected bool $marked = false;

    protected string $instanceId = '';

    protected string $ip = '';

    protected int $port = 0;

    /**
     * @var float|string
     */
    protected $weight = 0;

    protected array $metadata = [];

    protected bool $healthy = false;

    protected bool $enabled = false;

    protected bool $ephemeral = false;

    protected string $clusterName = '';

    protected string $serviceName = '';

    protected int $instanceHeartBeatInterval = 0;

    protected int $instanceHeartBeatTimeOut = 0;

    protected int $ipDeleteTimeout = 0;

    protected string $instanceIdGenerator = '';

    public function getValid(): bool
    {
        return $this->valid;
    }

    public function getMarked(): bool
    {
        return $this->marked;
    }

    public function getInstanceId(): string
    {
        return $this->instanceId;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return float|string
     */
    public function getWeight()
    {
        return $this->weight;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getHealthy(): bool
    {
        return $this->healthy;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function getEphemeral(): bool
    {
        return $this->ephemeral;
    }

    public function getClusterName(): string
    {
        return $this->clusterName;
    }

    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    public function getInstanceHeartBeatInterval(): int
    {
        return $this->instanceHeartBeatInterval;
    }

    public function getInstanceHeartBeatTimeOut(): int
    {
        return $this->instanceHeartBeatTimeOut;
    }

    public function getIpDeleteTimeout(): int
    {
        return $this->ipDeleteTimeout;
    }

    public function getInstanceIdGenerator(): string
    {
        return $this->instanceIdGenerator;
    }
}
