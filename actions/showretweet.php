<?php
if (!defined('SHAISHAI')) {
    exit(1);
}

class ShowretweetAction extends ProfileAction
{	
	function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}
        if($this->cur_user->id != $this->owner->id) {
        	$this->clientError('您不能访问别人的转载列表.');
        	return;
        }
        
        return true;
    }
	
    function handle($args)
    {
    	parent::handle($args);
		$notice = $this->owner->getNotices(($this->cur_page-1)*NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1, 
				0, 0, null, 0, 0, 4, 0, true);
		$total = $this->owner->noticeCountByType(0, 0, true);
		
		$this->addPassVariable('notice', $notice);
		$this->addPassVariable('total', $total);
		$this->addPassVariable('profile', $this->owner->getProfile());
		
    	$this->displayWith('ShowretweetHTMLTemplate');
    }
    
	function isReadOnly($args)
    {
        return true;
    }
}