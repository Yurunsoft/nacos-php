<?php

declare(strict_types=1);

namespace Yurun\Nacos\Provider\Instance\Model;

use Yurun\Nacos\Provider\Model\BaseResponse;
use Yurun\Nacos\Provider\Traits\TReturnJson;

class DetailResponse extends BaseResponse
{
    use TReturnJson;

    protected string $service = '';

    protected string $instanceId = '';

    protected string $ip = '';

    protected int $port = 0;

    /**
     * @var float|string
     */
    protected $weight = 0;

    protected ?array $metadata = null;

    protected bool $healthy = false;

    protected string $clusterName = '';

    public function getService(): string
    {
        return $this->service;
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

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function getHealthy(): bool
    {
        return $this->healthy;
    }

    public function getClusterName(): string
    {
        return $this->clusterName;
    }
}
