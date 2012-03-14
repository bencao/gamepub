<?php
/**
 * copyright @ shaishai.com
 */

if (!defined('SHAISHAI')) {
    exit(1);
}


/**
 * Action for displaying the public stream
 *
 * @category Public
 * @package  SHAISHAI
 * @link     http://www.shaishai.com/
 *
 */

class PublicAction extends ShaiAction
{
    
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}

    function isReadOnly($args)
    {
        return true;
    }

    function handle($args)
    {
        parent::handle($args);
        
//        $noticeids = Notice::getRetweetNoticeOrder(20, null, null, time() - 3600*24);
//        
//        $notice = Notice::getStreamByIds($noticeids);

//        $notice = Notice::publicStream(0, NOTICES_PER_PAGE + 1, 0, 0, null, 0, 0, 4);
        
//        $hotwords = Hotwords::getHotWords();

        $noticeids = Notice::getLatestDiscussionIds(($this->cur_page - 1) * NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1);
        
        $notice = Notice::getStreamByIds($noticeids);
        
        $vipids = Profile::getVipIds(10);
//		$vipids = array('100524', '100495', '100481', '100472', '100671', '109403', '100593', '100570', '100555', '100552');
        
        $randids = Profile::getRandom100();
        $rand = Profile::getProfileByIds(common_random_fetch($randids, 10));
        
		if(common_current_user()) {
			$noticeids = common_stream('noticeids:nodiss', array("Notice", "getNoticeidsWithnodiss"), array($this->cur_user->id, 20), 3600 * 2);
		//	$noticeids = Notice::getNoticeidsWithnodiss($this->cur_user->id, 20);
		} else {
			$noticeids = common_stream('noticeids:nodiss', array("Notice", "getNoticeidsWithnodiss"), array(null, 20), 3600 * 2);
		//	$noticeids = Notice::getNoticeidsWithnodiss(null, 20);
		}
		
		// 找个沙发坐坐
		if (count($noticeids) > 0) {	
			$randomnoticeid = common_random_fetch($noticeids, 1);
			$this->addPassVariable('sofanotice_id',$randomnoticeid[0]);
		} else {
			$this->addPassVariable('sofanotice_id', null);
		}                                 

		$this->addPassVariable('vipids', $vipids);
		$this->addPassVariable('rand', $rand);
		$this->addPassVariable('hotword', '游戏历程');
		$this->addPassVariable('notice', $notice);
		
    	$this->displayWith('PublicHTMLTemplate');
    }
}
