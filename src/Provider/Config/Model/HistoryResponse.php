<?php

declare(strict_types=1);

namespace Yurunsoft\Nacos\Provider\Config\Model;

use Yurunsoft\Nacos\Provider\Model\BaseResponse;
use Yurunsoft\Nacos\Provider\Traits\TReturnJson;

class HistoryResponse extends BaseResponse
{
    use THistoryItem;
    use TReturnJson;
}
