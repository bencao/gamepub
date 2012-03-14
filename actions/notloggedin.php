<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class NotLoggedInAction extends ShaiAction
{
	var $code;
	var $message;
	
	function __construct($message='Error', $code=400)
    {
    	$this->code = $code;
        $this->message = $message;
        $this->no_anonymous = false;
    }
    
	function handle($args)
	{
		parent::handle($args);
		$_SESSION['login_error'] = $this->message;
		
		common_redirect(common_path(''), 303);
		
//		$this->addPassVariable('login_error', $this->message);
//        $this->displayWith('HomepageHTMLTemplate'); 
	}
}