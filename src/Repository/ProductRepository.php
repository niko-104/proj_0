<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Repository;

use Doctrine\DBAL\Connection;
use Exception;
use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Entity\Product;

class ProductRepository
{
    private Connection $connection;
    private LoggerInterface $logger;

    public function __construct(Connection $connection, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->logger = $logger;
    }

    /**
     * @throws Exception
     */
    public function getByUuid(string $uuid): Product
    {
        $sql = "SELECT * FROM products 
                WHERE uuid = :uuid";

        try {
            $row = $this
                ->connection
                ->executeQuery($sql, ['uuid' => $uuid])
                ->fetchOne();
        } catch (Exception $e) {
            throw new Exception("Fetch product by uuid failed: " . $e->getMessage());
        }

        return $this->makeProduct($row);
    }

    /**
     * @throws Exception
     */
    public function getByCategory(string $category): array
    {
        $sql = "SELECT * FROM products 
                INNER JOIN categories 
                ON categories.id = products.category_id 
                WHERE categories.name = :category";

        try {
            $rows = $this
                ->connection
                ->executeQuery($sql, ['category' => $category])
                ->fetchAllAssociative();
        } catch (Exception $e) {
            throw new Exception("Fetch products by category failed: " . $e->getMessage());
        }

        return array_map(
            static fn (array $row): Product => $this->makeProduct($row),
            $rows
        );

    }

    public function makeProduct(array $row): Product
    {
        return new Product(
            $row['id'],
            $row['uuid'],
            $row['is_active'],
            $row['category'],
            $row['name'],
            $row['description'],
            $row['thumbnail'],
            $row['price'],
        );
    }
}
