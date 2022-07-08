<?php

declare(strict_types=1);

namespace Yurunsoft\Nacos\Provider\Config\Model;

trait THistoryItem
{
    protected string $id = '';

    protected int $lastId = -1;

    protected string $dataId = '';

    protected string $group = '';

    protected string $tenant = '';

    protected string $appName = '';

    protected ?string $md5 = null;

    protected ?string $content = null;

    protected string $srcIp = '';

    protected ?string $srcUser = null;

    protected string $opType = '';

    protected string $createdTime = '';

    protected string $lastModifiedTime = '';

    protected string $type = '';

    public function getId(): string
    {
        return $this->id;
    }

    public function getLastId(): int
    {
        return $this->lastId;
    }

    public function getDataId(): string
    {
        return $this->dataId;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function getTenant(): string
    {
        return $this->tenant;
    }

    public function getAppName(): string
    {
        return $this->appName;
    }

    public function getMd5(): ?string
    {
        return $this->md5;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getSrcIp(): string
    {
        return $this->srcIp;
    }

    public function getSrcUser(): string
    {
        return $this->srcUser;
    }

    public function getOpType(): string
    {
        return $this->opType;
    }

    public function getCreatedTime(): string
    {
        return $this->createdTime;
    }

    public function getLastModifiedTime(): string
    {
        return $this->lastModifiedTime;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
