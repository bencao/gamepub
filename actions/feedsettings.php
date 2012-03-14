<?php
/**
 * LShai, the distributed microblogging tool
 *
 * Change user password
 *
 * @category  Settings
 * @package   LShai
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR . '/lib/validatetool.php';

/**
 * Change password
 *
 * @category Settings
 * @package  LShai
 */

class FeedsettingsAction extends SettingsAction
{
    
	function getViewName() {
		return 'FeedsettingsHTMLTemplate';
	}
	
	function prepare($args) {
		if (! parent::prepare($args)) {return false;}
		$this->feed = Feed_table::getFeedByUserId($this->cur_user->id);
		return true;
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
        if ($this->trimmed('import')) {
        	if ($this->feed) {
        		Feed_table::importByUserId($this->cur_user->id);
        		$this->showForm('Feed已更新。', true);
        	} else {
        		$this->showForm('Feed地址不存在，无法导入', false);
        	}
        } else if ($this->_checkFeedurl()
        	&& $this->_updateFeedurl()) {
        	$this->showForm('Feed地址已更新。', true);
        } else {
        	$this->showForm($this->errorMessage, false);
        }
    }
    
    function _checkFeedurl() {
    	return true;
    }
    
    function _updateFeedurl() {
    	if ($this->feed) {
    		Feed_table::updateFeed($this->cur_user->id, $this->trimmed('feedurl'));
    	} else {
    		Feed_table::saveNew($this->cur_user->id, $this->trimmed('feedurl'));
    	}
    	$this->feed = Feed_table::getFeedByUserId($this->cur_user->id);
    	return true;
    }
    
    function showForm($msg=null, $success=false) {
    	$this->addPassVariable('feedurl', $this->feed ? $this->feed->uri : '');
    	parent::showForm($msg, $success);
    }
    
}
