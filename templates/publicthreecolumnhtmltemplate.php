<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class PublicthreecolumnHTMLTemplate extends PublicbaseHTMLTemplate
{
	
	function showScripts() {
    	parent::showScripts();
	    $this->script('js/lshai_relation.js');
    }
    
	function showCore() {
		$this->showLeftNav();
		
		$this->elementStart('div', array('id' => 'public_contents', 'style' => 'width: 519px; border-right: 1px solid rgb(176, 176, 176);'));
		$this->showContent();
		$this->elementEnd('div');
		
    	$this->elementStart('div', array('id' => 'public_widgets'));
		$this->showRightside();
		$this->elementEnd('div');
	}
	
	function showContent() {
		
	}
	
	function showRightside() {
		
	}
	
	function showSearchFormWidget() {
        $this->elementStart('dl', 'widget search');
		$this->element('dt', null, '搜索您感兴趣的文字');
		
		$this->elementStart('dd', 'clearfix');	
		$this->tu->startFormBlock(array('action' => common_path('search/notice')));
		$this->element('input', array('class' => 'text', 'type' => 'text', 'name' => 'q'));
		$this->element('input', array('class' => 'submit button60 green60', 'type' => 'submit', 'value' => '搜索'));
		$this->tu->endFormBlock();
        $this->elementEnd('dd');
		$this->elementEnd('dl');
		
    }
	
	function showUserListWidget($userlist, $title="", $url="#", $id=false, $area = 'all') {
    	$this->elementStart('dl', 'widget people');
		$this->elementStart('dt');
		$this->text($title);
		if ($url != '#') {
			$this->element('a', array('class' => 'toggle', 'href' => $url, 'title' => '查看更多的' . common_config('site', 'name') . '用户'), '更多');
		}
		$this->elementEnd('dt');
		
		$this->elementStart('dd');		
		$this->elementStart('ul');
    	$cnt = 0;
		foreach ($userlist as $user){
			$cnt ++;
			if($id)
				$profile = Profile::staticGet('id', $user);
			else 
				$profile = Profile::staticGet('id', $user['user_id']);
			if ($profile) {
	        	$avatar = $profile->getAvatar(AVATAR_STREAM_SIZE);
				$this->elementStart('li', array('pid' => $profile->id));
				$this->elementStart('div', 'avatar');
	
				$this->elementStart('a', array('href' => common_path($profile->uname), 'title' => $profile->nickname));
	
	        	$this->element('img', array('height' => '48', 'width' => '48',
			    		'src' => ($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_STREAM_SIZE, $profile->id, $profile->sex), 
			    		'alt' => $profile->nickname
			        	, 'width' => '42', 'height' => '42')); 
	        	$this->elementEnd('a');   
	        	$this->elementEnd('div');  
				
	        	$this->elementStart('p', 'nickname');
	        	$this->element('a', array('href' => common_path($profile->uname),
	        		 'title' => $profile->nickname), $profile->nickname);
	        	$this->elementEnd('p');  
	        	
	        	$game = Game::staticGet('id', $profile->game_id);
	        	$game_server = Game_server::staticGet('id', $profile->game_server_id);
	        	$game_big_zone = Game_big_zone::staticGet('id',$game_server->game_big_zone_id);
				switch($area)
	        	{
	        		case 'all': $this->element('p', null, $game->name);
	        					break;
	        		case 'game': $this->element('p', null, $game_big_zone->name. $game_server->name);
	        					break;
	        		case 'gameserver': $this->element('p', null, $game_big_zone->name. $game_server->name);
	        					break;
	        		default    : $this->element('p', null, $game->name);
	        					break;
	        	}
	        	
	        	$this->element('p', null, ($profile->province && $profile->city) ? $profile->province . ' - ' . $profile->city:'迷路中...');
	    	
	    		if (common_current_user()) {
	    			$isSubscribed = $this->cur_user->isSubscribed($profile);
	    	
		    		if (! $isSubscribed) {
		    			$this->elementStart('form', array('class' => 'subscribe','method' => 'post', 'action' => common_path('main/subscribe')));
		        		$this->elementStart('fieldset');
		        		$this->element('legend', null, '关注');
		        		$this->element('input', array('type' => 'hidden', 'name' => 'subscribeto', 'value' => $profile->id));
		        		$this->hidden('token', common_session_token());
		        		$this->element('input', array('class' => 'submit', 'type' => 'submit', 'value' => '', 'title' => '关注' . Profile::displayName($profile->sex, false)));
		        		$this->elementEnd('fieldset');
		        		$this->elementEnd('form');
		    		} else{
		    			$this->element('div', array('class' => 'subscribed', 'title' => '已关注', 'value' => '已关注'));
		    		}
	    		} else {
	    			$this->element('a', array('class' => 'subscribe trylogin', 
	    				'href' => common_path('register?ivid=' . $profile->id), 'title' => '在' . common_config('site', 'name') . '上关注' . Profile::displayName($profile->sex, false), 'rel' => 'nofollow'), '');
	    		}
	        	
				$this->elementEnd('li');
			}
			
		}
		
    	$this->elementEnd('ul');
    	$this->elementEnd('dd');
		$this->elementEnd('dl');
    }
}
?>