<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR . '/templates/htmltemplate.php';
require_once INSTALLDIR . '/lib/NavUtil.php';
require_once INSTALLDIR . '/lib/TemplateUtil.php';

class BasicHTMLTemplate extends HTMLTemplate
{	
	var $args;
	
	/**
	 * TemplateUtil的一个实例，是一个视图helper对象
	 * @var unknown_type
	 */
	var $tu;
	
	/**
	 * 访问当前页面的用户的User对象
	 * 如果是匿名用户，则cur_user == null
	 * @var unknown_type
	 */
	var $cur_user;
	
	/**
	 * 当前的页数
	 * @var unknown_type
	 */
	var $cur_page;
	
	var $is_anonymous;
	
	var $game = null;
	var $game_server = null;
	
	var $cache_time = 30;
	var $cache_allowed = true;
	
 	/**
     * show page 
     *
     * Show the public stream, using recipe method showPage()
     *
     * @param array $args arguments, mostly unused
     *
     * @return void
     */
    function show($args = array()) {
    	$this->args = $args;
    	
    	$this->cur_page = $args['cur_page'];
    	
    	$this->cur_user = $args['cur_user'];
    	
    	$this->cache_allowed = $args['cache_allowed'];
    	
    	$this->is_anonymous = is_null($this->cur_user);
    	
    	if (Event::handle('StartShowHTML', array($this))) {
            $this->startHTML();
            Event::handle('EndShowHTML', array($this));
        }
        if (Event::handle('StartShowHead', array($this))) {
            $this->showHead();
            Event::handle('EndShowHead', array($this));
        }
        if (Event::handle('StartShowBody', array($this))) {
            $this->showBody();
            Event::handle('EndShowBody', array($this));
        }
        if (Event::handle('StartEndHTML', array($this))) {
            $this->endHTML();
            Event::handle('EndEndHTML', array($this));
        }
    }
    
	function endXML()
    {
    	$this->xw->endDocument();
        $string = $this->xw->flush();
	    
        if ($this->is_anonymous
        	&& $_SERVER['REQUEST_METHOD'] == 'GET'
        	&& $this->cache_allowed) {
	        $cache = common_memcache();
			
			if (! empty($cache)) {
				$idkey = common_cache_key('statichtml:' . $this->trimmed('shai_path'));
				$cache->set($idkey, $string, 0, $this->cache_time);
			}
        }
        echo $string;
    }
    
	/**
     * Boolean understands english (yes, no, true, false)
     *
     * @param string $key query key we're interested in
     * @param string $def default value
     *
     * @return boolean interprets yes/no strings as boolean
     */
    function boolean($key, $def=false)
    {
        $arg = strtolower($this->trimmed($key));

        if (is_null($arg)) {
            return $def;
        } else if (in_array($arg, array('true', 'yes', '1'))) {
            return true;
        } else if (in_array($arg, array('false', 'no', '0'))) {
            return false;
        } else {
            return $def;
        }
    }
    
	/**
     * whether it is a var with name '@param varname' in array args.
     * @param $varname
     * @return unknown_type
     */
    function contains($varname) {
    	return array_key_exists($varname, $this->args);
    }
    
	/**
     * Returns trimmed query argument or default value if not found
     *
     * @param string $key requested argument
     * @param string $def default value to return if $key is not provided
     *
     * @return boolean is read only action?
     */
    function trimmed($key, $def=null)
    {
        $arg = $this->arg($key, $def);
        return is_string($arg) ? trim($arg) : $arg;
    }
    
	/**
     * Returns query argument or default value if not found
     *
     * @param string $key requested argument
     * @param string $def default value to return if $key is not provided
     *
     * @return boolean is read only action?
     */
    function arg($key, $def=null)
    {
        if (array_key_exists($key, $this->args)) {
            return $this->args[$key];
        } else {
            return $def;
        }
    }
    
