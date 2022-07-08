<?php

declare(strict_types=1);

namespace Yurunsoft\Nacos\Test;

use Yurunsoft\Nacos\Provider\Instance\InstanceProvider;
use Yurunsoft\Nacos\Provider\Instance\Model\RsInfo;

class InstanceTest extends BaseTest
{
    public const IP = '127.0.0.1';

    public const PORT = 1234;

    public const SERVICE_NAME = 'testInstance';

    public const RETURN_SERVICE_NAME = 'DEFAULT_GROUP@@testInstance';

    protected function getProvider(): InstanceProvider
    {
        return $this->getClient()->instance;
    }

    public function testRegister(): void
    {
        $this->assertTrue($this->getProvider()->register(self::IP, self::PORT, self::SERVICE_NAME));
    }

    public function testUpdate(): void
    {
        $this->assertTrue($this->getProvider()->update(self::IP, self::PORT, self::SERVICE_NAME, '', 1, false));
    }

    public function testBeat(): void
    {
        $beat = new RsInfo();
        $beat->setIp(self::IP);
        $beat->setPort(self::PORT);
        $beat->setServiceName(self::SERVICE_NAME);
        $this->assertTrue($this->getProvider()->beat(self::SERVICE_NAME, $beat));
    }

    public function testList(): void
    {
        $response = $this->getProvider()->list(self::SERVICE_NAME);
        $this->assertEquals(self::RETURN_SERVICE_NAME, $response->getName() ?: $response->getDom());
    }

    public function testDetail(): void
    {
        $response = $this->getProvider()->detail(self::IP, self::PORT, self::SERVICE_NAME);
        $this->assertEquals(self::RETURN_SERVICE_NAME, $response->getService());
    }

    public function testGet(): void
    {
        $response = $this->getProvider()->detail(self::IP, self::PORT, self::SERVICE_NAME);
        $this->assertEquals(self::RETURN_SERVICE_NAME, $response->getService());
    }

    // public function testDeregister(): void
    // {
    //     $this->assertTrue($this->getProvider()->deregister('127.0.0.1', 1234, self::SERVICE_NAME));
    // }
}
