<?php

declare(strict_types=1);

namespace Yurun\Nacos\Test;

use Yurun\Nacos\Provider\Operator\OperatorProvider;

class OperatorTest extends BaseTest
{
    public const SERVICE_NAME = 'testService';

    protected function getProvider(): OperatorProvider
    {
        return $this->getClient()->operator;
    }

    public function testSwitches(): void
    {
        $this->getProvider()->switches();
        $this->assertTrue(true);
    }

    public function testUpdateSwitches(): void
    {
        $this->assertTrue($this->getProvider()->updateSwitches('test', 'test'));
    }

    public function testMetrics(): void
    {
        $this->getProvider()->metrics(true);
        $this->getProvider()->metrics(false);
        $this->assertTrue(true);
    }

    public function testServers(): void
    {
        $this->assertGreaterThan(0, $this->getProvider()->servers());
    }
}
