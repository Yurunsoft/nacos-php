<?php

declare(strict_types=1);

namespace Yurun\Nacos\Provider\Config\Model;

class ListenerItem implements \Stringable
{
    protected string $dataId = '';

    protected string $group = '';

    protected string $contentMD5 = '';

    protected string $tenant = '';

    public function __construct(string $dataId, string $group, string $contentMD5 = '', string $tenant = '')
    {
        $this->dataId = $dataId;
        $this->group = $group;
        $this->contentMD5 = $contentMD5;
        $this->tenant = $tenant;
    }

    public function getDataId(): string
    {
        return $this->dataId;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function getContentMD5(): string
    {
        return $this->contentMD5;
    }

    public function getTenant(): string
    {
        return $this->tenant;
    }

    public function setDataId(string $dataId): self
    {
        $this->dataId = $dataId;

        return $this;
    }

    public function setGroup(string $group): self
    {
        $this->group = $group;

        return $this;
    }

    public function setContentMD5(string $contentMD5): self
    {
        $this->contentMD5 = $contentMD5;

        return $this;
    }

    public function setTenant(string $tenant): self
    {
        $this->tenant = $tenant;

        return $this;
    }

    public function __toString(): string
    {
        $result = $this->dataId . "\x2" . $this->group . "\x2" . $this->contentMD5;
        if ('' !== $this->tenant) {
            $result .= "\x2" . $this->tenant;
        }
        $result .= "\x1";

        return $result;
    }
}
