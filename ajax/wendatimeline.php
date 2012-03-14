<?php
if (!defined('SHAISHAI')) { exit(1); }

class WendatimelineAction extends ShaiAction
{
	var $type;
	
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
	
	function prepare($args) {
		if (! parent::prepare($args)) {return false;}
		$this->type = $this->trimmed('type');
		return true;
	}
	
	function handle($args) {
		parent::handle($args);
		
		if ($this->type == 'mylatest') {
			$questions = Question::getQuestionsByUserId($this->cur_user->id, ($this->cur_page - 1) * QUESTIONS_PER_PAGE, QUESTIONS_PER_PAGE + 1);
		} else if ($this->type == 'myclosed') {
			$questions = Question::getMyclosedQuestionsByUserId($this->cur_user->id, ($this->cur_page - 1) * QUESTIONS_PER_PAGE, QUESTIONS_PER_PAGE + 1);
		} else if ($this->type == 'mybestanswered') {
			$questions = Question::getMybestansweredQuestionsByUserId($this->cur_user->id, ($this->cur_page - 1) * QUESTIONS_PER_PAGE, QUESTIONS_PER_PAGE + 1);
		} else if ($this->type == 'myanswered') {
			$questions = Question::getMyansweredQuestionsByUserId($this->cur_user->id, ($this->cur_page - 1) * QUESTIONS_PER_PAGE, QUESTIONS_PER_PAGE + 1);
		} else if ($this->type == 'award') {
			$questions = Question::getAwardQuestions(($this->cur_page - 1) * QUESTIONS_PER_PAGE, QUESTIONS_PER_PAGE + 1);
		} else if ($this->type == 'latest') {
			$questions = Question::getLatestQuestions(($this->cur_page - 1) * QUESTIONS_PER_PAGE, QUESTIONS_PER_PAGE + 1);
		} else if ($this->type == 'closing') {
			$questions = Question::getClosingQuestions(($this->cur_page - 1) * QUESTIONS_PER_PAGE, QUESTIONS_PER_PAGE + 1);
		}
		
		$stringer = new XMLStringer();
		$stringer->elementStart('table');
    	$stringer->elementStart('thead');
    	$stringer->elementStart('tr');
    	$stringer->element('td', 'tt', '标题');
    	$stringer->element('td', null, '悬赏');
    	$stringer->element('td', null, '回答数');
    	$stringer->element('td', null, '状态');
    	$stringer->element('td', null, '提问时间');
    	$stringer->elementEnd('tr');
    	$stringer->elementEnd('thead');
    	$stringer->elementStart('tbody');
    	
    	$cnt = 0;
    	while ($cnt < QUESTIONS_PER_PAGE
    		&& $questions 
    		&& $questions->fetch()) {
	    	$stringer->elementStart('tr');
	    	$stringer->elementStart('td', 'tt');
	    	$stringer->element('a', array('href' => common_path('wenda/' . $questions->id)), $questions->title);
	    	$stringer->elementEnd('td');
	    	$stringer->element('td', null, $questions->award_amount . '铜');
	    	$stringer->element('td', null, $questions->answer_count);
	    	$stringer->element('td', null, $questions->is_alive ? '待解决' : '已关闭');
	    	$stringer->element('td', null, common_date_string($questions->created));
	    	$stringer->elementEnd('tr');
	    	$cnt ++;
    	}
    	
    	if ($cnt == 0) {
    		$stringer->element('td', array('colspan' => '5'), '没有记录');
    	}
    	
    	$stringer->elementEnd('tbody');
    	
    	$hasPrev = $this->cur_page > 1;
    	$hasNext = $questions->N > QUESTIONS_PER_PAGE;
    	
    	if ($hasPrev || $hasNext) {
	    	$stringer->elementStart('tfoot');
	    	$stringer->elementStart('tr');
	    	if ($hasPrev) {
	    		$stringer->elementStart('td', array('class' => 'prev', 'colspan' => '4'));
	    		$stringer->element('a', array('href' => '#', 'page' => ($this->cur_page - 1), 'type' => $this->type), '<<上一页');
	    		$stringer->elementEnd('td');
	    	} else {
	    		$stringer->element('td', array('colspan' => '4'));
	    	}
	    	if ($hasNext) {
	    		$stringer->elementStart('td');
	    		$stringer->element('a', array('href' => '#', 'page' => ($this->cur_page + 1), 'type' => $this->type), '下一页>>');
	    		$stringer->elementEnd('td');
	    	} else {
	    		$stringer->element('td');
	    	}
	    	$stringer->elementEnd('tr');
	    	$stringer->elementEnd('tfoot');
    	}
    	$stringer->elementEnd('table');
	
		$this->showJsonResult(array('type' => $this->type, 'html' => $stringer->getString(), 'result' => 'true'));
		
	}
}