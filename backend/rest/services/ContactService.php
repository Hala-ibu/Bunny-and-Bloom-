<?php
require_once __DIR__ . '/../dao/ContactDao.php'; 
require_once __DIR__ . '/BaseService.php';
class ContactService extends BaseService {

    public function __construct() {
        $dao = new ContactDao(); 
        parent::__construct($dao);
    }
    
    public function submitMessage($data) {
        $name = $data['sender_name'] ?? null;
        $email = $data['sender_email'] ?? null;
        $message = $data['message_content'] ?? null;
        $phone = $data['sender_phone'] ?? null; 

        if (empty($name) || empty($email) || empty($message)) {
            throw new Exception("Name, email, and message content are required.");
        }

        if (strlen($name) < 2) {
            throw new Exception("Name must be at least 2 characters long.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }
        
        if (strlen($message) < 10) {
            throw new Exception("Message must be at least 10 characters long.");
        }
        if (strlen($message) > 1000) {
            throw new Exception("Message must be no more than 1000 characters long.");
        }

        if (!empty($phone) && !preg_match('/^[+]*[0-9]{1,4}[-\s\./0-9]*$/', $phone)) {
            throw new Exception("Invalid phone number format.");
        }
        
        $cleanData = [
            'sender_name' => $name,
            'sender_email' => $email,
            'message_content' => $message,
            'received_at' => date('Y-m-d H:i:s')
        ];
        
        if (!empty($phone)) {
            $cleanData['sender_phone'] = $phone;
        }
        
        return $this->create($cleanData); 
    }

    public function getAllMessages() {
        return $this->dao->getAllMessages(); 
    }
}
?>