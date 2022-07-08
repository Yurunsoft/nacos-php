# nacos-php

**English** | [中文](README_CN.md)

Nacos php sdk for Go client allows you to access Nacos service, it supports service discovery and dynamic configuration.

Request and response data are all strongly typed and IDE friendly.

Complete test cases and support for Swoole Coroutine.

## Requirements

Supported PHP version over 7.4

Supported Swoole version over 4.4

Supported Nacos version over 1.x

## Installation

Use Composer to install SDK：

`composer require yurunsoft/nacos-php`

## Quick Examples

### Client

```php
use Yurun\Nacos\Client;
use Yurun\Nacos\ClientConfig;

// The parameters of ClientConfig are all optional, the ones shown below are the default values
// You can write only the configuration items you want to modify
$client = new Client(new ClientConfig([
    'host'                => '127.0.0.1',
    'port'                => 8848,
    'prefix'              => '/',
    'username'            => 'nacos',
    'password'            => 'nacos',
    'timeout'             => 60000, // Network request timeout time, in milliseconds
    'ssl'                 => false, // Whether to use ssl(https) requests
    'authorizationBearer' => false, // Whether to use the request header Authorization: Bearer {accessToken} to pass Token, older versions of Nacos need to be set to true
]));
```

### Providers

```php
// Get provider from client

$auth = $client->auth;
$config = $client->config;
$namespace = $client->namespace;
$instance = $client->instance;
$service = $client->service;
$operator = $client->operator;
```

### Dynamic configuration

#### Get config

```php
$value = $client->config->get('dataId', 'group');
```

#### Set config

```php
$client->config->set('dataId', 'group', 'value');
```

#### Delete config

```php
$client->config->delete('dataId', 'group', 'value');
```

#### Listen config

```php
use Yurun\Nacos\Provider\Config\Model\ListenerRequest;

$md5 = '';
while (true) {
    $request = new ListenerRequest();
    $request->addListener('dataId', 'group', $md5);
    $items = $client->listen($request);
    foreach ($items as $item) {
        if ($item->getChanged()) {
            $value = $config->get($item->getDataId(), $item->getGroup(), $item->getTenant());
            var_dump('newValue:', $value);
            $md5 = md5($value);
        }
    }
}
```

### Service Discovery

#### Register instance

```php
$client->instance->register('192.168.1.123', 8080, 'Service1');
// Complete Parameters
$client->instance->register('192.168.1.123', 8080, 'Service1', $namespaceId = '', $weight = 1, $enabled = true, $healthy = true, $metadata = '', $clusterName = '', $groupName = '', $ephemeral = false);
```

#### Deregister instance

```php
$client->instance->deregister('192.168.1.123', 8080, 'Service1');
// Complete Parameters
$client->instance->deregister('192.168.1.123', 8080, 'Service1', $namespaceId = '', $clusterName = '', $groupName = '', $ephemeral = false);
```

#### Update instance

```php
$client->instance->update('192.168.1.123', 8080, 'Service1');
// Complete Parameters
$client->instance->update('192.168.1.123', 8080, 'Service1', $namespaceId = '', $weight = 1, $enabled = true, $healthy = true, $metadata = '', $clusterName = '', $groupName = '', $ephemeral = false);
```

#### Heartbeat

```php
use Yurun\Nacos\Provider\Instance\Model\RsInfo;

$beat = new RsInfo();
$beat->setIp('192.168.1.123');
$beat->setPort(8080);
$client->instance->beat('Service1', $beat);
```

#### Get instance list

```php
$response = $client->instance->list('Service1');
$response = $client->instance->list('Service1', $groupName = '', $namespaceId = '', $clusters = '', $healthyOnly = false);
```

#### Get instance detail

```php
$response = $client->instance->detail('192.168.1.123', 8080, 'Service1');
// Complete Parameters
$response = $client->instance->detail('192.168.1.123', 8080, 'Service1', $groupName = '', $namespaceId = '', $clusters = '', $healthyOnly = false, $ephemeral = false);
```

> Other more functional interfaces can be used by referring to the provider object's IDE tips and Nacos documentation.

## Documentation

You can view the open-api documentation from the [Nacos Open API Guide](https://nacos.io/en-us/docs/open-api.html).

You can view the full documentation from the [Nacos website](https://nacos.io/en-us/docs/what-is-nacos.html).
