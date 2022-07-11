<?php

declare(strict_types=1);

namespace Yurun\Nacos\Provider\Config\Model;

use Stringable;

class ListenerItem implements Stringable
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
