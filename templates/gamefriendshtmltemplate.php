<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR.'/lib/noticelist.php';

class GamefriendsHTMLTemplate extends GametwocolumnHTMLTemplate
{
	var $sex;
	var $agebegin;
	var $ageend;
	var $loc;
	var $game_id;
	var $game_big_zone_id;
	var $game_server_id;
	
	var $profile; //查询结果：符合条件的用户profile对象数组
	var $total;
	
	var $cur_profile;	//当前用户
	var $gameplayer;	//推荐用户profile对象数组
	var $serverplayer; 
	var $cityplayer; 
	
	
	function show($args) {
		$this->sex = $args['sex'];
		$this->agebegin = $args['agebegin'];
		$this->ageend = $args['ageend'];
		$this->loc = $args['loc'];
		$this->game_id = $args['game_id'];
    	$this->game_big_zone_id = $args['game_big_zone_id'];
    	$this->game_server_id = $args['game_server_id'];
    	
    	//若确定了服务器，则大区也需确定
    	if ($this->game_server_id && ! $this->game_big_zone_id) {
    		$this->game_big_zone_id = Game_server::getGameBigZoneId($this->game_server_id);
    	}
		
		if (array_key_exists('curprofile', $args)) {
			$this->cur_profile = $args['curprofile'];
			$this->gameplayer = $args['gameplayer'];
			$this->serverplayer = $args['serverplayer'];
			$this->cityplayer = $args['cityplayer'];
		} else {
			$this->profile = $args['profile'];
			$this->total = $args['total'];
		}
		
		parent::show($args);	
	}
	
	function metaKeywords() {
		return '游戏酒馆，GamePub，玩家交友，游戏，精彩';
	}

	function metaDescription() {
		return '在这里一定能寻找到您的游戏朋友。';
	}
	

    function title()
    {
       	return common_config('site', 'name') . '玩家交友';
    }
    
    function showContent()
    {   
    	$this->elementStart('h2');
    	$this->text('玩家交友');
    	$this->element('span', null, '-- 游戏之乐在于朋友，找个朋友一起玩吧！');
        $this->elementEnd('h2');

        $this->showGameBoardWidget($this->cur_user,$this->cur_game);
        
        $this->showSearchForm();
        
       	if ($this->cur_profile) {
       		//推荐本游戏玩家, 同服务器玩家, 同城玩家
       		if($this->cur_profile->sex == 'M') {
       			$opp_sex = 'F';
	    		$opp_sex_player = '美女玩家推荐';
       		}
	    	else {
	    		$opp_sex = 'M';
	    		$opp_sex_player = '帅哥玩家推荐';
	    	}
	    	if ($this->gameplayer->N > 0) {
	    		$this->_showGamePlayerList($this->gameplayer, 
	    									$opp_sex_player,
	    									common_local_url('gamefriends', array('gameid' => $this->cur_game->id), array('sex' => $opp_sex)));   
	    	}
	    	if ($this->cur_game->id == $this->cur_user->game_id && $this->serverplayer->N > 0) {
	    		$this->_showGamePlayerList($this->serverplayer, 
	    									$this->cur_user->getGameServer()->name . $opp_sex_player,
	    									common_local_url('gamefriends', array('gameid' => $this->cur_game->id), array('sex' => $opp_sex, 'game_server' => $this->cur_user->game_server_id)));
	    	}
	    	if ($this->cityplayer->N > 0) {
	    		$this->_showGamePlayerList($this->cityplayer, 
	    									$this->cur_profile->province . $this->cur_profile->city . $opp_sex_player,
	    									common_local_url('gamefriends', array('gameid' => $this->cur_game->id), array('sex' => $opp_sex, 'loc' => $this->cur_profile->province . $this->cur_profile->city)));
	    	}
       	} else {
       		//搜索结果
       		$pl = new PeopleSearchList($this, $this->profile, null, $this->cur_user);
	        $cnt = $pl->show();
	        if ($cnt == 0) {
	        	$this->showEmptyList();
	        } else {
	        	$this->numpagination($this->total);
	        }
       	}
    }
    
