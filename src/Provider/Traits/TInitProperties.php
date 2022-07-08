<?php

declare(strict_types=1);

namespace Yurunsoft\Nacos\Provider\Traits;

trait TInitProperties
{
    public function __construct(array $data = [])
    {
        if ($data) {
            foreach ($data as $k => $v) {
                $this->$k = $v;
            }
        }
    }
}
