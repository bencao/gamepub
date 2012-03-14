<?php

if (!defined('SHAISHAI')) {
    exit(1);
}


class Rank2HTMLTemplate extends PublicthreecolumnHTMLTemplate
{
	var $femaleplayer = null;
	var $maleplayer = null;
	
	function title()
    {
    	$message = '风云榜';
    	switch($this->args['type'])
    	{
    		case 'discuss': $message .='-评论排行';
    				break;
    		default:$message .='-转载排行';
    				break;
    	}
    	return $message;
    }
	
	function showContent() {
		$this->showCorehead();
		
		if($this->args['type'] == 'discuss')
			$this->showDissRank();
		else
			$this->showRetweetRank();
		
	}
	function showEmptylist() {
		$message = '这是平台消息排行列表， 但是还没有人发表过任何消息。' . ' ';

        if (common_current_user()) {
            $message .= '快来第一个发言吧！';
        }
        else {
            if (! (common_config('site','closed') || common_config('site','inviteonly'))) {
                $message .= '赶快来 [注册](%%action.register%%) ， 成为第一个发言的人吧！';
            }
		}
		
		$this->tu->showEmptyListBlock(common_markup_to_html($message));
		
	}
	function showCorehead() {
		$this->elementStart('h2');
		$this->elementStart('ul',array('class' => 'clearfix','id' => 'public_thirdary_nav'));
		if($this->args['type'] == 'game')
			$this->elementStart('li',array('class' => 'active'));
		else
			$this->elementStart('li');
			$this->element('a',array('href' => common_local_url('rank').'/game','alt' => '游戏排行'),'游戏排行');
		$this->elementEnd('li');
		$this->element('li',null,'|');
		if($this->args['type'] == 'user')
			$this->elementStart('li',array('class' => 'active'));
		else
			$this->elementStart('li');
			$this->element('a',array('href' => common_local_url('rank').'/user','alt' => '人气排行'),'人气排行');
		$this->elementEnd('li');
		$this->element('li',null,'|');
		if($this->args['type'] == 'retweet')
			$this->elementStart('li',array('class' => 'active'));
		else
			$this->elementStart('li');
			$this->element('a',array('href' => common_local_url('rank').'/retweet','alt' => '转载排行'),'转载排行');
		$this->elementEnd('li');
		$this->element('li',null,'|');
		if($this->args['type'] == 'discuss')
			$this->elementStart('li',array('class' => 'active'));
		else
			$this->elementStart('li');
			$this->element('a',array('href' => common_local_url('rank').'/discuss','alt' => '讨论排行'),'讨论排行');
		$this->elementEnd('li');
		$this->elementEnd('ul');
		$this->elementEnd('h2');
	}
	
	
	function showRetweetRank() {
		
		$this->showWebRetweetRank();
		if(common_current_user())
		{
			$this->showGameRetweetRank();
			$this->showServerRetweetRank();
		}
	}
	
	function showDissRank() {
		$this->showWebDissRank();
		if(common_current_user())
		{
			$this->showGameDissRank();
			$this->showServerDissRank();
		}
	}
	
	function showWebRetweetRank()
	{
		$this->elementStart('div',array('class' => 'intro_hot'));
		$this->element('strong',null,'今日平台被转载最多的消息');
		$this->elementEnd('div');
		$nl = new RankNoticeList($this->args['webretweetnotices'], $this, 'retweet');
        $cnt = $nl->show();
	}
	
	function showGameRetweetRank()
	{
		$this->elementStart('div',array('class' => 'intro_hot'));
		$this->element('strong',null,'今日'.$this->args['gamename'].'游戏被转载最多的消息');
		$this->elementEnd('div');
		$nl = new RankNoticeList($this->args['gameretweetnotices'], $this, 'retweet');
        $cnt = $nl->show();
	}
	
	function showServerRetweetRank()
	{
		$this->elementStart('div',array('class' => 'intro_hot'));
		$this->element('strong',null,'今日'.$this->args['servername'].'服务器被转载最多的消息');
		$this->elementEnd('div');
		$nl = new RankNoticeList($this->args['serverretweetnotices'], $this, 'retweet');
        $cnt = $nl->show();
	}
	
	function showWebDissRank()
	{
		$this->elementStart('div',array('class' => 'intro_hot'));
		$this->element('strong',null,'今日平台被评论最多的消息');
		$this->elementEnd('div');
		$nl = new RankNoticeList($this->args['webdissnotices'], $this, 'discuss');
        $cnt = $nl->show();
	}
	
	function showGameDissRank()
	{
		$this->elementStart('div',array('class' => 'intro_hot'));
		$this->element('strong',null,'今日'.$this->args['gamename'].'游戏被评论最多的消息');
		$this->elementEnd('div');
		$nl = new RankNoticeList($this->args['gamedissnotices'], $this, 'discuss');
        $cnt = $nl->show();
	}
	
	function showServerDissRank()
	{
		$this->elementStart('div',array('class' => 'intro_hot'));
		$this->element('strong',null,'今日'.$this->args['servername'].'服务器被评论最多的消息');
		$this->elementEnd('div');
		$nl = new RankNoticeList($this->args['serverdissnotices'], $this, 'discuss');
        $cnt = $nl->show();
	}
	
	function showRightside() {
		$this->showSearchFormWidget();
    	
//		$users = common_stream('userids:mosttalk', array("Notice", "getMosttalkUsers"), array(20), 3600 * 24);
//    	$users = common_random_fetch($users,5);
//    	$this->showUserListWidget($users, '游戏酒馆草根达人',common_local_url('rank',array('type' => 'user')));
    	
    	$users = common_stream('userids:mostvisit', array("Profile", "getMostvisitUsers"), array(20), 3600 * 24);
    	$users = common_random_fetch($users,5);
    	if ($users) {
    		$this->showUserListWidget($users, '游戏酒馆人气之星',common_local_url('rank',array('type' => 'user')));
    	}
    	
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