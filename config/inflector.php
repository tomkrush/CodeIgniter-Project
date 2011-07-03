<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
| The following pluralise and singularise rules and exceptions are based on the CakePHP(tm)
| Inflector class by (http://book.cakephp.org/view/572/Class-methods)
|
| @author	milkboyuk@gmail.com based on Inflector class by CakePHP(tm)
| @copyright	Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
| @link	http://cakephp.org CakePHP(tm) Project
| @license	http://www.opensource.org/licenses/mit-license.php The MIT License
|
|
|--------------------------------------------------------------------------
| Words that do not follow usual singular/plural rules
|--------------------------------------------------------------------------
| Use the following format: $key => singular form / $value => plural
|
| Example: $config['irregular']['man'] = 'men';
*/
$config['irregular'] = array(
	'atlas' => 'atlases',
	'beef' => 'beefs',
	'brother' => 'brothers',
	'child' => 'children',
	'corpus' => 'corpuses',
	'cow' => 'cows',
	'ganglion' => 'ganglions',
	'genie' => 'genies',
	'genus' => 'genera',
	'hoof' => 'hoofs',
	'loaf' => 'loaves',
	'man' => 'men',
	'money' => 'monies',
	'mongoose' => 'mongooses',
	'move' => 'moves',
	'mythos' => 'mythoi',
	'numen' => 'numina',
	'occiput' => 'occiputs',
	'octopus' => 'octopuses',
	'opus' => 'opuses',
	'ox' => 'oxen',
	'penis' => 'penises',
	'person' => 'people',
	'sex' => 'sexes',
	'soliloquy' => 'soliloquies',
	'testis' => 'testes',
	'trilby' => 'trilbys',
	'turf' => 'turfs',
	'wave' => 'waves', // only used in singular() in cake
);


/*
|--------------------------------------------------------------------------
| Words that remain the same whether singular or plural
|--------------------------------------------------------------------------
| Example: $config['inflector_uninflected'][] = 'news';
*/
$config['uninflected'] = array(
	'.*[nrlm]ese',
	'.*deer',
	'.*fish',
	'.*measles',
	'.*ois',
	'.*pox',
	'.*sheep',
	'Amoyese',
	'bison',
	'Borghese',
	'bream',
	'breeches',
	'britches',
	'buffalo',
	'cantus',
	'carp',
	'chassis',
	'clippers',
	'cod',
	'coitus',
	'Congoese',
	'contretemps',
	'corps',
	'debris',
	'diabetes',
	'djinn',
	'eland',
	'elk',
	'equipment',
	'Faroese',
	'flounder',
	'Foochowese',
	'gallows',
	'Genevese',
	'Genoese',
	'Gilbertese',
	'graffiti',
	'headquarters',
	'herpes',
	'hijinks',
	'Hottentotese',
	'information',
	'innings',
	'jackanapes',
	'Kiplingese',
	'Kongoese',
	'Lucchese',
	'mackerel',
	'Maltese',
	'media',
	'mews',
	'moose',
	'mumps',
	'Nankingese',
	'news',
	'nexus',
	'Niasese',
	'Pekingese',
	'Piedmontese',
	'pincers',
	'Pistoiese',
	'pliers',
	'Portuguese',
	'proceedings',
	'rabies',
	'rice',
	'rhinoceros',
	'salmon',
	'Sarawakese',
	'scissors',
	'sea[- ]bass',
	'series',
	'Shavese',
	'shears',
	'siemens',
	'species',
	'swine',
	'trousers',
	'trout',
	'tuna',
	'Vermontese',
	'Wenchowese',
	'whiting',
	'wildebeest',
	'Yengeese',
);


/*
|--------------------------------------------------------------------------
| Conventional rules for turning singular into plural
|--------------------------------------------------------------------------
| Use the following format: $key => singular form regex / $value => plural form regex
|
| Example: $config['plural']['rules']['/(c)hild$/i'] => '\1hildren'';
|          matches 'Child' or 'child' and returns 'Children' or 'children'
*/
$config['plural']['rules'] = array(
	'/(s)tatus$/i' => '\1\2tatuses',
	'/(quiz)$/i' => '\1zes',
	'/^(ox)$/i' => '\1\2en',
	'/([m|l])ouse$/i' => '\1ice',
	'/(matr|vert|ind)(ix|ex)$/i'  => '\1ices',
	'/(x|ch|ss|sh)$/i' => '\1es',
	'/([^aeiouy]|qu)y$/i' => '\1ies',
	'/(hive)$/i' => '\1s',
	'/(?:([^f])fe|([lr])f)$/i' => '\1\2ves',
	'/sis$/i' => 'ses',
	'/([ti])um$/i' => '\1a',
	'/(p)erson$/i' => '\1eople',
	'/(m)an$/i' => '\1en',
	'/(c)hild$/i' => '\1hildren',
	'/(buffal|tomat)o$/i' => '\1\2oes',
	'/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|vir)us$/i' => '\1i',
	'/us$/' => 'uses',
	'/(alias)$/i' => '\1es',
	'/(ax|cris|test)is$/i' => '\1es',
	'/s$/' => 's',
	'/^$/' => '',
	'/$/' => 's',
);


/*
|--------------------------------------------------------------------------
| Conventional rules for turning plural into singular
|--------------------------------------------------------------------------
| Use the following format: $key => plural form regex / $value => singular form regex
|
| Example: $config['singular']['rules']['/(c)hildren$/i'] => '\1\2hild';
|          matches 'Children' or 'children' and returns 'Child' or 'child'
*/
$config['singular']['rules'] = array(
	'/(s)tatuses$/i' => '\1\2tatus',
	'/^(.*)(menu)s$/i' => '\1\2',
	'/(quiz)zes$/i' => '\\1',
	'/(matr)ices$/i' => '\1ix',
	'/(vert|ind)ices$/i' => '\1ex',
	'/^(ox)en/i' => '\1',
	'/(alias)(es)*$/i' => '\1',
	'/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|viri?)i$/i' => '\1us',
	'/([ftw]ax)es/' => '\1',
	'/(cris|ax|test)es$/i' => '\1is',
	'/(shoe)s$/i' => '\1',
	'/(o)es$/i' => '\1',
	'/ouses$/' => 'ouse',
	'/uses$/' => 'us',
	'/([m|l])ice$/i' => '\1ouse',
	'/(x|ch|ss|sh)es$/i' => '\1',
	'/(m)ovies$/i' => '\1\2ovie',
	'/(s)eries$/i' => '\1\2eries',
	'/([^aeiouy]|qu)ies$/i' => '\1y',
	'/([lr])ves$/i' => '\1f',
	'/(tive)s$/i' => '\1',
	'/(hive)s$/i' => '\1',
	'/(drive)s$/i' => '\1',
	'/([^fo])ves$/i' => '\1fe',
	'/(^analy)ses$/i' => '\1sis',
	'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
	'/([ti])a$/i' => '\1um',
	'/(p)eople$/i' => '\1\2erson',
	'/(m)en$/i' => '\1an',
	'/(c)hildren$/i' => '\1\2hild',
	'/(n)ews$/i' => '\1\2ews',
	'/eaus$/' => 'eau',
	'/^(.*us)$/' => '\\1',
	'/ss$/' => 'ss',
	'/s$/i' => '',
	'/^(.*)/' => '\1',
);


/* End of file inflector.php */
/* Location: ./system/application/config/inflector.php */