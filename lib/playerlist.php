<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class PlayerList extends ProfileList
{
	
	function show()
    {
    	$this->out->elementStart('ol', array('class' => 'users'));
    	
        $cnt = 0;
        while ($this->profiles->fetch()) {
            $cnt++;
            
            if($cnt > PROFILES_PER_PAGE) {
                break;
            }
            
            $this->out->elementStart('li', array('class' => 'user', 'pid' => $this->profiles->id));
            $this->showAvatar($this->profiles);
            $this->showNickname($this->profiles);
            $this->showInfos($this->profiles);
            $this->showSubs($this->profiles);
            $this->out->elementStart('div', 'op');
            $this->showOperations($this->profiles);
            $this->out->elementEnd('div');
            $this->showDetail($this->profiles);
            $this->out->elementEnd('li');
        }
    	if($cnt == 0)
 		{
 			$message = '这是平台游戏玩家列表，但现在还没有您感兴趣的用户。' . ' ';

       		 if (common_current_user()) {
            	$message .= '快邀请其他游戏玩家加入到这个网游玩家的互动社区吧！';
        	}
       		 else {
            		if (! (common_config('site','closed') || common_config('site','inviteonly'))) {
               		 $message .= '赶快来 [注册](%%action.register%%) ， 成为' . common_config('site', 'name') . '的一员吧！';
            		}
			}
		
			$this->out->tu->showEmptyListBlock(common_markup_to_html($message));
    	}
        $this->out->elementEnd('ol');
        
        return $cnt;
    }
    
    function showNickname($profile)
    {
    	$this->out->elementStart('p', 'nickname');
    	$this->out->elementStart('strong');
    	$this->out->elementStart('a', array('href' => $profile->profileurl, 'title' => '访问' . $profile->nickname . '在' . common_config('site', 'name') . '的主页'));
    	$this->out->text($profile->nickname);
    	$this->out->elementEnd('a');
    	$this->out->elementEnd('strong');
    	$game = Game::staticGet('id', $profile->game_id);
    	$game_server = Game_server::staticGet('id', $profile->game_server_id);
    	$this->out->element('span', null, $game->name);
    	$this->out->element('span', null, $game_server->name);
    	$this->out->elementEnd('p');
    }
    
	function showInfos($profile) {
    	$this->out->element('p',array('class' => 'bio'),$profile->bio?$profile->bio:'该用户还没有添加简介');
    }
    
    function showSubs($profile) {
    	$this->out->elementStart('p',array('class' => 'subscriptions'));
    	$this->out->element('span',null,$profile->followers);
    	$this->out->text('关注者');
    	$this->out->elementEnd('p');
    }
    
	function showOperations($profile) {
//    	if($this->cur_user && $profile->id == $this->cur_user->id || !common_current_user()){
//    		return;
//    	}
    	
    	$isSubscribed = null;
    	
		if ($this->cur_user && ! $this->is_own) {
    		$isSubscribed = $this->cur_user->isSubscribed($profile);
    	
	    	if (! $isSubscribed) {
	    		$this->_showSubscribe($profile);
	    	}else{
	    		$this->_showSubscribed($profile);
	    	}
    	} else {
    		$this->out->element('a', array('class' => 'button76 orange76 trylogin', 'href' => common_path('register'), 'title' => '在游戏酒馆上关注TA', 'rel' => 'nofollow'), '关注');
    	}
    	
    	$this->out->elementStart('a',array('class' => 'fold','title' => '查看TA的更多资料','href' => '#'));
    	$this->out->text('详细');
    	$this->out->elementEnd('a');
    	
    }
    
    function showDetail($profile)
    {
//    	if($this->cur_user && $profile->id == $this->cur_user->id || !common_current_user()){
//    		return;
//    	}
    	//get user's interest
    	$interest = '';
    	$classifiedCategories = User_interest::getClassifiedInterestByUser($profile->id);
    	
    	$userdefineInterests = User_interest::getSelfDefinedInterestByUser($profile->id);
    	
    	if (count($classifiedCategories) > 0) {
    		$interest = implode("、", $classifiedCategories);
    	}
    	
     	if (count($userdefineInterests) > 0) {
			$interest .= '、'.implode("、", $classifiedCategories);
        }
    	$this->out->elementStart('div',array('class' => 'detail', 'style' => 'display:none;'));
    	$this->out->elementStart('div',array('class' => 'profile'));
    	$this->out->elementStart('p');
    	$this->out->element('strong',null,'性别');
    	$this->out->text($profile->sex == 'F' ? '女':'男');
    	$this->out->elementEnd('p');
    	$this->out->elementStart('p');
    	$this->out->element('strong',null,'生日');
    	$this->out->text(($profile->birthday) ? $profile->birthday:'未完善');
    	$this->out->elementEnd('p');
    	$this->out->elementStart('p');
    	$this->out->element('strong',null,'所在地');
    	$this->out->text(($profile->province || $profile->city || $profile->location) ? $profile->province.'-'.$profile->city:'迷路中...');
    	$this->out->elementEnd('p');
    	$this->out->elementStart('p');
    	$this->out->element('strong',null,'个人主页');
    	if ($profile->profileurl) {
    		$this->out->element('a', array('href' => $profile->profileurl, 'target' => '_blank'), $profile->profileurl);
    	} else {
    		$this->out->text('未完善');
    	}
    	$this->out->elementEnd('p');
    	$this->out->elementStart('p');
    	$this->out->element('strong',null,'兴趣爱好');
    	
    	if ($interest) {
		    $this->out->text(common_cut_string($interest, 54).'...');
		}else {
			$this->out->text('想知道我的兴趣爱好？关注我吧！');
		}
    	$this->out->elementEnd('p');
    	$this->out->elementEnd('div');
    	
    	
    	
    	$notice = Notice::getRecentone($profile->id);

    	$this->out->elementStart('dl',array('class' => 'notice'));
    	$this->out->elementStart('dt');
    	$this->out->text('最近的消息');
    	$this->out->element('span',array('class' => 'image'));
    	$this->out->elementEnd('dt');
    	$this->out->elementStart('dd');
    	if($notice) {
	    	$this->out->elementStart('p',array('class' => 'info'));
	    	$dt = common_date_iso8601($notice->created);
	        $this->out->element('strong', array('class' => 'time timestamp',
	                                          'title' => $dt, 'time' => strtotime($notice->created)),
	                            common_date_string($notice->created));
	        $this->showNoticeSource($notice->source);
	    	$this->out->elementEnd('p');
	    	$this->out->elementStart('div', array('class' => 'content'));
	    	$this->out->raw($notice->rendered);
	    	$this->out->elementEnd('div');
    	} else {
    		$this->out->text('该用户还没有发布第一条消息');
    	}
    	$this->out->elementEnd('dd');
    	$this->out->elementEnd('dl');
    	$this->out->elementEnd('div');
    }
    
    function showNoticeSource($source_name)
    {
    	$this->out->raw(common_source_link($source_name));
    }
    
}
