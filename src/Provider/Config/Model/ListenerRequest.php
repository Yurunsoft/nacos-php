<?php

declare(strict_types=1);

namespace Yurunsoft\Nacos\Provider\Config\Model;

use Yurunsoft\Nacos\Provider\Model\BaseRequest;

class ListenerRequest extends BaseRequest
{
    /**
     * @var ListenerItem[]
     */
    protected array $listeningConfigs;

    public function addListener(string $dataId, string $group, string $contentMD5 = '', ?string $tenant = null): void
    {
        $this->listeningConfigs[] = new ListenerItem($dataId, $group, $contentMD5, $tenant);
    }

    /**
     * @return mixed
     */
    public function getRequestBody()
    {
        return ['Listening-Configs' => implode('', $this->listeningConfigs)];
    }
}
