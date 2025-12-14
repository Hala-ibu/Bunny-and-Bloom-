<?php
require_once 'BaseService.php';
require_once 'UserDao.php'; 

use Firebase\JWT\JWT; // <--- ADD THIS LINE

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
        $data['role'] = 'user'; // Ensure default role is lowercase 'user'
        
        $cleanData = [
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role']
        ];

        return $this->create($cleanData); 
    }
    
    // MODIFIED LOGIN FUNCTION
    public function login($email, $password) {
        $user = $this->dao->getByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            
            $time = time(); 
            $expiration_time = $time + JWT_EXPIRATION_SECONDS;
            
            $payload = [
                'iat'  => $time,
                'exp'  => $expiration_time,
                'iss'  => JWT_ISSUER,
                'sub'  => $user['id'], // Subject: user ID
                'user' => [ // Custom claims to store in the token
                    'id'   => $user['id'],
                    'role' => $user['role']
                ]
            ];
            
            $jwt = JWT::encode(
                $payload, 
                JWT_SECRET,
                JWT_ALGORITHM
            );
            
            return [
                'user'  => $user,
                'token' => $jwt // Return the generated token
            ];
        }
        
        return false;
    }
    // END MODIFIED LOGIN FUNCTION

    public function getAllUsers() {
        return $this->dao->getAllUsers(); 
    }
    
    public function updateProfile($id, $data) {
        $allowedRoles = ['user', 'admin']; // Ensure roles are checked and match your DB enum
        
        if (isset($data['role']) && !in_array(strtolower($data['role']), $allowedRoles)) {
            throw new Exception("Invalid role specified.");
        }
        
        return $this->update($id, $data);
    }
}
?>