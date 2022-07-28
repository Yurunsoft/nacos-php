<?php

declare(strict_types=1);

namespace Yurun\Nacos\Provider\Config;

use Psr\Log\LogLevel;
use Yurun\Nacos\Client;
use Yurun\Nacos\Exception\NacosApiException;
use Yurun\Nacos\Exception\NacosException;
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
    public function pull(bool $force = true): void
    {
        $listenerConfig = $this->listenerConfig;
        foreach ($this->listeningConfigs as $list1) {
            foreach ($list1 as $list2) {
                foreach ($list2 as $item) {
                    $dataId = $item->getDataId();
                    $group = $item->getGroup();
                    $tenant = $item->getTenant();
                    if ($force || !$this->loadCache($dataId, $group, $tenant, $listenerConfig->getFileCacheTime())) {
                        try {
                            $configItem = &$this->configs[$dataId][$group][$tenant];
                            $configItem['value'] = $value = $this->client->config->get($dataId, $group, $tenant, $type);
                            $configItem['type'] = $type;
                            $this->listeningConfigs[$dataId][$group][$tenant]->setContentMD5(md5($value));
                            $this->saveCache($dataId, $group, $tenant, $value, $type);
                        } catch (NacosApiException $e) {
                            if ($this->loadCache($dataId, $group, $tenant)) {
                                $this->listeningConfigs[$dataId][$group][$tenant]->setContentMD5('');
                            } else {
                                $this->client->getLogger()->logOrThrow(LogLevel::ERROR, sprintf('Nacos pull failed: %s', $e), [], $e);
                            }
                        }
                    } elseif ($this->loadCache($dataId, $group, $tenant)) {
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
                        $this->saveCache($dataId, $group, $tenant, $value, $type);
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

    protected function saveCache(string $dataId, string $group, string $tenant, string $value, string $type): bool
    {
        $savePath = $this->listenerConfig->getSavePath();
        if ('' === $savePath) {
            return false;
        }
        $group = ('' === $group ? 'DEFAULT_GROUP' : $group);
        $fileName = $savePath . '/' . $group . ('' === $tenant ? '' : ('/' . $tenant));
        if (!is_dir($fileName)) {
            mkdir($fileName, 0777, true);
        }
        $fileName .= '/' . $dataId;
        file_put_contents($fileName, $value);
        file_put_contents($fileName . '.meta', json_encode([
            'type'           => $type,
            'lastUpdateTime' => time(),
        ]));

        return true;
    }

    protected function loadCache(string $dataId, string $group, string $tenant, int $fileCacheTime = 0): bool
    {
        $savePath = $this->listenerConfig->getSavePath();
        if ('' === $savePath) {
            return false;
        }
        $group = ('' === $group ? 'DEFAULT_GROUP' : $group);
        $fileName = $savePath . '/' . $group . ('' === $tenant ? '' : ('/' . $tenant)) . '/' . $dataId;
        if (!is_file($fileName)) {
            return false;
        }
        $metaFileName = $fileName . '.meta';
        if (is_file($metaFileName)) {
            $value = file_get_contents($metaFileName);
            if (false === $value) {
                throw new NacosException(sprintf('Failed to read the contents of file %s', $metaFileName));
            }
            $meta = json_decode($value, true);
            if (!$meta) {
                return false;
            }
        } else {
            $meta = [];
        }
        if ($fileCacheTime > 0 && (time() - ($meta['lastUpdateTime'] ?? 0) > $fileCacheTime)) {
            return false;
        }
        $value = file_get_contents($fileName);
        if (false === $value) {
            throw new NacosException(sprintf('Failed to read the contents of file %s', $fileName));
        }
        $configItem = &$this->configs[$dataId][$group][$tenant];
        $configItem['value'] = $value;
        $configItem['type'] = $meta['type'] ?? 'text';

        return true;
    }
}
