<?php
require_once 'BaseDao.php';

class ProductDao extends BaseDao {
    public function __construct() {
        parent::__construct("products");
    }

    public function getByCategory($category) {
        $stmt = $this->connection->prepare("SELECT * FROM products WHERE description = :category");
        $stmt->bindParam(':category', $category);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function getByName($product_name) {
    $stmt = $this->connection->prepare("SELECT * FROM products WHERE name = :name");
    $stmt->bindParam(':name', $product_name);
    $stmt->execute();
    return $stmt->fetch(); 
    }
    public function getByName($product_name) {
    $stmt = $this->connection->prepare("SELECT * FROM products WHERE name = :name");
    $stmt->bindParam(':name', $product_name);
    $stmt->execute();
    return $stmt->fetch(); 
    }
}
?>
