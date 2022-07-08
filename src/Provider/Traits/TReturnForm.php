<?php

declare(strict_types=1);

namespace Yurunsoft\Nacos\Provider\Traits;

use Yurun\Util\YurunHttp\Http\Response;

trait TReturnForm
{
    public function __construct(Response $response)
    {
        parent::__construct($response);
        parse_str($response->body(), $result);
        foreach ($result as $k => $v) {
            $this->$k = $v;
        }
    }
}
