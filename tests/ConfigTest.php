<?php

declare(strict_types=1);

namespace Yurunsoft\Nacos\Test;

use Swoole\Coroutine;
use function Swoole\Coroutine\run;
use Yurun\Util\YurunHttp\Http\Psr7\Consts\StatusCode;
use Yurunsoft\Nacos\Exception\NacosApiException;
use Yurunsoft\Nacos\Provider\Config\ConfigProvider;
use Yurunsoft\Nacos\Provider\Config\Model\HistoryItem;
use Yurunsoft\Nacos\Provider\Config\Model\ListenerRequest;

class ConfigTest extends BaseTest
{
    public const DATA_ID = 'ConfigTestId';

    public const GROUP_ID = 'ConfigTest';

    protected function getProvider(): ConfigProvider
    {
        return $this->getClient()->config;
    }

    public function testSet(): void
    {
        $this->assertTrue($this->getProvider()->set(self::DATA_ID, 'ConfigTest', 'value'));
    }

    public function testGet(): void
    {
        $provider = $this->getProvider();
        $this->assertTrue($provider->set(self::DATA_ID, 'ConfigTest', 'value'));
        $this->assertEquals('value', $provider->get(self::DATA_ID, 'ConfigTest'));
    }

    public function testDelete(): void
    {
        $provider = $this->getProvider();
        $this->assertTrue($provider->set(self::DATA_ID, 'ConfigTest', 'value'));
        $this->assertEquals('value', $provider->get(self::DATA_ID, 'ConfigTest'));
        $this->assertTrue($provider->delete(self::DATA_ID, 'ConfigTest'));
        try {
            $this->assertEquals('value', $provider->get(self::DATA_ID, 'ConfigTest'));
            $this->assertTrue(false);
        } catch (NacosApiException $e) {
            $this->assertEquals(StatusCode::NOT_FOUND, $e->getResponse()->getStatusCode());
        }
    }

    public function testListener(): void
    {
        $this->skipNoSwoole();
        $exception = null;
        run(function () use (&$exception) {
            try {
                $content = (string) mt_rand();
                Coroutine::create(function () use ($content, &$exception) {
                    try {
                        $client = $this->getNewClient();
                        $config = $client->config;
                        $request = new ListenerRequest();
                        $request->addListener(self::DATA_ID, self::GROUP_ID);
                        $items = $config->listen($request);
                        $this->assertCount(1, $items);
                        $response = $items[0];
                        $this->assertTrue($response->getChanged());
                        $this->assertEquals(self::DATA_ID, $response->getDataId());
                        $this->assertEquals(self::GROUP_ID, $response->getGroup());
                        $this->assertEquals('', $response->getTenant());
                        $this->assertEquals($content, $config->get(self::DATA_ID, self::GROUP_ID));
                    } catch (\Throwable $exception) {
                    }
                });
                $this->assertTrue($this->getNewClient()->config->set(self::DATA_ID, self::GROUP_ID, $content));
            } catch (\Throwable $exception) {
            }
        });
        if ($exception) {
            throw $exception;
        }
    }

    /**
     * @return HistoryItem[]
     */
    public function testHistoryList(): array
    {
        $response = $this->getProvider()->historyList(self::DATA_ID, self::GROUP_ID, '', 1, 2);
        $this->assertGreaterThan(1, $response->getTotalCount());
        $this->assertEquals(1, $response->getPageNumber());
        $this->assertGreaterThan(1, $response->getPagesAvailable());
        $items = $response->getPageItems();
        $this->assertCount(2, $items);
        $this->assertEquals(self::DATA_ID, $items[0]->getDataId());
        $this->assertEquals(self::GROUP_ID, $items[0]->getGroup());

        return $items;
    }

    /**
     * @depends testHistoryList
     *
     * @param HistoryItem[] $items
     */
    public function testHistory(array $items): void
    {
        $response = $this->getProvider()->history($items[0]->getId(), self::DATA_ID, self::GROUP_ID);
        $this->assertEquals($items[0]->getId(), $response->getId());
        $this->assertEquals(self::DATA_ID, $response->getDataId());
        $this->assertEquals(self::GROUP_ID, $response->getGroup());
    }
}
