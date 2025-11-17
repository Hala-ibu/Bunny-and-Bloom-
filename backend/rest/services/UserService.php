<?php
require_once 'BaseService.php';
require_once 'UserDao.php'; 

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
        
        $cleanData = [
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role']
        ];

        return $this->create($cleanData); 
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
        return $this->dao->getAllUsers(); 
    }
    

    public function updateProfile($id, $data) {
        if (isset($data['role']) && !in_array($data['role'], ['User', 'Admin', 'Staff'])) {
            throw new Exception("Invalid role specified.");
        }
        return $this->dao->updateUser($id, $data);
    }


    public function deleteUser($id) {
        return $this->dao->deleteUser($id);
    }
}
?>