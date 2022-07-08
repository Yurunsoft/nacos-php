<?php

declare(strict_types=1);

namespace Yurun\Nacos\Provider\Instance\Model;

use Yurun\Nacos\Model\BaseModel;

class RsInfo extends BaseModel
{
    /**
     * @var float|string
     */
    protected $load = 0;

    /**
     * @var float|string
     */
    protected $cpu = 0;

    /**
     * @var float|string
     */
    protected $rt = 0;

    /**
     * @var float|string
     */
    protected $qps = 0;

    /**
     * @var float|string
     */
    protected $mem = 0;

    protected int $port = 0;

    protected String $ip = '';

    protected String $serviceName = '';

    protected String $ak = '';

    protected String $cluster = '';

    /**
     * @var float|string
     */
    protected $weight = 0;

    protected bool $ephemeral = true;

    protected array $metadata = [];

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
    public function getCpu()
    {
        return $this->cpu;
    }

    /**
     * @return float|string
     */
    public function getRt()
    {
        return $this->rt;
    }

    /**
     * @return float|string
     */
    public function getQps()
    {
        return $this->qps;
    }

    /**
     * @return float|string
     */
    public function getMem()
    {
        return $this->mem;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    public function getAk(): string
    {
        return $this->ak;
    }

    public function getCluster(): string
    {
        return $this->cluster;
    }

    /**
     * @return float|string
     */
    public function getWeight()
    {
        return $this->weight;
    }

    public function getEphemeral(): bool
    {
        return $this->ephemeral;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @param float|string $load
     */
    public function setLoad($load): self
    {
        $this->load = $load;

        return $this;
    }

    /**
     * @param float|string $cpu
     */
    public function setCpu($cpu): self
    {
        $this->cpu = $cpu;

        return $this;
    }

    /**
     * @param float|string $rt
     */
    public function setRt($rt): self
    {
        $this->rt = $rt;

        return $this;
    }

    /**
     * @param float|string $qps
     */
    public function setQps($qps): self
    {
        $this->qps = $qps;

        return $this;
    }

    /**
     * @param float|string $mem
     */
    public function setMem($mem): self
    {
        $this->mem = $mem;

        return $this;
    }

    public function setPort(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function setServiceName(string $serviceName): self
    {
        $this->serviceName = $serviceName;

        return $this;
    }

    public function setAk(string $ak): self
    {
        $this->ak = $ak;

        return $this;
    }

    public function setCluster(string $cluster): self
    {
        $this->cluster = $cluster;

        return $this;
    }

    /**
     * @param float|string $weight
     */
    public function setWeight($weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function setEphemeral(bool $ephemeral): self
    {
        $this->ephemeral = $ephemeral;

        return $this;
    }

    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }
}
