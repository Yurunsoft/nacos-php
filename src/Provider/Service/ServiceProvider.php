<?php

declare(strict_types=1);

namespace Yurunsoft\Nacos\Provider\Service;

use Yurun\Util\YurunHttp\Http\Psr7\Consts\RequestMethod;
use Yurunsoft\Nacos\Provider\BaseProvider;
use Yurunsoft\Nacos\Provider\Service\Model\ListResponse;
use Yurunsoft\Nacos\Provider\Service\Model\ServiceDetailResponse;

class ServiceProvider extends BaseProvider
{
    public const SERVICE_API_APTH = 'nacos/v1/ns/service';

    /**
     * @param string|float $protectThreshold
     */
    public function create(string $serviceName, string $groupName = '', string $namespaceId = '', $protectThreshold = 0, string $metadata = '', string $selector = ''): bool
    {
        return 'ok' === $this->client->request(self::SERVICE_API_APTH, [
            'serviceName'      => $serviceName,
            'groupName'        => $groupName,
            'namespaceId'      => $namespaceId,
            'protectThreshold' => $protectThreshold,
            'metadata'         => $metadata,
            'selector'         => $selector,
        ], RequestMethod::POST)->body();
    }

    /**
     * @param string|float $protectThreshold
     */
    public function delete(string $serviceName, string $groupName = '', string $namespaceId = ''): bool
    {
        return 'ok' === $this->client->request(self::SERVICE_API_APTH, [
            'serviceName'      => $serviceName,
            'groupName'        => $groupName,
            'namespaceId'      => $namespaceId,
        ], RequestMethod::DELETE)->body();
    }

    /**
     * @param string|float $protectThreshold
     */
    public function update(string $serviceName, string $groupName = '', string $namespaceId = '', $protectThreshold = 0, string $metadata = '', string $selector = ''): bool
    {
        return 'ok' === $this->client->request(self::SERVICE_API_APTH, [
            'serviceName'      => $serviceName,
            'groupName'        => $groupName,
            'namespaceId'      => $namespaceId,
            'protectThreshold' => $protectThreshold,
            'metadata'         => $metadata,
            'selector'         => $selector,
        ], RequestMethod::PUT)->body();
    }

    public function get(string $serviceName, string $groupName = '', string $namespaceId = ''): ServiceDetailResponse
    {
        return $this->client->request(self::SERVICE_API_APTH, [
            'serviceName' => $serviceName,
            'groupName'   => $groupName,
            'namespaceId' => $namespaceId,
        ], RequestMethod::GET, [], ServiceDetailResponse::class);
    }

    public function list(int $pageNo = 1, int $pageSize = 100, string $groupName = '', string $namespaceId = ''): ListResponse
    {
        return $this->client->request('nacos/v1/ns/service/list', [
            'pageNo'      => $pageNo,
            'pageSize'    => $pageSize,
            'groupName'   => $groupName,
            'namespaceId' => $namespaceId,
        ], RequestMethod::GET, [], ListResponse::class);
    }
}
