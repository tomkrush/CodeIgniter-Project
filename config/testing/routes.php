<?php

$route['migrations/created']			= 'migrations/created';
$route['migrations/create/(:any)']		= "migrations/create/$1";
$route['migrations'] 					= 'migrations/index';

$route['default_controller'] = "tests";
$route['404_override'] = '';