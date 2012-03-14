<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class ExperiencesHTMLTemplate extends PublicthreecolumnHTMLTemplate
{
	
	function title()
    {
    	$message = '游戏经验';
    	switch($this->args['area'])
    	{
    		case 'gameserver': $message .='-'.$this->args['servername'];
    				break;
    		case 'game': $message .='-'.$this->args['gamename'];
    				break;
    		default:$message .=' - ' . common_config('site', 'name') . '平台';
    				break;
    	}
    	return $message;
    }
	
	function showContent() {
		
		$this->showCorehead();
		
		$this->showExpelist();
	
	}
 
//	function showRightside() {
//		if (common_current_user()) {
//			$page_owner_profile = $this->cur_user->getProfile();
//    	
//    		$this->tu->showOwnerInfoWidget($page_owner_profile);
//    		$this->tu->showSubInfoWidget($page_owner_profile, true);		
//		}
//	}
	function showRightside() {
		$this->showSearchFormWidget();
		
//		$users = common_stream('userids:mosttalk', array("Notice", "getMosttalkUsers"), array(20), 3600 * 24);
//    	$users = common_random_fetch($users,5);
//    	$this->showUserListWidget($users, '游戏酒馆草根达人',common_local_url('rank',array('type' => 'user')));
    	
    	$users = common_stream('userids:mostvisit', array("Profile", "getMostvisitUsers"), array(20), 3600 * 24);
    	$users = common_random_fetch($users,5);
    	$this->showUserListWidget($users, '游戏酒馆人气之星',common_local_url('rank',array('type' => 'user')));
    
    	if($this->args['area'] == 'game')
    		$subs = common_stream('userids:active:game'.$this->args['game_id'], array("Grade_record", "getActiveUsers"), array(20,$this->args['area'],$this->args['game_id']), 3600 * 24);
    	else if($this->args['area'] == 'gameserver')
    		$subs = common_stream('userids:active:server'.$this->args['server_id'], array("Grade_record", "getActiveUsers"), array(20,$this->args['area'],$this->args['server_id']), 3600 * 24);
    	else 
    		$subs = common_stream('userids:active', array("Grade_record", "getActiveUsers"), array(20), 3600 * 24);
    	$subs = common_random_fetch($subs,5);
    	if($subs)
    		$this->showUserListWidget($subs, '今日活跃用户', common_local_url('rank',array('type' => 'user')), false, $this->args['area']);
    	
    	if ($this->cur_user) {
	    	$recommendids = $this->cur_user->getRecommmended();
	    	if($recommendids)
	    		$this->showUserListWidget($recommendids, '您可能感兴趣的人', common_local_url('rank',array('type' => 'user')), true, 'all');
    	}
    }
	function showExpelist() {
		$nl = new ShowgameNoticeList($this->args['exprnotices'], $this);
        $cnt = $nl->show();
        if($this->args['area'])
        	$this->pagination($this->cur_page > 1, $cnt > NOTICES_PER_PAGE,
                     $this->cur_page, 'experiences', array('area' => $this->args['area']));       		
       	else        	
        	$this->pagination($this->cur_page > 1, $cnt > NOTICES_PER_PAGE,
                          $this->cur_page, 'experiences');
	}
	
 	function showPageNotices($args) {
    	$this->args = $args;
    	$this->cur_page = $args['cur_page'];

    	$this->startHTML('text/xml;charset=utf-8');

    	$nl = new ShowgameNoticeList($this->args['exprnotices'], $this);
       	$cnt = $nl->show();
       	
       	if ($this->args['area'])
        	$this->pagination($this->cur_page > 1, $cnt > NOTICES_PER_PAGE,
                     $this->cur_page, 'experiences', array('area' => $this->args['area']));       		
       	else        	
        	$this->pagination($this->cur_page > 1, $cnt > NOTICES_PER_PAGE,
                          $this->cur_page, 'experiences');            
                                 	                        
	    $this->endHTML();
    }
 	function showEmptyList()
 	{
 		 $message = '这是平台游戏交流消息列表， 但是还没有人发表过任何消息。' . ' ';

        if (common_current_user()) {
            $message .= '快来第一个发交流吧！';
        }
        else {
            if (! (common_config('site','closed') || common_config('site','inviteonly'))) {
                $message .= '赶快来 [注册](%%action.register%%) ， 第一个发布交流信息！';
            }
		}
		
		$this->tu->showEmptyListBlock(common_markup_to_html($message));
 		
    }
    
	function showCorehead() {
		$this->elementStart('h2');
		$this->elementStart('ul',array('class' => 'clearfix','id' => 'public_thirdary_nav'));
		if ($this->args['area'] == 'all' || !$this->args['area'])
			$this->elementStart('li',array('class' => 'active'));
		else
			$this->elementStart('li');
			$this->element('a',array('href' => common_local_url('experiences', array('area' => 'all')),'alt' => common_config('site', 'name') . '平台'), common_config('site', 'name') . '平台');
		$this->elementEnd('li');
		if(common_current_user())
		{
			$this->element('li',null,'|');
			if($this->args['area'] == 'game')
				$this->elementStart('li',array('class' => 'active'));
			else
				$this->elementStart('li');
				$this->element('a',array('href' => common_local_url('experiences', array('area' => 'game')),'alt' => $this->args['gamename']),$this->args['gamename']);
			$this->elementEnd('li');
			$this->element('li',null,'|');
			if($this->args['area'] == 'gameserver')
				$this->elementStart('li',array('class' => 'active'));
			else
				$this->elementStart('li');
				$this->element('a',array('href' => common_local_url('experiences', array('area' => 'gameserver')),'alt' => $this->args['servername']),$this->args['servername']);
			$this->elementEnd('li');
		}
		$this->elementEnd('ul');
		$this->elementEnd('h2');
	}
	
	function showShaishaiScripts() {
    	parent::showShaishaiScripts();
	    $this->script('js/lshai_search.js');
    }
}
?>