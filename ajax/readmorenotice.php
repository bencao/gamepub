<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class ReadmorenoticeAction extends ShaiAction
{
	function handle($args) {
		parent::handle($args);
		$subed_id = $this->trimmed('subed_id');
		
        assert($this->cur_user); // XXX: maybe an error instead...
		Subscription::UpdateRead($this->cur_user, $subed_id);
		header('Content-type: text/xml'); 
    	echo '<p class="success"> read more notices </p>';
	}
	
}