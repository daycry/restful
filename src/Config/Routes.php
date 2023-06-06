<?php

namespace Daycry\RestFul\Config;

/** @var \CodeIgniter\Router\RouteCollection $routes */
$routes->options('(:any)', '', ['filter' => 'cors']);
