<?php 
if (!defined('SHAISHAI')) { exit(1); }

class PublictimelineAction extends ShaiAction
{
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
	
	function prepare($args) {
		if (! parent::prepare($args)) {return false;}
		$this->which = $this->trimmed('which');
		return true;
	}
    
    function handle($args)
    {
    	parent::handle($args);
    	
    	$cnt = 0;
    	
    	$stringer = new XMLStringer();
    	$stringer1 = new XMLStringer();
    	
    	if ($this->which == 'retweet') {
    		// get recent notices
//    		$noticeids = Notice::getRetweetNoticeOrder(20, null, null, time() - 3600*24);
			$noticeids = Notice::getLatestRetweetIds(($this->cur_page - 1) * NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1);
        	$notice = Notice::getStreamByIds($noticeids);
    	} else if ($this->which == 'discuss') {
//    		$noticeids = Notice::getDissNoticeOrder(20, null, null, time() - 3600*24);
			$noticeids = Notice::getLatestDiscussionIds(($this->cur_page - 1) * NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1);
        	$notice = Notice::getStreamByIds($noticeids);
    	} else {
    		$notice = Notice::publicStream(($this->cur_page - 1) * NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1, 0, 0, null, 0, 0, 4);
    	}
    	
    	if ($notice->N) {
	    	$nl = new ShowgameNoticeList($notice, $stringer);
	    	$cnt = $nl->show();
	    }
    	
    	if ($cnt > NOTICES_PER_PAGE) {
		    $stringer1->element('a', array('href' => common_path('ajax/publictimeline?page=' . ($this->cur_page + 1) . '&which=' . $this->which),
		            'id' => 'notice_more', 'rel' => 'nofollow'));
	    }
    	
    	$this->showJsonResult(array('result' => 'true', 'notices' => $stringer->getString(), 'which' => $this->which, 'pg' => $stringer1->getString()));
    }
}

class RankNoticeList extends ShowgameNoticeList
{
	var $type = 'retweet'; 	
	function __construct($notice, $out=null, $type='retweet')
   	{
       	 parent::__construct($notice, $out);
        $this->type = $type;
     }
    function newListItem($notice)
    {
        return new RankNoticeListItem($notice, $this->out, $this->type);
    }
}

class RankNoticeListItem extends ShowgameNoticeListItem
{
	var $type = 'retweet';
	function __construct($notice, $out=null, $type='retweet')
    {
        parent::__construct($notice, $out);
        $this->profile = $notice->getProfile();
        $this->profileUser = $this->profile->getUser();
        $this->user = common_current_user();
        $this->type = $type;
    }
    
	function showImage() {
    	$this->out->elementStart('div', array('class' => 'avatar'));
    	
    	if($this->type == 'discuss')
    	{
    		$this->out->element('p',array('class' => 'retweet_cnt'),$this->notice->discussion_num?$this->notice->discussion_num:0);
    		$this->out->element('p',null,'被评论');
    	} else {
    		$this->out->element('p',array('class' => 'retweet_cnt'),$this->notice->retweet_num?$this->notice->retweet_num:0);
    		$this->out->element('p',null,'被转');
    	}
    	$this->out->elementEnd('div');
    }
    
}

?>