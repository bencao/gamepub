<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class FollowsomepeopleHTMLTemplate extends RegisterWizardHTMLTemplate
{

	/**
     * Title of the page
     *
     * @return string Title of the page
     */
    function title()
    {
        return '开始关注其他玩家';
    }
    
	function extraHeaders() {
		setcookie('xt', 'los', time()+60*60*24*30, '/register');
	}
    
	function showGreeting() {
		$this->elementStart('div', 'greet');
		$this->elementStart('div', 'avatar');
		$welcomeUser = $this->arg('welcomeUser');
		if ($welcomeUser) {
			$avatar = $welcomeUser->getProfile()->getAvatar(AVATAR_STREAM_SIZE);
			if ($avatar) {
				$this->element('img', array('src' => $avatar->displayUrl()));
			} else {
				$this->element('img', array('src' => common_path('images/welcomeAnimal.png')));
			}
		} else {
			$this->element('img', array('src' => common_path('images/welcomeAnimal.png')));
		}
		$this->elementEnd('div');
		$this->elementStart('p');
		$this->text($this->greeting());
		$this->element('span', 'pointer');
		$this->elementEnd('p');
		$this->elementEnd('div');
	}
	
	function greeting() {
		$welcomeUser = $this->arg('welcomeUser');
		if ($welcomeUser) {
			return '注册成功啦！开始的时候多关注几个人，就可以一直收到有趣的新消息呢。';
		} else {
			return '注册成功！ 我们为您找到了一些有趣的玩家， 祝您玩的愉快。';
		}
	}
    
    function _getRandomServerHellowords() {
    	$arr = array('初来乍到，请牛人多指教！',
    		'很早就听说你了，能认识一下吗？',
    		'你好！我跟你是同服务器的，交个朋友好吗？',
    		'老大，我是新人，请多照照我啊~',
    		'之前我有见过你，我也是' . $this->cur_game_server->name . '的',
    		'你不是那谁谁谁么？',
    		'真巧，我也在玩' . $this->cur_game->name . '，希望认识一下。',
    		'这是我第一次来，可以和你聊聊吗？',
    		'很抱歉打扰你，我只是想跟你聊一聊。',
    		'哇，你好厉害哦',
    		'高手，能教我怎么发图片么？',
    		'你好！能麻烦你教我怎么发音乐么？',
    		'你好，能教我发视频么？谢谢啊！',
    		'你好啊，找个游戏朋友聊聊，你是哪里人？',
    		'在这里能找到玩同一个游戏的人，好开心，你好你好！',
    		'你好!交个朋友O(∩_∩)O哈哈~',
    		'很无聊找人聊聊\（￣▽￣）／',
    		'你好啊朋友，有空一起聊聊吗？',
    		'hello~~~~~~想认识你一下∩ 3 ∩',
    		'hello~~~~~~你是哪里人呀？可以聊聊吗？',
    		'你好啊，终于可以在这里安家了，以后多多指教！'
    	);
    	
    	$getarr = common_random_fetch($arr, 1);
    	return $getarr[0];
    }
    
	function _getRandomRelationHellowords() {
    	$arr = array('你也是刚注册不久的吗？',
    		'希望能与你交个朋友!',
    		'很高兴见到你！',
    		'真巧，我也刚注册的' . common_config('site', 'name'),
    		'看到你刚注册，很有缘，我是玩' . $this->cur_game->name . '的',
    		'hello~能聊聊么？',
    		'你也是第一次上' . common_config('site', 'name') . '么？',
    		'你是哪儿人啊？',
    		'居然我们俩是同时注册' . common_config('site', 'name') . '的……很高兴能认识你，真有缘^ ^',
    		'你好啊，我们是同时在这里注册的，真有缘啊！',
    		'在这里能找到同时注册的人，好有缘分啊。你知道怎样发视频了么？',
    		'你好啊，我们好像一起注册的，呵呵！',
    		'你好啊，认识你很高兴哈，玩什么游戏呢？',
    		'你好啊，我刚注册，一起聊聊吧！',
    		'很无聊找人聊聊\（￣▽￣）／',
    		'这里感觉还不错，我刚刚注册，呵呵，很高兴认识你！',
    		'你好啊，终于可以在这里安家了，以后多多指教！'
    	);
    	
    	$getarr = common_random_fetch($arr, 1);
    	return $getarr[0];
    }
    
    function _showServerPeople() {
		
		$this->elementStart('dl', 'server_people');
		$this->elementStart('dt');
		$this->element('input', array('type' => 'checkbox', 'class' => 'checkbox', 'name' => 'follow_server', 'checked' => 'checked'));
		$this->text('关注同游戏活跃游友');
		$this->element('span', null, ' - 游戏中交流与分享，生活中做真实朋友');
		$this->elementEnd('dt');
		$this->elementStart('dd');
		$this->elementStart('ul');
    	if (! empty($this->gamevipprofiles) && $this->gamevipprofiles->N > 0) {
			while ($this->gamevipprofiles->fetch()) {
				$this->elementStart('li');
				$this->elementStart('div', 'avatar');
        
				$this->elementStart('a', array('target' => '_blank', 'href' => $this->gamevipprofiles->profileurl, 'title' => ($this->gamevipprofiles->sex == 'M' ? '男' : '女')));
				$avatar = $this->gamevipprofiles->getAvatar(AVATAR_PROFILE_SIZE);
        		$this->element('img', array('src' => ($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_PROFILE_SIZE, $this->gamevipprofiles->id, $this->gamevipprofiles->sex),
                                         'width' => '60',
                                         'height' => '60',
                                         'alt' => $this->gamevipprofiles->nickname));
				$this->elementEnd('a');
				$this->elementEnd('div');
				$this->elementStart('p', 'nickname');
				$this->element('a', array('target' => '_blank', 'href' => $this->gamevipprofiles->profileurl, 'title' => '访问' . $this->gamevipprofiles->nickname . '的主页'), 
					$this->gamevipprofiles->nickname . '(名人)');
				$this->elementEnd('p');
				$this->element('p', 'job', $this->cur_game->name . $this->cur_game_server->name);
				$this->element('p', 'bio', empty($this->gamevipprofiles->bio) ? '该用户还没有添加简介' : $this->gamevipprofiles->bio);
				$this->elementEnd('li');
			}
		}
		if (! empty($this->serveractiveprofiles) && $this->serveractiveprofiles->N > 0) {
			while ($this->serveractiveprofiles->fetch()) {
				$this->elementStart('li');
				$this->elementStart('div', 'avatar');
        
				$this->elementStart('a', array('target' => '_blank', 'href' => $this->serveractiveprofiles->profileurl, 'title' => ($this->serveractiveprofiles->sex == 'M' ? '男' : '女')));
				$avatar = $this->serveractiveprofiles->getAvatar(AVATAR_PROFILE_SIZE);
        		$this->element('img', array('src' => ($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_PROFILE_SIZE, $this->serveractiveprofiles->id, $this->serveractiveprofiles->sex),
                                         'width' => '60',
                                         'height' => '60',
                                         'alt' => $this->serveractiveprofiles->nickname));
				$this->elementEnd('a');
				$this->elementEnd('div');
				$this->elementStart('p', 'nickname');
				$this->element('a', array('target' => '_blank', 'href' => $this->serveractiveprofiles->profileurl, 'title' => '访问' . $this->serveractiveprofiles->nickname . '的主页'), 
					$this->serveractiveprofiles->nickname);
				$this->elementEnd('p');
				$this->element('p', 'job', $this->cur_game->name . $this->cur_game_server->name);
				$this->element('p', 'bio', empty($this->serveractiveprofiles->bio) ? '该用户还没有添加简介' : $this->serveractiveprofiles->bio);
				$this->elementEnd('li');
			}
		}
    	if (! empty($this->serverpopularprofiles) && $this->serverpopularprofiles->N > 0) {
			while ($this->serverpopularprofiles->fetch()) {
				$this->elementStart('li');
				$this->elementStart('div', 'avatar');
        
				$this->elementStart('a', array('target' => '_blank', 'href' => $this->serverpopularprofiles->profileurl));
				$avatar = $this->serverpopularprofiles->getAvatar(AVATAR_PROFILE_SIZE);
        		$this->element('img', array('src' => ($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_PROFILE_SIZE, $this->serverpopularprofiles->id, $this->serverpopularprofiles->sex),
                                         'width' => '60',
                                         'height' => '60',
                                         'alt' => $this->serverpopularprofiles->nickname));
				$this->elementEnd('a');
				$this->elementEnd('div');
				$this->elementStart('p', 'nickname');
				$this->element('a', array('target' => '_blank', 'href' => $this->serverpopularprofiles->profileurl, 'title' => '访问' . $this->serverpopularprofiles->nickname . '的主页'), 
					$this->serverpopularprofiles->nickname);
				$this->elementEnd('p');
				$this->element('p', 'job', $this->cur_game->name . $this->cur_game_server->name);
				$this->element('p', 'bio', empty($this->serverpopularprofiles->bio) ? '该用户还没有添加简介' : $this->serverpopularprofiles->bio);
				$this->elementEnd('li');
			}
		}
    	if (! empty($this->gameactiveprofiles) && $this->gameactiveprofiles->N > 0) {
			while ($this->gameactiveprofiles->fetch()) {
				$this->elementStart('li');
				$this->elementStart('div', 'avatar');
        
				$this->elementStart('a', array('target' => '_blank', 'href' => $this->gameactiveprofiles->profileurl));
				$avatar = $this->gameactiveprofiles->getAvatar(AVATAR_PROFILE_SIZE);
        		$this->element('img', array('src' => ($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_PROFILE_SIZE, $this->gameactiveprofiles->id, $this->gameactiveprofiles->sex),
                                         'width' => '60',
                                         'height' => '60',
                                         'alt' => $this->gameactiveprofiles->nickname));
				$this->elementEnd('a');
				$this->elementEnd('div');
				$this->elementStart('p', 'nickname');
				$this->element('a', array('target' => '_blank', 'href' => $this->gameactiveprofiles->profileurl, 'title' => '访问' . $this->gameactiveprofiles->nickname . '的主页'), 
					$this->gameactiveprofiles->nickname);
				$this->elementEnd('p');
				$sserver = Game_server::staticGet($this->gameactiveprofiles->game_server_id);
				$this->element('p', 'job', $this->cur_game->name . $sserver->name);
				$this->element('p', 'bio', empty($this->gameactiveprofiles->bio) ? '该用户还没有添加简介' : $this->gameactiveprofiles->bio);
				$this->elementEnd('li');
			}
		} 
//		else {
//			$this->element('li', null, '暂时没有可以推荐的好友');
//		}
		$this->elementEnd('ul');
		$this->elementEnd('dd');
		$this->elementEnd('dl');
    }
    
	function _showMosttalkPeople() {
		
		$this->elementStart('dl', 'server_people');
		$this->elementStart('dt');
		$this->element('input', array('type' => 'checkbox', 'class' => 'checkbox', 'name' => 'follow_server', 'checked' => 'checked'));
		$this->text('关注同游戏活跃游友');
		$this->element('span', null, ' - 游戏中交流与分享，生活中做真实朋友');
		$this->elementEnd('dt');
		$this->elementStart('dd');
		$this->elementStart('ul');
    	if (! empty($this->gamevipprofiles) && $this->gamevipprofiles->N > 0) {
			while ($this->gamevipprofiles->fetch()) {
				$this->elementStart('li');
				$this->elementStart('div', 'avatar');
        
				$this->elementStart('a', array('target' => '_blank', 'href' => $this->gamevipprofiles->profileurl, 'title' => ($this->gamevipprofiles->sex == 'M' ? '男' : '女')));
				$avatar = $this->gamevipprofiles->getAvatar(AVATAR_PROFILE_SIZE);
        		$this->element('img', array('src' => ($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_PROFILE_SIZE, $this->gamevipprofiles->id, $this->gamevipprofiles->sex),
                                         'width' => '60',
                                         'height' => '60',
                                         'alt' => $this->gamevipprofiles->nickname));
				$this->elementEnd('a');
				$this->elementEnd('div');
				$this->elementStart('p', 'nickname');
				$this->element('a', array('target' => '_blank', 'href' => $this->gamevipprofiles->profileurl, 'title' => '访问' . $this->gamevipprofiles->nickname . '的主页'), 
					$this->gamevipprofiles->nickname . '(名人)');
				$this->elementEnd('p');
				$this->element('p', 'job', $this->cur_game->name . $this->cur_game_server->name);
				$this->element('p', 'bio', empty($this->gamevipprofiles->bio) ? '该用户还没有添加简介' : $this->gamevipprofiles->bio);
				$this->elementEnd('li');
			}
		}
		if (! empty($this->servermosttalkprofiles) && $this->servermosttalkprofiles->N > 0) {
			while ($this->servermosttalkprofiles->fetch()) {
				$this->elementStart('li');
				$this->elementStart('div', 'avatar');
        
				$this->elementStart('a', array('target' => '_blank', 'href' => $this->servermosttalkprofiles->profileurl, 'title' => ($this->servermosttalkprofiles->sex == 'M' ? '男' : '女')));
				$avatar = $this->servermosttalkprofiles->getAvatar(AVATAR_PROFILE_SIZE);
        		$this->element('img', array('src' => ($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_PROFILE_SIZE, $this->servermosttalkprofiles->id, $this->servermosttalkprofiles->sex),
                                         'width' => '60',
                                         'height' => '60',
                                         'alt' => $this->servermosttalkprofiles->nickname));
				$this->elementEnd('a');
				$this->elementEnd('div');
				$this->elementStart('p', 'nickname');
				$this->element('a', array('target' => '_blank', 'href' => $this->servermosttalkprofiles->profileurl, 'title' => '访问' . $this->servermosttalkprofiles->nickname . '的主页'), 
					$this->servermosttalkprofiles->nickname);
				$this->elementEnd('p');
				$this->element('p', 'job', $this->cur_game->name . $this->cur_game_server->name);
				$this->element('p', 'bio', empty($this->servermosttalkprofiles->bio) ? '该用户还没有添加简介' : $this->servermosttalkprofiles->bio);
				$this->elementEnd('li');
			}
		}
    	if (! empty($this->gamemosttalkprofiles) && $this->gamemosttalkprofiles->N > 0) {
			while ($this->gamemosttalkprofiles->fetch()) {
				$this->elementStart('li');
				$this->elementStart('div', 'avatar');
        
				$this->elementStart('a', array('target' => '_blank', 'href' => $this->gamemosttalkprofiles->profileurl));
				$avatar = $this->gamemosttalkprofiles->getAvatar(AVATAR_PROFILE_SIZE);
        		$this->element('img', array('src' => ($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_PROFILE_SIZE, $this->gamemosttalkprofiles->id, $this->gamemosttalkprofiles->sex),
                                         'width' => '60',
                                         'height' => '60',
                                         'alt' => $this->gamemosttalkprofiles->nickname));
				$this->elementEnd('a');
				$this->elementEnd('div');
				$this->elementStart('p', 'nickname');
				$this->element('a', array('target' => '_blank', 'href' => $this->gamemosttalkprofiles->profileurl, 'title' => '访问' . $this->gamemosttalkprofiles->nickname . '的主页'), 
					$this->gamemosttalkprofiles->nickname);
				$this->elementEnd('p');
				$sserver = Game_server::staticGet($this->gamemosttalkprofiles->game_server_id);
				$this->element('p', 'job', $this->cur_game->name . $sserver->name);
				$this->element('p', 'bio', empty($this->gamemosttalkprofiles->bio) ? '该用户还没有添加简介' : $this->gamemosttalkprofiles->bio);
				$this->elementEnd('li');
			}
		} 
//		else {
//			$this->element('li', null, '暂时没有可以推荐的好友');
//		}
		$this->elementEnd('ul');
		$this->elementEnd('dd');
		$this->elementEnd('dl');
    }
    function _showRelationPeople() {
    	$this->elementStart('dl', 'relation_people');
		$this->elementStart('dt');
		$this->element('input', array('type' => 'checkbox', 'class' => 'checkbox', 'name' => 'follow_relation', 'checked' => 'checked'));
		$this->text('关注有缘游友');
		$this->element('span', null, ' - 同时注册的人');
		$this->elementEnd('dt');
		$this->elementStart('dd');
		$this->elementStart('ul');
    	if (! empty($this->latestauthorprofiles) && $this->latestauthorprofiles->N > 0) {
			while ($this->latestauthorprofiles->fetch()) {
				$this->elementStart('li');
				$this->elementStart('div', 'avatar');
        
				$this->elementStart('a', array('target' => '_blank', 'href' => $this->latestauthorprofiles->profileurl, 'title' => '玩 ' . $this->latestauthorprofiles->getUser()->getGame()->name));
				$avatar = $this->latestauthorprofiles->getAvatar(AVATAR_PROFILE_SIZE);
        		$this->element('img', array('src' => ($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_PROFILE_SIZE, $this->latestauthorprofiles->id, $this->latestauthorprofiles->sex),
                                         'width' => '60',
                                         'height' => '60',
                                         'alt' => $this->latestauthorprofiles->nickname));
				$this->elementEnd('a');
				$this->elementEnd('div');
				$this->elementStart('p', 'nickname');
				$this->element('a', array('target' => '_blank', 'href' => $this->latestauthorprofiles->profileurl, 'title' => '访问' . $this->latestauthorprofiles->nickname . '的主页'), 
					$this->latestauthorprofiles->nickname);
				$this->elementEnd('p');
				$this->element('p', 'sex', $this->latestauthorprofiles->sex == 'M' ? '男' : '女');
				$this->elementEnd('li');
			}
    	}
    	if (! empty($this->recentprofiles) && $this->recentprofiles->N > 0) {
			while ($this->recentprofiles->fetch()) {
				$this->elementStart('li');
				$this->elementStart('div', 'avatar');
        
				$this->elementStart('a', array('target' => '_blank', 'href' => $this->recentprofiles->profileurl, 'title' => '玩 ' . $this->recentprofiles->getUser()->getGame()->name));
				$avatar = $this->recentprofiles->getAvatar(AVATAR_PROFILE_SIZE);
        		$this->element('img', array('src' => ($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_PROFILE_SIZE, $this->recentprofiles->id, $this->recentprofiles->sex),
                                         'width' => '60',
                                         'height' => '60',
                                         'alt' => $this->recentprofiles->nickname));
				$this->elementEnd('a');
				$this->elementEnd('div');
				$this->elementStart('p', 'nickname');
				$this->element('a', array('target' => '_blank', 'href' => $this->recentprofiles->profileurl, 'title' => '访问' . $this->recentprofiles->nickname . '的主页'), 
					$this->recentprofiles->nickname);
				$this->elementEnd('p');
				$this->element('p', 'sex', $this->recentprofiles->sex == 'M' ? '男' : '女');
				$this->elementEnd('li');
			}
    	}
//		} else {
//			$this->element('li', null, '暂时没有可以推荐的好友');
//		}
		$this->elementEnd('ul');
		$this->elementEnd('dd');
		$this->elementEnd('dl');
    }
    
    function _sayHelloToServerPeople() {
    	$this->elementStart('dl', 'hello_server_people');
		$this->elementStart('dt');
		$this->element('input', array('type' => 'checkbox', 'class' => 'checkbox', 'name' => 'say_to_server', 'checked' => 'checked'));
		$this->text('打声招呼');
		$this->elementEnd('dt');
		$this->elementStart('dd');
		$this->element('textarea', array('name' => 'say_server_word'), $this->_getRandomServerHellowords());
		$this->elementEnd('dd');
		$this->elementEnd('dl');
    }
    
    function _sayHelloToRelationPeople() {
    	$this->elementStart('dl', 'hello_relation_people');
		$this->elementStart('dt');
		$this->element('input', array('type' => 'checkbox', 'class' => 'checkbox', 'name' => 'say_to_relation', 'checked' => 'checked'));
		$this->text('打声招呼');
		$this->elementEnd('dt');
		$this->elementStart('dd');
		$this->element('textarea', array('name' => 'say_relation_word'), $this->_getRandomRelationHellowords());
		$this->elementEnd('dd');
		$this->elementEnd('dl');
    }
    
    function _showOps() {
    	$this->elementStart('div', 'op');
    	$this->element('input', array('type' => 'submit', 'class' => 'submit button99 green99',
						   'value' => '关注他们'));
    	$this->element('a', array('class' => 'help', 'target' => '_blank', 'href' => '/doc/help/relation', 'title' => '关注表示对某个人感兴趣，关注后，他的分享 会即时出现在您的空间'), '什么是关注');
//    	$this->element('a', array('class' => 'next', 'href' => common_local_url('inviteregister')), '跳过');
    	$this->elementEnd('div');
    }
    
    function showContent() {
    	$this->cur_game = $this->arg('cur_game');
    	$this->cur_game_server = $this->arg('cur_game_server');
    	$this->gamevipprofiles = $this->arg('gamevip');
//    	$this->gameactiveprofiles = $this->arg('gameactive');
//    	$this->serveractiveprofiles = $this->arg('serveractive');
//    	$this->serverpopularprofiles = $this->arg('serverpopular');
		$this->gamemosttalkprofiles = $this->arg('gamemosttalk');
		$this->servermosttalkprofiles = $this->arg('servermosttalk');
    	$this->recentprofiles = $this->arg('recent');
    	$this->latestauthorprofiles = $this->arg('latestauthor');
    	
//    	$list = array();
//
//    	if (! empty($gameactiveprofiles) && $gameactiveprofiles->N > 0) {
//			$gameactivelists = new ProfileListR($gameactiveprofiles, $this, $cur_game->name . '中活跃的用户', $list);
//			$list = array_merge($list, $gameactivelists->show());
//		}
//    	if (! empty($serveractiveprofiles) && $serveractiveprofiles->N > 0) {
//			$serveractivelists = new ProfileListR($serveractiveprofiles, $this, $cur_game_server->name . '中最活跃的用户', $list);
//			$list = array_merge($list, $serveractivelists->show());
//		}
//    	if (! empty($serverpopularprofiles) && $serverpopularprofiles->N > 0) {
//			$serverpopularlists = new ProfileListR($serverpopularprofiles, $this, $cur_game_server->name . '中最受欢迎的用户', $list);
//			$list = array_merge($list, $serverpopularlists->show());
//		}
//		
//		if (empty($list)) {
//			$this->elementStart('dl', 'b_rc_el');
//    		$this->element('dt', null, '目前还没有可推荐的游友。');
//    		$this->elementEnd('dl');			
//		}
		
    	$this->_showErrorMessage();
    	
    	$this->tu->startFormBlock(array('method' => 'post',
                                          'id' => 'followpeople',
                                          'class' => 'form_settings',
                                          'action' => common_local_url('followsomepeople')), '选择游戏好友');
		
    //	$this->_showServerPeople();
		
    	$this->_showMosttalkPeople();
    	
		$this->_showRelationPeople();
		
//		$this->_sayHelloToServerPeople();
//		
//		$this->_sayHelloToRelationPeople();
		
		$this->_showOps();
		
		$this->tu->endFormBlock();
		
		$this->_showAdwordsAna();
    	
//    	$this->element('a', array('href' => common_local_url('registersuccess')));
    }
    
    function _showAdwordsAna() {
    	$this->raw('<!-- Google Code for &#29992;&#25143;&#25104;&#21151;&#27880;&#20876; Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 1034144568;
var google_conversion_language = "zh_CN";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "eP2mCJbD0wEQuJaP7QM";
var google_conversion_value = 0;
if (1) {
  google_conversion_value = 1;
}
/* ]]> */
</script>
<script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/1034144568/?value=1&amp;label=eP2mCJbD0wEQuJaP7QM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>');
    }
    
    function showHeader() {
    	
    }
    
	function _showErrorMessage() {
		if ($this->arg('register_error')) {
			$this->element('div', 'error', $this->arg('register_error'));
		}
	}
    
//    function showScripts()
//    {
//        parent::showScripts();  
//        $this->script('js/lshai_relation.js');
//    }
	
}

?>