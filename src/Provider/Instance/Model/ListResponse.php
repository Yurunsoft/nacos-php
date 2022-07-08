<?php

declare(strict_types=1);

namespace Yurun\Nacos\Provider\Instance\Model;

use Yurun\Nacos\Provider\Model\BaseResponse;
use Yurun\Util\YurunHttp\Http\Response;

class ListResponse extends BaseResponse
{
    protected string $dom = '';

    protected int $cacheMillis = 0;

    protected bool $useSpecifiedURL = false;

    /**
     * @var Host[]
     */
    protected array $hosts = [];

    protected string $checksum = '';

    protected int $lastRefTime = 0;

    protected string $env = '';

    protected string $clusters = '';

    protected string $name = '';

    protected string $groupName = '';

    protected bool $allIPs = false;

    protected bool $reachProtectionThreshold = false;

    protected bool $valid = false;

    public function __construct(Response $response)
    {
        parent::__construct($response);
        foreach ($response->json(true) as $k => $v) {
            if ('hosts' === $k) {
                $pageItems = [];
                foreach ($v as $row) {
                    $pageItems[] = new Host($row);
                }
                $v = $pageItems;
            }
            $this->$k = $v;
        }
    }

    public function getDom(): string
    {
        return $this->dom;
    }

    public function getCacheMillis(): int
    {
        return $this->cacheMillis;
    }

    public function getUseSpecifiedURL(): bool
    {
        return $this->useSpecifiedURL;
    }

    /**
     * @return Host[]
     */
    public function getHosts(): array
    {
        return $this->hosts;
    }

    public function getChecksum(): string
    {
        return $this->checksum;
    }

    public function getLastRefTime(): int
    {
        return $this->lastRefTime;
    }

    public function getEnv(): string
    {
        return $this->env;
    }

    public function getClusters(): string
    {
        return $this->clusters;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getGroupName(): string
    {
        return $this->groupName;
    }

    public function getAllIPs(): bool
    {
        return $this->allIPs;
    }

    public function getReachProtectionThreshold(): bool
    {
        return $this->reachProtectionThreshold;
    }

    public function getValid(): bool
    {
        return $this->valid;
    }
}
