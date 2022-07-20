<?php

declare(strict_types=1);

namespace Yurun\Nacos\Test;

use Swoole\Coroutine;
use Swoole\Coroutine\Channel;

use function Swoole\Coroutine\run;

use Yurun\Nacos\Exception\NacosApiException;
use Yurun\Nacos\Provider\Config\ConfigListener;
use Yurun\Nacos\Provider\Config\ConfigProvider;
use Yurun\Nacos\Provider\Config\Model\HistoryItem;
use Yurun\Nacos\Provider\Config\Model\ListenerConfig;
use Yurun\Nacos\Provider\Config\Model\ListenerRequest;
use Yurun\Util\YurunHttp\Http\Psr7\Consts\StatusCode;

class ConfigTest extends BaseTest
{
    public const DATA_ID = 'ConfigTestId';

    public const GROUP_ID = 'ConfigTest';

    public const JSON_DATA_ID = 'json';

    public const JSON_VALUE = '{"id": 19260817}';

    public const XML_DATA_ID = 'xml';

    public const XML_VALUE = <<<XML
    <?xml version="1.0"?>
    <xml><id>19260817</id></xml>

    XML;

    public const YAML_DATA_ID = 'yaml';

    public const YAML_VALUE = 'foo: bar';

    protected function getProvider(): ConfigProvider
    {
        return $this->getClient()->config;
    }

    public function testSet(): void
    {
        $this->assertTrue($this->getProvider()->set(self::DATA_ID, 'ConfigTest', 'value'));
        $this->assertTrue($this->getProvider()->set(self::JSON_DATA_ID, 'ConfigTest', self::JSON_VALUE, '', 'json'));
        $this->assertTrue($this->getProvider()->set(self::XML_DATA_ID, 'ConfigTest', self::XML_VALUE, '', 'xml'));
        if (\function_exists('yaml_parse')) {
            $this->assertTrue($this->getProvider()->set(self::YAML_DATA_ID, 'ConfigTest', self::YAML_VALUE, '', 'yaml'));
        }
        usleep(100000); // If v1.3.x does not wait for set, it will not get the value
    }

    /**
     * @depends testSet
     */
    public function testGet(): bool
    {
        $this->assertEquals('value', $this->getProvider()->get(self::DATA_ID, 'ConfigTest', '', $type));
        if ('' !== $type) {
            $this->assertEquals('text', $type);

            $this->assertEquals(json_decode(self::JSON_VALUE, true), $this->getProvider()->getParsedConfig(self::JSON_DATA_ID, 'ConfigTest', '', $type));
            $this->assertEquals('json', $type);

            /** @var \SimpleXMLElement $xml */
            $xml = $this->getProvider()->getParsedConfig(self::XML_DATA_ID, 'ConfigTest', '', $type);
            $this->assertEquals(self::XML_VALUE, $xml->saveXML());
            $this->assertEquals('xml', $type);

            return true;
        } else {
            return false;
        }
    }

    /**
     * @depends testGet
     */
    public function testGetYaml(bool $supportType): void
    {
        if (!$supportType) {
            $this->markTestSkipped('not support type');
        }
        if (!\function_exists('yaml_parse')) {
            $this->markTestSkipped('no yaml');
        }
        $this->assertEquals(yaml_parse(self::YAML_VALUE), $this->getProvider()->getParsedConfig(self::YAML_DATA_ID, 'ConfigTest', '', $type));
        $this->assertEquals('yaml', $type);
    }

    /**
     * @depends testSet
     */
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

    /**
     * @depends testGet
     */
    public function testListener(bool $supportType): void
    {
        $this->skipNoSwoole();
        $exception = null;
        run(function () use (&$exception, $supportType) {
            try {
                $channel = new Channel();
                $content = json_encode(['id' => 1]);
                Coroutine::create(function () use ($content, $channel, $supportType, &$exception) {
                    try {
                        $client = $this->getNewClient();
                        $config = $client->config;
                        $request = new ListenerRequest();
                        $request->addListener(self::DATA_ID, self::GROUP_ID);
                        $channel->push(1);
                        $items = $config->listen($request);
                        $this->assertCount(1, $items);
                        $response = $items[0];
                        $this->assertTrue($response->getChanged());
                        $this->assertEquals(self::DATA_ID, $response->getDataId());
                        $this->assertEquals(self::GROUP_ID, $response->getGroup());
                        $this->assertEquals('', $response->getTenant());
                        $this->assertEquals($content, $config->get(self::DATA_ID, self::GROUP_ID, '', $type));
                        if ($supportType) {
                            $this->assertEquals('json', $type);
                            $type = null;
                            $this->assertEquals(json_decode($content, true), $config->getParsedConfig(self::DATA_ID, self::GROUP_ID, '', $type));
                            $this->assertEquals('json', $type);
                        }
                    } catch (\Throwable $exception) {
                    }
                });
                $channel->pop(5);
                $this->assertTrue($this->getNewClient()->config->set(self::DATA_ID, self::GROUP_ID, $content, '', 'json'));
            } catch (\Throwable $exception) {
            }
        });
        if ($exception) {
            throw $exception;
        }
    }

    /**
     * @depends testSet
     *
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

    /**
     * @depends testSet
     */
    public function testConfigListener(): void
    {
        $this->skipNoSwoole();
        $exception = null;
        run(function () use (&$exception) {
            try {
                $num = mt_rand();
                $configProvider = $this->getProvider();
                $configProvider->set(self::DATA_ID, self::GROUP_ID, (string) $num);
                usleep(100000); // If v1.3.x does not wait for set, it will not get the value
                $content = (string) ($num + 1);
                $listenerConfig = new ListenerConfig();
                $listenerConfig->setSavePath(\dirname(__DIR__) . '/tmp-config');
                $listener = $configProvider->getConfigListener($listenerConfig);
                $channel = new Channel();
                $listener->addListener(self::DATA_ID, self::GROUP_ID, '', function (ConfigListener $listener, string $dataId, string $group, string $tenant) use ($channel) {
                    $listener->stop();
                    $channel->push($listener->get($dataId, $group, $tenant));
                });
                $listener->pull();
                $this->assertEquals((string) $num, $listener->get(self::DATA_ID, self::GROUP_ID));
                Coroutine::create(function () use ($configProvider, $content) {
                    usleep(1);
                    $configProvider->set(self::DATA_ID, self::GROUP_ID, $content);
                });
                $listener->start();
                $value = $channel->pop(5);
                $this->assertEquals($content, $value);
                $fileName = \dirname(__DIR__) . '/tmp-config/' . self::GROUP_ID . '/' . self::DATA_ID;
                $this->assertTrue(is_file($fileName));
                $this->assertEquals($content, file_get_contents($fileName));
            } catch (\Throwable $exception) {
            }
        });
        if ($exception) {
            throw $exception;
        }
    }
}
