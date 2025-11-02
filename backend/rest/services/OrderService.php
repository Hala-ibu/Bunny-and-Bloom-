<?php
require_once 'BaseService.php';
require_once 'OrderDao.php';
require_once 'ProductService.php';
require_once 'InventoryService.php'; 
require_once 'OrderProductsDao.php';

class OrderService extends BaseService {
    
    private $productService;
    private $inventoryService;  
    private $orderProductsDao;  

    public function __construct() {
        $dao = new OrderDao(); 
        parent::__construct($dao);
        $this->productService = new ProductService(); 
        $this->inventoryService = new InventoryService(); 
        $this->orderProductsDao = new OrderProductsDao();
    }

    public function getByUserId($user_id) {
        return $this->dao->getByUserId($user_id);
    }
    
    public function getAllOrders() {
        return $this->getAll();
    }
    
    public function placeOrder($orderData) {
        $totalAmount = 0.0;
        $items = $orderData['items'] ?? [];
        
        if (empty($items)) {
            throw new Exception("Cannot place an empty order.");
        }
        
        foreach ($items as $item) {
            $product = $this->productService->getById($item['product_id']); 
            
            if (!$product || $product['price'] <= 0) {
                throw new Exception("Invalid or missing product details for ID: " . $item['product_id']);
            }
            
            if (!$this->inventoryService->checkStockAvailability($item['product_id'], $item['quantity'])) {
                 throw new Exception("Insufficient stock available for product ID: " . $item['product_id']);
            }
            
            $totalAmount += $product['price'] * $item['quantity'];
        }
        
        $orderData['total_amount'] = round($totalAmount, 2);
        $orderData['status'] = 'Pending';
        
        $newOrderId = $this->create($orderData);
        
        if ($newOrderId) {
            $this->orderProductsDao->saveOrderItems($newOrderId, $items);

            foreach ($items as $item) {
                $this->inventoryService->adjustStock($item['product_id'], -$item['quantity']);
            }
            
            return $newOrderId;
        }
        
        return false;
    }
    
    public function cancelOrder($order_id) {
        $order = $this->getById($order_id);
        
        if (!$order) {
            throw new Exception("Order with ID {$order_id} not found.");
        }
        
        if ($order['status'] !== 'Pending') {
            throw new Exception("Order #{$order_id} cannot be cancelled as its status is '{$order['status']}'.");
        }

        $items = $this->orderProductsDao->getItemsByOrderId($order_id);
        
        try {
            foreach ($items as $item) {
                $this->inventoryService->adjustStock($item['product_id'], $item['quantity']); 
            }

            $updateData = ['status' => 'Cancelled'];
            $result = $this->update($order_id, $updateData); 

            if (!$result) {
                throw new Exception("Failed to update order status to Cancelled. Inventory may be out of sync.");
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Order Cancellation Error for ID {$order_id}: " . $e->getMessage());
            throw new Exception("Order cancellation failed due to an error: " . $e->getMessage());
        }
    }
}
?>