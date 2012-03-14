<?php

if (!defined('SHAISHAI')) { exit(1); }

class IncreasegamewebclickAction extends ShaiAction
{
	var $gameweb_id;
	
	function prepare($args)
	{
		if (! parent::prepare($args)) {return false;}
		$this->gameweb_id = $this->trimmed('gwid');
		return true;
	}
	
    function handle($args)
    {
    	parent::handle($args);
    	if (Game_web::increaseClickById($this->gameweb_id)) {
    		$this->showJsonResult(array('result' => 'true'));
    	} else {
    		$this->showJsonResult(array('result' => 'false'));
    	}
    }
}

?>