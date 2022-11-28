<?php

declare(strict_types=1);

namespace Yurun\Nacos\Provider\Model;

use Yurun\Util\YurunHttp\Http\Response;

abstract class BaseResponse implements \JsonSerializable
{
    private Response $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $result = [];
        foreach ($this as $k => $v) {
            if ('response' === $k) {
                continue;
            }
            $result[$k] = $v;
        }

        return $result;
    }
}
