<?php
require_once 'BaseDao.php';

class OrderDao extends BaseDao {
    public function __construct() {
        parent::__construct("orders");
    }


    public function getAllOrders() {
        return $this->getAll(); 
    }

    public function getByUserId($user_id) {
        $stmt = $this->connection->prepare("SELECT * FROM orders WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    

    public function getById($order_id) {
        $stmt = $this->connection->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->bindParam(':id', $order_id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function updateStatus($order_id, $new_status) {
        $stmt = $this->connection->prepare("UPDATE orders SET status = :status WHERE id = :id");
        $stmt->bindParam(':status', $new_status);
        $stmt->bindParam(':id', $order_id);
        return $stmt->execute();
    }
}
?>