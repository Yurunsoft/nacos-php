<?php

declare(strict_types=1);

namespace Yurunsoft\Nacos;

use Yurunsoft\Nacos\Model\BaseModel;

class ClientConfig extends BaseModel
{
    protected string $host = '127.0.0.1';

    protected int $port = 8848;

    protected string $prefix = '/';

    protected string $username = '';

    protected string $password = '';

    /**
     * Timeout time, in milliseconds.
     */
    protected int $timeout = 60000;

    protected bool $ssl = false;

    protected bool $authorizationBearer = false;

    public function getHost(): string
    {
        return $this->host;
    }

    public function setHost(int $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function setPort(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get timeout time, in milliseconds.
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * Set timeout time, in milliseconds.
     */
    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function getSsl(): bool
    {
        return $this->ssl;
    }

    public function setSsl(bool $ssl): self
    {
        $this->ssl = $ssl;

        return $this;
    }

    public function getAuthorizationBearer(): bool
    {
        return $this->authorizationBearer;
    }

    public function setAuthorizationBearer(bool $authorizationBearer): self
    {
        $this->authorizationBearer = $authorizationBearer;

        return $this;
    }
}
