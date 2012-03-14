<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class NewwendaAction extends ShaiAction
{
	function handle($args)
    {
    	parent::handle($args);
    	
    	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    		if ($this->_preHandle()) {
    			$this->handlePost();
    		}
    	} else {
    		$this->displayWith('NewwendaHTMLTemplate');
    	}
    }
    
    function _preHandle() {
    	$this->wenda_title = $this->trimmed('title');
    	$this->wenda_desc = $this->trimmed('desc');
    	$this->wenda_anonymous = $this->trimmed('anonymous');
    	$this->wenda_award = $this->trimmed('award');
    	$this->wenda_image_url = $this->trimmed('image_url', false);
    	// validate
    	$score = User_grade::getScore($this->cur_user->id);
    	if ($score - ($this->wenda_award + $this->wenda_anonymous) * 10 < 0) {
    		$this->addPassVariable('error_msg', '您只有' . (int)($score / 10) . '个铜G币，不足以支付该问题的悬赏！');
    		$this->displayWith('NewwendaHTMLTemplate');
    		return false;
    	} 
    	return true;
    }
    
    function handlePost() {
    	// save
    	$this->question = Question::saveNew($this->cur_user->id, $this->wenda_title, $this->wenda_desc, $this->wenda_award, 
    		$this->wenda_anonymous, $this->wenda_image_url);
    		
    	$notice = Notice::saveNew($this->cur_user->id, 
    			'我提出了一个问题《' . $this->question->title . '》，如果你能回答，请访问 ' . common_path('wenda/' . $this->question->id) . ' 查看详情，谢谢！', 
        		'', 'web', true);
    	
    	common_redirect(common_path('wenda'), 303);
    }
    
    
}