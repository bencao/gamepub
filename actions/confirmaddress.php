<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Confirm an address
 *
 * When users change their SMS, email, Jabber, or other addresses, we send out
 * a confirmation code to make sure the owner of that address approves. This class
 * accepts those codes.
 *
 * @category Confirm
 * @package  LShai
 */

class ConfirmaddressAction extends ShaiAction
{

	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
		$this->cache_allowed = false;
	}
	
	function prepare($args) {
		
		if (! parent::prepare($args)) {return false;}
		
		// XXX: 未登录情况的处理
		
		$code = $this->trimmed('code');
        if (!$code) {
            $this->clientError('请求中缺少验证码。', 404);
            return false;
        }
        
        $this->confirm = Confirm_address::staticGet('code', $code);
        if (! $this->confirm) {
            $this->clientError('没有此验证码。', 404);
            return false;
        }
        
        return true;
	}
	
    /**
     * Accept a confirmation code
     *
     * Checks the code and confirms the address in the
     * user record
     *
     * @param args $args $_REQUEST array
     *
     * @return void
     */

    function handle($args)
    {
        parent::handle($args);

        $this->confirmedUser = User::staticGet('id', $this->confirm->user_id);
        
        if (! in_array($this->confirm->address_type, array('email', 'qq'))) {
        	$this->clientError(sprintf('无法识别的验证类型 － %s', $this->confirm->address_type));
            return;
        }
        
        if ($this->confirm->address_type == 'email') {
         	$this->_handleEmailConfirm();
    	} else if ($this->confirm->address_type == 'qq') {
    		$this->_handleQQConfirm();
        }
        
        $this->addPassVariable('address', $this->confirm->address);
        $this->addPassVariable('uname', $this->confirmedUser->uname);
        $this->displayWith('ConfirmaddressHTMLTemplate');
    }
    
    function _handleEmailConfirm() {
    	
    	if (Profile::existEmail($this->confirm->address)) {
        	$this->clientError('该邮箱已被其他用户绑定');
        	return;
        }
	    
	    $this->confirmedUser->query('BEGIN');
	    
    	$profile = $this->confirmedUser->getProfile();
        
    	$orig_profile = clone($profile);
        
        $profile->email = $this->confirm->address;
        
        $profile->updateKeys($orig_profile);

    	if (! $this->confirm->delete()) {
	        common_log_db_error($this->confirmedUser, 'UPDATE', __FILE__);
	        $this->serverError('因技术原因暂时无法更新，请稍候重试。');
	        return;
	    }
	    
	    $this->confirmedUser->updateCompleteness();
	    
	    $this->confirmedUser->query('COMMIT');
	    
//	    common_redirect(common_local_url('emailsettings', null, array('change' => 'true')), 303);
    }

    function _handleQQConfirm() {
    	
    	if (common_exist_qq($this->confirm->address)) {
        	$this->clientError('该QQ号已被其他用户绑定');
        	return;
        }
        
        $this->confirmedUser->query('BEGIN');
        
    	$profile = $this->confirmedUser->getProfile();
        
    	$orig_profile = clone($profile);
        
        $profile->qq = $this->confirm->address;
        
        $profile->updateKeys($orig_profile);

    	if (! $this->confirm->delete()) {
	        common_log_db_error($this->confirmedUser, 'UPDATE', __FILE__);
	        $this->serverError('因技术原因暂时无法更新，请稍候重试。');
	        return;
	    }
	    
	    $this->confirmedUser->updateCompleteness();
	    
	    $this->confirmedUser->query('COMMIT');
	    
//	    common_redirect(common_local_url('qqbindingsettings',null, array('binded'=>'true')), 303);
    }
    
}

function common_exist_qq($qq) {
	if (preg_match('/\d+/', $qq)) {
		$p = Profile::staticGet('qq', $qq);
    	return ($p !== false);
	}
	return false;
}
