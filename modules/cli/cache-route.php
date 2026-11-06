<?php

use Libraries\Route;
use Libraries\RouteCache;

require 'config/routes.php';

$routes = Route::exportCache();
(new RouteCache('cache/routes.php'))->save($routes);