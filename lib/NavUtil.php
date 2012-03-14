<?php

if (!defined('SHAISHAI')) {
    exit(1);
}


class NavListElement {
	var $href;
	var $text;
	var $action_name;
	
	function __construct($action_name, $href, $text) {
		$this->action_name = $action_name;
		$this->href = $href;
		$this->text = $text;
	}
}

abstract class NavList {
	var $elements = array();
	
	function __construct() {
		$this->init();
	}
	
	abstract function init();
	
	function addElement($action_name, $href, $text) {
		$this->elements[] = new NavListElement($action_name, $href, $text);
	}
	
	function lists() {
		return $this->elements;
	}
}

class NavList_Settings extends NavList {
	var $user;
	
	function __construct($user) {
		$this->user = $user;
		$this->init();
	}
	
	function init() {
		$this->addElement('profilesettings', common_path('settings/profile'), '个人设置');
		$this->addElement('gamesettings', common_path('settings/game'), '游戏设置');
		$this->addElement('passwordsettings', common_path('settings/password'), '密码设置');
		$this->addElement('emailsettings', common_path('settings/email'), '邮件设置');
		$this->addElement('interestsettings', common_path('settings/interest'), '兴趣设置');
		$this->addElement('avatarsettings', common_path('settings/avatar'), '头像设置');
		$this->addElement('feedsettings', common_path('settings/feed'), '导入设置');
	}
}

class NavList_Home extends NavList {
	
	var $user;
	var $from_action;
	var $param1;
	var $param2;
	
	function __construct($user, $from_action, $param1 = array(), $param2 = array()) {
		$this->user = $user;
		$this->from_action = $from_action;
		$this->param1 = $param1;
		$this->param2 = $param2;
		$this->init();
	}
	
	function init() {
		
		// 添加默认的分组“全部”
		$this->addElement($this->from_action, common_local_url($this->from_action, $this->param1, $this->param2), '全部');
		
		$tags = $this->user->getAllTags();
		
		foreach ($tags as $t) {
			$this->addElement($this->from_action, 
				common_local_url($this->from_action, $this->param1, array_merge($this->param2, array('gtag' => $t->tag))), $t->tag);
		}
		
		$this->addElement($this->from_action, common_local_url($this->from_action, $this->param1, array_merge($this->param2, array('gtag' => '未分组'))), '未分组');
	}
}

class NavList_PersonalMessages extends NavList {
	
	var $profile;
	
	function __construct($profile) {
		$this->profile = $profile;
		$this->init();
	}
	
	function init() {
		$this->addElement('inbox', common_path($this->profile->uname . '/inbox'), '收件箱');
		$this->addElement('outbox', common_path($this->profile->uname . '/outbox'), '发件箱');
		$this->addElement('sysmessage', common_path('main/sysmessage'), '系统通知');
	}
}

class NavList_MyNotices extends NavList {
	
	var $profile;
	
	function __construct($profile) {
		$this->profile = $profile;
		$this->init();
	}
	
	function init() {
		$this->addElement('showstream', common_path($this->profile->uname), '我的消息');
        $this->addElement('showretweet', common_path($this->profile->uname . '/showretweet'), '我的转载');
	}
}

class NavList_Help extends NavList {
	function init() {
		$this->addElement('help', common_path('doc/help/modules'), '平台使用帮助');
		$this->addElement('hooyouhelp', common_path('doc/hooyouhelp/hooyouparts'), '呼游桌面端帮助');
		$this->addElement('skills', common_path('doc/skills/usage'), '使用技巧');
	}
}

