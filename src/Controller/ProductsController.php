<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\Repository\ProductRepository;
use Raketa\BackendTestTask\View\ProductsView;
use Psr\Log\LoggerInterface;

class ProductsController
{
    public function __construct(
        private ProductsView $productsVew,
        private ProductRepository $productRepository,
        private LoggerInterface $logger
    ) {
    }

    public function getProductsByCategory(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $rawRequest = json_decode($request->getBody()->getContents(), true);

        try {
            $products = $this->productRepository->getByCategory($rawRequest['category']);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            return $response
                ->getBody()
                ->write(
                    json_encode(
                        [
                            'status' => 'error',
                            'message' => 'Fetch products by category failed'
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
                    $this->productsVew->toArray($products),
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                )
            )
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus(200);

    }
}
