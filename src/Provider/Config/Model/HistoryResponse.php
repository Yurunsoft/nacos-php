<?php

declare(strict_types=1);

namespace Yurun\Nacos\Provider\Config\Model;

use Yurun\Nacos\Provider\Model\BaseResponse;
use Yurun\Nacos\Provider\Traits\TReturnJson;

class HistoryResponse extends BaseResponse
{
    use THistoryItem;
    use TReturnJson;
}
