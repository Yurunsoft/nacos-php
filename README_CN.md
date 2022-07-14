# nacos-php

[![Latest Version](https://img.shields.io/packagist/v/yurunsoft/nacos-php.svg)](https://packagist.org/packages/yurunsoft/nacos-php)
![GitHub Workflow Status (branch)](https://img.shields.io/github/workflow/status/yurunsoft/nacos-php/test/master)
[![Php Version](https://img.shields.io/badge/php-%3E=7.4-brightgreen.svg)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=4.4.0-brightgreen.svg)](https://github.com/swoole/swoole-src)
[![License](https://img.shields.io/badge/license-Apache2-brightgreen.svg)](https://github.com/yurunsoft/nacos-php/blob/master/LICENSE)

[English](README.md) | **中文**

Nacos PHP 客户端允许你访问 Nacos 服务，它支持服务发现和动态配置。

请求和响应数据全部支持强类型，IDE 友好。

完善的测试用例，并且支持 Swoole 协程化。

## 使用限制

支持 PHP >= 7.4 版本

支持 Swoole >= 4.4 版本

支持 Nacos >= 1.x 版本

## 安装

使用 Composer 安装 SDK：

`composer require yurunsoft/nacos-php`

## 快速使用

### 客户端

```php
use Yurun\Nacos\Client;
use Yurun\Nacos\ClientConfig;

// ClientConfig 的参数全部为可选项，下面展示的都是默认值
// 你可以只写你要修改的配置项
$config = new ClientConfig([
    'host'                => '127.0.0.1', // 主机名
    'port'                => 8848, // 端口号
    'prefix'              => '/', // 前缀
    'username'            => 'nacos', // 用户名
    'password'            => 'nacos', // 密码
    'timeout'             => 60000, // 网络请求超时时间，单位：毫秒
    'ssl'                 => false, // 是否使用 ssl(https) 请求
    'authorizationBearer' => false, // 是否使用请求头 Authorization: Bearer {accessToken} 方式传递 Token，旧版本 Nacos 需要设为 true
]);
// 实例化客户端
$client = new Client($config);

// 启用日志，支持 PSR-3
$logger = new \Monolog\Logger();
$client = new Client($config, $logger);
```

### 提供者

```php
// 从客户端获取提供者

// 鉴权提供者
$auth = $client->auth;
// 配置提供者
$config = $client->config;
// 命名空间提供者
$namespace = $client->namespace;
// 实例提供者
$instance = $client->instance;
// 服务提供者
$service = $client->service;
// 操作提供者
$operator = $client->operator;
```

### 动态配置

#### 获取配置

```php
$value = $client->config->get('dataId', 'group');
```

#### 获取处理后的配置

> 支持 json、xml、yaml（需要安装 yaml 扩展）
> Nacos >= 1.4

```php
$client->config->set('dataId', 'group', json_encode(['id' => 19260817]), 'json');
$value = $client->config->getParsedConfig('dataId', 'group', '', $type);

// 输出：
// array(1) {
//   ["id"]=>
//   int(19260817)
// }
var_dump($value);

var_dump($type); // json
```

#### 写入配置

```php
$client->config->set('dataId', 'group', 'value');
```

#### 删除配置

```php
$client->config->delete('dataId', 'group', 'value');
```

#### 监听配置

##### 配置监听器

```php
use Yurun\Nacos\Provider\Config\ConfigListener;
use Yurun\Nacos\Provider\Config\Model\ListenerConfig;

// 获取监听器
$listenerConfig = new ListenerConfig([
    'timeout'  => 30000, // 配置监听器长轮询超时时间，单位：毫秒
    'savePath' => '', // 配置保存路径，默认为空不保存到文件
]);
$listener = $client->config->getConfigListener($listenerConfig);

$dataId = 'dataId';
$groupId = 'groupId';
$tenant = '';

// 增加监听项
$listener->addListener($dataId, $groupId, $tenant);
// 带回调监听配置
$listener->addListener($dataId, $groupId, $tenant, function (ConfigListener $listener, string $dataId, string $group, string $tenant) {
    // $listener->stop();
});

// 开始监听，在停止之前不会继续执行下面的语句
$listener->start();

// 从监听器中获取配置缓存，需要在其它协程中调用
$listener->get($dataId);
```

##### 手动监听配置

> 推荐使用配置监听器

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

### 服务发现

#### 注册实例

```php
$client->instance->register('192.168.1.123', 8080, 'Service1');
// 完整参数
$client->instance->register('192.168.1.123', 8080, 'Service1', $namespaceId = '', $weight = 1, $enabled = true, $healthy = true, $metadata = '', $clusterName = '', $groupName = '', $ephemeral = false);
```

#### 注销实例

```php
$client->instance->deregister('192.168.1.123', 8080, 'Service1');
// 完整参数
$client->instance->deregister('192.168.1.123', 8080, 'Service1', $namespaceId = '', $clusterName = '', $groupName = '', $ephemeral = false);
```

#### 更新实例

```php
$client->instance->update('192.168.1.123', 8080, 'Service1');
// 完整参数
$client->instance->update('192.168.1.123', 8080, 'Service1', $namespaceId = '', $weight = 1, $enabled = true, $healthy = true, $metadata = '', $clusterName = '', $groupName = '', $ephemeral = false);
```

#### 心跳

```php
use Yurun\Nacos\Provider\Instance\Model\RsInfo;

$beat = new RsInfo();
$beat->setIp('192.168.1.123');
$beat->setPort(8080);
$client->instance->beat('Service1', $beat);
```

#### 获取实例列表

```php
$response = $client->instance->list('Service1');
$response = $client->instance->list('Service1', $groupName = '', $namespaceId = '', $clusters = '', $healthyOnly = false);
```

#### 获取实例详情

```php
$response = $client->instance->detail('192.168.1.123', 8080, 'Service1');
// 完整参数
$response = $client->instance->detail('192.168.1.123', 8080, 'Service1', $groupName = '', $namespaceId = '', $clusters = '', $healthyOnly = false, $ephemeral = false);
```

> 其它更多功能接口可以参考提供者对象的 IDE 提示和 Nacos 文档来使用。

## 文档

Nacos open-api相关信息可以查看文档 [Nacos Open API 指南](https://nacos.io/zh-cn/docs/open-api.html)。

Nacos产品了解可以查看 [Nacos 官网](https://nacos.io/zh-cn/docs/what-is-nacos.html)。
