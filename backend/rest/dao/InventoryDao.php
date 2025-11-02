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
}

?>
