<?php

declare(strict_types=1);

namespace Yurun\Nacos\Test;

use Yurun\Nacos\Client;
use Yurun\Nacos\ClientConfig;
use Yurun\Nacos\Provider\Auth\AuthProvider;

class AuthTest extends BaseTest
{
    protected function getProvider(): AuthProvider
    {
        return $this->getClient()->auth;
    }

    public function testLogin(): void
    {
        $response1 = $this->getProvider()->login();
        $response2 = $this->getProvider()->login(false);
        $this->assertNotEquals($response1, $response2);
    }

    public function testGetAccessToken(): void
    {
        $auth = $this->getNewClient()->auth;
        $response = $auth->login();
        $this->assertEquals($response->getAccessToken() ?: (explode(' ', $response->getData())[1] ?? ''), $auth->getAccessToken());
    }

    public function testGetIsExpired(): void
    {
        $auth = $this->getNewClient()->auth;
        $this->assertTrue($auth->isExpired());
        $auth->login();
        $this->assertFalse($auth->isExpired());
    }

    public function testIsAccessTokenRequired(): void
    {
        $auth = $this->getNewClient()->auth;
        $this->assertTrue($auth->isAccessTokenRequired());

        $client = new Client(new ClientConfig());
        $this->assertFalse($client->auth->isAccessTokenRequired());
    }
}
