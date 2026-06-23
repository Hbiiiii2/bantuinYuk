<?php
require 'vendor/autoload.php';
$app = new \CodeIgniter\CodeIgniter(config('App'));
$app->initialize();
$db = \Config\Database::connect();
print_r($db->getFieldNames('users'));
