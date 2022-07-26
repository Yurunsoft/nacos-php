<?php

declare(strict_types=1);

namespace Yurun\Nacos\Provider\Config;

use Psr\Log\LogLevel;
use Yurun\Nacos\Client;
use Yurun\Nacos\Exception\NacosApiException;
use Yurun\Nacos\Provider\Config\Model\ListenerConfig;
use Yurun\Nacos\Provider\Config\Model\ListenerItem;
use Yurun\Nacos\Provider\Config\Model\ListenerRequest;

class ConfigListener
{
    protected Client $client;

    protected ListenerConfig $listenerConfig;

    /**
     * @var ListenerItem[][][]
     */
    protected array $listeningConfigs = [];

    protected bool $running = false;

    protected array $configs = [];

    public function __construct(Client $client, ListenerConfig $listenerConfig)
    {
        $this->client = $client;
        $this->listenerConfig = $listenerConfig;
    }

    /**
     * Pull all configs.
     */
    public function pull(): void
    {
        $listenerConfig = $this->listenerConfig;
        foreach ($this->listeningConfigs as $list1) {
            foreach ($list1 as $list2) {
                foreach ($list2 as $item) {
                    $dataId = $item->getDataId();
                    $group = $item->getGroup();
                    $tenant = $item->getTenant();
                    $savePath = $listenerConfig->getSavePath();
                    if ('' !== $savePath) {
                        $fileName = ('' === $group ? 'DEFAULT_GROUP' : $group);
                        if ('' !== $tenant) {
                            $fileName = $tenant . '/' . $fileName;
                        }
                        $fileName = $savePath . '/' . $fileName;
                        if (!is_dir($fileName)) {
                            mkdir($fileName, 0777, true);
                        }
                        $fileName .= '/' . $dataId;
                    } else {
                        $fileName = '';
                    }
                    $configItem = &$this->configs[$dataId][$group][$tenant];
                    try {
                        $configItem['value'] = $value = $this->client->config->get($dataId, $group, $tenant, $type);
                        $configItem['type'] = $type;
                        $this->listeningConfigs[$dataId][$group][$tenant]->setContentMD5(md5($value));
                        if ('' !== $fileName) {
                            file_put_contents($fileName, $value);
                        }
                    } catch (NacosApiException $e) {
                        $configItem['value'] = $value = file_get_contents($fileName);
                        $this->listeningConfigs[$dataId][$group][$tenant]->setContentMD5('');
                    }
                }
            }
        }
    }

    public function start(): void
    {
        $this->running = true;
        while ($this->running) {
            if (!$this->listeningConfigs) {
                usleep(100_000);
                continue;
            }
            $this->polling();
        }
    }

    public function polling(?int $timeout = null): void
    {
        try {
            $configProvider = $this->client->config;
            $listenerConfig = $this->listenerConfig;
            $request = new ListenerRequest();
            foreach ($this->listeningConfigs as $list1) {
                foreach ($list1 as $list2) {
                    foreach ($list2 as $item) {
                        $request->addListener($item->getDataId(), $item->getGroup(), $item->getContentMD5(), $item->getTenant());
                    }
                }
            }
            $result = $configProvider->listen($request, $timeout ?? $listenerConfig->getTimeout());
            foreach ($result as $item) {
                if ($item->getChanged()) {
                    $dataId = $item->getDataId();
                    $group = $item->getGroup();
                    $tenant = $item->getTenant();
                    if (isset($this->listeningConfigs[$dataId][$group][$tenant])) {
                        $configItem = &$this->configs[$dataId][$group][$tenant];
                        $configItem['value'] = $value = $configProvider->get($dataId, $group, $tenant, $type);
                        $configItem['type'] = $type;
                        $this->listeningConfigs[$dataId][$group][$tenant]->setContentMD5(md5($value));
                        $savePath = $listenerConfig->getSavePath();
                        if ('' !== $savePath) {
                            $fileName = ('' === $group ? 'DEFAULT_GROUP' : $group);
                            if ('' !== $tenant) {
                                $fileName = $tenant . '/' . $fileName;
                            }
                            $fileName = $savePath . '/' . $fileName;
                            if (!is_dir($fileName)) {
                                mkdir($fileName, 0777, true);
                            }
                            $fileName .= '/' . $dataId;
                            file_put_contents($fileName, $value);
                        }
                    }
                    if (isset($configItem['callback'])) {
                        $configItem['callback']($this, $dataId, $group, $tenant);
                    }
                }
            }
        } catch (\Throwable $th) {
            $this->client->getLogger()->logOrThrow(LogLevel::ERROR, sprintf('Nacos listen failed: %s', $th), [], $th);
            usleep($listenerConfig->getFailedTimeout() * 1000);
        }
    }

    public function stop(): void
    {
        $this->running = false;
    }

    public function addListener(string $dataId, string $group, string $tenant = '', ?callable $callback = null): void
    {
        $this->configs[$dataId][$group][$tenant] = [
            'value'    => '',
            'type'     => 'text',
            'callback' => $callback,
        ];
        $this->listeningConfigs[$dataId][$group][$tenant] = new ListenerItem($dataId, $group, '', $tenant);
    }

    public function removeListener(string $dataId, string $group, string $tenant = ''): void
    {
        if (isset($this->listeningConfigs[$dataId][$group][$tenant])) {
            unset($this->listeningConfigs[$dataId][$group][$tenant]);
        }
    }

    public function get(string $dataId, string $group, string $tenant = '', ?string &$type = null): string
    {
        $type = $this->configs[$dataId][$group][$tenant]['type'] ?? 'text';

        return $this->configs[$dataId][$group][$tenant]['value'] ?? '';
    }

    /**
     * @return array|SimpleXMLElement|mixed
     */
    public function getParsed(string $dataId, string $group, string $tenant = '', ?string &$type = null)
    {
        return $this->client->config->parseConfig($this->get($dataId, $group, $tenant, $type), $type);
    }
}
