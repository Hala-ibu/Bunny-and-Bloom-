<?php
require_once 'BaseService.php';
require_once 'dao/UserDao.php'; 

class UserService extends BaseService {

    public function __construct() {
        parent::__construct(new UserDao()); 
    }

    public function registerUser($data) {
        if (empty($data['email']) || empty($data['password']) || empty($data['username'])) {
            throw new Exception("Username, email, and password are required fields.");
        }
        
        if ($this->dao->getByEmail($data['email'])) { 
            throw new Exception("This email address is already registered.");
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['role'] = 'User'; 
        
        return $this->create($data); 
    }
    

    public function login($email, $password) {
        $user = $this->dao->getByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            return $user;
        }
        return false;
    }
    

    public function getAllUsers() {
        return $this->dao->getAll();
    }
    
 
    public function updateProfile($id, $data) {
        return $this->update($id, $data);
    }
}
?>