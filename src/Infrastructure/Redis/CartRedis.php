<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Infrastructure\Redis;

use Exception;
use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Exceptions\RedisConnectorException;

class CartRedis extends RedisConnector
{
    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();

        try {
            parent::build();
        } catch (RedisConnectorException $e) {
            throw new Exception('Redis connection failed: ' . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function saveCart(string $key, Cart $cart): void
    {
        try {
            $this->redis->set($key, $cart);
        } catch (RedisConnectorException $e) {
            throw new Exception("Save cart failed: " . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function getCart(): Cart
    {
        try {
            return $this->redis->get(session_id());
        } catch (RedisConnectorException $e) {
            throw new Exception("Get cart failed: " . $e->getMessage());
        }
    }
}
