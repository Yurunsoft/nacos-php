<?php

declare(strict_types=1);

namespace Yurun\Nacos\Provider\Config\Model;

use Yurun\Nacos\Model\BaseModel;

class ListenerConfig extends BaseModel
{
    /**
     * Listener timeout time, in milliseconds.
     */
    protected int $timeout = 30000;

    protected string $savePath = '';

    /**
     * Get Listener timeout time, in milliseconds.
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * Set Listener timeout time, in milliseconds.
     */
    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function getSavePath(): string
    {
        return $this->savePath;
    }

    public function setSavePath(string $savePath): self
    {
        $this->savePath = $savePath;

        return $this;
    }
}
