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

    public function updateProduct($id, $data) {
        $setClauses = [];
        foreach (array_keys($data) as $key) {
            $setClauses[] = "$key = :$key";
        }
        $sql = "UPDATE products SET " . implode(', ', $setClauses) . " WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function deleteProduct($id) {
        return $this->delete($id);
    }
}
?>