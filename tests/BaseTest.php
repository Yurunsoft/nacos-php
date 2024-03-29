<?php

declare(strict_types=1);

namespace Yurun\Nacos\Test;

use PHPUnit\Framework\TestCase;
use Yurun\Nacos\Client;
use Yurun\Nacos\ClientConfig;

abstract class BaseTest extends TestCase
{
    protected ?Client $client = null;

    protected function getClient(): Client
    {
        return $this->client ??= $this->getNewClient();
    }

    protected function getNewClient(): Client
    {
        return new Client(new ClientConfig([
            'host'                => getenv('NACOS_TEST_HOST') ?: '127.0.0.1',
            'port'                => (int) (getenv('NACOS_TEST_PORT') ?: 8848),
            'username'            => getenv('NACOS_TEST_USERNAME') ?: 'nacos',
            'password'            => getenv('NACOS_TEST_PASSWORD') ?: 'nacos',
            'authorizationBearer' => true,
            'configParser'        => [
                'test' => static fn (string $value): array => ['data' => $value],
            ],
        ]));
    }

    protected function skipNoSwoole(): void
    {
        if (!\defined('SWOOLE_VERSION')) {
            $this->markTestSkipped('no swoole');
        }
    }
}
