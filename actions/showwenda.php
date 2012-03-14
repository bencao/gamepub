<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class ShowwendaAction extends ShaiAction
{
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
	
	function prepare($args) {
		if (! parent::prepare($args)) {return false;}
		$this->qid = $this->trimmed('qid');
			
		return true;
	}
	
	function handle($args) {
		parent::handle($args);
		$this->question = Question::staticGet('id', $this->qid);
		$this->answers = $this->question->getAnswers();
		
		$this->addPassVariable('question', $this->question);
		$this->addPassVariable('answers', $this->answers);
		
		$this->displayWith('ShowwendaHTMLTemplate');
	}
}