<?php

if (!defined('SHAISHAI')) { exit(1); }

class DelmusicAction extends ShaiAction
{
    
    function handle($args)
    {
    	parent::handle($args);
    	$nid = $this->trimmed('id');
    	if (Music_history::deleteMusic($this->cur_user->id, $nid)) {
    		echo $nid . " del ok";
    	} else {
    		echo $nid . " del fail";
    	}
    }
}

?>