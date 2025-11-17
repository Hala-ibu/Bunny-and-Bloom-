<?php
require_once 'BaseDao.php';

class ContactDao extends BaseDao {
    public function __construct() {
        parent::__construct("contact_messages");
    }


    public function getAllMessages() {
        return $this->getAll(); 
    }
}
?>