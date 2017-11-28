<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require './SimpleRouter.php';
$router = new SimpleRouter('/simple-router/');
print_r($router->parse());
echo "<br>";
print_r($router->find('user', 'profile', ['id' => 100], ['a' => 'b']));
echo "<br>";
print_r($router->find('user', 'profile', ['id' => 'xxx']));
echo "<br>";
print_r($router->find());
echo "<br>";
print_r($router->find('user', 'save_profile', ['id' => 'test'], ['a' => 'b']));
echo "<br>";
print_r($router->find('catalog', 'show', ['section2' => 222, 'section1' => 111], ['a' => 'b']));