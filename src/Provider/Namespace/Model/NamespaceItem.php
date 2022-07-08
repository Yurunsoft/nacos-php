<?php

declare(strict_types=1);

namespace Yurunsoft\Nacos\Provider\Namespace\Model;

use Yurunsoft\Nacos\Model\BaseModel;

class NamespaceItem extends BaseModel
{
    protected string $namespace = '';

    protected string $namespaceShowName = '';

    protected int $quota = 0;

    protected int $configCount = 0;

    protected int $type = 0;

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getNamespaceShowName(): string
    {
        return $this->namespaceShowName;
    }

    public function getQuota(): int
    {
        return $this->quota;
    }

    public function getConfigCount(): int
    {
        return $this->configCount;
    }

    public function getType(): int
    {
        return $this->type;
    }
}
