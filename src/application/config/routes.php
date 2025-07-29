<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// API Routes
$route['api/health'] = 'api/health/index';
$route['api/users/(:num)'] = 'api/users/show/$1';
$route['api/users'] = 'api/users/index';