<?php
/**
 * Shaishai, the distributed microblog
 *
 * Login form
 *
 * PHP version 5
 *
 * @category  Login
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 *       modified zhcao 20090905 <benb88@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
	exit(1);
}

class RegisterHTMLTemplate extends RegisterWizardHTMLTemplate
{
	function title()
	{
		return '注册 ' . common_config('site', 'name');
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
			return '我是' . $welcomeUser->nickname . '，你已收到邀请啦？快搞定注册进来找我吧！';
		} else {
			return '您好，我是GamePub小助手，欢迎入住' . common_config('site', 'name') . '！';
		}
	}
	
	function _showErrorMessage() {
		if ($this->arg('register_error')) {
			$this->element('div', 'error', $this->arg('register_error'));
		}
	}
	
	function _showUname() {
		$this->elementStart('dt');
		$this->element('label', array('for' => 'uname', 'class' => 'label100'), '用户名');
		$this->elementEnd('dt');
		$this->elementStart('dd', 'clearfix');
		$this->element('input', 
			array('type' => 'text',
				'class' => 'text200', 
				'name' => 'uname', 
				'id' => 'uname', 
				'tip' => '只包含小写字母和数字，最少五个字符',
				'value' => $this->trimmed('uname')));
		$this->elementEnd('dd');
	}
	
	function _showNickname() {
		$this->elementStart('dt');
		$this->element('label', array('for' => 'nickname', 'class' => 'label100'), '昵称');
		$this->elementEnd('dt');
		$this->elementStart('dd', 'clearfix');
		$this->element('input', 
			array('type' => 'text',
				'class' => 'text200', 
				'name' => 'nickname', 
				'id' => 'nickname', 
				'tip' => '建议您使用游戏角色的名字，方便好友找到您',
				'value' => $this->trimmed('nickname')));
		$this->elementEnd('dd');
	}
	
	function _showPassword() {
		$this->elementStart('dt');
		$this->element('label', array('for' => 'password', 'class' => 'label100'), '密码');
		$this->elementEnd('dt');
		$this->elementStart('dd', 'clearfix');
		$this->element('input', 
			array('type' => 'password',
				'class' => 'text200', 
				'name' => 'password', 
				'id' => 'password', 
				'maxlength' => '255',
				'tip' => '6位以上字符',
				'value' => $this->trimmed('password')));
		$this->elementEnd('dd');
	}
	
	function _showPasswordConfirm() {
		$this->elementStart('dt');
		$this->element('label', array('for' => 'confirm', 'class' => 'label100'), '确认密码');
		$this->elementEnd('dt');
		$this->elementStart('dd', 'clearfix');
		$this->element('input', 
			array('type' => 'password',
				'class' => 'text200', 
				'name' => 'confirm', 
				'id' => 'confirm', 
				'maxlength' => '255',
				'tip' => '再次输入密码',
				'value' => $this->trimmed('confirm')));
		$this->elementEnd('dd');
	}
	
	function _showGame() {
		$this->elementStart('dt');
		$this->element('label', array('for' => 'game', 'class' => 'label100'), '我正在玩');
		$this->elementEnd('dt');
		$this->elementStart('dd', 'game clearfix');
//		$this->elementStart('select', 
//			array('name' => 'game', 
//				'id' => 'game'));
//		
//		$this->option('', '游戏');
//		
//        $this->elementEnd('select');
//        
//        $this->elementStart('select', array('id' => 'game_big_zone', 'name' => 'game_big_zone'));
//        $this->option('', '大区');
//        $this->elementEnd('select');
//        
//        $this->elementStart('select', array('id' => 'game_server', 'name' => 'game_server'));
//        $this->option('', '服务器');
//        $this->elementEnd('select');

		$ggame = $this->arg('ggame');
		$ggame_big_zone = $this->arg('ggame_big_zone');
		$ggame_server = $this->arg('ggame_server');
		
		$this->elementStart('div', array('id' => 'game_select', 'class' => 'droper'));
		if ($ggame) {
			$this->text($ggame->name);
		} else {
			$this->text('选择游戏');
		}
		
		$this->elementEnd('div');
		
		$this->elementStart('div', array('id' => 'game_big_zone_select', 'class' => 'droper'));
		if ($ggame_big_zone) {
			$this->text($ggame_big_zone->name);
		} else {
			$this->text('选择大区');
		}
		$this->elementEnd('div');
		
		$this->elementStart('div', array('id' => 'game_server_select', 'class' => 'droper'));
		if ($ggame_server) {
			$this->text($ggame_server->name);
		} else {
			$this->text('选择服务器');
		}
		$this->elementEnd('div');
        
		$this->element('input', array('type' => 'hidden', 'name' => 'game', 'id' => 'game', 'value' => $this->trimmed('game')));
		$this->element('input', array('type' => 'hidden', 'name' => 'game_big_zone', 'id' => 'game_big_zone', 'value' => $this->trimmed('game_big_zone')));
		$this->element('input', array('type' => 'hidden', 'name' => 'game_server', 'id' => 'game_server', 'value' => $this->trimmed('game_server')));
		
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
        
        $this->elementStart('div', array('id' => 'big_zone_more', 'class' => 'more'));
        $this->elementStart('ul', 'clearfix');
        $gbigzones = $this->arg('bigzones');
        if ($gbigzones) {
        	foreach ($gbigzones as $gb) {
	        	$this->elementStart('li');
	        	$this->element('a', array('href' => '#', 'bzid' => $gb['id']), $gb['name']);
	        	$this->elementEnd('li');
        	}
        }
        $this->elementEnd('ul');
        $this->elementEnd('div');
        
        $this->elementStart('div', array('id' => 'server_more', 'class' => 'more'));
		$this->elementStart('ul', 'clearfix');
        $gservers = $this->arg('servers');
        if ($gservers) {
        	foreach ($gservers as $gs) {
	        	$this->elementStart('li');
	        	$this->element('a', array('href' => '#', 'sid' => $gs['id']), $gs['name']);
	        	$this->elementEnd('li');
        	}
        }
        $this->elementEnd('ul');
        $this->elementEnd('div');
        
		$this->elementEnd('dd');
	}
	
	function _showSex() {
		$this->elementStart('dt');
		$this->element('label', array('class' => 'label100'), '性别');
		$this->elementEnd('dt');
		$this->elementStart('dd', 'sex clearfix');
		$sex = $this->arg('sex') ? $this->arg('sex') : 'M';
		if ($sex == 'M') {
			$this->element('input', 
				array('type' => 'radio', 
					'name' => 'sex', 
					'id' => 'sexm', 
					'value' => 'M', 
					'checked' => 'checked'));
		} else {
			$this->element('input', 
				array('type' => 'radio', 
					'name' => 'sex', 
					'id' => 'sexm', 
					'value' => 'M'));
		}
		$this->element('label', array('for' => 'sexm'), 'GG');

		if ($sex == 'F') {
			$this->element('input', 
				array('type' => 'radio', 
					'name' => 'sex', 
					'id' => 'sexf', 
					'value' => 'F', 
					'checked' => 'checked'));
		} else {
			$this->element('input', 
				array('type' => 'radio', 
					'name' => 'sex', 
					'id' => 'sexf',
					'value' => 'F'));
		}
		$this->element('label', array('for' => 'sexf'), 'MM');
		$this->elementEnd('dd');
	}
	
	function _showEmail() {
		$this->elementStart('dt');
		$this->element('label', array('for' => 'email', 'class' => 'label100'), '邮件');
		$this->elementEnd('dt');
		$this->elementStart('dd', 'clearfix');
		$this->element('input', 
			array('type' => 'email', 
				'name' => 'email',
				'class' => 'text200',
				'id' => 'email', 
				'maxlength' => '155',
				'tip' => '仅用于接收系统消息和找回密码',
				'value' => $this->trimmed('email')));
		$this->elementEnd('dd');
	}
	
	function _showRecruit() {
		$this->elementStart('dt');
		$this->element('label', array('for' => 'recruit', 'class' => 'label100'), '新手卡号(选填)');
		$this->elementEnd('dt');
		$this->elementStart('dd', 'clearfix');
		$this->element('input', 
			array('type' => 'text', 
				'name' => 'recruit',
				'class' => 'text200',
				'id' => 'recruit', 
				'maxlength' => '11',
				'tip' => '填了新手卡号可以抽奖！问朋友看有没有！',
				'value' => $this->trimmed('recruit')));
			
		$this->raw('<span htmlfor="recruit" generated="true">填写新手卡号可以抽奖！问朋友看有没有！</span>');
		
		$this->elementEnd('dd');
	}
	
	function _showVerifyPicture() {
		$this->elementStart('dt');
		$this->element('label', array('for' => 'reg_rand', 'class' => 'label100'), '验证码');
		$this->elementEnd('dt');
		$this->elementStart('dd', 'verify clearfix');
		$this->element('input', 
			array('type' => 'text', 
				'class' => 'text70',
				'name' => 'reg_rand', 
				'id' => 'reg_rand', 
				'maxlength' => '5',
				'tip' => '请输入5位数字验证码',
				'value' => ''));
		$this->element('img', array('id' => 'vefimg', 'width' => '200', 'height' => '47', 
						'src' => common_path('ajax/randverifypic')));
		$this->element('a', array('href' => 'javascript:void(0);', 'id' => 'reloadimg', 'class' => 'reload'), '看不清换一张');
		$this->elementEnd('dd');
	}
	
	function _showLicenseAgreement() {
		$this->elementStart('dt');
		$this->raw('&#160;');
		$this->elementEnd('dt');
		$this->elementStart('dd', 'clearfix');
		$this->elementStart('input', 
			array('type' => 'checkbox',
				'class' => 'checkbox', 
				'name' => 'agreelicense', 
				'id' => 'agreelicense', 
				'checked' => 'checked',
				'value' => 'true'));
		$this->elementEnd('input');
		$this->text('我已阅读并同意');
		$this->element('a', array('href' => common_local_url('doc', array('type'=>'statement', 'title' => 'tos')), 'target' => '_blank'),
			'《' . common_config('site', 'name') . '服务协议》');
		$this->elementEnd('dd');
	}
	
	function _showSubmit() {
		$this->elementStart('div', 'op');
		$this->element('input', array('type' => 'submit', 'class' => 'submit button99 green99',
						   'value' => '马上加入'));
		$this->elementEnd('div');
	}
	
	function showContent()
	{	
		$this->tu->startFormBlock(array('method' => 'post',
                                          'id' => 'register',
                                          'class' => 'form_settings',
                                          'action' => common_local_url('register')), '填写个人基本注册信息');
		
		$this->_showErrorMessage();
		
		$this->elementStart('dl', 'clearfix');
		
		$this->_showUname();
		
		$this->_showNickname();
		
		$this->_showPassword();
		
		$this->_showPasswordConfirm();
		
		$this->_showEmail();
		
		$this->_showSex();
		
		$this->_showGame();
		
//		$this->_showRecruit();
		
		$this->_showVerifyPicture();
		
		$this->_showLicenseAgreement();
		
		$this->elementEnd('dl');
		
		$this->elementStart('div', 'op');
		
		$this->_showSubmit();
		
		$this->elementEnd('div');
		
		$this->tu->endFormBlock();
		
		$this->_showAdwordsAna();
	}
	
	function _showAdwordsAna() {
		$this->raw('<!-- Google Code for &#36827;&#20837;&#27880;&#20876;&#39029; Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 1034144568;
var google_conversion_language = "zh_CN";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "rhoNCI7E0wEQuJaP7QM";
var google_conversion_value = 0;
if (0.5) {
  google_conversion_value = 0.5;
}
/* ]]> */
</script>
<script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/1034144568/?value=0.5&amp;label=rhoNCI7E0wEQuJaP7QM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>');
	}
	
 	function showTopMenu() {
    	if (Event::handle('StartPrimaryNav', array($this))) {    		
            $this->elementStart('ul', array('id' => 'tool_nav'));
            $this->elementStart('li', 'clients first');
            $this->element('a', array('href' => common_local_url('clients'), 'title' => '手机等', 'alt' => '手机等'), '更多玩法');
    		$this->elementEnd('li');
    		$this->elementStart('li', 'clients');
    		$this->element('a', array('href' => common_local_url('public'), 'title' => '酒馆地带', 'class' => 'public', 'alt' => '酒馆地带'), '酒馆地带');
    		$this->elementEnd('li');
            if (!common_config('site', 'closed')) {
            	$this->elementStart('li');
    			$this->element('a', array('href' => common_local_url('register'), 'title' => '注册新用户'), '注册');
    			$this->elementEnd('li');
            }
    		$this->elementStart('li');
    		$this->element('a', array('href' => common_local_url('login')), '登录');
    		$this->elementEnd('li');
            $this->elementEnd('ul');
            
    		Event::handle('EndPrimaryNav', array($this));
    	}
    }
	
	function showScripts() {
		parent::showScripts();
		$this->script('js/jquery.validate.min.js');
		$this->script('js/lshai_reg.js');
		$this->script('js/lshai_gamechoose.js');
	}
}
