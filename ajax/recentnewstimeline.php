<?php 
if (!defined('SHAISHAI')) { exit(1); }

class RecentnewstimelineAction extends ShaiAction
{
	var $which;
	var $gameid;
	
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
	
	function prepare($args) {
		if (! parent::prepare($args)) {return false;}
		$this->which = $this->trimmed('which');
		$this->gameid = $this->trimmed('gameid');
		return true;
	}
    
    function handle($args)
    {
    	parent::handle($args);
    	
    	$cnt = 0;
    	
    	$stringer = new XMLStringer();
    	$stringer1 = new XMLStringer();
    	
    	if ($this->which == 'retweet') {
			$noticeids = Notice::getLatestRetweetIds(($this->cur_page - 1) * NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1, $this->gameid);
    	} else if ($this->which == 'discuss') {
			$noticeids = Notice::getLatestDiscussionIds(($this->cur_page - 1) * NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1, $this->gameid);
    	} else {
    		$noticeids = Notice::getLatestIds(($this->cur_page - 1) * NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1, $this->gameid);
    	}
    	
    	$notice = Notice::getStreamByIds($noticeids);
    	if ($notice->N) {
	    	$nl = new ShowgameNoticeList($notice, $stringer);
	    	$cnt = $nl->show();
	    }
    	
    	if ($cnt > NOTICES_PER_PAGE) {
		    $stringer1->element('a', array('href' => common_path('ajax/recentnewstimeline?page=' . ($this->cur_page + 1) . '&which=' . $this->which . '&gameid=' . $this->gameid),
		            'id' => 'notice_more', 'rel' => 'nofollow'));
	    }
    	
    	$this->showJsonResult(array('result' => 'true', 'notices' => $stringer->getString(), 'which' => $this->which, 'pg' => $stringer1->getString()));
    }
}

?>