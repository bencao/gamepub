<?php
if (!defined('SHAISHAI')) {
    exit(1);
}

class HalloffameHTMLTemplate extends PublictwocolumnHTMLTemplate
{
	var $vippeople;
	var $starNotice;
	var $cur_game;
	
	function title()
    {
    	return 'GamePub名人堂，星光灿烂的游戏T台';
    }
    
	function metaKeywords() {
		return '游戏高级玩家、游戏插件制作人、游戏漫画家、游戏音乐人、GamePub游戏名人堂';
	}
	
	function metaDescription() {
		return 'GamePub吸引了大批游戏高级玩家、游戏插件制作人、游戏漫画家、游戏音乐人的加盟。他们期待与您近距离的沟通，跟您分享他们领域的最新进展、感悟和生活八卦。';
	}
	
    function show($args = array()) {
    	$this->vippeople = $args['vippeople'];
    	$this->starNotice = $args['starnotice'];
    	$this->cur_game = $args['cur_game'];
    	parent::show($args);
    }
    
    function showContent() {
    	$this->elementStart('div', 'hofwrap');
    	$this->elementStart('div', 'uppart');
    	
    	$this->elementStart('dl', 'star');
    	$this->element('dt', null, '名人最新动态');
    	$this->elementStart('dd');
    	
    	if ($this->starNotice) {
    		$star = $this->starNotice->getProfile();
    		
	    	$this->elementStart('div', 'card');
	    	$this->elementStart('div', 'avatar');
	    	$this->elementStart('a', array('href' => $star->profileurl, 'title' => '访问' . $star->nickname . '的个人主页'));
	    	$this->element('img', array('src' => $star->avatarUrl(AVATAR_STREAM_SIZE), 'width' => '48', 'height' => '48'));
	    	$this->elementEnd('a');
	    	$this->elementEnd('div');
	    	$this->elementStart('div', 'op');
	    	if (! $this->cur_user) {
	    		$this->element('a', array('href' => common_local_url('register', null, array('ivid' => $star->id)), 'class' => 'trylogin subscribe', 'title' => '关注'), '关注');
	    	} else if ($this->cur_user->isSubscribed($star)) {
	    		$this->element('div', array('class' => 'subscribed'), '关注中');
	    	} else {
	    		$this->tu->startFormBlock(array('method' => 'post',
	                                           'class' => 'subscribe',
	                                           'action' => common_local_url('subscribe')), '关注');
	    		$this->element('input', array('type' => 'hidden', 'name' => 'subscribeto', 'value' => $star->id));
	        	$this->element('input', array('class' => 'submit', 'type' => 'submit', 'value' => '关注'));
				$this->tu->endFormBlock();
	    	}
	    	$this->elementEnd('div');
	    	$this->elementEnd('div');
	    	
	    	$this->elementStart('p', 'nickname');
	    	$this->element('a', array('href' => $star->profileurl, 'title' => '访问' . $star->nickname . '的个人主页'), $star->nickname);
	    	$this->elementEnd('p');
	    	$this->elementStart('p', 'info');
	    	$this->element('span', null, $star->getGameString());
	    	$this->text($star->subscriberCount() . '人关注');
	    	$this->elementEnd('p');
	    	$this->elementStart('p', 'msg');
	    	$this->element('a', array('href' => common_local_url('discussionlist', array('notice_id' => $this->starNotice->id)), 'target' => '_blank'), $this->starNotice->content);
	    	$this->elementEnd('p');
    	}
    	$this->elementEnd('dd');
    	$this->elementEnd('dl');
    	
    	$this->element('a', array('href' => common_local_url('applyvip'), 'class' => 'requestvip'));
    	
    	$this->elementStart('p', 'vipintro');
    	$this->text('为保证名人的真实性，Gamepub需要对名人身份进行验证。');
    	$this->element('a', array('href' => common_local_url('doc', array('type'=>'help', 'title'=>'usergrade'))), '查看详情>>');
    	$this->elementEnd('p');
    	
    	$this->elementStart('dl', 'recommendpeople vips');
    	$this->elementStart('dt', 'tt');
    	
    	if ($this->cur_game) {
    		$this->element('strong', null, $this->cur_game->name . '最新加入');
	    	$this->element('a', array('href' => common_local_url('halloffame')), '查看所有游戏名人');
    	} else {
	    	$this->element('strong', null, '最新加入');
	    	$this->element('a', array('id' => 'game_select', 'href' => '#'), '按游戏查看');
    	}
    	$this->elementEnd('dt');
    	$this->elementStart('dd');
    	$playerlist = new PlayerList($this, $this->vippeople, null, $this->cur_user);
        $this->cnt = $playerlist->show();
    	$this->elementEnd('dd');
    	$this->elementEnd('dl');
    	
    	$this->elementStart('div', array('class' => 'more', 'id' => 'game_more'));
        $this->elementStart('div', 'filter clearfix');
        $this->element('a', array('class' => 'hots', 'href' => '#'), '热门游戏');
        $this->element('a', array('class' => 'A', 'href' => '#'), 'A');
        $this->element('a', array('class' => 'B', 'href' => '#'), 'B');
        $this->element('a', array('class' => 'C', 'href' => '#'), 'C');
        $this->element('a', array('class' => 'D', 'href' => '#'), 'D');
        $this->element('a', array('class' => 'E', 'href' => '#'), 'E');
        $this->element('a', array('class' => 'F', 'href' => '#'), 'F');
        $this->element('a', array('class' => 'G', 'href' => '#'), 'G');
        $this->element('a', array('class' => 'H', 'href' => '#'), 'H');
        $this->element('a', array('class' => 'I', 'href' => '#'), 'I');
        $this->element('a', array('class' => 'J', 'href' => '#'), 'J');
        $this->element('a', array('class' => 'K', 'href' => '#'), 'K');
        $this->element('a', array('class' => 'L', 'href' => '#'), 'L');
        $this->element('a', array('class' => 'M', 'href' => '#'), 'M');
        $this->element('a', array('class' => 'N', 'href' => '#'), 'N');
        $this->element('a', array('class' => 'O', 'href' => '#'), 'O');
        $this->element('a', array('class' => 'P', 'href' => '#'), 'P');
        $this->element('a', array('class' => 'Q', 'href' => '#'), 'Q');
        $this->element('a', array('class' => 'R', 'href' => '#'), 'R');
        $this->element('a', array('class' => 'S', 'href' => '#'), 'S');
        $this->element('a', array('class' => 'T', 'href' => '#'), 'T');
        $this->element('a', array('class' => 'U', 'href' => '#'), 'U');
        $this->element('a', array('class' => 'V', 'href' => '#'), 'V');
        $this->element('a', array('class' => 'W', 'href' => '#'), 'W');
        $this->element('a', array('class' => 'X', 'href' => '#'), 'X');
        $this->element('a', array('class' => 'Y', 'href' => '#'), 'Y');
        $this->element('a', array('class' => 'Z', 'href' => '#'), 'Z');
        $this->elementEnd('div');
        $this->elementStart('ul', 'clearfix');
        
		$games = $this->arg('hotgames');
		foreach ($games as $g) {
			$this->elementStart('li', 'hot');
        	$this->element('a', array('href' => '#', 'gid' => $g['id']), $g['name']);
        	$this->elementEnd('li');
		}
        
        $this->elementEnd('ul');
        $this->elementEnd('div');
    	
    	$this->elementEnd('div');
    	$this->element('div', 'foot');
    	$this->elementEnd('div');
    }
    
    function showShaishaiScripts() {
    	parent::showShaishaiScripts();
    	$this->script('js/lshai_halloffame.js');
    }
    
	function showShaishaiStylesheets() {
    	parent::showShaishaiStylesheets();
    	$this->cssLink('css/settings.css', 'default');
    }
    
}