class NavList_HelpDetail extends NavList {
	function init() {
		$this->addElement('register', common_path('doc/help/register'), '登录与注册');
		$this->addElement('sentnotice', common_path('doc/help/sentnotice'), '发送消息');
		$this->addElement('shownotice', common_path('doc/help/shownotice'), '查看消息');
		$this->addElement('settings', common_path('doc/help/settings'), '设置');
		$this->addElement('invite', common_path('doc/help/invite'), '邀请好友');
		$this->addElement('relation', common_path('doc/help/relation'), '关系管理');
		$this->addElement('groups', common_path('doc/help/groups'), '群组');
		$this->addElement('gameregion', common_path('doc/help/gameregion'), '游戏版块');
		$this->addElement('integratedregion', common_path('doc/help/integratedregion'), '酒馆地带');
		$this->addElement('search', common_path('doc/help/search'), '搜索');
		$this->addElement('retweetandreply', common_path('doc/help/retweetandreply'), '收藏转发与回复');
		$this->addElement('personalmessage', common_path('doc/help/personalmessage'), '私信与通知');
		$this->addElement('usergrade', common_path('doc/help/usergrade'), '等级、财富系统');
		$this->addElement('link', common_path('doc/help/link'), '更多玩法');
	}
}

class NavList_Info extends NavList {
	function init() {
		$this->addElement('about', common_path('doc/info/about'), '什么是' . common_config('site', 'name'));
		$this->addElement('why', common_path('doc/info/why'), '为什么要使用' . common_config('site', 'name'));
		$this->addElement('ourteam', common_path('doc/info/ourteam'), '我们的团队');
		$this->addElement('contact', common_path('doc/info/contact'), '联系我们');
		$this->addElement('joinus', common_path('doc/info/joinus'), '加入我们');
		$this->addElement('friendlinks', common_path('doc/info/friendlinks'), '友情链接');
	}
}

class NavList_Statement extends NavList {
	function init() {
		$this->addElement('tos', common_path('doc/statement/tos'), '服务协议');
		$this->addElement('privacy', common_path('doc/statement/privacy'), '隐私声明');
	}
}

// right navigation in visitor's view
class NavList_Visitor extends NavList {
	var $profile;
	var $is_own;
	
	function __construct($profile, $is_own) {
		$this->profile = $profile;
		$this->is_own = $is_own;
		$this->init();
	}
	
	function init() {
		if ($this->is_own) {
			$sex = '我';
		} else {
			if (!$this->profile->sex) {
				$sex = 'TA';
			} else if($this->profile->sex == 'M') {
				$sex = '他';
			} else {
				$sex = '她';
			}
		}
	    $this->addElement('showstream', common_path($this->profile->uname), $sex.'的消息');
		$this->addElement('showall', common_path($this->profile->uname . '/showall'), $sex.'的视角');
		//$this->addElement('showstream', common_local_url('showstream', array('uname' =>
        //                                          $this->profile->uname)), $this->profile->nickname.'的消息');
//        $this->addElement('showreplies', common_local_url('showreplies', array('uname' =>
//                                                  $this->profile->uname)), $sex.'的回复');
        $this->addElement('checkfavorites', common_path($this->profile->uname . '/checkfavorites'), $sex.'的收藏');
	}
}

class NavList_Relation extends NavList {
	
	var $profile;
	var $is_own;
	
	function __construct($profile, $is_own = true) {
		$this->profile = $profile;
		$this->is_own = $is_own;
		$this->init();
	}
	
	function init() {
		if ($this->is_own) {
			$this->addElement('subscriptions', common_path($this->profile->uname . '/subscriptions'), '我关注的人');
			$this->addElement('subscribers', common_path($this->profile->uname . '/subscribers'), '关注我的人');
			$this->addElement('blacklist', common_path($this->profile->uname . '/blacklist'), '我的黑名单');
			$this->addElement('invite', common_path('main/invite'), '邀请好友');
			$this->addElement('peoplesearch', common_path('search/people'), '查找好友');
		} else {
			$this->addElement('subscriptions', common_path($this->profile->uname . '/subscriptions'), ($this->profile->sex == 'M' ? '他' : '她') . '关注的人');
			$this->addElement('subscribers', common_path($this->profile->uname . '/subscribers'), '关注' . ($this->profile->sex == 'M' ? '他' : '她') . '的人');
		}
	}
}

class LoginNavListElement extends NavListElement {
	
	// new的链接可以加一个闪动的特效引起用户注意
	var $is_new;
	var $need_login;
	
