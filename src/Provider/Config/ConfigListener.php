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

    /**
     * @var string[][][]
     */
    protected array $configs = [];

    /**
     * @var callable[][][]
     */
    protected array $callbacks = [];

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
                    try {
                        $this->configs[$dataId][$group][$tenant] = $value = $this->client->config->get($dataId, $group, $tenant);
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
                    } catch (NacosApiException $e) {
                    }
                }
            }
        }
    }

    public function start(): void
    {
        $this->running = true;
        $configProvider = $this->client->config;
        $listenerConfig = $this->listenerConfig;
        while ($this->running) {
            try {
                if (!$this->listeningConfigs) {
                    usleep(100_000);
                    continue;
                }
                $request = new ListenerRequest();
                foreach ($this->listeningConfigs as $list1) {
                    foreach ($list1 as $list2) {
                        foreach ($list2 as $item) {
                            $request->addListener($item->getDataId(), $item->getGroup(), $item->getContentMD5(), $item->getTenant());
                        }
                    }
                }
                $result = $configProvider->listen($request, $this->listenerConfig->getTimeout());
                foreach ($result as $item) {
                    if ($item->getChanged()) {
                        $dataId = $item->getDataId();
                        $group = $item->getGroup();
                        $tenant = $item->getTenant();
                        if (isset($this->listeningConfigs[$dataId][$group][$tenant])) {
                            $this->configs[$dataId][$group][$tenant] = $value = $configProvider->get($dataId, $group, $tenant);
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
                        if (isset($this->callbacks[$dataId][$group][$tenant])) {
                            $this->callbacks[$dataId][$group][$tenant]($this, $dataId, $group, $tenant);
                        }
                    }
                }
            } catch (\Throwable $th) {
                $this->client->getLogger()->logOrThrow(LogLevel::ERROR, sprintf('Nacos listen failed: %s', $th->getMessage()), [], $th);
                usleep($listenerConfig->getFailedTimeout() * 1000);
            }
        }
    }

    public function stop(): void
    {
        $this->running = false;
    }

    public function addListener(string $dataId, string $group, string $tenant = '', ?callable $callback = null): void
    {
        $this->configs[$dataId][$group][$tenant] = '';
        $this->listeningConfigs[$dataId][$group][$tenant] = new ListenerItem($dataId, $group, '', $tenant);
        if ($callback) {
            $this->callbacks[$dataId][$group][$tenant] = $callback;
        }
    }

    public function removeListener(string $dataId, string $group, string $tenant = ''): void
    {
        if (isset($this->listeningConfigs[$dataId][$group][$tenant])) {
            unset($this->listeningConfigs[$dataId][$group][$tenant]);
        }
        if (isset($this->callbacks[$dataId][$group][$tenant])) {
            unset($this->callbacks[$dataId][$group][$tenant]);
        }
    }

    public function get(string $dataId, string $group, string $tenant = ''): string
    {
        return $this->configs[$dataId][$group][$tenant] ?? '';
    }
}
