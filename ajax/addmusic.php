<?php

if (!defined('SHAISHAI')) { exit(1); }

class AddmusicAction extends ShaiAction
{
    
    function handle($args)
    {
    	parent::handle($args);
    	$nid = $this->trimmed('id');
    	if (Music_history::saveNew($this->cur_user->id, $nid)) {
    		echo $nid . " add ok";
    	} else {
    		echo $nid . " add fail";
    	}
    }
}

?>