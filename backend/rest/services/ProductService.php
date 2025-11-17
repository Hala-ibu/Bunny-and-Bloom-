<?php
require_once 'BaseService.php';
require_once 'ProductDao.php'; 
require_once 'InventoryService.php'; 

class ProductService extends BaseService {

    private $inventoryService;

    public function __construct() {
        $dao = new ProductDao(); 
        parent::__construct($dao);
        $this->inventoryService = new InventoryService(); 
    }

    public function createProduct($data) {
        if (!isset($data['price']) || !is_numeric($data['price']) || $data['price'] <= 0) {
            throw new Exception('Price must be a positive numeric value.');
        }

        if ($this->dao->getByName($data['product_name'])) { 
            throw new Exception("A product with this name already exists.");
        }
        
        return $this->create($data); 
    }
    
    public function getByCategory($category) {
        return $this->dao->getByCategory($category);
    }
    
    public function updateProduct($id, $data) {
        if (isset($data['price']) && (!is_numeric($data['price']) || $data['price'] <= 0)) {
            throw new Exception('Price must be a positive numeric value.');
        }
        
        return $this->update($id, $data);
    }
    
  
    public function deleteProduct($product_id) {
        $inventoryDeleted = $this->inventoryService->deleteInventoryRecord($product_id);
        
        $productDeleted = $this->dao->deleteProduct($product_id);
        
        return $inventoryDeleted && $productDeleted;
    }
}
?>