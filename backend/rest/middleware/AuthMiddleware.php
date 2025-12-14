<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;

class AuthMiddleware {

    public function verifyToken($token) {
        if (!$token) {
            throw new Exception("Missing JWT token in Authorization header.", 401);
        }

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        try {
            $decoded = JWT::decode($token, new Key(JWT_SECRET, JWT_ALGORITHM));
            
            if ($decoded->iss !== JWT_ISSUER) {
                throw new Exception("Invalid token issuer.", 401);
            }

            Flight::set('user', (array) $decoded->user); 

            return $decoded;

        } catch (ExpiredException $e) {
            throw new Exception("Token has expired. Please log in again.", 401);
        } catch (SignatureInvalidException $e) {
            throw new Exception("Invalid signature. Token unauthorized.", 401);
        } catch (BeforeValidException $e) {
             throw new Exception("Token is not yet valid.", 401);
        } catch (Exception $e) {
            throw new Exception("Invalid token format or secret.", 401);
        }
    }

    public function authorizeRoles($allowedRoles) {
        $user = Flight::get('user');

        if (!$user || !isset($user['role'])) {
            Flight::halt(403, "Authorization required.");
        }
        
        $userRole = strtolower($user['role']);
        $allowedRoles = array_map('strtolower', $allowedRoles);

        if (!in_array($userRole, $allowedRoles)) {
            Flight::halt(403, "Access forbidden. Required roles: " . implode(', ', $allowedRoles) . ".");
        }
    }
    
    public function authorizeRole($allowedRole) {
        $this->authorizeRoles([$allowedRole]);
    }
}
?>