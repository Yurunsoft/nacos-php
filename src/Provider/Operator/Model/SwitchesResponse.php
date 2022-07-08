<?php

declare(strict_types=1);

namespace Yurun\Nacos\Provider\Operator\Model;

use Yurun\Nacos\Provider\Model\BaseResponse;
use Yurun\Util\YurunHttp\Http\Response;

class SwitchesResponse extends BaseResponse
{
    /**
     * @var string[]|null
     */
    protected ?array $masters = null;

    /**
     * @var array<string, int>
     */
    protected array $adWeightMap = [];

    protected int $defaultPushCacheMillis = 0;

    protected int $clientBeatInterval = 0;

    protected int $defaultCacheMillis = 0;

    /**
     * @var float|string
     */
    protected $distroThreshold = 0;

    protected bool $healthCheckEnabled = true;

    protected bool $autoChangeHealthCheckEnabled = true;

    protected bool $distroEnabled = true;

    protected bool $enableStandalone = true;

    protected bool $pushEnabled = true;

    protected int $checkTimes = 3;

    protected ?HealthParams $httpHealthParams = null;

    protected ?HealthParams $tcpHealthParams = null;

    protected ?HealthParams $mysqlHealthParams = null;

    /**
     * @var string[]
     */
    protected array $incrementalList = [];

    protected int $serverStatusSynchronizationPeriodMillis = 0;

    protected int $serviceStatusSynchronizationPeriodMillis = 0;

    protected bool $disableAddIP = false;

    protected bool $sendBeatOnly = false;

    protected bool $lightBeatEnabled = true;

    protected bool $doubleWriteEnabled = true;

    /**
     * @var array<string, int>
     */
    protected array $limitedUrlMap = [];

    /**
     * The server is regarded as expired if its two reporting interval is lagger than this variable.
     */
    protected int $distroServerExpiredMillis = 0;

    /**
     * since which version, push can be enabled.
     */
    protected string $pushGoVersion = '';

    protected string $pushJavaVersion = '';

    protected string $pushPythonVersion = '';

    protected string $pushCVersion = '';

    protected string $pushCSharpVersion = '';

    protected bool $enableAuthentication = false;

    protected ?string $overriddenServerStatus = null;

    protected bool $defaultInstanceEphemeral = true;

    protected ?string $checkSum = null;

    /**
     * @var string[]
     */
    protected array $healthCheckWhiteList = [];

    protected string $name = '';

    public function __construct(Response $response)
    {
        parent::__construct($response);
        foreach ($response->json(true) as $k => $v) {
            switch ($k) {
                case 'httpHealthParams':
                case 'tcpHealthParams':
                case 'mysqlHealthParams':
                    $v = new HealthParams($v);
                    break;
            }
            $this->$k = $v;
        }
    }

    /**
     * @return string[]|null
     */
    public function getMasters(): ?array
    {
        return $this->masters;
    }

    /**
     * Get int>.
     *
     * @return array<string, int>
     */
    public function getAdWeightMap(): array
    {
        return $this->adWeightMap;
    }

    public function getDefaultPushCacheMillis(): int
    {
        return $this->defaultPushCacheMillis;
    }

    public function getClientBeatInterval(): int
    {
        return $this->clientBeatInterval;
    }

    public function getDefaultCacheMillis(): int
    {
        return $this->defaultCacheMillis;
    }

    /**
     * @return float|string
     */
    public function getDistroThreshold()
    {
        return $this->distroThreshold;
    }

    public function getHealthCheckEnabled(): bool
    {
        return $this->healthCheckEnabled;
    }

    public function getAutoChangeHealthCheckEnabled(): bool
    {
        return $this->autoChangeHealthCheckEnabled;
    }

    public function getDistroEnabled(): bool
    {
        return $this->distroEnabled;
    }

    public function getEnableStandalone(): bool
    {
        return $this->enableStandalone;
    }

    public function getPushEnabled(): bool
    {
        return $this->pushEnabled;
    }

    public function getCheckTimes(): int
    {
        return $this->checkTimes;
    }

    public function getHttpHealthParams(): ?HealthParams
    {
        return $this->httpHealthParams;
    }

    public function getTcpHealthParams(): ?HealthParams
    {
        return $this->tcpHealthParams;
    }

    public function getMysqlHealthParams(): ?HealthParams
    {
        return $this->mysqlHealthParams;
    }

    /**
     * @return string[]
     */
    public function getIncrementalList(): array
    {
        return $this->incrementalList;
    }

    public function getServerStatusSynchronizationPeriodMillis(): int
    {
        return $this->serverStatusSynchronizationPeriodMillis;
    }

    public function getServiceStatusSynchronizationPeriodMillis(): int
    {
        return $this->serviceStatusSynchronizationPeriodMillis;
    }

    public function getDisableAddIP(): bool
    {
        return $this->disableAddIP;
    }

    public function getSendBeatOnly(): bool
    {
        return $this->sendBeatOnly;
    }

    public function getLightBeatEnabled(): bool
    {
        return $this->lightBeatEnabled;
    }

    public function getDoubleWriteEnabled(): bool
    {
        return $this->doubleWriteEnabled;
    }

    /**
     * @return array<string, int>
     */
    public function getLimitedUrlMap(): array
    {
        return $this->limitedUrlMap;
    }

    public function getDistroServerExpiredMillis(): int
    {
        return $this->distroServerExpiredMillis;
    }

    public function getPushGoVersion(): string
    {
        return $this->pushGoVersion;
    }

    public function getPushJavaVersion(): string
    {
        return $this->pushJavaVersion;
    }

    public function getPushPythonVersion(): string
    {
        return $this->pushPythonVersion;
    }

    public function getPushCVersion(): string
    {
        return $this->pushCVersion;
    }

    public function getPushCSharpVersion(): string
    {
        return $this->pushCSharpVersion;
    }

    public function getEnableAuthentication(): bool
    {
        return $this->enableAuthentication;
    }

    public function getOverriddenServerStatus(): ?string
    {
        return $this->overriddenServerStatus;
    }

    public function getDefaultInstanceEphemeral(): bool
    {
        return $this->defaultInstanceEphemeral;
    }

    public function getCheckSum(): ?string
    {
        return $this->checkSum;
    }

    /**
     * @return string[]
     */
    public function getHealthCheckWhiteList(): array
    {
        return $this->healthCheckWhiteList;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
