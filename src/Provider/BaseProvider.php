<?php

declare(strict_types=1);

namespace Yurun\Nacos\Provider;

use Yurun\Nacos\Client;

abstract class BaseProvider
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
