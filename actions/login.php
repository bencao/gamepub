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
 * @author    Ben Cao <benb88@gmail.com> 20090905
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
 * @author   Ben Cao <benb88@gmail.com> 20090905
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

//require_once INSTALLDIR.'/templates/loginhtmltemplate.php';

class LoginAction extends ShaiAction
{
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
		$this->cache_allowed = false;
	}
	
	/**
     * Is this a read-only action?
     *
     * @return boolean false
     */

    function isReadOnly($args)
    {
        return false;
    }

    /**
     * Handle input, produce output
     *
     * Switches on request method; either shows the form or handles its input.
     *
     * @param array $args $_REQUEST data
     *
     * @return void
     */
    function handle($args)
    {
        parent::handle($args);
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->checkLogin();
        } else {
            common_ensure_session();
           	$this->showForm();
        }
    }
    
   /**
     * Check the login data
     *
     * Determines if the login data is valid. If so, logs the user
     * in, and redirects to the 'with friends' page, or to the stored
     * return-to URL.
     *
     * @return void
     */

    function checkLogin()
    {
        // XXX: login throttle

        $uname = strtolower($this->trimmed('uname'));
        
        $password = $this->arg('password');
        
        $profile = Profile::getByUNameAndPassword($uname, $password);
        
        if (! $profile) {
            $this->showForm('用户名或密码无效。');
            return;
        } 
//        else if ($user->passworderrorcount > 2) {
//        	if (! $this->hasVerifyCode()) {
//        		$this->addPassVariable('login_needverify', true);
//	        	$this->showForm('密码错误次数过多，需要输入验证码。');
//	        	return;
//        	} else if (! $this->isVerifyPass()) {
//        		$this->addPassVariable('login_needverify', true);
//        		$this->showForm('验证码错误。');
//        		return;
//        	}
//        }
        
        if (strcmp(common_munge_password($password, $profile->id),
	                        $profile->password) != 0) {
	        $this->showForm('用户名或密码无效。');
	        return;        	
	    }
        
//    	if (0 == strcmp(common_munge_password($password, $user->id),
//	                        $user->password)) {
//	         if ($user->passworderrorcount > 0) {
//		         $original = clone($user);
//		         $user->passworderrorcount = 0;
//		         $user->update($original);
//	         }
//	    } else {
//	    	 $original = clone($user);
//	         $user->passworderrorcount ++;
//	         $user->update($original);
//	         if ($user->passworderrorcount > 2) {
//	         	$this->addPassVariable('login_needverify', true);
//	         }
//	         $this->showForm('用户名或密码无效。');
//	         return;
//	    }

	    if ($profile->is_banned) {
	    	$this->showForm('你的账号已被封禁。');
            return;
	    }
	    
	    $user = $profile->getUser();

        // success!
        if (!common_set_user($user)) {
            $this->serverError('设置用户时发生错误。');
            return;
        }

        if ($this->boolean('rememberme')) {
            Remember_me::remember($user);
        }
        
        $log = new Login_log();
		$log->logNew($user);
        
        if ($this->trimmed('ajax')) {
        	$this->showJsonResult(array('result' => 'true'));
        } else {
	    	$url = common_path('home');
        	common_redirect($url, 303);
        }
    }
    
    function hasVerifyCode() {
    	return common_have_session() && array_key_exists('login_check_num', $_SESSION);
    }
    
	function isVerifyPass() {
    	return $this->args['login_rand'] == $_SESSION["login_check_num"];
    }
    
	/**
     * Store an error and show the page
     *
     * This used to show the whole page; now, it's just a wrapper
     * that stores the error in an attribute.
     *
     * @param string $error error, if any.
     *
     * @return void
     */
    function showForm($error=null)
    {
//        if ($error != null) {
//        	$this->addPassVariable('login_error', $error);
//        }
//        $this->displayWith('HomepageHTMLTemplate');
		
    	if ($this->trimmed('ajax')) {
    		if ($error) {
    			$datas = array('result' => 'false', 'msg' => $error);
    		} else {
    			$datas = array('result' => 'true');
    		}
    		$this->showJsonResult($datas);
    	} else {
	    	if ($error) {
				$_SESSION['login_error'] = $error;
			}
			
			common_redirect(common_path(''), 303);
    	}
    }
    
    
}