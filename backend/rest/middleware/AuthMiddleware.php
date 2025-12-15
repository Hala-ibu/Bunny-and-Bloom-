<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once __DIR__ . '/../constants/Roles.php';

class AuthMiddleware
{
    protected $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function verifyToken($authHeader) {
        if (!$authHeader) {
            throw new Exception("Missing authorization header.");
        }

        if (preg_match('/Bearer\s+(.*)/', $authHeader, $matches)) {
            $token = $matches[1];
        } else {
            $token = $authHeader;
        }
        
        if (!$token) {
            throw new Exception("Missing token in authorization header.");
        }

        $decoded_token = JWT::decode($token, new Key(Config::JWT_SECRET(), 'HS256'));

        $this->app->set('user', $decoded_token->user);
        
        return true;
    }

    public function authorizeRole($requiredRole) {
        $user = $this->app->get('user');

        if (!$user || !isset($user['role']) || $user['role'] !== $requiredRole) {
            Flight::halt(403, "Access denied. Role does not meet requirement: {$requiredRole}");
        }
        return true;
    }

    public function authorizeRoles(array $requiredRoles) {
        $user = $this->app->get('user');

        if (!$user || !isset($user['role']) || !in_array($user['role'], $requiredRoles)) {
            $rolesString = implode(', ', $requiredRoles);
            Flight::halt(403, "Access denied. User must be one of the following roles: {$rolesString}");
        }
        return true;
    }
}