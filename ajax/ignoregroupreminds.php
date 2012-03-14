<?php
if (!defined('SHAISHAI')) { exit(1); }

class IgnoregroupremindsAction extends ShaiAction
{
    function handle($args)
    {
    	parent::handle($args);
    	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    		Group_unread_notice::setReadByUserid($this->cur_user->id);
			$this->showJsonResult(array('result' => 'true'));
    	}
    }
}
