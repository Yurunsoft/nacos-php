<?php

declare(strict_types=1);

namespace Yurun\Nacos\Provider\Instance;

use Yurun\Nacos\Provider\BaseProvider;
use Yurun\Nacos\Provider\Instance\Model\DetailResponse;
use Yurun\Nacos\Provider\Instance\Model\ListResponse;
use Yurun\Nacos\Provider\Instance\Model\RsInfo;
use Yurun\Nacos\Util\StringUtil;
use Yurun\Util\YurunHttp\Http\Psr7\Consts\RequestMethod;

class InstanceProvider extends BaseProvider
{
    public const INSTANCE_API_APTH = 'nacos/v1/ns/instance';

    /**
     * @param string|float $weight
     */
    public function register(string $ip, int $port, string $serviceName, string $namespaceId = '', $weight = 1, bool $enabled = true, bool $healthy = true, string $metadata = '', string $clusterName = '', string $groupName = '', bool $ephemeral = false): bool
    {
        return 'ok' === $this->client->request(self::INSTANCE_API_APTH, [
            'ip'          => $ip,
            'port'        => $port,
            'namespaceId' => $namespaceId,
            'weight'      => $weight,
            'enabled'     => StringUtil::convertBoolToString($enabled),
            'healthy'     => StringUtil::convertBoolToString($healthy),
            'metadata'    => $metadata,
            'clusterName' => $clusterName,
            'serviceName' => $serviceName,
            'groupName'   => $groupName,
            'ephemeral'   => StringUtil::convertBoolToString($ephemeral),
        ], RequestMethod::POST)->body();
    }

    public function deregister(string $ip, int $port, string $serviceName, string $namespaceId = '', string $clusterName = '', string $groupName = '', bool $ephemeral = false): bool
    {
        return 'ok' === $this->client->request(self::INSTANCE_API_APTH, [
            'ip'          => $ip,
            'port'        => $port,
            'namespaceId' => $namespaceId,
            'clusterName' => $clusterName,
            'serviceName' => $serviceName,
            'groupName'   => $groupName,
            'ephemeral'   => $ephemeral,
        ], RequestMethod::DELETE)->body();
    }

    /**
     * @param string|float $weight
     */
    public function update(string $ip, int $port, string $serviceName, string $namespaceId = '', $weight = 1, bool $enabled = true, bool $healthy = true, string $metadata = '', string $clusterName = '', string $groupName = '', bool $ephemeral = false): bool
    {
        return 'ok' === $this->client->request(self::INSTANCE_API_APTH, [
            'ip'          => $ip,
            'port'        => $port,
            'namespaceId' => $namespaceId,
            'weight'      => $weight,
            'enabled'     => StringUtil::convertBoolToString($enabled),
            'healthy'     => StringUtil::convertBoolToString($healthy),
            'metadata'    => $metadata,
            'clusterName' => $clusterName,
            'serviceName' => $serviceName,
            'groupName'   => $groupName,
            'ephemeral'   => StringUtil::convertBoolToString($ephemeral),
        ], RequestMethod::PUT)->body();
    }

    /**
     * @param string|string[] $clusters
     */
    public function list(string $serviceName, string $groupName = '', string $namespaceId = '', $clusters = '', bool $healthyOnly = false): ListResponse
    {
        return $this->client->request('nacos/v1/ns/instance/list', [
            'serviceName' => $serviceName,
            'groupName'   => $groupName,
            'namespaceId' => $namespaceId,
            'clusters'    => \is_array($clusters) ? implode(',', $clusters) : $clusters,
            'healthyOnly' => StringUtil::convertBoolToString($healthyOnly),
        ], RequestMethod::GET, [], ListResponse::class);
    }

    /**
     * @param string|string[] $clusters
     */
    public function detail(string $ip, int $port, string $serviceName, string $groupName = '', string $namespaceId = '', $clusters = '', bool $healthyOnly = false, bool $ephemeral = false): DetailResponse
    {
        return $this->client->request(self::INSTANCE_API_APTH, [
            'ip'          => $ip,
            'port'        => $port,
            'serviceName' => $serviceName,
            'groupName'   => $groupName,
            'namespaceId' => $namespaceId,
            'clusters'    => \is_array($clusters) ? implode(',', $clusters) : $clusters,
            'healthyOnly' => StringUtil::convertBoolToString($healthyOnly),
            'ephemeral'   => StringUtil::convertBoolToString($ephemeral),
        ], RequestMethod::GET, [], DetailResponse::class);
    }

    public function beat(string $serviceName, RsInfo $beat, string $groupName = '', string $namespaceId = '', bool $ephemeral = false): bool
    {
        return 'ok' === $this->client->request(self::INSTANCE_API_APTH, [
            'serviceName' => $serviceName,
            'ip'          => $beat->getIp(),
            'port'        => $beat->getPort(),
            'namespaceId' => $namespaceId,
            'beat'        => json_encode($beat),
            'groupName'   => $groupName,
            'ephemeral'   => StringUtil::convertBoolToString($ephemeral),
        ], RequestMethod::PUT)->body();
    }
}
