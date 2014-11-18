Light php routing library support:
- routes
- optional & requirements params
- group routes
- actions before & after route
- define controller, method & params

Composer install:

```php
composer require sw04/route-me
```

Initialize:

```php
$router = \Router\Singleton::getInstance();
```

Simple route for GET and POST methods with required integer and string param:

```php
$router->get('/show/{[0-9]+}'); //sample: GET /show/1024
$router->post('/show/{[a-z]+}'); //sample: POST /show/sample
```

Simple route with not required param:

```php
$router->get('/show/!{[0-9]+}'); //sample: GET /show or /show/1024
```

Simple route with defined controller & method:
```php
$router
    ->setController('index')
    ->setMethod('index')
    ->get('/')
    ->clear();
```

Simple group routes(set prefix "/admin") and add actions before route match:

```php
function isAuth() {
    //check auth & return true or false
    return true;
}
function isAdmin() {
    //check role is admin or not & return true or false
    return false;
}
$router
    ->setPrefix('/admin')
    ->setAction('before', 'isAuth')
    ->setAction('before', 'isAdmin')
    ->get('/dashboard')
    ->clear();
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

All routes set next requirements for routes:

```text
method - GET, POST, ANY
prefix - for route url
url - to route
actions - before & after route match
defineClass - define controller
defineMethod - define method
defineParams - define params
```

For clear all this requirements use:
```php
$routes->clear();
```
