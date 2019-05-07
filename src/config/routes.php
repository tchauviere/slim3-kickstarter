<?php

// Routes Loading
foreach (glob(__DIR__ . '/../routers/*.php') as $router) {
    include $router;
}
