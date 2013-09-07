<?php

/*
|--------------------------------------------------------------------------
| Set Enverlopment For Zend Framework v.1
|--------------------------------------------------------------------------
| Include 3 library only.
| 	* Application
|	* Ldap
| 	* Loader
| If you use other path of zend framework v.1 disable it.
|
*/
set_include_path(implode(PATH_SEPARATOR, array(
    __DIR__ . '/libraries',
    get_include_path(),
)));

require_once Bundle::path('zendldapauth') . '/libraries/Zend/Loader/Autoloader.php';

$autoloader = Zend_Loader_Autoloader::getInstance();


/*
|--------------------------------------------------------------------------
| Set For ZendLDAP
|--------------------------------------------------------------------------
| Autoloader 
| 	* map
|	* alias
| Auth
|	* extend
|
| Don't Remove or Disable it.
*/
Autoloader::map(array(
	'ZendLDAP\\ZendLDAP' => Bundle::path('zendldapauth').'zendldap.php',
));

Autoloader::alias('ZendLDAP\\ZendLDAP', 'zendldapauth');

Auth::extend('zendldapauth', function()
{
	return new ZendLDAP\ZendLDAP;
});
