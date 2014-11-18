Light php routing library

Composer install:
```php
composer install sw04/route-me
```

Initialize:

```php
$router = \Router\Singleton::getInstance();
```

Add route with group prefix & before action and match:

```php
function auth() {
    if (array_key_exist('user', $_SESSION) {
        return true;
    }
    return false;
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
```

This sample search "Host" class in root directory(DOCUMENT_ROOT).

TODO:

1. Add default controller & method

2. Add domain & sub domain support

3. Add documentation