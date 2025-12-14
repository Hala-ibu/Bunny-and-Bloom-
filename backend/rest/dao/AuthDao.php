<?php
require_once 'BaseDao.php';
require_once 'UserDao.php'; 
class AuthDao extends UserDao {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function get_user_by_email($email) {
        return $this->getByEmail($email);
    }
}
?>