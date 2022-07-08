<?php

declare(strict_types=1);

namespace Yurunsoft\Nacos\Test;

use Yurunsoft\Nacos\Provider\Service\ServiceProvider;

class ServiceTest extends BaseTest
{
    public const SERVICE_NAME = 'testService';

    protected function getProvider(): ServiceProvider
    {
        return $this->getClient()->service;
    }

    public function testCreate(): void
    {
        $this->assertTrue($this->getProvider()->create(self::SERVICE_NAME));
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(): void
    {
        $this->assertTrue($this->getProvider()->update(self::SERVICE_NAME, '', '', 0, json_encode(['a' => 123])));
    }

    /**
     * @depends testCreate
     */
    public function testGet(): void
    {
        $response = $this->getProvider()->get(self::SERVICE_NAME);
        $this->assertEquals(self::SERVICE_NAME, $response->getName());
    }

    /**
     * @depends testCreate
     */
    public function testList(): void
    {
        $response = $this->getProvider()->list();
        $this->assertGreaterThan(1, $response->getCount());
        $this->assertTrue(\in_array(self::SERVICE_NAME, $response->getDoms()));
    }

    /**
     * @depends testCreate
     */
    public function testDelete(): void
    {
        $this->assertTrue($this->getProvider()->delete(self::SERVICE_NAME));
    }
}
