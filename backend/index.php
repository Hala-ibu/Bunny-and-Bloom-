<?php
require 'vendor/autoload.php';

require_once 'config.php'; 
require_once __DIR__ . '/rest/constants/Roles.php'; 

use flight\Engine;
$app = new Engine();

require_once __DIR__ . '/rest/middleware/AuthMiddleware.php'; 
$app->register('auth_middleware', AuthMiddleware::class, [$app]);

require_once __DIR__ . '/rest/service/AuthService.php'; 
require_once __DIR__ . '/rest/service/UserService.php'; 
require_once __DIR__ . '/rest/service/ProductService.php';
require_once __DIR__ . '/rest/service/OrderService.php'; 
require_once __DIR__ . '/rest/service/ReviewService.php'; 
require_once __DIR__ . '/rest/service/InventoryService.php'; 
require_once __DIR__ . '/rest/service/ContactService.php'; 

$app->register('authService', AuthService::class);
$app->register('userService', UserService::class);
$app->register('productService', ProductService::class);
$app->register('orderService', OrderService::class);
$app->register('reviewService', ReviewService::class);
$app->register('inventoryService', InventoryService::class);
$app->register('contactService', ContactService::class);

Flight::route('/*', function(){
    $url = Flight::request()->url;
    
    if (
        strpos($url, '/auth/login') === 0 || 
        strpos($url, '/auth/register') === 0 || 
        strpos($url, '/docs') === 0 || 
        strpos($url, '/contact') === 0 || 
        (Flight::request()->method === 'GET' && strpos($url, '/products') === 0)
    ) {
        return true;
    }

    try {
        $authHeader = Flight::request()->getHeader('Authorization');
        
        Flight::auth_middleware()->verifyToken($authHeader);
        
        return true;
        
    } catch (\Exception $e) {
        Flight::halt(401, $e->getMessage()); 
    }
});

require_once __DIR__ . '/rest/routes/AuthRoutes.php'; 
require_once __DIR__ . '/rest/routes/ContactRoutes.php'; 
require_once __DIR__ . '/rest/routes/InventoryRoutes.php'; 
require_once __DIR__ . '/rest/routes/OrderRoutes.php'; 
require_once __DIR__ . '/rest/routes/ProductsRoutes.php'; 
require_once __DIR__ . '/rest/routes/ReviewRoutes.php'; 
require_once __DIR__ . '/rest/routes/UserRoutes.php'; 

Flight::start();