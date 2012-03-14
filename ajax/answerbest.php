<?php

if (!defined('SHAISHAI')) { exit(1); }

class AnswerbestAction extends ShaiAction
{
	function prepare($args) {
		if (! parent::prepare($args)) {return false;}
		
		$this->aid = $this->trimmed('aid');
		
		return true;
	}
	
    function handle($args)
    {
    	parent::handle($args);
    	
    	$answer = Answer::staticGet('id', $this->aid);
    	$question = $answer->getQuestion();
    	if ($question->best_answer_id > 0) {
    		$this->showJsonResult(array('result' => 'false', 'msg' => '已经有最佳答案了'));
    		return false;
    	}
    	
    	$question->setBestAnswer($answer);
    	
    	$this->showJsonResult(array('result' => 'true'));
    }
}