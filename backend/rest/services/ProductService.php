<?php
require_once 'BaseService.php';
require_once 'ProductDao.php'; 

class ProductService extends BaseService {

    public function __construct() {
        $dao = new ProductDao(); 
        parent::__construct($dao);
    }

    public function createProduct($data) {
        if (!isset($data['price']) || !is_numeric($data['price']) || $data['price'] <= 0) {
            throw new Exception('Price must be a positive numeric value.');
        }

        if ($this->dao->getByName($data['product_name'])) { 
            throw new Exception("A product with this name already exists.");
        }
        
        $data['stock_quantity'] = $data['stock_quantity'] ?? 0;

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
}
?>