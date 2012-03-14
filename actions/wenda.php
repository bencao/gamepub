<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class WendaAction extends ShaiAction
{
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
	
	function handle($args)
    {
        parent::handle($args);
        
        if ($this->cur_user) {
	        // 提问数
	        $this->questionCount = Question::getQuestionsCountByUserId($this->cur_user->id);
	        
	        // 回答数
	        $this->answerCount = Answer::getAnswersCountByUserId($this->cur_user->id);
	        
	        // 被采纳数
	        $this->goodAnswerCount = Answer::getGoodAnswersCountByUserId($this->cur_user->id);
	        
	        // 我的新提问(取9条)
	        $this->questions = Question::getQuestionsByUserId($this->cur_user->id, 0, QUESTIONS_PER_PAGE + 1);
	        
	        // 悬赏的提问(取9条)
	        $this->awardQuestions = Question::getAwardQuestions(0, QUESTIONS_PER_PAGE + 1);
        	$this->addPassVariable('questionCount', $this->questionCount);
	        $this->addPassVariable('answerCount', $this->answerCount);
	        $this->addPassVariable('goodAnswerCount', $this->goodAnswerCount);
	        $this->addPassVariable('questions', $this->questions);
	        $this->addPassVariable('awardQuestions', $this->awardQuestions);
        } else {
        	// 悬赏的提问(取9条)
	        $this->awardQuestions = Question::getAwardQuestions(0, QUESTIONS_PER_PAGE + 1);
        	$this->addPassVariable('questionCount', 0);
	        $this->addPassVariable('answerCount', 0);
	        $this->addPassVariable('goodAnswerCount', 0);
	        $this->addPassVariable('questions', false);
	        $this->addPassVariable('awardQuestions', $this->awardQuestions);
        }
        
        $this->displayWith("WendaHTMLTemplate");
    }
}