<?php

$route['migrations/create/(:any)']		= "migrations/create/$1";
$route['migrations/seed']				= "migrations/seed";
$route['migrations/reset']				= "migrations/reset";
$route['migrations'] 					= 'migrations/index';

$route['unit/javascript/(:any)'] 		= 'unit/javascript/$1';
$route['unit/images/(:any)'] 			= 'unit/images/$1';
$route['unit/stylesheets/(:any)'] 		= 'unit/stylesheets/$1';
$route['(:any)'] 						= 'unit';

$route['default_controller'] 			= "unit";
$route['404_override'] 					= '';