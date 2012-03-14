<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class Homepage2HTMLTemplate extends BasicHTMLTemplate
{
	function show($args) {
		$this->isOldUser = $args['isOldUser'];
		parent::show($args);
	}
	
	function metaKeywords() {
		return 'GamePub，游戏酒馆，玩家社区，公会，部落，家族，记录旅程，游戏截图，一键上传';
	}
	
	function metaDescription() {
		return '游戏酒馆是一个可以使您与游戏朋友的沟通交流变得更开放，更有趣的社区。在这里寻找同服务器、同游戏玩家，了解他们结识他们，您会感觉到朋友一下变多了。';
	}
	
	function title() {
		return  '游戏酒馆(GamePub) - 最大的网游交友社区，来这里找寻你的游戏朋友';
	}
	
	function extraHead() {
	}
	
	function showBody()
    {
        $this->elementStart('body');
        
        $this->showCore();
        
        $this->elementEnd('body');
    }
    
    function showCore()
    {
		$this->_showHead();
		$this->_showHot();
		$this->_showCore();
		$this->_showFoot();
    }
    
    function _showCore() {
    	$this->elementStart('div', array('id' => 'core'));
		$this->elementStart('div', 'wrap clearfix');
		$this->_showUsers();
		$this->_showTimeline();
		$this->_showSidebar();
		
		$this->elementEnd('div');
		$this->elementEnd('div');
    }
    
    function _showTimeline() {
    	$this->elementStart('dl', array('id' => 'timeline'));
		$this->elementStart('dt');
		$this->element('a', array('href' => common_path('public')), '最近的精彩分享');
		$this->elementEnd('dt');
		$this->elementStart('dd');
		$this->elementStart('ol', array('id' => 'talking'));
		
		$this->raw($this->trimmed('posts'));
		
		$this->elementEnd('ol');
		$this->elementEnd('dd');
		$this->elementEnd('dl');
    }
    
    function _showUsers() {
    	$this->elementStart('div', array('id' => 'showuser'));
		$this->element('h2', null, '看看谁在游戏酒馆');
		$this->elementStart('ul', 'clearfix');
		$recents = $this->arg('recents');
    	$this->raw($recents);
		$this->elementEnd('ul');
		$this->element('p', null, '您可以在酒馆中找到您的战友、同服的GGMM，当然，您也可以找到名人，跟他们交流学习。');
		$this->elementEnd('div');
    }
    
    function _showSidebar() {
    	$this->elementStart('div', array('id' => 'sidebar'));
		$this->elementStart('div', 'panel');
		$this->element('a', array('href' => '#', 'class' => 'sw-reg'), '新用户注册');
		$this->element('a', array('href' => '#', 'class' => 'sw-login', 'accesskey' => 'l'), '我要登录');
		$this->_showLogin();
		$this->_showReg();
		$this->elementEnd('div');
		$this->_showSearch();
		$this->_showActs();
		$this->elementEnd('div');
    }
    
    function _showActs() {
    	$this->elementStart('dl', 'acts');
    	$this->element('dt', null, '游戏酒馆最新动态');
    	$this->elementStart('dd');
    	//bookmark
    	$this->elementStart('p');
    	$this->text('新增玩法');
    	$this->element('a', array('href' => common_path('clients/bookmark')), '分享书签');
    	$this->elementEnd('p');
    	//fxext,chromeext
    	$this->elementStart('p');
    	$this->text('新增');
    	$this->element('a', array('href' => common_path('clients/fxext')), 'Firefox');
    	$this->text('和');
    	$this->element('a', array('href' => common_path('clients/chromeext')), 'Chrome');
    	$this->text('扩展');
    	$this->elementEnd('p');
    	//hooyou
    	$this->elementStart('p');
    	$this->text('发布');
    	$this->element('a', array('href' => common_path('clients/hooyou')), '桌面端呼游');
    	$this->text('1.2版本');
    	$this->elementEnd('p');
    	$this->elementStart('p');
    	$this->text('发布');
    	$this->element('a', array('href' => common_path('showtime')), 'GamePub介绍视频');
    	$this->elementEnd('p');
    	$this->elementEnd('dd');
    	$this->elementEnd('dl');
    }
    
    function _showReg() {
    	$this->elementStart('div', array('class' => 'register', 'style' => ($this->isOldUser ? 'display:none;' : '')));
    	$this->element('h2', null, '第一次来游戏酒馆？');
    	$this->element('p', null, '游戏酒馆是国内第一家专门为网游玩家打造的互动社区！记录您的游戏生活，与好友保持联络和互动。');
    	$this->elementStart('a', array('href' => common_path('register')));
    	$this->element('span', null, '立即注册');
    	$this->elementEnd('a');
    	$this->element('p', 'tip', '关注您感兴趣的人，您就可以在第一时间收到他们的分享。');
    	$this->elementEnd('div');
    }
    
    function _showHead() {
    	$this->elementStart('div', array('id' => 'header'));
		$this->elementStart('div', 'wrap');
		$this->elementStart('a', array('href' => common_path('public'), 'class' => 'logo'));
		$this->element('img', array('src' => '/images/beta.jpg', 'alt' => 'GamePub'));
		$this->elementEnd('a');
		$this->element('h1', null, '游戏酒馆(GamePub) - 中国最大、最专业的网游玩家交友社区，助您找寻游戏朋友');
		$this->elementEnd('div');
		$this->elementEnd('div');
    }
    
    function _showFoot() {
    	$this->elementStart('div', array('id' => 'footer'));
    	$this->elementStart('div', 'wrap');
    	$this->elementStart('ul');
    	$this->elementStart('li');
    	$this->element('a', array('href' => common_path('doc/info/about'), 'rel' => 'nofollow'), '关于我们');
    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->element('a', array('href' => common_path('doc/info/contact'), 'rel' => 'nofollow'), '联系方式');
    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->element('a', array('href' => 'http://blog.gamepub.cn/'), '官方博客');
    	$this->elementEnd('li');
    	$this->elementStart('li');
		$this->element('a', array('href' => common_path('doc/statement/tos'), 'rel' => 'nofollow'), '服务条款');
    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->element('a', array('href' => common_path('doc/statement/privacy'), 'rel' => 'nofollow'), '隐私');
    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->element('a', array('href' => common_path('doc/info/friendlinks')), '友情链接');
    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->element('a', array('href' => common_path('halloffame'), 'title' => '去看看游戏酒馆的名人堂'), '名人堂');
    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->element('a', array('href' => common_path('doc/help/modules'), 'rel' => 'nofollow'), '帮助');
    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->element('a', array('href' => common_path('yesterdaynotices')), '最新消息');
    	$this->elementEnd('li');
    	$this->elementEnd('ul');
    	$this->element('span', 'copyright', '© 2011 北京晒尔网络科技有限公司');
    	$this->element('span', 'license', '京ICP备10005313号');
    	$this->elementEnd('div');
    	$this->elementEnd('div');
    }
    
    function _showLogo() {
    	$this->elementStart('a', array('id' => 'logo', 'href' => common_path('public'), 'title' => '看看游戏酒馆中都有啥新鲜人，新鲜事'));
//    	$this->element('img', array('alt' => common_config('site', 'name'), 'src' => common_path('images/logo134.png'), 'width' => '134', 'height' => '31'));
    	$this->elementEnd('a');
    }
    
    function _showHot() {
    	$this->elementStart('div', array('id' => 'hot'));
		$this->elementStart('dl', 'wrap');
		$this->element('dt', null, '正在热议');
		$this->elementStart('dd');
		$this->elementStart('ol');
		
		$tags = $this->arg('tags');
    	$this->raw($tags);
    	
		$this->elementEnd('ol');
		$this->elementEnd('dd');
		$this->elementEnd('dl');
		$this->elementEnd('div');
    }
    
    function _showLogin() {
    	$this->tu->startFormBlock(array('class' => 'login', 'action' => common_path('main/login'), 'method' => 'post', 'style' => ($this->isOldUser ? '' : 'display:none;')), '登录游戏酒馆');
    	
    	if (array_key_exists('login_error', $this->args)) {
        	$this->hidden('login_error', $this->arg('login_error'));
        }
        
    	$this->elementStart('dl');
    	$this->element('dt', null, '登录名');
    	$this->elementStart('dd');
    	$this->element('input', array('class' => 'text', 'type' => 'text', 'name' => 'uname','id' => 'uname'));
    	$this->elementEnd('dd');
    	$this->element('dt', null, '密码');
    	$this->elementStart('dd');
    	$this->element('input', array('class' => 'text', 'type' => 'password', 'name' => 'password','id' => 'password'));
    	$this->elementEnd('dd');
    	$this->element('dt');
    	$this->elementStart('dd');
    	$this->element('input', array('name' => 'rememberme', 'type' => 'checkbox', 'class' => 'checkbox',  'id' => 'rememberme'));
    	$this->element('label', array('for' => 'rememberme'), '两周之内自动登录');
    	$this->elementEnd('dd');
    	$this->elementEnd('dl');
    	$this->element('input', array('type' => 'submit', 'class' => 'submit', 'value' => ''));
    	$this->elementStart('p');
    	$this->text('忘记密码？');
    	$this->element('a', array('id' => 'forget', 'href' => common_path('main/recoverpassword'), 'title' => '找回您在游戏酒馆的密码', 'rel' => 'nofollow'), '点击这里');
    	$this->elementEnd('p');
    	$this->tu->endFormBlock();
    }
    
    function _showSearch() {
    	$this->elementStart('div', 'search');
    	$this->element('p', null, '搜消息、找朋友、找公会？');
    	$this->element('p', null, '使用GamePub站内搜索');
    	$this->tu->startFormBlock(array('id' => 'search', 'action' => common_path('search/notice'), 'method' => 'post'), '搜索');
    	$this->element('input', array('class' => 'text', 'type' => 'text', 'name' => 'q', 'id' => 'q'));
    	$this->element('input', array('class' => 'submit', 'type' => 'submit', 'value' => 'GO'));
    	$this->tu->endFormBlock();
    	$this->elementEnd('div');
    }
    
    function showShaishaiStylesheets() {
          if (Event::handle('StartShowShaishaiStyles', array($this))) {
               $this->cssLink('css/login.css','default','screen, projection');
//				$this->cssLink('css/h.css','default','screen, projection');
                Event::handle('EndShowShaishaiStyles', array($this));
          }
    }
    
    function showUAStylesheets () {
    	
    }
	
    function showShaishaiScripts() {
    	if (Event::handle('StartShowShaishaiScripts', array($this))) {
    		$this->script('js/jquery.timers.js');
            $this->script('js/lshai_login.js');
            Event::handle('EndShowShaishaiScripts', array($this));
        }
        
    }
}

?>