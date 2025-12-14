<?php
require_once 'BaseService.php';
require_once 'InventoryDao.php'; 
require_once 'ProductService.php';

class InventoryService extends BaseService {
    
    private $productService;

    public function __construct() {
        $dao = new InventoryDao(); 
        parent::__construct($dao);
        $this->productService = Flight::get('productService');
    }
    


    public function adjustStock($product_id, $quantity_change) {
        if (!is_numeric($quantity_change) || $quantity_change == 0) {
            throw new Exception("Invalid quantity change value.");
        }
        
        $currentStock = $this->dao->getStockByProductId($product_id);
        $newStock = $currentStock + $quantity_change;
        
        if ($newStock < 0) {
            throw new Exception("Cannot reduce stock below zero. Current stock is " . $currentStock);
        }
        
        $this->dao->updateStock($product_id, $newStock);
        
        if ($newStock <= 10 && $newStock > 0) {
            error_log("LOW STOCK ALERT: Product ID {$product_id} is at {$newStock} units.");
        }
        
        return $newStock;
    }
    
    public function checkStockAvailability($product_id, $quantity_needed) {
        $currentStock = $this->dao->getStockByProductId($product_id);
        
        return $currentStock >= $quantity_needed;
    }

    public function getAllStockDetails() {
        return $this->dao->getAllStock();
    }
}
?>