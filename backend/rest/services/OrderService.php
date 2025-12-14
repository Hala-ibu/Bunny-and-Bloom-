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
        return $this->dao->getAllOrders();
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
                throw new Exception("Invalid or missing product details for ID: {$item['product_id']}");
            }
            
            if (!$this->inventoryService->checkStockAvailability($item['product_id'], $item['quantity'])) {
                throw new Exception("Insufficient stock for product ID: {$item['product_id']}");
            }
            
            $totalAmount += $product['price'] * $item['quantity'];
        }
        
        $orderRecord = [
            'user_id' => $orderData['user_id'],
            'total_amount' => $totalAmount,
            'status' => 'Pending',
            'order_date' => date('Y-m-d H:i:s')
        ];
        
        $newOrderId = $this->dao->create($orderRecord);
        
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
            $result = $this->dao->updateStatus($order_id, 'Cancelled'); 
            if (!$result) {
                throw new Exception("Failed to update order status to Cancelled. Inventory may be out of sync.");
            }
            return true;
        } catch (Exception $e) {
            error_log("Order Cancellation Error for ID {$order_id}: " . $e->getMessage());
            throw $e;
        }
    }



    public function completeOrder($order_id) {
        $order = $this->getById($order_id);
        
        if (!$order) {
            throw new Exception("Order with ID {$order_id} not found.");
        }
        
        if ($order['status'] !== 'Pending') {
            throw new Exception("Order #{$order_id} cannot be completed as its status is '{$order['status']}'.");
        }
        
        return $this->dao->updateStatus($order_id, 'Completed');
    }

    public function getOrderDetails($order_id) {
        $order = $this->dao->getById($order_id);
        if (!$order) {
            return null;
        }
        $items = $this->orderProductsDao->getItemsByOrderId($order_id);

        $detailedItems = [];
        foreach ($items as $item) {
            $product = $this->productService->getById($item['product_id']);
            $detailedItems[] = [
                'product_name' => $product['name'] ?? 'Unknown Product',
                'quantity' => $item['quantity'],
                'price_at_order' => $product['price'] ?? 0.00,
            ];
        }

        $order['items'] = $detailedItems;
        return $order;
    }
}
?>