<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR.'/lib/noticelist.php';

class HotnoticeHTMLTemplate extends PublicthreecolumnHTMLTemplate
{
    /**
     * Title of the page
     *
     * @return string Title of the page
     */

    function title()
    {
    switch($this->args['type']) {
        	case 'video':
        		 return '热门视频';
        		 break;
        	case 'text':
        		 return '热门文字';
        		 break;
        	case 'photo':
        		 return '热门图片';
        		 break;
        	case 'music':
        		 return '热门音乐';
        		 break;
        	default: return '热门视频';
        	     break;		
        }
    }

    function showRightside() {
		$this->showSearchFormWidget();
    	
//		$users = common_stream('userids:mosttalk', array("Notice", "getMosttalkUsers"), array(20), 3600 * 24);
//    	$users = common_random_fetch($users,5);
//    	$this->showUserListWidget($users, '游戏酒馆草根达人',common_local_url('rank',array('type' => 'user')));
    	
    	$users = common_stream('userids:mostvisit', array("Profile", "getMostvisitUsers"), array(20), 3600 * 24);
    	$users = common_random_fetch($users,5);
    	if($users)
    		$this->showUserListWidget($users, '游戏酒馆人气之星',common_local_url('rank',array('type' => 'user')));
    	
    	
		$subs = common_stream('userids:active', array("Grade_record", "getActiveUsers"), array(20), 3600 * 24);
    	$subs = common_random_fetch($subs,5);
    	if($subs)
    		$this->showUserListWidget($subs, '今日活跃用户',common_local_url('rank',array('type' => 'user')));
    	
    	if($this->cur_user) {
	    	$recommendids = $this->cur_user->getRecommmended();
	    	if($recommendids)
	    		$this->showUserListWidget($recommendids, '您可能感兴趣的人', common_local_url('rank',array('type' => 'user')), true);
    	}
    }
    
	function showEmptyList()
    {
        $message = '抓住机会发两条高质量的消息，赢取顶、转载、收藏，您的消息马上就成为热门哦。';

        $emptymsg = array();
        $emptymsg['p'] = '热门列表暂时为空';
        $emptymsg['p'] = $message;
        $this->tu->showEmptyListBlock($emptymsg);
    }

    /**
     * Content area
     *
     * Shows the list of popular notices
     *
     * @return void
     */

    function showContent()
    {	
    	$this->elementStart('h2');
    	$this->elementStart('ul', array('id' => 'public_thirdary_nav', 'class' => 'clearfix'));
    	if($this->args['type'] == 'video')
			$this->elementStart('li', 'active');
    	else 
			$this->elementStart('li');
    	$this->element('a', array('alt' => '热门视频', 'href' => common_local_url('hotnotice', array('type' => 'video'))), '热门视频');
    	$this->elementEnd('li');
    	$this->element('li', null, '|');
    	if($this->args['type'] == 'music')
			$this->elementStart('li', 'active');
    	else 
			$this->elementStart('li');
    	$this->element('a', array('alt' => '热门音乐', 'href' => common_local_url('hotnotice', array('type' => 'music'))), '热门音乐');
    	$this->elementEnd('li');
    	$this->element('li', null, '|');
    	if($this->args['type'] == 'photo')
			$this->elementStart('li', 'active');
    	else 
			$this->elementStart('li');
    	$this->element('a', array('alt' => '热门图片', 'href' => common_local_url('hotnotice', array('type' => 'photo'))), '热门图片');
    	$this->elementEnd('li');
    	$this->element('li', null, '|');
    	if($this->args['type'] == 'text')
			$this->elementStart('li', 'active');
    	else 
			$this->elementStart('li');
    	$this->element('a', array('alt' => '热门文字', 'href' => common_local_url('hotnotice', array('type' => 'text'))), '热门文字');
    	$this->elementEnd('li');
		$this->elementEnd('ul');
		$this->elementEnd('h2');
		
    	$this->element('div', 'intro', '平台' . $this->title() . '消息 ');
    	
    	$nl = new ShowgameNoticeList($this->args['notice'], $this);
        $cnt = $nl->show();
           	
        $this->pagination($this->cur_page > 1, $cnt > NOTICES_PER_PAGE,
                     $this->cur_page, 'hotnotice', array('type' => $this->args['type'])); 
    }
    
	function showShaishaiScripts() {
    	parent::showShaishaiScripts();
	    $this->script('js/lshai_search.js');
    }
}