<?php

declare(strict_types=1);

namespace Yurunsoft\Nacos\Provider\Model;

abstract class BaseRequest
{
    /**
     * @return mixed
     */
    abstract public function getRequestBody();
}
