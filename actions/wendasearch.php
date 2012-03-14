<?php
/**
 * Notice search action class.
 *
 * PHP version 5
 *
 * @category Action
 * @package  ShaiShai
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Notice search action class.
 *
 * @category Action
 * @package  ShaiShai
 */
class WendasearchAction extends SearchAction
{
	var $wt = null;
	
	function prepare($args)
	{
		if (! parent::prepare($args)) {return false;}
		
		$this->wt = $this->trimmed('wt');
		
		if ($this->trimmed('submit') == '我要回答') {
			$this->wt = 'active';
		}
		
		if (empty($this->wt)) {
			$this->wt = 'solved';
		}
		
		if (! in_array($this->wt, array('solved', 'active'))) {
			$this->clientError('无效的查询参数wt');
			return false;
		}
		
		$this->addPassVariable('wt', $this->wt);
		
		return true;
	}
	
	function doSearch($args) {
		if ($this->wt == 'solved') {
			$extra = 'best_answer_id > 0';
		} else {
			$extra = 'is_alive = 1';
		}
		$this->resultset = Question::getQuestionsByTitleLike(array($this->q), ($this->cur_page - 1) * NOTICES_PER_PAGE, NOTICES_PER_PAGE, $extra);
		$this->total = Question::getQuestionsCountByTitleLike(array($this->q), $extra);
		
		return true;
	}
	
	function getViewName() {
    	return 'WendasearchHTMLTemplate';
    }
}