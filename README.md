Laravel ZendLDAP Auth
================

Laravel ZendLDAP Auth

### Requirement

- Laravel v.3
- Zend Framework v.1 
- LDAP ( OpenLDAP or AD etc... )

### Pre-Config

This bundles include 3 libraries only.
- Application
- Ldap
- Loader

Disable , If you already use zend framework v.1

*bundles/zendldapauth/start.php*
```php
set_include_path(implode(PATH_SEPARATOR, array(
    __DIR__ . '/libraries',
    get_include_path(),
)));

require_once Bundle::path('zendldapauth') . '/libraries/Zend/Loader/Autoloader.php';

$autoloader = Zend_Loader_Autoloader::getInstance();
```

### Config 

*application/config/auth.php*
```php
	// change to zendldapauth
	'driver' => 'zendldapauth',
```

*application/bundles.php*
```php	
	return array(
	    'zendldapauth' => array(
	        'auto' => true
	    )
	);
```

*application/config/auth.php*
```php
	// Examples with Openldap
	'ldap' => array(
		'host' 	=> 'localhost',
		'username' => 'uid=test,ou=people,dc=example,dc=com',
		'password' => 'test',
		'bindRequiresDn' => true,
		'accountFilterFormat' => '(uid=%s)',
		'accountDomainName' => 'example.com',
		'accountDomainNameShort' => 'example',
		// request for get RDN
		'accountCanonicalForm' => 1, // ACCTNAME_FORM_DN
		'baseDn' => 'ou=people,dc=example,dc=com',
	),

	'ldap_options' => array(
		// request for Auth::user() , return attributes after login
		'attributes' => array('dn','uid','cn','sn','givenname','pwdchangedtime'),
        // group attributes
        'groupdn' => 'ou=group,dc=example,dc=com',
        'group_attribute' => 'cn',
        'group_member' => 'roleOccupant',
	),
```

### Document
- Zend Framework LDAP 	http://framework.zend.com/manual/1.12/en/zend.ldap.html

### Thanks
- knowledge
	- https://github.com/kbanman/laravel-ldapauth
	- https://github.com/ccovey/ldap-auth
