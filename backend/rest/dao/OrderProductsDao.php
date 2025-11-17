<?php
require_once 'BaseDao.php';

class OrderProductsDao extends BaseDao {
    public function __construct() {
        parent::__construct("Order_Products"); 
    }


    public function saveOrderItems($order_id, $items) {
        $success = true;
        
        $sql = "INSERT INTO Order_Products (order_id, product_id, quantity) 
                VALUES (:order_id, :product_id, :quantity)";
        $stmt = $this->connection->prepare($sql);
        
        foreach ($items as $item) {
            $result = $stmt->execute([
                ':order_id' => $order_id,
                ':product_id' => $item['product_id'],
                ':quantity' => $item['quantity']
            ]);
            if (!$result) {
                $success = false;
            }
        }
        return $success;
    }
    

    public function getItemsByOrderId($order_id) {
        $stmt = $this->connection->prepare(
            "SELECT product_id, quantity 
             FROM Order_Products 
             WHERE order_id = :order_id"
        );
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>