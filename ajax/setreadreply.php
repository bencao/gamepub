<?php
if (!defined('SHAISHAI')) { exit(1); }

class SetreadReplyAction extends ShaiAction
{
    function handle($args)
    {
    	parent::handle($args);
    	
    	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	    	if($this->cur_user) {
	    		if ($this->boolean('ajax')) {
	    			Reply::setRead($this->cur_user->id);
	    		}
	    	}
    	}
    }
}