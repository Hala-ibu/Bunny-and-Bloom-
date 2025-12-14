<?php
require_once 'BaseService.php';
require_once 'UserDao.php'; 
require_once __DIR__ . '/../../config.php'; 

use Firebase\JWT\JWT; 

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
        $data['role'] = 'user'; 
        
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
            
            $time = time(); 
            $expiration_time = $time + Config::JWT_EXPIRATION_SECONDS();
            
            $payload = [
                'iat'  => $time,
                'exp'  => $expiration_time,
                'iss'  => Config::JWT_ISSUER(), 
                'sub'  => $user['id'], 
                'user' => [ 
                    'id'   => $user['id'],
                    'role' => $user['role']
                ]
            ];
            
            $jwt = JWT::encode(
                $payload, 
                Config::JWT_SECRET(),
                Config::JWT_ALGORITHM() 
            );
            
            return [
                'user'  => $user,
                'token' => $jwt 
            ];
        }
        
        return false;
    }

    public function getAllUsers() {
        return $this->dao->getAllUsers(); 
    }
    
    public function updateProfile($id, $data) {
        $allowedRoles = ['user', 'admin']; 
        
        if (isset($data['role']) && !in_array(strtolower($data['role']), $allowedRoles)) {
            throw new Exception("Invalid role specified.");
        }
        
        return $this->update($id, $data);
    }
}
?>