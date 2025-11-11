<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\Infrastructure\Redis\CartRedis;
use Raketa\BackendTestTask\View\CartView;
use Raketa\BackendTestTask\Domain\CartItem;
use Raketa\BackendTestTask\Repository\ProductRepository;
use Ramsey\Uuid\Uuid;
use Psr\Log\LoggerInterface;

readonly class CartController
{
    public function __construct(
        private CartView $cartView,
        private CartRedis $cartRedis,
        private ProductRepository $productRepository,
        private LoggerInterface $logger
    ) {
    }

    public function getCart(ResponseInterface $response): ResponseInterface
    {
        try {
            $cart = $this->cartRedis->getCart();

            $cartArray = $this->cartView->toArray($cart);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            return $response
                ->getBody()
                ->write(
                    json_encode(
                        [
                            'status' => 'error',
                            'message' => 'Cart not found'
                        ],
                        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                    )
                )
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(404);
        }

        return $response
            ->getBody()
            ->write(
                json_encode(
                    [
                        'status' => 'success',
                        'cart' => $cartArray,
                    ],
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                )
            )
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus(200);

    }

    public function addItemToCart(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $rawRequest = json_decode($request->getBody()->getContents(), true);

        try {
            $product = $this->productRepository->getByUuid($rawRequest['productUuid']);

            $cart = $this->cartRedis->getCart();

            $cart->addItem(new CartItem(
                Uuid::uuid4()->toString(),
                $product->getUuid(),
                $product->getPrice(),
                $rawRequest['quantity'],
            ));

            $this->cartRedis->saveCart(session_id(), $cart);

            $cartArray = $this->cartView->toArray($cart);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            return $response
                ->getBody()
                ->write(
                    json_encode(
                        [
                            'status' => 'error',
                            'message' => 'Add item to cart failed'
                        ],
                        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                    )
                )
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(404);
        }

        return $response
            ->getBody()
            ->write(
                json_encode(
                    [
                        'status' => 'success',
                        'cart' => $cartArray
                    ],
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                )
            )
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus(200);
    }
}
