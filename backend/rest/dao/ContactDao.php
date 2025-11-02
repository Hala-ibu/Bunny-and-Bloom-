<?php
require_once 'BaseDao.php';

class ContactDao extends BaseDao {
    public function __construct() {
        parent::__construct("contact_messages"); 
    }


    public function saveMessage($data) {
        return $this->insert($data);
    }
}
?>