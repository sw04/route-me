Light php routing library

Composer install:

Initialize:

```php
$router = new \Router();
```

Add route with group prefix & before action:

```php
function auth() {
    if (array_key_exist('user', $_SESSION) {
        return true;
    }
    return false;
}

$router
    ->setPrefix('/host')
    ->setAction('before', 'auth')
    ->get('/{[0-9]+}');
```

This sample search "Host" class in root directory(DOCUMENT_ROOT).

TODO:

1. Add default controller & method

2. Add domain & sub domain support

3. Add documentation