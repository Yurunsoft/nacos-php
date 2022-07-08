<?php

declare(strict_types=1);

namespace Yurunsoft\Nacos\Provider\Ns;

use Yurun\Util\YurunHttp\Http\Psr7\Consts\RequestMethod;
use Yurunsoft\Nacos\Provider\BaseProvider;
use Yurunsoft\Nacos\Provider\Ns\Model\NamespaceItem;

class NamespaceProvider extends BaseProvider
{
    public const NAMESPACE_API_APTH = 'nacos/v1/console/namespaces';

    /**
     * @return NamespaceItem[]
     */
    public function list(): array
    {
        $response = $this->client->request(self::NAMESPACE_API_APTH);
        $result = [];
        foreach ($response->json(true)['data'] as $row) {
            $result[] = new NamespaceItem($row);
        }

        return $result;
    }

    public function create(string $namespaceName = '', string $customNamespaceId = '', string $namespaceDesc = ''): bool
    {
        return 'true' === $this->client->request(self::NAMESPACE_API_APTH, [
            'customNamespaceId' => $customNamespaceId,
            'namespaceName'     => $namespaceName,
            'namespaceDesc'     => $namespaceDesc,
        ], RequestMethod::POST)->body();
    }

    public function update(string $namespaceId, string $namespaceName = '', string $namespaceDesc = ''): bool
    {
        return 'true' === $this->client->request(self::NAMESPACE_API_APTH, [
            'namespace'         => $namespaceId,
            'namespaceShowName' => $namespaceName,
            'namespaceDesc'     => $namespaceDesc,
        ], RequestMethod::PUT)->body();
    }

    public function delete(string $namespaceId): bool
    {
        return 'true' === $this->client->request(self::NAMESPACE_API_APTH, [
            'namespaceId'   => $namespaceId,
        ], RequestMethod::DELETE)->body();
    }
}
