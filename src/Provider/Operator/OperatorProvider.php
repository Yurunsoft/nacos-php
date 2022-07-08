<?php

declare(strict_types=1);

namespace Yurunsoft\Nacos\Provider\Operator;

use Yurun\Util\YurunHttp\Http\Psr7\Consts\RequestMethod;
use Yurunsoft\Nacos\Provider\BaseProvider;
use Yurunsoft\Nacos\Provider\Operator\Model\Leader;
use Yurunsoft\Nacos\Provider\Operator\Model\MetricsResponse;
use Yurunsoft\Nacos\Provider\Operator\Model\Server;
use Yurunsoft\Nacos\Provider\Operator\Model\SwitchesResponse;
use Yurunsoft\Nacos\Util\StringUtil;

class OperatorProvider extends BaseProvider
{
    public const OPERATION_SWITCHES_API_APTH = 'nacos/v1/ns/operator/switches';

    public function switches(): SwitchesResponse
    {
        return $this->client->request(self::OPERATION_SWITCHES_API_APTH, [], RequestMethod::GET, [], SwitchesResponse::class);
    }

    public function updateSwitches(string $entry, string $value, bool $debug = false): bool
    {
        return 'ok' === $this->client->request(self::OPERATION_SWITCHES_API_APTH, [
            'entry' => $entry,
            'value' => $value,
            'debug' => StringUtil::convertBoolToString($debug),
        ], RequestMethod::PUT)->body();
    }

    public function metrics(bool $onlyStatus = true): MetricsResponse
    {
        return $this->client->request('nacos/v1/ns/operator/metrics', [
            'onlyStatus' => StringUtil::convertBoolToString($onlyStatus),
        ], RequestMethod::GET, [], MetricsResponse::class);
    }

    /**
     * @return Server[]
     */
    public function servers(bool $healthy = false): array
    {
        $response = $this->client->request('nacos/v1/ns/operator/servers', [
            'healthy' => StringUtil::convertBoolToString($healthy),
        ], RequestMethod::GET);
        $result = [];
        foreach ($response->json(true)['servers'] as $row) {
            $result[] = new Server($row);
        }

        return $result;
    }

    public function leader(): Leader
    {
        $response = $this->client->request('nacos/v1/ns/raft/leader', [], RequestMethod::GET);

        return new Leader($response->json(true)['leader']);
    }

    public function updateHealth(string $serviceName, string $ip, int $port, bool $healthy, string $namespaceId = '', string $groupName = '', string $clusterName = ''): bool
    {
        return 'ok' === $this->client->request('nacos/v1/ns/health/instance', [
            'serviceName' => $serviceName,
            'ip'          => $ip,
            'port'        => $port,
            'healthy'     => StringUtil::convertBoolToString($healthy),
            'namespaceId' => $namespaceId,
            'groupName'   => $groupName,
            'clusterName' => $clusterName,
        ], RequestMethod::PUT)->body();
    }
}
