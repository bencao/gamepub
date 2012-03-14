<?php
if (!defined('SHAISHAI')) { exit(1); }

class IgnoreremindsAction extends ShaiAction
{
    function handle($args)
    {
    	parent::handle($args);
    	
    	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			// set read reply
			Reply::setRead($this->cur_user->id);
			// set read message
			Message::setRead($this->cur_user->id);
			// set read discussion
			Discussion_unread::setRead($this->cur_user->id);
			
			$this->showJsonResult(array('result' => 'true'));
    	} 
    }
}
