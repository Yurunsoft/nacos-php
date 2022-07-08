<?php

declare(strict_types=1);

namespace Yurunsoft\Nacos\Provider\Operator\Model;

use Yurunsoft\Nacos\Model\BaseModel;

class HealthParams extends BaseModel
{
    /**
     * @var int|float|string
     */
    protected $max = 0;

    /**
     * @var int|float|string
     */
    protected $min = 0;

    /**
     * @var int|float|string
     */
    protected $factor = 0;

    /**
     * @return int|float|string
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @return int|float|string
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @return int|float|string
     */
    public function getFactor()
    {
        return $this->factor;
    }
}
