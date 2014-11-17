<?php
require_once realpath(dirname(__FILE__)).'/vendor/autoload.php';
require 'vendor/autoload.php';

function auth() {
    return true;
}

$router = new \Router();
$router
    ->setPrefix('/admin')
    ->setAction('before', 'auth')
    ->get('/dashboard/{[0-9]+}')
    ->clear();

$router->get('/show/{[0-9]+}');

$router->post('/api/getMoreComments/{[0-9]+}/{[0-9]+}');

$router->match(getenv('REQUEST_URI'));