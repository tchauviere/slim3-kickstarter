<?php

$di = new RecursiveDirectoryIterator(getenv('ROUTER_DIR'),RecursiveDirectoryIterator::SKIP_DOTS);
$it = new RecursiveIteratorIterator($di);

foreach($it as $router) {
    if (pathinfo($router,PATHINFO_EXTENSION) == "php") {
        include $router;
    }
}
