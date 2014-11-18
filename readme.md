Light php routing library

Composer install:
```php
composer install sw04/route-me
```

Initialize:

```php
$router = \Router\Singleton::getInstance();
```

Simple route with required integer param:

```php
$router->get('/show/{[0-9]+}'); //sample: GET /show/1024
$router->get('/show/{[a-z]+}'); //sample: POST /show/sample
```

Simple route with not required param:

```php
$router->get('/show/!{[0-9]+}'); //sample: GET /show or /show/1024
```

Match routes:

```php
try {
    $result = $router->match(getenv('REQUEST_URI'));
    if (is_array($result)) { //convert to json if is array
        $result = json_encode($result);
    }
    echo $result; //echo result of match
} catch(\Router\RouterException $e) {
    echo $e->getMessage().' code is '.$e->getCode();
}
```


TODO:

1. Add domain & sub domain support

2. Add documentation