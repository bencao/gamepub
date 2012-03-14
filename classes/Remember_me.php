<?php
/**
 * Table Definition for remember_me
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

define('REMEMBERME', 'rememberme');
define('REMEMBERME_EXPIRY', 14 * 24 * 60 * 60); // 30 days
	
class Remember_me extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'remember_me';                     // table name
    public $code;                            // varchar(32)  primary_key not_null
    public $user_id;                         // int(4)   not_null
    public $modified;                        // timestamp()   not_null default_CURRENT_TIMESTAMP

    /* Static get */
    function staticGet($k,$v=null)
    { return Memcached_DataObject::staticGet('Remember_me',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function sequenceKey()
    { return array(false, false); }

	static function remember($user=null) {
	    if (!$user) {
	        $user = common_current_user();
	        if (!$user) {
	            common_debug('No current user to remember', __FILE__);
	            return false;
	        }
	    }
	
	    $rm = new Remember_me();
	
	    $rm->code = common_good_rand(16);
	    $rm->user_id = $user->id;
	
	    // Wrap the insert in some good ol' fashioned transaction code
	
	    $rm->query('BEGIN');
	
	    $result = $rm->insert();
	
	    if (!$result) {
	        common_log_db_error($rm, 'INSERT', __FILE__);
	        return false;
	    }
	
	    $rm->query('COMMIT');
	
	    $cookieval = $rm->user_id . ':' . $rm->code;
	
	    common_set_cookie(REMEMBERME, $cookieval, time() + REMEMBERME_EXPIRY);
	
	    return true;
	}

	static function getRememberedUser()
	{
	
	    $user = null;
	
	    $packed = isset($_COOKIE[REMEMBERME]) ? $_COOKIE[REMEMBERME] : null;
		
	    if (!$packed
			|| strpos($packed, ':') == false) {
	        return null;
	    }
	
	    list($id, $code) = explode(':', $packed);
	
	    if (!$id || !$code) {
	        common_log(LOG_WARNING, 'Malformed rememberme cookie: ' . $packed);
	        self::forget();
	        return null;
	    }
	
	    $rm = Remember_me::staticGet($code);
	
	    //防止用户修改Cookie的Code, 也要到数据库查找是否正确
	    if (!$rm) {
	        common_log(LOG_WARNING, 'No such remember code: ' . $code);
	        self::forget();
	        return null;
	    }
	
	    if ($rm->user_id != $id) {
	        common_log(LOG_WARNING, 'Rememberme code for wrong user: ' . $rm->user_id . ' != ' . $id);
	        self::forget();
	        return null;
	    }
	
	    $user = User::staticGet($rm->user_id);
	
	    if (!$user) {
	        common_log(LOG_WARNING, 'No such user for rememberme: ' . $rm->user_id);
	        self::forget();
	        return null;
	    }
	
	    // successful!
	    $result = $rm->delete();
	
	    if (!$result) {
	        common_log_db_error($rm, 'DELETE', __FILE__);
	        common_log(LOG_WARNING, 'Could not delete rememberme: ' . $code);
	        self::forget();
	        return null;
	    }
	
	    common_set_user($user);
	
	    // We issue a new cookie, so they can log in
	    // automatically again after this session
	
	    self::remember($user);
	
	    return $user;
	}
	
	static function forget()
	{
		common_set_cookie(REMEMBERME, '', 0);
	}
}
