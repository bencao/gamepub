<?php

if (!defined('SHAISHAI')) { exit(1); }

class AnswerusefulAction extends ShaiAction
{
	function prepare($args) {
		if (! parent::prepare($args)) {return false;}
		
		$this->content = $this->trimmed('content');
		$this->asnotice = $this->trimmed('asnotice');
		$this->qid = $this->trimmed('qid');
		
		return true;
	}
	
    function handle($args)
    {
    	parent::handle($args);
    	
    	Answer::saveNew($this->cur_user->id, $this->qid, $this->content);
    	
    	if ($this->asnotice) {
    		// save new notice
    	}
    	
    	$this->showJsonResult(array('result' => 'true'));
    }
}