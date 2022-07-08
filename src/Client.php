<?php

declare(strict_types=1);

namespace Yurunsoft\Nacos;

use Yurun\Util\HttpRequest;
use Yurun\Util\YurunHttp\Http\Psr7\Consts\RequestHeader;
use Yurun\Util\YurunHttp\Http\Psr7\Consts\StatusCode;
use Yurun\Util\YurunHttp\Http\Response;
use Yurunsoft\Nacos\Exception\NacosApiException;
use Yurunsoft\Nacos\Exception\NacosException;
use Yurunsoft\Nacos\Provider\Auth\AuthProvider;
use Yurunsoft\Nacos\Provider\BaseProvider;
use Yurunsoft\Nacos\Provider\Config\ConfigProvider;
use Yurunsoft\Nacos\Provider\Instance\InstanceProvider;
use Yurunsoft\Nacos\Provider\Ns\NamespaceProvider;
use Yurunsoft\Nacos\Provider\Operator\OperatorProvider;
use Yurunsoft\Nacos\Provider\Service\ServiceProvider;

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

    public function __construct(ClientConfig $config)
    {
        $this->clientConfig = $config;
        $this->httpRequest = new HttpRequest();
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
            throw new NacosApiException(sprintf('Request failed [%d] %s', $response->errno(), $response->error()));
        }

        // Nacos error
        if (StatusCode::OK !== $response->getStatusCode()) {
            // json
            $result = $response->json(true);
            if (false !== $result && isset($result['message'], $result['status'])) {
                throw new NacosApiException($result['message'], $result['status'], null, $response);
            }

            // not json
            $body = $response->body();
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

    protected function buildUrl(string $path): string
    {
        $config = $this->getConfig();

        return ($config->getSsl() ? 'https' : 'http') . '://' . $config->getHost() . ':' . $config->getPort() . $config->getPrefix() . $path;
    }
}
