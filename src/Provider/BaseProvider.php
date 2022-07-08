<?php

declare(strict_types=1);

namespace Yurunsoft\Nacos\Provider;

use Yurunsoft\Nacos\Client;

abstract class BaseProvider
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}