    /**
     * Generate pagination links
     *
     * @param boolean $have_before is there something before?
     * @param boolean $have_after  is there something after?
     * @param integer $page        current page
     * @param string  $action      current action
     * @param array   $args        rest of query arguments
     *
     * @return nothing
     */
    function pagination($have_before, $have_after, $page, $action, $args=null, $notice_num=0)
    {
        if ($have_after) {
	        $pargs   = array('page' => $page+1);
	        $this->element('a', array('href' => common_local_url($action, $args, $pargs),
	                                   'id' => 'notice_more', 'rel' => 'nofollow'));
        }
    }
    
    /**
     * Generate pagination links
     *
     * @param boolean $have_after  is there something after?
     * @param integer $page        current page
     * @param string  $action      current action
     * @param array   $args        rest of query arguments
     * @param array	  $params	   other parameters
     *
     * @return nothing
     */
    function morepagination($out, $have_after, $cur_page, $action, $args=null, $params=array())
    {
        if ($have_after) {
	        $pargs = array_merge($params, array('page' => $cur_page+1));
	        $out->element('a', array('href' => common_local_url($action, $args, $pargs),
	                                   'id' => 'notice_more', 'rel' => 'nofollow'));
        }
    }
    
    function numpagination($totalnum, $action, $actionParam1 = array(), $actionParam2 = array(), $displayPerPage = NOTICES_PER_PAGE, $inviter_id=false)
    {
    	$have_before = $this->cur_page > 1;
    	$have_after = ($this->cur_page * $displayPerPage) < $totalnum;
    	
    	if($have_before || $have_after) {
	    	$this->elementStart('ol', array('id' => 'pagination'));
//	    	if ($have_before) {
//	    		$this->elementStart('li');
//	    		$this->element('a', array('href'=>common_local_url($action, $args), 'class' => $class, 'title' => '前十页'), '上');
//	    		$this->elementEnd('li');
//	    		$this->elementStart('li', 'b_pga_prev');
//	    		$this->element('a', array('href'=>common_local_url($action, $args, 
//	    		                    array('page'=>$page-1)), 'class' => $class), '<');
//	    		$this->elementEnd('li');
//	    	}
//	    	common_debug('total ' . $noticenum);
	    	
	    	$pages = floor(($totalnum - 1 + $displayPerPage) / $displayPerPage);
	    	
	    	$start = (floor(($this->cur_page-1)/10))*10 + 1;
	    	if ($start > 10) {
	    		$this->elementStart('li');
	    		if (! $this->cur_user && $inviter_id) {
		    		$this->element('a', array('href' => common_path('register?ivid=' . $inviter_id), 'title' => '前十页', 'class' => 'trylogin', 'rel' => 'nofollow'), '上');
		    	} else {
	    			$this->element('a', array('href' => common_local_url($action, $actionParam1, array_merge($actionParam2, array('page' => $start - 10))), 
	    				'title' => '前十页', 'rel' => 'nofollow'), '上');
		    	}
	    		$this->elementEnd('li');
	    	}
	    	
	    	$pp = $start;
	    	do {
	    		if($pp != $this->cur_page){
		    		$this->elementStart('li');
		    		if (! $this->cur_user && $inviter_id) {
		    			$this->element('a', array('href' => common_path('register?ivid=' . $inviter_id), 'class' => 'trylogin', 'rel' => 'nofollow'), $pp);
		    		} else {
		    			$this->element('a', array('href' => common_local_url($action, $actionParam1, array_merge($actionParam2, array('page' => $pp))), 'rel' => 'nofollow'), $pp);
		    		}
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
	    		if (! $this->cur_user && $inviter_id) {
		    		$this->element('a', array('href' => common_path('register?ivid=' . $inviter_id), 'title' => '后十页', 'class' => 'trylogin', 'rel' => 'nofollow'), '下');
		    	} else {
	    			$this->element('a', array('title' => '后十页',
		    			'href' => common_local_url($action, $actionParam1, array_merge($actionParam2, array('page' => $start + 10))), 'rel' => 'nofollow'), '下');
		    	}
	    		$this->elementEnd('li');
	    	}
	    	
	    	$this->elementEnd('ol');
    	}
    }
    
    /**
     * Generate a menu item
     *
     * @param string  $url         menu URL
     * @param string  $text        menu name
     * @param string  $title       title attribute, null by default
     * @param boolean $is_selected current menu item, false by default
     * @param string  $id          element id, null by default
     *
     * @return nothing
     */
    function menuItem($url, $text, $title=null, $is_selected=false, $id=null, $nofollow=false)
    {
        // Added @id to li for some control.
        // XXX: We might want to move this to htmloutputter.php
        $lattrs = array();
        if ($is_selected) {
            $lattrs['class'] = 'current';
        }

        (is_null($id)) ? $lattrs : $lattrs['id'] = $id;

        $this->elementStart('li', $lattrs);
        $attrs['href'] = $url;
        if ($title) {
            $attrs['title'] = $title;
        }
        if ($nofollow) {
        	$attrs['rel'] = 'nofollow';
        }
        $this->element('a', $attrs, $text);
        $this->elementEnd('li');
    }
    
	/**
     * Constructor
     *
     * Just wraps the HTMLOutputter constructor.
     *
     * @param string  $output URI to output to, default = stdout
     * @param boolean $indent Whether to indent output, default true
     *
     * @see XMLOutputter::__construct
     * @see HTMLOutputter::__construct
     */
    function __construct($output='php://output', $indent=true)
    {
//        parent::__construct($output, $indent);
        $this->xw = new XMLWriter();
        $this->xw->openMemory();
        $this->xw->setIndent($indent);
        $this->tu = new TemplateUtil($this);
    }
    
    /**
     * Show head, a template method.
     *
     * @return nothing
     */
    function showHead()
    {
        
        $this->elementStart('head');
        $this->showMeta();
        $this->showTitle();
        $this->showShortcutIcon();
        $this->showStylesheets();
        $this->showUAStylesheets();
        $this->showScripts();
        // Frame-busting code to avoid clickjacking attacks.
        $this->element('script', array('type' => 'text/javascript'),
                           'if (window.top !== window.self) { window.top.location.href = window.self.location.href; }');
        if (common_config('site', 'use_analytics')) {
        	$this->showStaticScript();
        }
//        $this->showOpenSearch();
//        $this->showFeeds();
        $this->showDescription();
        $this->extraHead();
//        $this->raw('<style type="text/css">html {filter:progid:DXImageTransform.Microsoft.BasicImage(grayscale=1); }</style>');
        $this->elementEnd('head');
    }
    
    /**
     * Show title, a template method.
     *
     * @return nothing
     */
    function showTitle()
    {
    	if($this->title()){
    		// 当有页数时，显示页数
    		if ($this->cur_page != 1) {
    			$this->element('title', null, $this->title() . ' - 第' . $this->cur_page . '页');
    		} else {
            	$this->element('title', null,
                                   $this->title());
    		}
    	}else {
    		// 未指定标题时，显示默认标题 - 但是从SEO角度来说不建议出现重复标题
    		$this->element('title', null, common_config('site', 'name') . " - 记录你的游戏旅程");
    	}
    }
    
    function metaKeywords() {
    	return "";
    }
    
    function metaDescription() {
    	return "";
    }
    
    function showMeta()
    {
    	$this->element('meta', array('content' => 'text/html; charset=utf-8', 'http-equiv' => 'Content-Type'));
//    	$this->element('meta', array('content' => 'zh-CN', 'http-equiv' => 'Content-Language'));
//		$this->element('meta', array('charset' => 'utf-8'));
    	$this->element('meta', array('content' => $this->title(), 'name' => 'title'));
    	$this->element('meta', array('content' => $this->metaKeywords(), 'name' => 'keywords'));
    	$this->element('meta', array('content' => $this->metaDescription(), 'name' => 'Description'));
    	$this->element('meta', array('http-eqiv' => 'X-UA-Compatible', 'content' => 'chrome=1'));
    }
    
    /**
     * Show themed shortcut icon
     *
     * @return nothing
     */
    function showShortcutIcon()
    {
//        if (is_readable(INSTALLDIR . '/theme/' . common_config('site', 'theme') . '/favicon.ico')) {
//            $this->element('link', array('rel' => 'shortcut icon',
//                                         'href' => theme_path('favicon.ico')));
//        } else {
          $this->element('link', array('rel' => 'shortcut icon',
                                         'href' => common_path('favicon.ico')));
//        }

//        if (common_config('site', 'mobile')) {
//            if (is_readable(INSTALLDIR . '/theme/' . common_config('site', 'theme') . '/apple-touch-icon.png')) {
//                $this->element('link', array('rel' => 'apple-touch-icon',
//                                             'href' => theme_path('apple-touch-icon.png')));
//            } else {
//                $this->element('link', array('rel' => 'apple-touch-icon',
//                                             'href' => common_path('apple-touch-icon.png')));
//            }
//        }
    }

    function showShaishaiStylesheets() {
          if (Event::handle('StartShowShaishaiStyles', array($this))) {
//            	$this->cssLink('css/jqueryui/jui.css', 'default', 'screen, projection, tv');
                $this->cssLink('css/base.css','default','screen, projection');
//                $this->cssLink('css/s.css','default','screen, projection');
                Event::handle('EndShowShaishaiStyles', array($this));
          }
    }
    
    function showUAStylesheets () {
		if (Event::handle('StartShowUAStyles', array($this))) {
//			$this->comment('[if lt IE 8]><link href="'.theme_path('css/ie.css', 'default').'?'.SHAISHAI_VERSION.'" rel="stylesheet" type="text/css" />'
//				.'<![endif]');
//			$this->comment('[if IE 6]><link href="'.theme_path('css/ie6.css', 'default').'?'.SHAISHAI_VERSION.'" rel="stylesheet" type="text/css" />'
//				.'<![endif]');
			$hour = (int) date('H', time());
			if ($hour > 16 && $hour < 20) {
				$this->raw('<style>body{background-image:url(/theme/default/i/bg2.jpg);}</style>');
			} else if ($hour > 19 || $hour < 7) {
				$this->raw('<style>body{background-image:url(/theme/default/i/bg3.jpg);}a.outbound{color:#fff;}</style>');
			}
			$this->comment('[if lt IE 8]><link href="'.theme_path('css/ie.css', 'default').'?'.SHAISHAI_VERSION.'" rel="stylesheet" type="text/css" />'
				.'<![endif]');
			$this->comment('[if IE 6]><link href="'.theme_path('css/ie6.css', 'default').'?'.SHAISHAI_VERSION.'" rel="stylesheet" type="text/css" />'
				.'<![endif]');
			Event::handle('EndShowUAStyles', array($this));
		}
    }
    /**
     * Show stylesheets
     *
     * @return nothing
     */
    function showStylesheets()
    {
        if (Event::handle('StartShowStyles', array($this))) {
			$this->showShaishaiStylesheets();
         	Event::handle('EndShowStyles', array($this));
        }
    }

    /**
     * Show javascript headers
     *
     * @return nothing
     */
    function showScripts()
    {   
    	if (Event::handle('StartShowScripts', array($this))) {
            $this->showJqueryScripts();
    		$this->showShaishaiScripts();
        }
        Event::handle('EndShowScripts', array($this));
    }
    
    function showStaticScript() {
    	$this->elementStart('script',array('type' => 'text/javascript'));
    	$this->raw("var _gaq = _gaq || [];_gaq.push(['_setAccount', 'UA-16713568-1']);_gaq.push(['_trackPageview']);(function() {var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);})();");
    	$this->elementEnd('script');
    }
    
    function showShaishaiScripts() {
    	if (Event::handle('StartShowShaishaiScripts', array($this))) {
            $this->comment('[if lt IE 7]><script type="text/javascript" src="' . common_path('js/ie6png.js') . '"></script><![endif]');
            $this->script('js/common.js');
            Event::handle('EndShowShaishaiScripts', array($this));
        }
        
    }
    
    function showJqueryScripts() {
	     if (Event::handle('StartShowJQueryScripts', array($this))) {
			$this->script('js/lib.js');  
	        Event::handle('EndShowJQueryScripts', array($this));
	     }
    }


    /**
     * Show OpenSearch headers
     *
     * @return nothing
     */
    function showOpenSearch()
    {
        $this->element('link', array('rel' => 'search',
                                     'type' => 'application/opensearchdescription+xml',
                                     'href' =>  common_local_url('opensearch', array('type' => 'people')),
                                     'title' => common_config('site', 'name').' People Search'));
        $this->element('link', array('rel' => 'search', 'type' => 'application/opensearchdescription+xml',
                                     'href' =>  common_local_url('opensearch', array('type' => 'notice')),
                                     'title' => common_config('site', 'name').' Notice Search'));
    }

    
    /**
     * Show feed headers
     *
     * MAY overload
     *
     * @return nothing
     */

    function showFeeds()
    {
        $feeds = $this->getFeeds();

        if ($feeds) {
            foreach ($feeds as $feed) {
                $this->element('link', array('rel' => $feed->rel(),
                                             'href' => $feed->url,
                                             'type' => $feed->mimeType(),
                                             'title' => $feed->title));
            }
        }
        
    }

    /**
     * Show description.
     *
     * SHOULD overload
     *
     * @return nothing
     */
    function showDescription()
    {
        // does nothing by default
    }

    /**
     * Show extra stuff in <head>.
     *
     * MAY overload
     *
     * @return nothing
     */
    function extraHead()
    {
        // does nothing by default
    }

    /**
     * Show body.
     *
     * Calls template methods
     *
     * @return nothing
     */
    function showBody()
    {
        $this->elementStart('body', array('token' => common_session_token(), 'anonymous' => $this->cur_user ? '0' : '1'));
        
        if (Event::handle('StartShowHeader', array($this))) {
            $this->showHeader();
            Event::handle('EndShowHeader', array($this));
        }
        
		$this->elementStart('div', array('id' => 'wrap', 'class' => 'rounded5 clearfix'));
        $this->showCore();
        $this->elementEnd('div');
        
        if (Event::handle('StartShowFooter', array($this))) {
            $this->showFooter();
            Event::handle('EndShowFooter', array($this));
        }
        
        $this->showFloatBar();
        
        $this->showWaiter();
        
        $this->elementEnd('body');
    }
    
    function showWaiter() {
    	if ($this->cur_user) {
    		$this->elementStart('div', array('id' => 'waiter'));
    		$this->element('a', array('href' => '#', 'class' => 'standby'));
    		$this->elementEnd('div');
    	}
    }
    
    /**
     * Show header of the page.
     *
     * Calls template methods
     *
     * @return nothing
     */
    function showHeader()
    {
        $this->elementStart('div', array('id' => 'header_outter_wrap'));
        $this->elementStart('div', array('id' => 'header_inner_wrap'));
        $this->elementStart('div', array('id' => 'header', 'class' => 'clearfix'));
        $this->showLogo();
        $this->showTopMenu();
        $this->elementEnd('div');
        $this->elementEnd('div');
        $this->elementEnd('div');
    }
    
    function showLogo()
    {    	
        if (Event::handle('StartAddressData', array($this))) {
        	if($this->cur_user) {
            	$this->elementStart('a', array('id' => 'logo', 'href' => common_path('public'), 'title' => '看看有啥新鲜人，新鲜事'));
        	} else {
        		$this->elementStart('a', array('id' => 'logo', 'href' => common_path(''), 'title' => '返回游戏酒馆登录页'));
        	}
            $this->element('img', array('alt' => common_config('site', 'name'), 'src' => common_path('images/logoe6.png'), 'width' => '114', 'height' => '28'));
            $this->elementEnd('a');
            Event::handle('EndAddressData', array($this));
        }
    }
    
    private function _showMainNavItem($href, $text, $class, $title = '', $dropdown_list = array()) {
    	// 高亮鸡肋，取消
    	$this->elementStart('li');
    	$this->element('a', array('href' => $href, 'title' => $title, 'class' => $class), $text);
    	
    	if (! empty($dropdown_list)) {
	    	$this->elementStart('ul', 'more');
		    $this->element('li', 'head');
		    
		    $cnt = 0;
			foreach($dropdown_list as $list_item) {
				if ($cnt == 0) {
					$this->elementStart('li', 'first');
				} else {
					$this->elementStart('li');
				}
	    		$this->element('a', array('href' => $list_item->href), $list_item->text);
	    		$this->elementEnd('li');
				$cnt++;
			}
			
	    	$this->element('li', 'foot');
	    	$this->elementEnd('ul');
    	}
    	
    	$this->elementEnd('li');
    }
    
    private function _showMainNavSplitItem() {
    	$this->elementStart('li', 'split');
	    $this->raw('&nbsp;');
	    $this->elementEnd('li');
    }
    
    function showTopMenu() {
    	if (Event::handle('StartPrimaryNav', array($this))) {    		
    		if ($this->cur_user) {
    			$this->elementStart('ul', array('id' => 'main_nav'));
    			
	    		$publiclist = new NavList_Public();
				$this->_showMainNavItem(common_path('public'), '酒馆地带', 'public', 
					'去看看有什么新鲜人，新鲜事', $publiclist->lists());
    			
    			$this->_showMainNavSplitItem();
	    		
    			//所在游戏
    			$game = Game::staticGet('id', $this->cur_user->game_id);
				$gamelist = new NavList_Game($game);
				$this->_showMainNavItem(common_path('game/' . $game->id), 
					$game->name, 'game', '看看游戏中又发生了什么', $gamelist->lists());
    			
    			$this->_showMainNavSplitItem();
    			
    			$grouplist = new NavList_Group();
				$this->_showMainNavItem(common_path('groups'), GROUP_NAME(), 'group', 
					'', $grouplist->lists());
    			
    			$this->_showMainNavSplitItem();
	    		
    			$this->_showMainNavItem(common_path('home'), '我的空间', 'home', '返回您的个人空间');
    			
    			$this->elementEnd('ul');
    			
    			
    			$this->elementStart('ul', array('id' => 'tool_nav'));
    			
    			$this->elementStart('li', array('class' => 'first'));
    			$this->element('a', array('href' => common_path($this->cur_user->uname)), $this->cur_user->uname);
    			$this->elementEnd('li');
    			$this->elementStart('li', 'clients');
    			$this->element('a', array('href' => common_path('clients'), 'title' => '各种各样的' . common_config('site', 'name') . '应用'), '更多玩法');
    			$this->elementEnd('li');
    			if (common_config('invite', 'enabled') == 'true') {
	    			$this->elementStart('li');
	    			$this->element('a', array('href' => common_path('invite'), 'title' => '邀请好友加入得G币'), '邀请');
	    			$this->elementEnd('li');
    			}
    			$this->elementStart('li');
    			$this->element('a', array('href' => common_path('settings/profile'), 'title' => '修改您的' . common_config('site', 'name') . '设置'), '设置');
    			$this->elementEnd('li');
    			$this->elementStart('li');
    			$this->element('a', array('href' => common_path('main/logout')), '退出');
    			$this->elementEnd('li');
    			$this->elementEnd('ul');
            } else {
            	$this->elementStart('ul', array('id' => 'tool_nav'));
            	$this->elementStart('li', 'clients first');
            	$this->element('a', array('href' => common_path('clients'), 'title' => '各种各样的' . common_config('site', 'name') . '应用'), '更多玩法');
    			$this->elementEnd('li');
    			$this->elementStart('li', 'clients');
    			$this->element('a', array('href' => common_path('public'), 'title' => '游戏酒馆公共区', 'class' => 'public'), '酒馆地带');
				
    			$this->elementEnd('li');
            	if (!common_config('site', 'closed')) {
            		$this->elementStart('li');
	    			$this->element('a', array('href' => common_path('register'), 'title' => '注册一个游戏酒馆账号', 'rel' => 'nofollow'), '注册');
	    			$this->elementEnd('li');
            	}
    			$this->elementStart('li');
    			$this->element('a', array('href' => common_path('login'), 'class' => 'trylogin', 'title' => '登录游戏酒馆'), '登录');
    			$this->elementEnd('li');
            	$this->elementEnd('ul');
            }
            
    		Event::handle('EndPrimaryNav', array($this));
    	}
    }
    
    function showCore()
    {
    	
    }
    
    /**
     * Show footer.
     *
     * @return nothing
     */
    function showFooter()
    {
        $this->elementStart('div', array('id' => 'footer', 'class' => 'rounded5'));
        $this->showFooterSpans();
        $this->showSecondaryNav();
        $this->elementEnd('div');
    }

    function showFooterSpans() {
    	$this->element('span', 'copyright', '晒尔公司 © 2011');
        $this->element('span', 'license', '京ICP备10005313号');
        $this->element('span', 'lc');
        $this->element('span', 'rc');
    }

    /**
     * Show secondary navigation.
     *
     * @return nothing
     */
    function showSecondaryNav()
    {
        $this->elementStart('ul');
        if (Event::handle('StartSecondaryNav', array($this))) {
        	
            $this->menuItem(common_path('doc/info/about'),
                            '关于我们', '查看晒尔公司的历史', false, 'aboutus', true);
            
            $this->menuItem('http://blog.gamepub.cn/',
                            '官方博客', '了解产品最新进展', false, 'officialblog', true);
            
            $this->menuItem(common_path('doc/statement/privacy'),
                            '隐私', '查看游戏酒馆隐私条款', false, 'viewprivacy', true);
            
//            $this->menuItem(common_local_url('doc', array('type'=>'statement', 'title' => 'tos')),
//                                '服务协议', '查看游戏酒馆服务协议', false, 'viewtos', true);

            $this->menuItem(common_path('doc/info/friendlinks'),
            				'友情链接', '看看游戏酒馆的合作伙伴', false, 'friendlinks', false);
            
//            $this->menuItem('#', 'API', '查看开放的API文档', false, 'viewapi', true);
            
            $this->menuItem(common_path('doc/help/modules'),
                            '帮助', '有问题？进酒馆帮助系统看看', false, 'helpmodules', true);
            
            $this->menuItem(common_path('main/userfeedback'), '反馈', '向游戏酒馆提出宝贵的意见和建议', false, 'viewfeedback', true);
            
            if (! $this->cur_user) {
	            $this->menuItem(common_path(''), '登录', '返回游戏酒馆登录', false, 'tohomepage', false);
            }
            Event::handle('EndSecondaryNav', array($this));
        }
        $this->elementEnd('ul');
    }

    /**
     * Show licenses.
     *
     * @return nothing
     */
    function showLicenses()
    {
        $this->elementStart('dl', array('id' => 'licenses'));
        
        $this->element('dt', array('id' => 'site_software_license'), '站点软件协议');
        $this->elementStart('dd', null);
        $this->elementStart('p');
        $this->text(common_config('site', 'name') . '是由' . common_config('site', 'broughtby') . '开发和运营的微博客服务。');
        $this->showStaticScript();
        $this->elementEnd('p');
        $this->elementEnd('dd');
        
        $this->elementEnd('p');
        $this->elementEnd('dd');
        
        $this->elementEnd('dl');
    }
    
    function showFloatBar() 
    {
    	if ($this->cur_user) {
    		$expandFloatbar = array_key_exists('floatbar', $_SESSION) && $_SESSION['floatbar'] == 1;
    		
    		$this->elementStart('div', array('id' => 'floatbar', 'class' => 'rounded5', 'sync' => 'null'));
    		$this->elementStart('ul', array('class' => 'expanded clearfix rounded5', 
    			'style' => ($expandFloatbar ? '' : 'display:none;')));
    		$this->elementStart('li', 'search_li first');
    		$this->elementStart('form', array('class' => 'search',
                                           'action' => common_path('search/notice')));
            $this->elementStart('fieldset', 'clearfix');
            $this->element('legend', null, '搜索');
            $this->elementStart('div', 'text_wrap');
            $this->element('input', array('type' => 'text', 'name' => 'q', 'class' => 'text'));
            $this->elementEnd('div');
            $this->element('input', array('type' => 'submit', 'value' => '', 'class' => 'submit'));
            $this->elementEnd('fieldset');
            $this->elementEnd('form');    		
    		$this->elementEnd('li');

	    	$this->elementStart('li', array('class' => 'item remind'));
	    	$this->elementStart('a', array('href' => '#', 'class' => 'btn'));
	    	$this->element('span', null, '提醒');
	    	$this->elementEnd('a');
	    	$this->elementEnd('li');
	    		
    		$this->elementStart('li', 'item group');
    		$this->elementStart('a', array('href' => common_path('groups'), 'class' => 'btn'));
    		$this->element('span', null, GROUP_NAME());
    		$this->elementEnd('a');
    		
    		$this->elementEnd('li');
    		
    		$this->elementStart('li', 'toggle_li');
    		$this->elementStart('a', array('href' => '#', 'class' => 'btn toggle', 'title' => '隐藏工具栏'));
    		$this->elementStart('span');
    		$this->raw('&nbsp;');
    		$this->elementEnd('span');
    		$this->elementEnd('a');
    		$this->elementEnd('li');
    		$this->elementEnd('ul');
    		
    		$this->elementStart('ul', array('class' => 'folded clearfix rounded5',
    			'style' => ($expandFloatbar ? 'display:none;' : '')));
    		$this->elementStart('li', 'first toexpand_li');
    		$this->elementStart('a', array('href' => '#', 'class' => 'btn toexpand'));
    		$this->element('span', null, '工具箱');
    		$this->elementEnd('a');
    		$this->elementEnd('li');
    		    		
    		$this->elementStart('li', 'toggle_li');
    		$this->elementStart('a', array('href' => '#', 'class' => 'btn toggle', 'title' => '展开工具栏'));
    		$this->elementStart('span');
    		$this->raw('&nbsp;');
    		$this->elementEnd('span');
    		$this->elementEnd('a');
    		$this->elementEnd('li');
    		$this->elementEnd('ul');
    		
    		$this->elementEnd('div');
    	}
    }
    
    /**
     * Returns the page title
     *
     * SHOULD overload
     *
     * @return string page title
     */

    function title()
    {
        return "";
    }
    
	function getFeeds() {
    	return null;
    }
    
    /**
     * Show anonymous message.
     *
     * SHOULD overload
     *
     * @return nothing
     */
    function showAnonymousMessage()
    {
        // needs to be defined by the class
    }
    
    function serverError($msg, $code=500)
    {
        $action = $this->trimmed('action');
        throw new ServerException($msg, $code);
    }
    
    function notLoggedInError($msg, $code=200) {
    	$action = $this->trimmed('action');
       // common_debug("Server error '$code' on '$action': $msg", __FILE__);
       	common_set_returnto($this->selfUrl());
        throw new NotLoggedInException($msg, $code);
    }

    /**
     * Client error
     *
     * @param string  $msg  error message to display
     * @param integer $code http error code, 400 by default
     *
     * @return nothing
     */

    function clientError($msg, $code=400)
    {
        $action = $this->trimmed('action');
        throw new ClientException($msg, $code);
    }
    
}

?>