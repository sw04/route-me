<?php
require_once realpath(dirname(__FILE__)).'/vendor/autoload.php';
require 'vendor/autoload.php'; // use PCRE patterns you need Pux\PatternCompiler class.

function auth() {
    return true;
}
$router = new \Router();
$router
    ->setPrefix('/host')
    ->setAction('before', 'auth')
    ->get('/{[0-9]+}');

$router->match(getenv('REQUEST_URI'));