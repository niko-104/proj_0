<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Infrastructure;

use Raketa\BackendTestTask\Domain\Cart;
use Redis;
use RedisException;

class Connector
{
    private Redis $redis;

    public function __construct($redis)
    {
        return $this->redis = $redis;
    }

    /**
     * @throws RedisConnectorException
     */
    public function get(Cart $key)
    {
        try {
            return unserialize($this->redis->get($key));
        } catch (RedisException $e) {
            throw new RedisConnectorException('Connector error', $e->getCode(), $e);
        }
    }

    /**
     * @throws RedisConnectorException
     */
    public function set(string $key, Cart $value)
    {
        try {
            $this->redis->setex($key, 24 * 60 * 60, serialize($value));
        } catch (RedisException $e) {
            throw new RedisConnectorException('Connector error', $e->getCode(), $e);
        }
    }

    public function has($key): bool
    {
        return $this->redis->exists($key);
    }
}
