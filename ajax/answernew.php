<?php

if (!defined('SHAISHAI')) { exit(1); }

class AnswernewAction extends ShaiAction
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
    	
    	$this->question = Question::staticGet('id', $this->qid);
    	if (! $this->question->is_alive) {
    		$this->showJsonResult(array('result' => 'false', 'msg' => '该问题已关闭'));
    		return false;
    	}
    	
    	if ($this->question->author_id == $this->cur_user->id) {
    		$this->showJsonResult(array('result' => 'false', 'msg' => '自己不能回答自己提出的问题'));
    		return false;
    	}
    	
    	if (Answer::checkDuplicate($this->cur_user->id, $this->question->id)) {
    		$this->showJsonResult(array('result' => 'false', 'msg' => '不能多次回答同一问题'));
    		return false;
    	}
    	
    	$this->answer = Answer::saveNew($this->cur_user->id, $this->qid, $this->content);
    	$this->question->increaseAnswerCount();
    	
    	if ($this->asnotice) {
    		// save new notice
    		$notice = Notice::saveNew($this->cur_user->id, 
    			'我回答了一个问题《' . $this->question->title . '》，如果你感兴趣，请访问' . common_path('wenda/' . $this->question->id) . '查看详情。', 
        		'', 'web', true);
    	}
    	
    	$stringer = new XMLStringer();
    	
    	$qAuthor = $this->answer->getAuthor();
		$stringer->elementStart('li', 'notice');
		$stringer->elementStart('div', 'avatar');
		$stringer->element('img', array('src' => $qAuthor->avatarUrl(AVATAR_STREAM_SIZE), 'alt' => $qAuthor->nickname));
		$stringer->elementEnd('div');
		$stringer->elementStart('h3');
		$stringer->element('a', array('href' => $qAuthor->profileurl, 'title' => '访问' . $qAuthor->nickname . '的主页'), $qAuthor->nickname);
		$stringer->elementEnd('h3');
		$stringer->elementStart('div', 'content');
		$stringer->text($this->answer->content);
		$stringer->elementEnd('div');
		$stringer->elementStart('div', 'bar');
		$stringer->elementStart('div', 'info');
		$dt = common_date_iso8601($this->answer->created);
		$stringer->element('span', array('class' => 'time', 'title' => $dt),
                            common_date_string($this->answer->created));
		$stringer->elementEnd('div');
		$stringer->elementStart('ul', 'op');
		
		if ($this->question->author_id == $this->cur_user->id) {
			// is question author
			$stringer->elementStart('li', 'bestanswer');
			$stringer->element('a', array('href' => common_path('ajax/answerbest'), 'aid' => $this->answer->id), '采纳');
			$stringer->elementEnd('li');
		} else if ($this->answer->author_id == $this->cur_user->id) {
			// is answer author
			$stringer->elementStart('li', 'modifyanswer');
			$stringer->element('a', array('href' => common_path('ajax/answermodify'), 'aid' => $this->answer->id), '修改');
			$stringer->elementEnd('li');
		} else if ($this->cur_user) {
			$stringer->elementStart('li', 'usefulanswer');
			$stringer->element('a', array('href' => common_path('ajax/answeruseful'), 'aid' => $this->answer->id), '有用');
			$stringer->elementEnd('li');
		}
		
		$stringer->elementEnd('ul');
		$stringer->elementEnd('div');
		$stringer->elementEnd('li');
		
    	
    	$this->showJsonResult(array('result' => 'true', 'html' => $stringer->getString()));
    }
}