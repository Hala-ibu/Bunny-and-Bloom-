<?php
require_once 'dao/UserDao.php';
require_once 'dao/OrderDao.php';


$userDao = new UserDao();
$orderDao = new OrderDao();


$users = $userDao->getAll();
print_r($users);


$orders = $orderDao->getAll();
print_r($orders);


?>
