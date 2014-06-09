#SubdomainMap

[![Build Status](https://travis-ci.org/AlejandroHerr/stack-subdomainMap.svg?branch=develop)](https://travis-ci.org/AlejandroHerr/stack-subdomainMap)

Middleware to map the kernels depending on the subdomain.

**Heavily** inspired in [URL Map Stack Middleware](https://github.com/stackphp/url-map).

##HOW TO

###Example using Silex Application
```php
<?php
$loader = require ROOT . "/vendor/autoload.php";

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$app=new Application();
$app->get('/', function () use ($app) {
    return 'Main app';
});

$appA=new Application();
$appA->get('/', function () use ($appA) {
    return 'appA';
});
$appB=new Application();
$appB->get('/', function () use ($appB) {
    return 'appB';
});

$map = array(
    'appa' => $appA,
    'appb' => $appB
);

$app = new AlejandroHerr\Stack\SubdomainMap($app,$map);

$request = Request::createFromGlobals();
$response = $app->handle($request);
$response->send();
```

##Recommendations
When working with large apps/HttpKernelsInterfaces, try the [LazyHttpKernel](https://github.com/stackphp/LazyHttpKernel)

####Example
```php
<?php
$loader = require ROOT . "/vendor/autoload.php";

use Silex\Application;
use Stack\lazy;
use Symfony\Component\HttpFoundation\Request;

$app=new Application();
$app->get('/', function () use ($app) {
    return 'Nothing here';
});

$appA=new Application();
$appA->get('/', function () use ($appA) {
    return 'I am appA';
});
$appA = lazy(function () use ($appA) {
    return $appA;
});

$app = new AlejandroHerr\Stack\SubdomainMap(
    $app,
    array('appa' => $appA)
);

$request = Request::createFromGlobals();
$response = $app->handle($request);
$response->send();
```
