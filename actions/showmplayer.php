<?php

if (!defined('SHAISHAI')) { exit(1); }

class ShowmplayerAction extends ShaiAction
{   
    function handle($args)
    {
    	parent::handle($args);
    	
    	$nid = $this->trimmed('nid');
    	
    	if ($this->cur_user) {
    		Music_history::saveNew($this->cur_user->id, $nid);
    	}
    	
    	$this->displayWith('ShowmplayerHTMLTemplate');
    }
}