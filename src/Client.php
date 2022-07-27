<?php

declare(strict_types=1);

namespace Yurun\Nacos;

use Psr\Log\LoggerInterface;
use Yurun\Nacos\Exception\NacosApiException;
use Yurun\Nacos\Exception\NacosException;
use Yurun\Nacos\Provider\Auth\AuthProvider;
use Yurun\Nacos\Provider\BaseProvider;
use Yurun\Nacos\Provider\Config\ConfigProvider;
use Yurun\Nacos\Provider\Instance\InstanceProvider;
use Yurun\Nacos\Provider\Ns\NamespaceProvider;
use Yurun\Nacos\Provider\Operator\OperatorProvider;
use Yurun\Nacos\Provider\Service\ServiceProvider;
use Yurun\Util\HttpRequest;
use Yurun\Util\YurunHttp\ConnectionPool;
use Yurun\Util\YurunHttp\Http\Psr7\Consts\RequestHeader;
use Yurun\Util\YurunHttp\Http\Psr7\Consts\StatusCode;
use Yurun\Util\YurunHttp\Http\Response;

/**
 * @property AuthProvider      $auth
 * @property ConfigProvider    $config
 * @property NamespaceProvider $namespace
 * @property InstanceProvider  $instance
 * @property ServiceProvider   $service
 * @property OperatorProvider  $operator
 */
class Client
{
    protected array $providersConfig = [
        'auth'      => AuthProvider::class,
        'config'    => ConfigProvider::class,
        'namespace' => NamespaceProvider::class,
        'instance'  => InstanceProvider::class,
        'service'   => ServiceProvider::class,
        'operator'  => OperatorProvider::class,
    ];

    /**
     * @var BaseProvider[]
     */
    protected array $providers = [];

    protected ClientConfig $clientConfig;

    protected HttpRequest $httpRequest;

    protected Logger $logger;

    public function __construct(ClientConfig $config, ?LoggerInterface $logger = null)
    {
        $this->clientConfig = $config;
        $this->logger = new Logger($logger);
        $this->httpRequest = $httpRequest = new HttpRequest();
        $httpRequest->connectionPool(true);
        ConnectionPool::setConfig($this->buildUrl(), $config->getMaxConnections(), $config->getPoolWaitTimeout());
    }

    /**
     * @param mixed $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->providers[$name])) {
            return $this->providers[$name];
        }

        if (!isset($this->providersConfig[$name])) {
            throw new NacosException(sprintf('Provider %s does not exists', $name));
        }

        return new $this->providersConfig[$name]($this);
    }

    public function getConfig(): ClientConfig
    {
        return $this->clientConfig;
    }

    public function getLogger(): Logger
    {
        return $this->logger;
    }

    /**
     * @template T
     *
     * @param mixed           $params
     * @param class-string<T> $responseClass
     *
     * @return T|Response|mixed
     */
    public function request(string $path, $params = [], string $method = 'GET', array $headers = [], ?string $responseClass = null, bool $useAccessToken = true)
    {
        $config = $this->getConfig();
        $url = $this->buildUrl($path);
        if ('GET' === $method) {
            $queryParams = $params;
            $params = [];
        } else {
            $queryParams = [];
        }
        if ($useAccessToken) {
            $queryParams['accessToken'] = $accessToken = $this->auth->getAccessToken();
            if ($config->getAuthorizationBearer()) {
                $this->httpRequest->header(RequestHeader::AUTHORIZATION, 'Bearer ' . $accessToken);
            }
            $this->httpRequest->header('accessToken', $accessToken);
        }
        if (!empty($queryParams)) {
            if (strpos($url, '?')) {
                $url .= '&';
            } else {
                $url .= '?';
            }
            $url .= http_build_query($queryParams, '', '&');
        }
        $response = $this->httpRequest->timeout($config->getTimeout())->headers($headers)->send($url, $params, $method);
        // request failed
        if (!$response->success) {
            throw new NacosApiException(sprintf('Request failed [%d] %s. Request method[%s], url[%s], header:[%s], params:[%s]', $response->errno(), $response->error(), $method, $url, json_encode($headers, \JSON_PRETTY_PRINT), json_encode($params, \JSON_PRETTY_PRINT)));
        }

        // Nacos error
        if (StatusCode::OK !== $response->getStatusCode()) {
            $body = $response->body();

            // json
            $result = json_decode($body, true);
            if (false !== $result && isset($result['message'], $result['status'])) {
                throw new NacosApiException($result['message'], $result['status'], null, $response);
            }

            // not json
            $statusCode = $response->getStatusCode();
            throw new NacosApiException('' === $body ? StatusCode::getReasonPhrase($statusCode) : $body, $statusCode, null, $response);
        }

        // success
        if ($responseClass) {
            return new $responseClass($response);
        } else {
            return $response;
        }
    }

    public function reopen(): void
    {
        $this->httpRequest->open();
    }

    protected function buildUrl(string $path = ''): string
    {
        $config = $this->getConfig();

        return ($config->getSsl() ? 'https' : 'http') . '://' . $config->getHost() . ':' . $config->getPort() . $config->getPrefix() . $path;
    }
}
