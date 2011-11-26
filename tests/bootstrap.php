<?php
chdir(dirname(__FILE__));
$pearDirectory = exec('pear config-get php_dir');
$includePaths = array(
	realpath('classes'),
	realpath('resources/classes'),
	$pearDirectory.'/PHPUnit',
	$pearDirectory.'/PHPUnit/dbunit',
	$pearDirectory.'/PHPUnit/phpunit-mock-objects',
	realpath('../classes/'),
	realpath('../classes/Exception')
);
set_include_path(get_include_path().PATH_SEPARATOR.implode(PATH_SEPARATOR, $includePaths));

require_once 'Autoloader.php';
Autoloader::init();
spl_autoload_register(array('Autoloader', 'load'));
define('TEST_DB_NAME', 'unit_tests');
define('DEFAULT_DATE_FORMAT', 'Y-m-d H:i:s');
mb_internal_encoding('utf-8');