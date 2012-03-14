<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR . '/lib/apiauth.php';

class ApiAccountAutoLoginAction extends ApiAction//ApiAuthAction
{
	var $token   = null;
	var $key = null;
	var $action = null;
	var $url = null;
	var $profile = null;
	
    /**
     * Take arguments for running
     *
     * @param array $args $_REQUEST args
     *
     * @return boolean success flag
     *
     */

    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}

        $authenticatedUser = false;
        $uname = $this->trimmed('uname');
        //common_munge_password($password, $profile->id)
        $pass_md5 = $this->trimmed('pass');
    	$this->profile = Profile::getByUNameAndPassword($uname, $pass_md5); 
        if ($this->profile) {
            if (!empty($pass_md5)) { // never allow login with blank password
                if (0 == strcmp($pass_md5,$this->profile->password)) {
                    //internal checking passed
                    $authenticatedUser = $this->profile->getUser();
                }
            }
        }
        
    	if (!$authenticatedUser) {
             $this->clientError('登录失败!', 404);
             return;
        }
        $this->user = $authenticatedUser;
        $this->token = $this->trimmed('token');
		$this->url = $this->trimmed('url');
		
        return true;
     }

    /**
     * Handle the request
     *
     * Delete the notice and all related replies
     *
     * @param array $args $_REQUEST data (unused)
     *
     * @return void
     */

    function handle($args)
    {
        parent::handle($args);
        
//    	if (!in_array($this->action, array('home', 'groups', 'public', 'game'))) {
//             $this->clientError('链接不正确!', 404);
//             return;
//        }
        
        if (empty($this->token) || $this->token != $this->profile->token) {
             $this->clientError('您输入的参数错误.',
                 404, $this->format);
             return;
         }
        
    	// success!
        if (!common_set_user($this->user)) {
            $this->clientError('设置用户时发生错误。');
            return;
        }
        
        common_redirect($this->url, 303);
        
//        if($this->action == 'game') {        	
//        	common_redirect(common_local_url($this->action, array('gameid' => $this->user->game_id)), 303);
//        } else {
//        	common_redirect(common_local_url($this->action), 303);
//        }
		
    }
}