<?php

declare(strict_types=1);

namespace Yurunsoft\Nacos\Provider\Config\Model;

use Yurunsoft\Nacos\Model\BaseModel;

class ListenerResponseItem extends BaseModel
{
    protected bool $changed = false;

    protected string $dataId = '';

    protected string $group = '';

    protected string $tenant = '';

    public static function createFromListener(string $content): self
    {
        $instance = new self();
        if ($instance->changed = ('' !== $content)) {
            $splited = explode('%02', $content);
            $instance->dataId = $splited[0] ?? '';
            $instance->group = $splited[1] ?? '';
            $instance->tenant = $splited[2] ?? '';
        }

        return $instance;
    }

    public function getChanged(): bool
    {
        return $this->changed;
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
}
