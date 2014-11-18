Light php routing library

Composer install:
```php
composer install sw04/route-me
```

Initialize:

```php
$router = \Router\Singleton::getInstance();
```

Simple route:

```php
$router->get('/show/{[0-9]+}');
```

```php
try {
    $result = $router->match(getenv('REQUEST_URI'));
    if (is_array($result)) {
        echo json_encode($result);
    }
} catch(\Router\RouterException $e) {
    echo $e->getMessage().' code is '.$e->getCode();
}
```
This sample search "Host" class in root directory(DOCUMENT_ROOT).

TODO:

1. Add domain & sub domain support

2. Add documentation