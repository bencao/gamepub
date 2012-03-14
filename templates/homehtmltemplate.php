<?php
/**
 * Shaishai, the distributed microblog
 *
 * Show user all notices
 *
 * PHP version 5
 *
 * @category  Login
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Show user all notices
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

require_once INSTALLDIR.'/lib/noticelist.php';
require_once INSTALLDIR.'/lib/noticeunreadlist.php';

//默认只有自己才能访问这个页面
class HomeHTMLTemplate extends PersonalHTMLTemplate
{
    
	function extraHeaders() {
		setcookie('o', 'y', time()+60*60*24*30, '/');
	}
	
	function title()
    {
        return "我在" . common_config('site', 'name') . "的空间";
    }
    
	function showScripts()
    {   
    	parent::showScripts();
    	
    	$this->script('js/lshai_interval.js');
    	
    	if ($this->trimmed('wizard', false)) {
        	$this->script('js/lshai_guide.js');
        }
        Event::handle('EndHomeScripts', array($this));
    }
    
	function showStylesheets()
    {
    	parent::showStylesheets();
    	if ($this->trimmed('wizard', false)) {
        	$this->cssLink('css/guide.css', 'default');
        }
        Event::handle('EndHomeStylesheets', array($this));
    }
    
    function showContentInfo()
    {
    	$profile = $this->page_owner->getProfile();
    	
    	// 一级时要求做任务
    	if ($profile->getUserGrade() == 1) {
    		$this->_showMission($profile);
    	}
    	
    	if ($this->trimmed('wizard', false)) {
    		$this->elementStart('div');
    	}

    	if ($this->args['gtag']) {
    		$arguments = array('gtag' => $this->args['gtag']);
    	} else {
    		$arguments = array();
    	}
    	$tag = $this->args['tag'];
    	$filter_content = $this->args['filter_content'];
    	
        $this->tu->showNewContentFilterBoxBlock($profile, $filter_content, $tag, 'home', $arguments); 
        
       	$this->element('input', array('class' => 'latestId', 'type' => 'hidden', 'value' => $this->args['latest_notice_id']));

		$cnt = $this->showNoticeList($this->args['notice'], $this);
		if ($cnt > NOTICES_PER_PAGE) {
			$params = array();
		    if ($tag) {
		    	$params = array_merge($params, array('tag' => $tag));
		    }
		    if ($filter_content) {
		    	$params = array_merge($params, array('filter_content' => $filter_content));
		    }
		    $this->morepagination($this, $cnt > NOTICES_PER_PAGE, $this->cur_page, 'home', $arguments, $params);
		}
        
        if ($this->trimmed('wizard', false)) {
        	$this->elementEnd('div');
        }
        
        $this->tu->showMakeYourTheme($this->cur_user);
        
        if ($this->trimmed('wizard', false)) {
        	$this->_showGuideContent();
        }
        
        Event::handle('EndHomeContentInfo', array($this, $this->cur_user));
   }
   
   function _showGuideContent() {
   		$this->elementStart('div', array('id' => 'gsteps', 'style' => 'display:none;', 'class' => 'rounded8'));
   		
   		$this->elementStart('div', array('id' => 'gstep1', 'class' => 'step', 'style' => 'display:none;'));
    	$this->element('h2', null, '欢迎来到GamePub，1分钟时间熟悉您的新家');
    	$this->element('h3', null, '您的个人空间可以分为两部分：');
    	$this->elementStart('ol', 'content');
    	$this->elementStart('li');
    	$this->raw('<strong>左侧</strong>是消息区，提供了您日常的<strong>看消息</strong>和<strong>发消息</strong>所需的所有功能。您所关注的人的最新消息会在这里显示，您可以简单快捷地了解他们的动态。');
    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->raw('<strong>右侧</strong>是导航区，提供了您的<strong>影响力数据</strong>和最常用的<strong>导航链接</strong>，方便您更好的玩转GamePub。');
    	$this->elementEnd('li');
    	$this->elementEnd('ol');
    	$this->element('span', 'pg', '1/5');
    	$this->element('a', array('href' => '#', 'class' => 'button60 silver60 next', 'step' => '1'), '下一步');
    	$this->elementEnd('div');
    	
    	$this->elementStart('div', array('id' => 'gstep2', 'class' => 'step', 'style' => 'display:none;'));
    	$this->element('h2', null, '发消息 -- 与朋友分享之窗');
    	$this->element('h3', null, '您发的消息可以很丰富，很有意思：');
    	$this->elementStart('ol', 'content');
    	$this->elementStart('li');
    	$this->raw('为消息增加<strong>话题</strong>属性，如“心情”-“开心”，让朋友们可以更清楚的了解你要表达的意思，让不认识的游友用话题找到你。');
    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->raw('想给消息配张<strong>图片</strong>？加一段<strong>音乐</strong>？附一个<strong>视频</strong>？两次点击全部搞定！升到二级更可以加入个性<strong>表情</strong>！');
    	$this->elementEnd('li');
    	$this->elementEnd('ol');
    	$this->element('span', 'pg', '2/5');
    	$this->element('a', array('href' => '#', 'class' => 'prev', 'step' => '2'), '上一步');
    	$this->element('a', array('href' => '#', 'class' => 'button60 silver60 next', 'step' => '2'), '下一步');
    	$this->element('span', 'up pointer');
    	$this->elementEnd('div');
    	
    	$this->elementStart('div', array('id' => 'gstep3', 'class' => 'step', 'style' => 'display:none;'));
    	$this->element('h2', null, '看消息 -- 探寻世界之窗');
    	$this->element('h3', null, '您想看什么，就让这里显示什么：');
    	$this->elementStart('ol', 'content');
    	$this->elementStart('li');
    	$this->raw('按您<strong>喜爱的展现方式</strong>来看消息，可以只看图片消息，可以只“听”音乐消息，也可以只看视频消息。');
    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->raw('按您<strong>喜爱的话题</strong>来看消息，可以只看职业攻略，可以只看好友心情，也可以只看游戏新闻等。');
    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->raw('想跟TA互动，想参与讨论，想发表意见？不需要任何学习，您已经拥有评论、转载、收藏、回复<strong>四大技能</strong>。');
    	$this->elementEnd('li');
    	$this->elementEnd('ol');
    	$this->element('span', 'pg', '3/5');
    	$this->element('a', array('href' => '#', 'class' => 'prev', 'step' => '3'), '上一步');
    	$this->element('a', array('href' => '#', 'class' => 'button60 silver60 next', 'step' => '3'), '下一步');
    	$this->element('span', 'down pointer');
    	$this->elementEnd('div');
    	
    	$this->elementStart('div', array('id' => 'gstep4', 'class' => 'step', 'style' => 'display:none;'));
    	$this->element('h2', null, '查状态 - 旅程的仪表盘');
    	$this->element('h3', null, '您的影响力和智慧尽显于此：');
    	$this->elementStart('ol', 'content');
    	$this->elementStart('li');
    	$this->raw('随时关注您的<strong>等级、财富、关注者数量</strong>，您有潜力登上排行榜！');
    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->raw('巧妙的<strong>设置分组</strong>，管理您关注的人；小技巧：点击分组名字就可以看到该分组用户所发的最新消息。');
    	$this->elementEnd('li');
    	$this->elementEnd('ol');
    	$this->element('span', 'pg', '4/5');
    	$this->element('a', array('href' => '#', 'class' => 'prev', 'step' => '4'), '上一步');
    	$this->element('a', array('href' => '#', 'class' => 'button60 silver60 next', 'step' => '4'), '下一步');
    	$this->element('span', 'right pointer');
    	$this->elementEnd('div');
    	
    	$this->elementStart('div', array('id' => 'gstep5', 'class' => 'step', 'style' => 'display:none;'));
    	$this->element('h2', null, '长见识 -- 搜寻有趣' . GROUP_NAME() . '，关注同游戏玩家');
    	$this->element('h3', null, '您所在的小世界，就在这里：');
    	$this->elementStart('ol', 'content');
    	$this->elementStart('li');
    	$this->raw('进行<strong>' . GROUP_NAME() . '管理</strong>，参与<strong>' . GROUP_NAME() . '讨论</strong>，接收' . GROUP_NAME() . '最新消息。');
    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->raw('<strong>“' . $this->cur_user->getGame()->name . '”</strong>玩家齐聚一堂，<strong>同游戏</strong>或者<strong>同服务器</strong>玩家在此尽情互动。');
    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->raw('我们还为您准备了介绍视频，<a href="' . common_local_url('showtime') . '" target="_blank">点击观看</a>。');
    	$this->elementEnd('li');
    	$this->elementEnd('ol');
    	$this->element('span', 'pg', '5/5');
    	$this->element('a', array('href' => '#', 'class' => 'prev', 'step' => '5'), '上一步');
    	$this->element('a', array('href' => common_local_url('home'), 'class' => 'button60 silver60 next', 'step' => '5'), '完成');
    	$this->element('span', 'up pointer');
    	$this->elementEnd('div');
    	
   		$this->elementEnd('div');
   }
    
   function ajaxShowNoticeSinceId($args) {
    	$this->args = $args;
       	
//    	$this->startHTML('text/xml;charset=utf-8');
        
    	$view = TemplateFactory::get('JsonTemplate');
        $view->init_document();
        
    	
        $notice = $this->args['notice'];
        if ($notice->N > 0) {
       		$xs = new XMLStringer();
        	$cnt = $this->showNoticeList($notice, $xs);
        	$resultArray = array('result' => 'true', 'html' => $xs->getString()); 	
        } else {
        	$resultArray = array('result' => 'false');
        }
       	      	 	       	 	
        $view->show_json_objects($resultArray);
        $view->end_document();
       	
//	    $this->endHTML();
    }
    
    //加上tag
    function ajaxShowPageNotices($args) {
    	$this->args = $args;
    	$this->cur_page = $this->args['page'];
    	
    	$view = TemplateFactory::get('JsonTemplate');
        $view->init_document();
        
    	
        $notice = $this->args['notice'];
        if ($notice->N > 0) {
       		$xs = new XMLStringer();
        	$cnt = $this->showNoticeList($notice, $xs);
        	
        	$xs1 = new XMLStringer();
       		if ($cnt > NOTICES_PER_PAGE) {
		       	if ($this->args['gtag']) {
		    		$arguments = array('gtag' => $this->args['gtag']);
		    	} else {
		    		$arguments = array();
		    	}
		    	
	       		$tag = $this->args['tag'];
	       		$filter_content = $this->args['filter_content'];
		    	$params = array();
		    	if ($tag) {
		    		$params = array_merge($params, array('tag' => $tag));
		    	}
		    	if ($filter_content) {
		    		$params = array_merge($params, array('filter_content' => $filter_content));
		    	}

		    	$this->morepagination($xs1, $cnt > NOTICES_PER_PAGE, $this->cur_page, 'home', $arguments, $params);
        	}
	       	
        	$resultArray = array('result' => 'true', 'notices' => $xs->getString(), 'pg' => $xs1->getString()); 	
        } else {
        	$resultArray = array('result' => 'false');
        }
       	      	 	       	 	
        $view->show_json_objects($resultArray);
        $view->end_document();
    }
    
	function showEmptyList()
    {   
        $message = '这是您所关注的消息列表 , 但是还没人发消息。';
        
        $emptymsg = array();
        $emptymsg['p'] = $message;
        $emptymsg['p'] = '与朋友分享游戏中的心得体会，快乐悲伤。'.
                          '同时关注更多的您感兴趣的人，您会发现每天上' . common_config('site', 'name') . '是非常有意义的事！';
        $this->tu->showEmptyListBlock($emptymsg);
    }
    
	function showRightSection($templateutil, $page_owner_profile) {
    	 //不是tag, 要新定义参数 
    	$this->tu->showTagNavigationWidget($this->cur_user, $this->trimmed('action'), $this->trimmed('gtag'));
    }
    
	function showRightsidebar() {
    	$page_owner_profile = $this->page_owner->getProfile();
    	
		if ($this->trimmed('wizard', false)) {
        	$this->elementStart('div');
        }
        
    	$this->tu->showOwnerInfoWidget($page_owner_profile);
    	$this->tu->showSubInfoWidget($page_owner_profile, $this->is_own);
    	$this->tu->showToolbarWidget($page_owner_profile);
    	$this->showRightSection($this->tu, $page_owner_profile);
    	
		if ($this->trimmed('wizard', false)) {
        	$this->elementEnd('div');
        }
        
    	$this->tu->showGroupsWidget($page_owner_profile, 18);
    	$subscriptions = $page_owner_profile->getSubscriptions(0, 18);
    	$this->tu->showUserListWidget($subscriptions, '我关注的人');
    	$this->tu->showInviteWidget();
    	
    	$this->tu->showTagcloudWidget();
    }
    
    function showNoticeList($notice, $out) {
    	$nl = new HomeNoticeList($notice, $out, common_current_user());
        $cnt = $nl->show();
        return $cnt;
    }
    
	function _showMission($profile)
    {
    	$this->elementStart('div', array('class' => 'mission'));
    	
    	$this->elementStart('div', array('class' => 'avatar'));
    	$avatar = $profile->getAvatar(AVATAR_PROFILE_SIZE, AVATAR_PROFILE_SIZE);
    	$this->element('img', array('src' => ($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_PROFILE_SIZE, $profile->id, $profile->sex),
                                         'class' => 'rounded5',
                                         'alt' => $profile->uname));
    	$this->elementEnd('div');
    	
    	$this->elementStart('p', array('class' => 'intro'));
    	$this->element('strong', null, $profile->nickname);
    	//内容
    	$this->text('添加头像 + 完善资料 = 升至二级');
    	$this->element('a', array('href' => common_path('doc/help/usergrade'), 'class' => 'help', 'title' => '二级用户可以在消息中插入表情，还可以建立一个自己的生活' . GROUP_NAME()), ' ');
    	$this->elementEnd('p');
    	
    	$gradeinfo = $profile->getUserUpgradePercent();    	
    	$this->elementStart('p', array('class' => 'level'));
    	$this->element('strong', null, '等级：');
    	//内容
    	
    	$this->text($gradeinfo['grade'] . '级');
    	$this->elementStart('span', array('class' => 'progress'));
    	//改动
    	$this->element('em', array('class' => 'bar', 'style' =>'width: ' . $gradeinfo['percent'] . '%', 'title' => '1'),  ' ');
    	//改动
//    	$this->element('em', array('class' => 'text'),  $gradeinfo['score'] . '/' . $gradeinfo['nextScore']);
    	$this->elementEnd('span');
    	$this->elementEnd('p');
    	
    	$this->elementStart('p', array('class' => 'point'));
    	$this->element('strong', null, '财富：');
    	//内容
    	$wealth = $profile->getUserScoreDetail();
    	$this->elementStart('span');
    	$this->element('em', array('class' => 'gold', 'title' => '金G币'), $wealth['gold']);
    	$this->element('em', array('class' => 'silver', 'title' => '银G币'), $wealth['silver']);
    	$this->element('em', array('class' => 'bronze', 'title' => '铜G币'), $wealth['bronze']);
    	$this->elementEnd('span');
    	
//    	$this->text($gradeinfo['score'] . ' / ' . $gradeinfo['nextScore']);
    	$this->elementEnd('p');
    	
    	$this->elementStart('p', array('class' => 'completeness'));
    	$this->element('strong', null, '资料：');
    	$this->text($profile->completeness . '%');
    	$this->elementStart('span', array('class' => 'progress'));
    	$this->element('em', array('class' => 'bar', 'style' =>'width: ' . $profile->completeness . '%;'),  ' ');
    	//改动
//    	$this->element('em', array('class' => 'text'),  $profile->completeness . '%');
    	$this->elementEnd('span');
    	$this->elementEnd('p');
    	
    	$this->element('a', array('href' => common_path('main/missionstep1'), 'class' => 'start'), '开始任务>>');
    	
    	$this->elementEnd('div');
    }

}

class HomeNoticeList extends NoticeList
{
	var $owner;
	
	function __construct($notice, $out, $owner)
    {
        parent::__construct($notice, $out);
        $this->owner = $owner;
    }
    
	function newListItem($notice)
    {
        return new HomeNoticeListItem($notice, $this->out, $this->owner);
    }
}

class HomeNoticeListItem extends NoticeListItem
{
	var $owner;
	
	function __construct($notice, $out, $owner)
    {
        parent::__construct($notice, $out);
        $this->owner = $owner;
    }
    
    function showNickname() {
    	$this->out->elementStart('h3'); 
        $this->out->element('a', array('href' => common_local_url('showstream', array('uname' => $this->profile->uname)),
        			'class' => 'name', 'title' => '去' . $this->profile->nickname . '在' . common_config('site', 'name') . '的主页看看'), $this->profile->nickname);
        if ($this->profile->is_vip) {
        	$this->out->element('strong', null, 'V');
        }
        
        // different game
        if ($this->profile->game_id != $this->owner->game_id) {
	        $game_server = Game_server::staticGet('id', $this->profile->game_server_id);
	    	$game = Game::staticGet('id', $this->profile->game_id);    	
	    	$text = sprintf('<span class="tag"><a rel="tag" target="_blank" title="去看看%s的最新动态" href="%s">%s</a>- %s</span>',
    			$game->name, common_local_url('recentnews', array('gameid' => $game->id)), $game->name, $game_server->name);
	    				
	    	$this->out->raw($text);
        }
    	
        $this->out->elementEnd('h3');
    }
}