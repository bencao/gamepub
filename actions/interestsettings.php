<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class InterestsettingsAction extends SettingsAction
{
	function getViewName() {
		return 'InterestsettingsHTMLTemplate';
    }
    
	/**
     * Handle a post
     *
     * Validate input and save changes. Reload the form with a success
     * or error message.
     *
     * @return void
     */

    function handlePost()
    {
        if (Event::handle('StartInterestSaveForm', array($this))) {
        	
        	if (array_key_exists('userinterests', $_POST)) {
        		$userinterests = $_POST['userinterests'];
        	} else {
        		$userinterests = array();
        	}
        	
        	$tagstring = $this->trimmed('user_self_define');
        	
        	if (! empty($tagstring)) {
        		require_once INSTALLDIR . '/lib/renderhelper.php';
                $tags = array_map('common_canonical_tag', preg_split('/[\s,]+/', $tagstring));
            } else {
                $tags = array();
            }
            
            $userinterests = array_merge($userinterests, $tags);

        	User_interest::saveInterests($this->cur_user->id, $userinterests);
        	
        	$this->cur_user->updateCompleteness();
        	
        	$this->showForm('设置已保存', true);
        	
        	Event::handle('EndInterestSaveForm', array($this));
        }
    }
    
	/**
     * show the settings form
     *
     * @param string $msg     an extra message for the user
     * @param string $success good message or bad message?
     *
     * @return void
     */

    function showForm($msg=null, $success=false)
    {	
    	$this->addPassVariable('interest_categories', Interest_category::getCategories());
    	$this->addPassVariable('interest_currinterests', User_interest::getClassifiedInterestByUser($this->cur_user->id));
    	$this->addPassVariable('interest_self_define', User_interest::getSelfDefinedInterestStringByUser($this->cur_user->id));
    	
    	parent::showForm($msg, $success);
    }
    
    
}

?>