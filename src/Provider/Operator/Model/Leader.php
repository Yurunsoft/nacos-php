<?php

declare(strict_types=1);

namespace Yurun\Nacos\Provider\Operator\Model;

use Yurun\Nacos\Model\BaseModel;

class Leader extends BaseModel
{
    protected int $heartbeatDueMs = 0;

    protected string $ip = '';

    protected int $leaderDueMs = 0;

    protected string $state = '';

    protected int $term = 0;

    protected string $voteFor = '';

    public function getHeartbeatDueMs(): int
    {
        return $this->heartbeatDueMs;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getLeaderDueMs(): int
    {
        return $this->leaderDueMs;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getTerm(): int
    {
        return $this->term;
    }

    public function getVoteFor(): string
    {
        return $this->voteFor;
    }
}
