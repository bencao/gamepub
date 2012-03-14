<?php

if (!defined('SHAISHAI')) {
	exit(1);
}

class DocHTMLTemplate extends RightsidebarHTMLTemplate
{
	/**
     * Title of the page
     *
     * @return page title
     */

    function title()
    {
    	$fn = $this->args['thistitle'];
    	if ($fn == 'friendlinks') {
    		return common_config('site', 'name') . '友情链接';
    	} else if ($fn == 'about') {
    		return '关于' . common_config('site', 'name');
    	} else if ($fn == 'why') {
    		return '为什么要使用' . common_config('site', 'name');
    	} else if ($fn == 'ourteam') {
    		return common_config('site', 'name') . '团队';
    	} else if ($fn == 'contact') {
    		return '联系' . common_config('site', 'name') . '团队';
    	} else if ($fn == 'joinus') {
    		return '加入' . common_config('site', 'name');
    	} else if ($fn == 'privacy') {
    		return common_config('site', 'name') . '隐私声明';
    	} else if ($fn == 'tos') {
    		return common_config('site', 'name') . '服务协议';
    	} else if ($fn == 'hooyouparts') {
    		return '呼游桌面端帮助';
    	} else if ($fn == 'usage') {
    		return common_config('site', 'name') . '使用技巧';
    	} else if ($fn == 'register') {
    		return '登录与注册帮助';
    	} else if ($fn == 'sentnotice') {
    		return '发送消息帮助';
    	} else if ($fn == 'shownotice') {
    		return '查看消息帮助';
    	} else if ($fn == 'settings') {
    		return '设置帮助';
    	} else if ($fn == 'invite') {
    		return '邀请好友帮助';
    	} else if ($fn == 'relation') {
    		return '关系管理帮助';
    	} else if ($fn == 'groups') {
    		return '群组帮助';
    	} else if ($fn == 'gameregion') {
    		return '游戏版块帮助';
    	} else if ($fn == 'integratedregion') {
    		return '酒馆地带帮助';
    	} else if ($fn == 'search') {
    		return '搜索帮助';
    	} else if ($fn == 'retweetandreply') {
    		return '收藏转发与回复帮助';
    	} else if ($fn == 'personalmessage') {
    		return '私信与通知帮助';
    	} else if ($fn == 'usergrade') {
    		return '等级、财富系统帮助';
    	} else if ($fn == 'link') {
    		return '更多玩法帮助';
    	} else {
        	return common_config('site', 'name') . '帮助系统';
    	}
    }
    
	function metaKeywords() {
		return '游戏酒馆，GamePub，' . $this->title();
	}
    
    function showRightSection() {
    	// XXX: I make here a little comples, need to rethink how to manage the docs - Andray
        if ($this->args['thistype'] == 'info') {
    		$navs = new NavList_Info();
	    	$this->tu->showNavigationWidget($navs->lists(), $this->args['title']);
    	} else if ($this->args['thistype'] == 'statement') {
            $navs = new NavList_Statement();
	    	$this->tu->showNavigationWidget($navs->lists(), $this->args['title']);
    	} else if ($this->args['thistype'] == 'help' && $this->args['thistitle'] != 'modules') {
    		$navs = new NavList_HelpDetail();
    		$this->tu->showNavigationWidget($navs->lists(), $this->args['title']);
    	} else if ($this->args['thistype'] == 'help' || $this->args['thistype'] == 'hooyouhelp' 
    	    || $this->args['thistype'] == 'skills') {
	    	$navs = new NavList_Help();
	    	$this->tu->showNavigationWidget($navs->lists(), $this->args['thistype']);
    	}
    }
    
    function showRightsidebar() {
    	// show navigation
    	$this->showRightSection();
		
		// show FAQ, we need to manage it here temporarily
		if ($this->args['thistype'] == 'help' || $this->args['thistype'] == 'hooyouhelp') {
			$this->elementStart('dl', 'widget faq');
			
			$this->elementStart('dt');
			$this->element('a', array('href' => '#'), '常见问题');
			$this->element('a', array('class' => 'unfold', 'href' => '#'));
			$this->elementEnd('dt');
			$this->elementStart('dd');
			$this->elementStart('ul');
			// put your docs filenames into $docnames, must have corresponding question
			$docs = array('usergrade'=>'谁动了我的G币？', 
			              'groups'=>'如何邀请我QQ' . GROUP_NAME() . '里的班级进入' . GROUP_NAME() . '？', 'search'=>'如何找到我的好友？',
			              'personalmessage'=>'想给TA发消息又不想其它人看到？');
			foreach($docs as $name=>$question){
				$this->elementStart('li');
				$this->element('a', array('href' => common_path('doc/help/' . $name)), $question);
				$this->elementEnd('li');
			}
			$this->elementEnd('ul');
			$this->elementEnd('dd');
			
			$this->elementEnd('dl');
		}
		
    }

	/**
	 * Display content.
	 *
	 * @return nothing
	 */
	function showContent()
	{
		
		$c      = file_get_contents($this->args['filename']);
//		$output = common_markup_to_html($c);
		$this->raw($c);
		
		if(array_key_exists('thistype', $this->args) 
			&& ($this->args['thistype'] == 'help' || $this->args['thistype'] == 'hooyouhelp' || $this->args['thistype'] == 'skills')) {
			$this->elementStart('div', 'section');
			$this->elementStart('div', array('class' => 'text', 'style' => 'margin-top:20px;'));
			$this->elementStart('p');
			$this->text('如果您有其它的问题，或者不知道怎么玩，可以向我们');
			$this->element('a', array('href'=>common_path('main/userfeedback')), '反馈您的问题或建议');
			$this->elementEnd('p');
			$this->elementStart('p');
			$this->element('a', array('href'=>common_path('doc/help/modules')), '返回帮助主页');
			$this->elementEnd('p');
			$this->elementEnd('div');
			$this->elementEnd('div');
		}

	}

}