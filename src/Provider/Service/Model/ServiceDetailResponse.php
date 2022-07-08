<?php

declare(strict_types=1);

namespace Yurun\Nacos\Provider\Service\Model;

use Yurun\Nacos\Provider\Model\BaseResponse;
use Yurun\Util\YurunHttp\Http\Response;

class ServiceDetailResponse extends BaseResponse
{
    protected string $namespaceId = '';

    protected string $groupName = '';

    protected string $name = '';

    /**
     * @var float|string
     */
    protected $protectThreshold = 0;

    protected array $metadata = [];

    protected array $selector = [];

    /**
     * @var Cluster[]
     */
    protected array $clusters = [];

    public function __construct(Response $response)
    {
        parent::__construct($response);
        foreach ($response->json(true) as $k => $v) {
            if ('clusters' === $k) {
                $clusters = [];
                foreach ($v as $row) {
                    $clusters[] = new Cluster($row);
                }
                $v = $clusters;
            }
            $this->$k = $v;
        }
    }

    public function getNamespaceId(): string
    {
        return $this->namespaceId;
    }

    public function getGroupName(): string
    {
        return $this->groupName;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return float|string
     */
    public function getProtectThreshold()
    {
        return $this->protectThreshold;
    }

    /**
     * Get the value of metadata.
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getSelector(): array
    {
        return $this->selector;
    }

    /**
     * @return Cluster[]
     */
    public function getClusters(): array
    {
        return $this->clusters;
    }
}
