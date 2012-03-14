<?php
if (!defined('SHAISHAI')) { exit(1); }

class ReportstatusAction extends ShaiAction
{
    function handle($args)
    {
    	parent::handle($args);
    	
    	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	    	//$session_id = $this->trimmed('session_id');
	    	if($this->cur_user) {
	    		User_status::setTime($this->cur_user->id);
	    	}
    	} else {
    		if($this->cur_user) {
    			$url = common_path('home');
	    	} else {
	    		$url = common_path('public');
	    	}
       		common_redirect($url, 303);
    	}
    }
}