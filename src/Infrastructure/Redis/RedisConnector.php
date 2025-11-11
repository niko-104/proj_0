<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Infrastructure\Redis;

use Raketa\BackendTestTask\Exceptions\RedisConnectorException;
use Redis;
use RedisException;

class RedisConnector
{
    private const HOST = '127.0.0.1';
    private const PORT = 6379;
    private const PASSWORD = '12345';
    private const DBINDEX = 1;

    protected Redis $redis;

    public function __construct()
    {
        $this->redis = new Redis();
    }

    protected function check(): bool {
        try {
            return $this->redis->isConnected() && $this->redis->ping();
        } catch (RedisException $e) {
            return false;
        }
    }

    /**
     * @throws RedisConnectorException
     */
    protected function build(): void
    {
        try {
            $this->redis->connect(self::HOST, self::PORT);
            $this->redis->auth(self::PASSWORD);
            $this->redis->select(self::DBINDEX);
        } catch (RedisException $e) {
            throw new RedisConnectorException('Redis connection failed', $e->getCode(), $e);
        }
    }

    /**
     * @throws RedisConnectorException
     */
    protected function get(string $key)
    {
        try {
            return unserialize($this->redis->get($key));
        } catch (RedisException $e) {
            throw new RedisConnectorException('Redis get failed', $e->getCode(), $e);
        }
    }

    /**
     * @throws RedisConnectorException
     */
    protected function set(string $key, $value, int $ttl = 24 * 60 * 60): void
    {
        try {
            $this->redis->setex($key, $ttl, serialize($value));
        } catch (RedisException $e) {
            throw new RedisConnectorException('Redis set failed', $e->getCode(), $e);
        }
    }

    /**
     * @throws RedisConnectorException
     */
    protected function has($key): bool
    {
        try {
            return $this->redis->exists($key);
        } catch (RedisException $e) {
            throw new RedisConnectorException('Redis exists failed', $e->getCode(), $e);
        }
    }
}
