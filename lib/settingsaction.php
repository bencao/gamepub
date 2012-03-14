<?php
/**
 * LShai, the distributed microblogging tool
 *
 * Base class for settings actions
 *
 * PHP version 5
 *
 * @category  Settings
 * @package   LShai
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Base class for settings group of actions
 *
 * @category Settings
 * @package  LShai
 *
 * @see      Widget
 */

class SettingsAction extends OwnerDesignAction
{
	var $cur_user_profile;
	
	function prepare($args)
	{
		if (! parent::prepare($args)) {return false;}
		$this->cur_user_profile = $this->cur_user->getProfile();
		return true;
	}
	
    /**
     * Handle input and output a page
     *
     * @param array $args $_REQUEST arguments
     *
     * @return void
     */
    function handle($args)
    {
        parent::handle($args);
        
        $this->addPassVariable('settings_cur_user', $this->cur_user);
        $this->addPassVariable('settings_cur_user_profile', $this->cur_user_profile);
        	
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		        
	    	$this->handlePost();
	    } else {
	    	$this->showForm();
	    }
    }

    // 子类实现此方法
    function handlePost()
    {
    }
    
    function showForm($msg=null, $success=false)
    {
    	if ($msg != null) {
    		$this->addPassVariable('page_msg', $msg);
    		$this->addPassVariable('page_success', $success);
    	}
    	$this->displayWith($this->getViewName());
    }

}
