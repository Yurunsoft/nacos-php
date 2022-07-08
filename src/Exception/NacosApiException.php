<?php

declare(strict_types=1);

namespace Yurunsoft\Nacos\Exception;

use Yurun\Util\YurunHttp\Http\Response;

class NacosApiException extends NacosException
{
    private ?Response $response = null;

    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null, ?Response $response = null)
    {
        parent::__construct($message, $code, $previous);
        $this->response = $response;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }
}
