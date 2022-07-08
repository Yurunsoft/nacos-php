<?php

declare(strict_types=1);

namespace Yurunsoft\Nacos\Provider\Operator\Model;

use Yurunsoft\Nacos\Provider\Model\BaseResponse;

class MetricsResponse extends BaseResponse
{
    protected string $status = 'UP';

    protected int $serviceCount = 0;

    protected int $instanceCount = 0;

    protected int $subscribeCount = 0;

    protected int $raftNotifyTaskCount = 0;

    protected int $responsibleServiceCount = 0;

    protected int $responsibleInstanceCount = 0;

    protected int $clientCount = 0;

    protected int $connectionBasedClientCount = 0;

    protected int $ephemeralIpPortClientCount = 0;

    protected int $persistentIpPortClientCount = 0;

    protected int $responsibleClientCount = 0;

    /**
     * @var float|string
     */
    protected $cpu = 0;

    /**
     * @var float|string
     */
    protected $load = 0;

    /**
     * @var float|string
     */
    protected $mem = 0;

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getServiceCount(): int
    {
        return $this->serviceCount;
    }

    public function getInstanceCount(): int
    {
        return $this->instanceCount;
    }

    public function getSubscribeCount(): int
    {
        return $this->subscribeCount;
    }

    public function getRaftNotifyTaskCount(): int
    {
        return $this->raftNotifyTaskCount;
    }

    public function getResponsibleServiceCount(): int
    {
        return $this->responsibleServiceCount;
    }

    public function getResponsibleInstanceCount(): int
    {
        return $this->responsibleInstanceCount;
    }

    public function getClientCount(): int
    {
        return $this->clientCount;
    }

    public function getConnectionBasedClientCount(): int
    {
        return $this->connectionBasedClientCount;
    }

    public function getEphemeralIpPortClientCount(): int
    {
        return $this->ephemeralIpPortClientCount;
    }

    public function getPersistentIpPortClientCount(): int
    {
        return $this->persistentIpPortClientCount;
    }

    public function getResponsibleClientCount(): int
    {
        return $this->responsibleClientCount;
    }

    /**
     * @return float|string
     */
    public function getCpu()
    {
        return $this->cpu;
    }

    /**
     * @return float|string
     */
    public function getLoad()
    {
        return $this->load;
    }

    /**
     * @return float|string
     */
    public function getMem()
    {
        return $this->mem;
    }
}
