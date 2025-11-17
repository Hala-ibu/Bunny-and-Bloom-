<?php
require_once 'BaseDao.php';

class InventoryDao extends BaseDao {
    public function __construct() {
        parent::__construct("inventory");
    }

    public function getByProductId($product_id) {
        $stmt = $this->connection->prepare("SELECT * FROM inventory WHERE product_id = :product_id");
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getStockByProductId($product_id) {
        $stmt = $this->connection->prepare("SELECT quantity FROM inventory 
            WHERE product_id = :product_id");
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        $result = $stmt->fetchColumn();
        return $result !== false ? (int)$result : 0; 
    }

    public function updateStock($product_id, $newStock) {
        $stmt = $this->connection->prepare("UPDATE inventory SET quantity = :quantity 
            WHERE product_id = :product_id");
        $stmt->bindParam(':quantity', $newStock);
        $stmt->bindParam(':product_id', $product_id);
        return $stmt->execute();
    }

 
    public function getAllStock() {
        $sql = "SELECT i.quantity AS stock_quantity, p.name AS product_name, p.description AS category, p.price
                FROM inventory i
                JOIN products p ON i.product_id = p.id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

 
    public function deleteByProductId($product_id) {
        $stmt = $this->connection->prepare("DELETE FROM inventory WHERE product_id = :product_id");
        $stmt->bindParam(':product_id', $product_id);
        return $stmt->execute();
    }
}
?>