<?php

namespace think\swoole\concerns;

use RuntimeException;
use Swoole\Coroutine;
use Swoole\Coroutine\Channel;

trait InteractsWithPool
{

    /** @var Channel[] */
    protected $pools = [];

    protected $connectionCount = [];

    /**
     * 获取连接池
     * @param $name
     * @return Channel
     */
    protected function getPool($name)
    {
        if (empty($this->pools[$name])) {
            $this->pools[$name] = new Channel($this->getPoolMaxActive($name));
        }
        return $this->pools[$name];
    }

    protected function getPoolConnection($name)
    {
        $pool = $this->getPool($name);

        if (!isset($this->connectionCount[$name])) {
            $this->connectionCount[$name] = 0;
        }

        if ($pool->isEmpty() && $this->connectionCount[$name] < $this->getPoolMaxActive($name)) {
            //新建
            $connection = $this->createPoolConnection($name);
            $this->connectionCount[$name]++;
        } else {
            $connection = $pool->pop($this->getPoolMaxWaitTime($name));

            if ($connection === false) {
                throw new RuntimeException(sprintf(
                    'Borrow the connection timeout in %.2f(s), connections in pool: %d, all connections: %d',
                    $this->getPoolMaxWaitTime($name),
                    $pool->length(),
                    $this->connectionCount[$name] ?? 0
                ));
            }
        }

        return $this->wrapProxy($pool, $connection);
    }

    protected function wrapProxy(Channel $pool, $connection)
    {
        Coroutine::defer(function () use ($pool, $connection) {
            //自动归还
            if (!$pool->isFull()) {
                $pool->push($connection, 0.001);
            }
        });

        return $connection;
    }

    abstract protected function createPoolConnection(string $name);

    abstract protected function getPoolMaxActive($name): int;

    abstract protected function getPoolMaxWaitTime($name): int;

}