	function _showGameChoice() {
		$list_games = Game::listAll();
		$this->elementStart('select',array('class' => 'choosegame', 'name' => 'game'));
		foreach ($list_games as $g) {	
			if ($g['id'] == $this->game_id) {
        		$this->element('option', array('value' => $g['id'], 'selected' => 'selected'), $g['name']);
			} else {
				$this->element('option', array('value' => $g['id']), $g['name']);
			}
		}
		$this->elementEnd('select');
		
		$list_bigzones = Game::staticGet('id', $this->game_id)->getBigZones();
		$this->elementStart('select', array('class' => 'choosebigzone', 'name' => 'game_big_zone'));
		$this->element('option', array('value' => '0'), '不限大区');
		foreach ($list_bigzones as $gbz) {	
			if ($this->game_big_zone_id && $gbz['id'] == $this->game_big_zone_id) {
        		$this->element('option', array('value' => $gbz['id'], 'selected' => 'selected'), $gbz['name']);
			} else {
				$this->element('option', array('value' => $gbz['id']), $gbz['name']);
			}
		}
		$this->elementEnd('select');
		
		if ($this->game_big_zone_id) {
        	$list_servers = Game::getServers($this->game_big_zone_id);
        } else {
        	$list_servers = array();
        }
		$this->elementStart('select', array('class' => 'chooseserver', 'name' => 'game_server'));
		$this->element('option', array('value' => '0'), '不限服务器');
		foreach ($list_servers as $server) {	
			if ($this->game_server_id && $server['id'] == $this->game_server_id) {
        		$this->element('option', array('value' => $server['id'], 'selected' => 'selected'), $server['name']);
			} else {
				$this->element('option', array('value' => $server['id']), $server['name']);
			}
		}
		$this->elementEnd('select');
	}
	
    function showSearchForm()
    {
    	//不用验证, 直接查询, 加上一些脚本
    	$this->tu->startFormBlock(array('method' => 'post',
                                          		'id' => 'game_friends_form',
                                          		'class' => 'mfsearch',
                                          		'action' => common_local_url('gamefriends', array('gameid' => $this->cur_game->id))), 
                                          '搜索玩家');
        $this->elementStart('dl');
       	$this->elementStart('dt');
        $this->text('您的搜索条件');
        $this->element('a', array('href' => common_local_url('peoplesearch')), '高级搜索');
        $this->elementEnd('dt');
        
        $this->elementStart('dd');
        $this->elementStart('p');
        $this->element('label', array('for' => 'sex'), '我要找：');
        $this->elementStart('select', array('name' => 'sex'));
        $this->option('0', '所有玩家', $this->sex);
    	$this->option('M', '男玩家', $this->sex);
    	$this->option('F', '女玩家', $this->sex);
        $this->elementEnd('select');
        $this->element('label', 'hm', '年龄：');
        $this->elementStart('select', array('name' => 'agebegin', 'class' => 'age'));
        $this->option('10', '10', $this->agebegin);
        $this->option('20', '20', $this->agebegin);
        $this->option('30', '30', $this->agebegin);
        $this->option('40', '40', $this->agebegin);
        $this->option('50', '50', $this->agebegin);
        $this->elementEnd('select');
        $this->element('span', null, '到');
        $this->elementStart('select', array('name' => 'ageend', 'class' => 'age'));
        $this->option('20', '20', $this->ageend);
        $this->option('30', '30', $this->ageend);
        $this->option('40', '40', $this->ageend);
        $this->option('50', '50', $this->ageend);
        $this->option('60', '60', $this->ageend);        
        $this->elementEnd('select');
        $this->element('label', array('class' => 'hms', 'for' => 'loc'), '所在地：');
        $this->element('input', array('id' => 'loc', 'name' => 'loc', 'class' => 'text', 'type' => 'text', 'value' => $this->loc));
        $this->elementEnd('p');
        $this->elementStart('div','buttwrap');
        $this->element('input', array('type' => 'hidden', 'value' => $this->cur_page, 'name' => 'page'));
        $this->element('input', array('id' => 'acpro_inp4', 'class' => 'submit orange94 button94', 'type' => 'submit', 'value' => '开始搜索'));
        $this->elementEnd('div');
       
        $this->elementStart('p');
        $this->_showGameChoice();
        $this->elementEnd('p');
        
        $this->elementEnd('dd'); 
        $this->elementEnd('dl');
        $this->tu->endFormBlock();
    }
    
	function showEmptyList()
    {
        $emptymsg = array();
        $emptymsg['dt'] = '没有找到符合条件的玩家。';
        $this->tu->showEmptyListBlock($emptymsg);
    }
    
	function numpagination($totalnum, $displayPerPage = PROFILES_PER_PAGE)
    {
    	$have_before = $this->cur_page > 1;
    	$have_after = ($this->cur_page * $displayPerPage) < $totalnum;
    	
    	if($have_before || $have_after) {
	    	$this->elementStart('ol', array('id' => 'pagination'));
	    	
	    	$pages = floor(($totalnum - 1 + $displayPerPage) / $displayPerPage);
	    	
	    	$start = (floor(($this->cur_page-1)/10))*10 + 1;
	    	if ($start > 10) {
	    		$this->elementStart('li');
	    			$this->element('a', array('href' => '#','page' => $start - 10, 
	    				'title' => '前十页', 'rel' => 'nofollow'), '上');
	    		$this->elementEnd('li');
	    	}
	    	
	    	$pp = $start;
	    	do {
	    		if($pp != $this->cur_page){
		    		$this->elementStart('li');	
		    			$this->element('a', array('href' =>'#','page' => $pp, 'rel' => 'nofollow'), $pp);
		    		$this->elementEnd('li');
	    		} else {
	    			$this->elementStart('li', 'active');
	    			$this->element('span', null, $pp);
	    			$this->elementEnd('li');
	    		}
	    		$pp++;
	    	} while ($pp <= $pages && $pp < $start + 10);
	    	
	    	// when page num is more than current displayed, show more...
	    	if ($pages > $start + 9) {
	    		$this->elementStart('li');
	    			$this->element('a', array('title' => '后十页',
		    			'href' => '#','page' => $start + 10, 'rel' => 'nofollow'), '下');
	    		$this->elementEnd('li');
	    	}
	    	
	    	$this->elementEnd('ol');
    	}
    }

