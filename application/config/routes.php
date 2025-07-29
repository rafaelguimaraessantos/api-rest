<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// API Routes
$route['api/users']['GET'] = 'api/users/index';
$route['api/users']['POST'] = 'api/users/create';
$route['api/users/(:num)']['GET'] = 'api/users/show/$1';
$route['api/users/(:num)']['PUT'] = 'api/users/update/$1';
$route['api/users/(:num)']['DELETE'] = 'api/users/delete/$1';