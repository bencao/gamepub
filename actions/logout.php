<?php
/**
 * Shaishai, the distributed microblog
 *
 * Login form
 *
 * PHP version 5
 *
 * @category  Login
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Login form
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class LogoutAction extends ShaiAction
{
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
	
   	/**
     * This is read only.
     *
     * @return boolean true
     */
    function isReadOnly($args)
    {
        return false;
    }

    /**
     * Class handler.
     *
     * @param array $args array of arguments
     *
     * @return nothing
     */
    function handle($args)
    {
        parent::handle($args);
        if (! common_current_user()) {
            common_redirect(common_path(''), 303);
        } else {
            if (Event::handle('StartLogout', array($this))) {
                $this->logout();
            }
            Event::handle('EndLogout', array($this));

            common_set_returnto(null);
            common_redirect(common_path(''), 303);
        }
    }

    function logout()
    {
    	$user = common_current_user();
    	Logout_log::logNew($user->id);
        common_set_user(null);
        Remember_me::forget();
        session_destroy();
    }
}
?>