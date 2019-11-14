<?php

$di = new RecursiveDirectoryIterator(__DIR__ . '/../routers',RecursiveDirectoryIterator::SKIP_DOTS);
$it = new RecursiveIteratorIterator($di);

foreach($it as $router) {
    if (pathinfo($router,PATHINFO_EXTENSION) == "php") {
        include $router;
    }
}
