<?php

$route['migrations/create/(:any)']		= "migrations/create/$1";
$route['migrations/seed']				= "migrations/seed";
$route['migrations/reset']				= "migrations/reset";
$route['migrations'] 					= 'migrations/index';

$route['(:any)'] = 'tests';

$route['default_controller'] = "tests";
$route['404_override'] = '';