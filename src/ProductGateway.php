<?php

class ProductGateway extends ProductGatewayAbstract
{
    private PDO $conn;
    
    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }
    
    public function getAll(): array
    {
        $sql = "SELECT p.*, t.name AS type_name
                FROM products p
                Join product_type t ON t.id = p.type";
                
        $stmt = $this->conn->query($sql);
        
        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $data[] = $row;
        }
        return $data;
    }
    
    public function create(array $data): string
    {
        $sql = "INSERT INTO products (name, type, SKU, price, weight, size, dimensions)
                VALUES (:name, :type, :SKU, :price, :weight, :size, :dimensions)";        
        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            $error = $this->conn->errorInfo();
            throw new Exception("Error preparing statement: " . $error[2]);
        }
    
        try {
            $stmt->bindValue(':name',       $data["name"], PDO::PARAM_STR);
            $stmt->bindValue(':type',       $data["type"], PDO::PARAM_INT);
            $stmt->bindValue(':SKU',        $data["SKU"], PDO::PARAM_STR);
            $stmt->bindValue(':price',      $data["price"], PDO::PARAM_INT);
            $stmt->bindValue(':weight',     $data["weight"]?? null, PDO::PARAM_INT);
            $stmt->bindValue(':size',       $data["size"] ?? null, PDO::PARAM_INT);
            $stmt->bindValue(':dimensions', $data["dimensions"] ?? null, PDO::PARAM_STR);
            $stmt->execute();

            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Error executing statement: " . $e->getMessage());
        }
    }
    
    

    public function massDelete(array $ids): int
    {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "DELETE FROM products
            WHERE id IN ($placeholders)";
        $stmt = $this->conn->prepare($sql);

        foreach ($ids as $key => $value) {
            $stmt->bindValue(($key+1), $value, PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->rowCount();
    }
}











