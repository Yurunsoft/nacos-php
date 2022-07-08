<?php

declare(strict_types=1);

namespace Yurun\Nacos\Provider\Model;

abstract class BaseRequest
{
    /**
     * @return mixed
     */
    abstract public function getRequestBody();
}