	function showScripts() {
		parent::showScripts();
		$this->script('js/lshai_relation.js');
		$this->script('js/lshai_gamefriend.js');
		$this->script('js/lshai_gameselect.js');
	}
	
    function showStylesheets() {
    	parent::showStylesheets();
    	$this->cssLink('css/settings.css','default','screen, projection');
    }
    
	function _showGamePlayerList($profile, $title="", $url="#", $id=false) {
    	$this->elementStart('dl', 'mfresult');
		$this->elementStart('dt');
		$this->text($title);
		if ($url != '#') {
			$this->element('a', array('class' => 'more', 'href' => $url, 'title' => '查看更多的' . common_config('site', 'name') . '用户'), '更多');
		}
		$this->elementEnd('dt');
		
		$this->elementStart('dd');		
		$this->elementStart('ul', 'clearfix');
    	$cnt = 0;
		while ($profile && $profile->fetch()) {
			$cnt ++;
        	$avatar = $profile->getAvatar(AVATAR_PROFILE_SIZE);
			$this->elementStart('li', array('pid' => $profile->id));
			
			$this->elementStart('div', 'avatar');
			$this->elementStart('a', array('href' => common_path($profile->uname), 'title' => $profile->nickname));
        	$this->element('img', array('height' => '120', 'width' => '120',
		    		'src' => ($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_PROFILE_SIZE, $profile->id, $profile->sex), 
		    		'alt' => $profile->nickname)); 
        	$this->elementEnd('a');   
        	$this->elementEnd('div');  
			
        	$this->elementStart('div', 'op clearfix');
        	$this->element('a', array('href' => common_path($profile->uname),
        		 'title' => $profile->nickname, 'class' => 'nickname'), $profile->nickname);
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
	    			$this->element('div', array('class' => 'subscribed', 'title' => '已关注'));
	    		}
    		} else {
    			$this->element('a', array('class' => 'subscribe trylogin', 
    				'href' => common_path('register?ivid=' . $profile->id), 'title' => '在' . common_config('site', 'name') . '上关注' . Profile::displayName($profile->sex, false), 'rel' => 'nofollow'), '');
    		}
        	$this->element('a', array('href' => common_path($profile->uname),
        		 'title' => '查看主页', 'class' => 'showdetail'), '查看主页');
        	$this->elementEnd('div');  
        	$this->element('p', null, '游戏职业：' . $profile->game_job); 
        	
			$this->elementEnd('li');
			
		}
		
    	$this->elementEnd('ul');
    	$this->element('div', 'mfresult_foot');
    	$this->elementEnd('dd');
		$this->elementEnd('dl');
    	
    }
}

class PeopleSearchList extends ProfileList
{
    var $terms = null;
    var $pattern = null;
    
    function __construct($out, $profiles, $owner, $cur_user) {
    	parent::__construct($out, $profiles, $owner, $cur_user);
    }
    
	function showNickname($profile) {
    	$this->out->elementStart('p', 'nickname');
    	$this->out->elementStart('strong');
    	$this->out->elementStart('a', array('href' => $profile->profileurl, 'title' => '访问' . $profile->nickname . '的主页'));
    	$this->out->raw($profile->nickname);
    	$this->out->elementEnd('a');
    	$this->out->elementEnd('strong');
    	$this->out->element('span', null, $profile->sex == 'M' ? '男' : '女');
    	$this->out->element('span', null, $profile->followers . '人关注');
    	$this->out->elementEnd('p');
    }
    
	function showInfos($profile) {
    	$game = Game::staticGet('id', $profile->game_id);
    	$game_server = Game_server::staticGet('id', $profile->game_server_id);
    	$this->out->elementStart('p');
    	$this->out->raw('游戏：' . $game->name . ' - ' . $game_server->name);
    	$this->out->elementEnd('p');
    	
    	$this->out->elementStart('p');
    	$this->out->raw('所在地: ' . $profile->location);
    	$this->out->elementEnd('p');
    }

}