<?php
require_once 'BaseService.php';
require_once 'ContactDao.php'; 

class ContactService extends BaseService {

    public function __construct() {
        $dao = new ContactDao(); 
        parent::__construct($dao);
    }
    
 
    public function submitMessage($data) {
        if (empty($data['sender_name']) || empty($data['sender_email']) || empty($data['message_content'])) {
            throw new Exception("Name, email, and message content are required.");
        }

        if (!filter_var($data['sender_email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }
        
        $data['received_at'] = date('Y-m-d H:i:s');
        
        return $this->create($data); 
    }


    public function getAllMessages() {
        return $this->getAll();
    }
}
?>