	function __construct($action_name, $href, $text, $is_new = false, $need_login = false) {
		parent::__construct($action_name, $href, $text);
		$this->is_new = $is_new;
		$this->need_login = $need_login;
	}
}

abstract class LoginNavList extends NavList {
	function addElement($action_name, $href, $text, $is_new = false, $need_login  = false) {
		$this->elements[] = new LoginNavListElement($action_name, $href, $text, $is_new, $need_login);
	}
}

class NavList_Public extends LoginNavList {
	function init() {
		$this->addElement('public', common_path('public'), '酒馆动态', false, false);
		$this->addElement('wenda', common_path('wenda'), '酒馆问答', true, false);
		$this->addElement('flashgame', common_path('flash/list'), '小游戏', true, false);
		$this->addElement('halloffame', common_path('halloffame'), '名人堂', false, false);
		$this->addElement('rank', common_path('rank'), '风云榜', false, true);
		
		// 火爆话题不是强项，不做明显导航。通过登录页上的“正在讨论”可以进入。
//		$this->addElement('hottopics', common_local_url('hottopics'), '火爆话题', false, false);
		// 有趣人太少，暂时屏蔽
//		$this->addElement('funnypeople', common_local_url('funnypeople'), '有趣游友', false, true);
		$this->addElement('hotnotice', common_path('hotnotice'), '热门消息', false, true);
		$this->addElement('citypeople', common_path('citypeople'), '同城游友', false, true);
	}
}

class NavList_Game extends LoginNavList {
	var $game;
	
	function __construct($game) {
		$this->game = $game;
		$this->init();
	}
	
	function init() {
		$this->addElement('recentnews', common_path('game/' . $this->game->id), '游戏动态', false, true);
		$this->addElement('gamefriends', common_path('game/' . $this->game->id . '/friends'), '玩家交友', false, true);
		$this->addElement('gamedeal', common_path('game/' . $this->game->id . '/deal'), '游戏交易', false, true);
		$this->addElement('gameexperiences', common_path('game/' . $this->game->id . '/experiences'), '游戏经验', false, true);
		$this->addElement('gamewebnav', common_path('game/' . $this->game->id . '/webnav'), '游戏资源', false, true);
	}
}

class NavList_Group extends LoginNavList {
	function init() {
		$this->addElement('groups', common_path('groups'), '我的' . GROUP_NAME(), false, true);
		$this->addElement('gamegroups', common_path('groups/game'), '游戏' . GROUP_NAME(), false, true);
		$this->addElement('lifegroups', common_path('groups/life'), '生活' . GROUP_NAME(), false, true);
		$this->addElement('auditgroups', common_path('groups/audit'), '待审核' . GROUP_NAME(), false, true);
	}
}

class NavList_GroupMembers extends NavList {
	var $group;
	var $is_group_admin;
	
	function __construct($group, $is_group_admin = true) {
		$this->group = $group;
		$this->is_group_admin = $is_group_admin;
		$this->init();
	}
	
	function init() {
		$this->addElement('groupmembers', common_path('group/' . $this->group->id . '/members'), '成员列表');
		if ($this->is_group_admin) {
			$this->addElement('groupapplication', common_path('group/' . $this->group->id . '/application'), '待处理请求');
			$this->addElement('groupinvitation', common_path('group/' . $this->group->id . '/invitation'), '邀请好友');
			$this->addElement('groupblacklist', common_path('group/' . $this->group->id . '/blacklist'), '屏蔽列表');
		}
	}
}

class NavList_GroupEdit extends NavList {
	var $group;
	
	function __construct($group) {
		$this->group = $group;
		$this->init();
	}
	
	function init() {
		$this->addElement('groupedit', common_path('group/' . $this->group->id . '/edit'), GROUP_NAME() . '资料');
		$this->addElement('grouplogo', common_path('group/' . $this->group->id . '/logo'), GROUP_NAME() . 'Logo');
		$this->addElement('showgroup', common_path('group/' . $this->group->id . '/?theme=true'), '个性模板');
		$this->addElement('groupeditpost', common_path('group/' . $this->group->id . '/editpost'), GROUP_NAME() . '公告');
	}
}