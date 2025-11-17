<?php
require 'vendor/autoload.php';


require_once __DIR__ . '/rest/services/UserService.php';
require_once __DIR__ . '/rest/services/ProductService.php';
require_once __DIR__ . '/rest/services/OrderService.php';
require_once __DIR__ . '/rest/services/InventoryService.php';
require_once __DIR__ . '/rest/services/ReviewService.php';
require_once __DIR__ . '/rest/services/ContactService.php';

Flight::register('userService', 'UserService');
Flight::register('productService', 'ProductService');
Flight::register('orderService', 'OrderService');
Flight::register('inventoryService', 'InventoryService');
Flight::register('reviewService', 'ReviewService');
Flight::register('contactService', 'ContactService');


require_once __DIR__ . '/rest/routes/OrderRoutes.php';
require_once __DIR__ . '/rest/routes/UserRoutes.php';
require_once __DIR__ . '/rest/routes/ProductsRoutes.php';

require_once __DIR__ . '/rest/routes/InventoryRoutes.php'; 


require_once __DIR__ . '/rest/routes/ReviewRoutes.php';
require_once __DIR__ . '/rest/routes/ContactRoutes.php';

Flight::start();
?>