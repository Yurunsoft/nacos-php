<?php

declare(strict_types=1);

namespace Yurunsoft\Nacos\Provider\Auth;

use Yurun\Util\YurunHttp\Http\Psr7\Consts\RequestMethod;
use Yurunsoft\Nacos\Client;
use Yurunsoft\Nacos\Provider\Auth\Model\LoginResponse;
use Yurunsoft\Nacos\Provider\BaseProvider;

class AuthProvider extends BaseProvider
{
    protected ?LoginResponse $lastLoginResponse = null;

    protected int $expireTime = 0;

    protected bool $accessTokenRequired = false;

    public function __construct(Client $client)
    {
        parent::__construct($client);
        $config = $client->getConfig();
        $this->accessTokenRequired = '' !== $config->getUsername() && '' !== $config->getPassword();
    }

    public function login(bool $cached = true): LoginResponse
    {
        if ($cached && !$this->isExpired()) {
            return $this->lastLoginResponse;
        }

        $config = $this->client->getConfig();
        /** @var LoginResponse $response */
        $response = $this->client->request('nacos/v1/auth/login', [
            'username' => $config->getUsername(),
            'password' => $config->getPassword(),
        ], RequestMethod::POST, [], LoginResponse::class, false);

        if ($cached) {
            $this->expireTime = time() + $response->getTokenTtl();
            $this->lastLoginResponse = $response;
        }

        return $response;
    }

    public function getAccessToken(): string
    {
        return $this->login()->getAccessToken();
    }

    public function setAccessToken(): string
    {
        return $this->login()->getAccessToken();
    }

    public function isExpired(): bool
    {
        return 0 === $this->expireTime || null === $this->lastLoginResponse || time() >= $this->expireTime;
    }

    public function isAccessTokenRequired(): bool
    {
        return $this->accessTokenRequired;
    }
}
