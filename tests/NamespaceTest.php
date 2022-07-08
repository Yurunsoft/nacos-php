<?php

declare(strict_types=1);

namespace Yurunsoft\Nacos\Test;

use Yurunsoft\Nacos\Provider\Ns\NamespaceProvider;

class NamespaceTest extends BaseTest
{
    protected function getProvider(): NamespaceProvider
    {
        return $this->getClient()->namespace;
    }

    public function testList(): void
    {
        $list = $this->getProvider()->list();
        $this->assertGreaterThanOrEqual(1, \count($list));
    }

    public function testCreate(): string
    {
        $namespaceId = md5(uniqid('', true));
        $this->assertTrue($this->getProvider()->create(__FUNCTION__, $namespaceId));

        return $namespaceId;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(string $namespaceId): void
    {
        $this->assertTrue($this->getProvider()->update($namespaceId, __FUNCTION__, 'desc'));
    }

    /**
     * @depends testCreate
     */
    public function testDelete(string $namespaceId): void
    {
        $this->assertTrue($this->getProvider()->delete($namespaceId));
    }
}
