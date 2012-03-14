<?php

if (!defined('SHAISHAI')) { exit(1); }

class AnswermodifyAction extends ShaiAction
{
	function prepare($args) {
		if (! parent::prepare($args)) {return false;}
		
		$this->content = $this->trimmed('content');
		$this->asnotice = $this->trimmed('asnotice');
		$this->aid = $this->trimmed('aid');
		
		return true;
	}
	
    function handle($args)
    {
    	parent::handle($args);
    	
    	$answer = Answer::staticGet('id', $this->aid);
    	
    	$question = $answer->getQuestion();
    	
    	if (! $question->is_alive) {
    		$this->showJsonResult(array('result' => 'false', 'msg' => '该问题已关闭'));
    		return false;
    	}
    	$answer->modifyContent($this->content);
    	
    	if ($this->asnotice) {
    		// save new notice
    		$notice = Notice::saveNew($this->cur_user->id, 
    			'我修改了对问题《' . $this->question->title . '》的回答，如果你感兴趣，请访问' . common_path('wenda/' . $this->question->id) . '查看详情。', 
        		'', 'web', true);
    	}
    	
    	$this->showJsonResult(array('result' => 'true', 'newcontent' => $this->content, 'aid' => $this->aid));
    }
}