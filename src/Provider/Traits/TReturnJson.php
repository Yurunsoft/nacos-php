<?php

declare(strict_types=1);

namespace Yurun\Nacos\Provider\Traits;

use Yurun\Nacos\Exception\NacosApiException;
use Yurun\Util\YurunHttp\Http\Response;

trait TReturnJson
{
    public function __construct(Response $response)
    {
        parent::__construct($response);
        $jsonData = $response->json(true);
        if (\is_array($jsonData)) {
            foreach ($jsonData as $k => $v) {
                $this->$k = $v;
            }
        } else {
            throw new NacosApiException('Data does not exists');
        }
    }
}
