<?php 
namespace ZendLDAP; 

use Config;
use DB;
use Exception;
use Zend_Ldap;


/**
 * Zend LDAP Auth Driver for Laravel
 *
 * @author	Watcharapol Tadtiang (Aborigines)
 * @link	https://github.com/aborigines/laravel-zendldap
 * 
 */
class ZendLDAP extends \Laravel\Auth\Drivers\Driver {

	protected $conn;

	private $config;

	private $config_options;

	private $config_options_attributes;

	public function __construct()
	{

		// get ldap config
		$this->config = Config::get('auth.ldap');

		// get ldap config optional 
		$this->config_options = Config::get('auth.ldap_options');

		// get ldap config optional atrributes
		$this->config_options_attributes = Config::get('auth.ldap_options.attributes');

		// connect
		$this->connect();

		parent::__construct();
	}

	/**
	 * Zend_Ldap Connect
	 * @return void
	 */
	public function connect() {
		$this->conn = new Zend_Ldap();
		$this->conn->setOptions($this->config);
		$this->conn->bind();
	}

	/**
	 * Get the current user of the application.
	 *
	 * If the user is a guest, null should be returned.
	 *
	 * @param  int|object  $token
	 * @return mixed|null
	 */
	public function retrieve($token)
	{
		if(empty($token)) 
		{
			return;
		} 

		$checkRDN = $this->conn->exists($token);

		if ($checkRDN) {
			return $this->get_user_object($token);
		}
	}

	/**
	 * Attempt to log a user into the application.
	 *
	 * @param  array  $arguments
	 * @return void
	 */
	public function attempt($arguments = array())
	{	
		$username = $arguments['username'];
		$password = $arguments['password'];

		// bind user
		$checkBind = $this->conn->bind($username,$password);

		if ($checkBind) 
		{
			// get rdn from baseDN
			$rdn =  $this->get_rdn($username);

			$user = $this->get_user_object($rdn);

			return $this->login($user->dn, 
				array_get($arguments, 'remember'));
		} 
		return false;
	}

	/**
	 * get rdn from baseDN
	 *
	 * @param  string $username
	 * @return string
	 */
	public function get_rdn($username) 
	{	
		$accountForm = $this->config['accountCanonicalForm'];
		$rdn = $this->conn->getCanonicalAccountName($username, $accountForm);
		return $rdn;
	}

	/**
	 * fetch attributes to Auth::user();
	 *
	 * @param  string $rdn
	 * @return array
	 */
	protected function get_user_object($rdn) 
	{
		// user attribute 
		$userInfo = $this->conn->getNode($rdn);
		
		foreach ( $this->config_options_attributes as $k => $attr ) 
		{
			$user[$attr] = $userInfo->$attr;
	    }
	    
	    // group attributes
	    $groupdn = $this->config_options['groupdn'];
	    $filter = sprintf('%s=%s',$this->config_options['group_member'],$rdn);
	    $attributes = $this->config_options['group_attribute'];
	    
	    $search = $this->conn->search($filter, $groupdn,
	    		Zend_Ldap::SEARCH_SCOPE_SUB, $attributes);

	    foreach( $search as $value)
		{
			$user['group'][] = strtolower(current(current($value)));
		}
		
		return (object) $user;	
	}
}
