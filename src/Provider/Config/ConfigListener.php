<?php

declare(strict_types=1);

namespace Yurun\Nacos\Provider\Config;

use Psr\Log\LogLevel;
use Yurun\Nacos\Provider\Config\Model\ListenerItem;
use Yurun\Nacos\Provider\Config\Model\ListenerRequest;

class ConfigListener
{
    protected ConfigProvider $configProvider;

    /**
     * @var ListenerItem[][][]
     */
    protected array $listeningConfigs;

    protected bool $running = false;

    /**
     * @var string[][][]
     */
    protected array $configs = [];

    /**
     * @var callable[][][]
     */
    protected array $callbacks = [];

    public function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    public function start(): void
    {
        $this->running = true;
        while ($this->running) {
            try {
                $request = new ListenerRequest();
                foreach ($this->listeningConfigs as $list1) {
                    foreach ($list1 as $list2) {
                        foreach ($list2 as $item) {
                            $request->addListener($item->getDataId(), $item->getGroup(), $item->getContentMD5(), $item->getTenant());
                        }
                    }
                }
                $result = $this->configProvider->listen($request, $this->configProvider->getClient()->getConfig()->getListenerTimeout());
                foreach ($result as $item) {
                    if ($item->getChanged()) {
                        $dataId = $item->getDataId();
                        $group = $item->getGroup();
                        $tenant = $item->getTenant();
                        if (isset($this->listeningConfigs[$dataId][$group][$tenant])) {
                            $this->configs[$dataId][$group][$tenant] = $value = $this->configProvider->get($dataId, $group, $tenant);
                            $this->listeningConfigs[$dataId][$group][$tenant]->setContentMD5(md5($value));
                        }
                        if (isset($this->callbacks[$dataId][$group][$tenant])) {
                            $this->callbacks[$dataId][$group][$tenant]($this, $dataId, $group, $tenant);
                        }
                    }
                }
            } catch (\Throwable $th) {
                $this->configProvider->getClient()->getLogger()->logOrThrow(LogLevel::ERROR, sprintf('Nacos listen failed: %s', $th->getMessage()), [], $th);
            }
        }
    }

    public function stop(): void
    {
        $this->running = false;
    }

    public function addListener(string $dataId, string $group, string $tenant = '', ?callable $callback = null): void
    {
        $this->configs[$dataId][$group][$tenant] = $value = $this->configProvider->get($dataId, $group, $tenant);
        $this->listeningConfigs[$dataId][$group][$tenant] = new ListenerItem($dataId, $group, md5($value), $tenant);
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

    public function get(string $dataId, string $group, string $tenant): string
    {
        return $this->configs[$dataId][$group][$tenant] ?? '';
    }
}
