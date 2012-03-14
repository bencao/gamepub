<?php
/**
 * Shaishai, the distributed microblog
 *
 * Register form
 *
 * PHP version 5
 *
 * @category  Register
 * @package   Shaishai
 * @author    Huang Bin <huangbin180@gmail.com>
 *    modified 20090905 Zhenhua Cao <benb88@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR.'/lib/mail.php';
require_once INSTALLDIR . '/lib/validatetool.php';

class RegisterAction extends ShaiAction
{
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
		$this->cache_allowed = false;
	}
	
    function prepare($args)
    {
        if (! parent::prepare($args)) {
        	return false;
        }
        
        $ivid = $this->trimmed('ivid');
    	if (!empty($ivid)) {
        	common_ensure_session();
            $_SESSION['ivid'] = $ivid;
        }
        
        // group invite code
        $givid = $this->trimmed('givid');
    	if (!empty($givid)) {
        	common_ensure_session();
            $_SESSION['givid'] = $givid;
        }
        
        $code = $this->trimmed('mailcode');
        if (! empty($code)) {
        	$invitation = Invitation::staticGet('code', $code);
        	if ($invitation) {
        		common_ensure_session();
            	$_SESSION['mailIv'] = $invitation;
            	$_SESSION['ivid'] = $invitation->user_id;
            	if (! $this->trimmed('email')) {
            		$this->addPassVariable('email', $invitation->address);
            	}
//        		if (! $this->trimmed('recruit')) {
//            		$this->addPassVariable('recruit', $invitation->rcode);
//            	}
        	}
        }

    	if (common_current_user()) {
            $this->clientError('您已经登录!');
            return false;
        }

        return true;
    }
    
    function _validateForm() {
    	
    	if ($this->args['reg_rand'] != $_SESSION["login_check_num"]) {
			$this->errorMessage = '验证码有误，请重试。';
			return false;
    	}
        
    	$this->uname = strtolower($this->trimmed('uname'));
        if (! isValidUname($this->uname)) {
        	$this->errorMessage = '用户名只能由5-20位小写字母与数字组成。';
            return false;
        }
        
        if (User::staticGet('uname', $this->uname) !== false) {
        	$this->errorMessage = '用户名已存在';
        	return false;
        }
        
        $this->nickname = $this->trimmed('nickname');
        if (! isValidNickname($this->nickname)) {
        	$this->errorMessage = '昵称长度应在1~12个字之间。';
            return false;
        }
        
        $this->password = $this->arg('password');
        $this->confirm  = $this->arg('confirm');
        
        if (! isValidPassword($this->password)) {
            $this->showForm('密码长度应在5~64位之间。');
            return false;
        }
        
    	if ($this->password != $this->confirm) {
            $this->errorMessage = '密码不匹配。';
            return false;
        }
        
    	$this->email = strtolower($this->trimmed('email'));
        if (! isValidEmail($this->email)) {
        	$this->errorMessage = '邮件地址无效。';
            return false;
        }
        
    	if (Profile::existEmail($this->email)) {
        	$this->errorMessage = '邮件地址已存在';
        	return false;
        }
        
        $this->sex = $this->arg('sex');
        if (! isValidSex($this->sex)) {
        	$this->errorMessage = '没有选择性别';
        	return false;
        }
        
        $this->game_id = $this->arg('game');
        if (! in_array($this->game_id, Game::listAllGameIds())) {
        	$this->errorMessage = '不存在该游戏';
        	return false;
        }
        
    	$this->game_big_zone = $this->trimmed('game_big_zone');
		$this->game_server = $this->trimmed('game_server');
		
		if (empty($this->game_big_zone) 
			|| ! isNum($this->game_big_zone)
			|| empty($this->game_server)
			|| ! isNum($this->game_server)
			|| ! Game::isValidServer($this->game_id, $this->game_big_zone, $this->game_server)) {
			$this->errorMessage = '选择的服务器不存在';
			return false;
		}
		
		$recruit_code = $this->trimmed('recruit');
		if (! empty($recruit_code) 
			&& ! isNum($recruit_code)) {
			$this->errorMessage = '您的新手卡号(11位数字)输入不正确';
			return false;
		}
		
//		$this->recruit = Recruit::staticGet('fullcode', $recruit_code);
//		if ($recruit_code && ! $this->recruit) {
//			$this->errorMessage = '无效的新手卡号';
//			return false;
//		}
//		if ($recruit_code && $this->recruit->uid) {
//			$this->errorMessage = '该新手卡已经被使用过了';
//			return false;
//		}
		
        
        $this->inviter = null;
        if (array_key_exists('ivid', $_SESSION) && ! empty($_SESSION['ivid'])) { 
        	$this->inviter = User::staticGet('id', $_SESSION['ivid']);
        }
        
        $this->g_inviter = null;
        if (array_key_exists('givid', $_SESSION)) {
	        $givid = Group_ivcode::staticGet('code', $_SESSION['givid'])->groupid;
	        $this->g_inviter = User_group::staticGet('id', $givid);
        }
    	
        $this->mailInvitation = null;
        if (array_key_exists('mailIv', $_SESSION)) {
	        $this->mailInvitation = $_SESSION['mailIv'];
        }
        
        return true;
    }

    function handle($args)
    {
        parent::handle($args);
        
//        $this->addPassVariable('allgames', Game::listAll());
        $this->addPassVariable('hotgames', Game::listHots());
		
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        	if ($this->_validateForm()) {
        		$this->doRegister();
        	} else {
        		$this->showForm($this->errorMessage);
        	}
        } else {
        	$this->showForm();
        }
    }

    /**
     * Try to register a user
     *
     * Validates the input and tries to save a new user and profile
     * record. On success, shows an instructions page.
     *
     * @return void
     */

    function doRegister()
    {
        if (Event::handle('StartRegistration', array($this))) {
        	
			$user = User::register(array('uname' => $this->uname,
                                         'password' => $this->password,
                                         'email' => $this->email,
                                         'nickname' => $this->nickname,
										 'sex' => $this->sex,
										 'game_id' => $this->game_id,
										 'game_server_id' => $this->game_server,
										 'mail_confirmed' => $this->mailInvitation && $this->mailInvitation->address == $this->email));
            if ($user) {
            	
	            if (! empty($this->inviter)) {
		        	Subscription::subscribeTo($this->inviter, $user, false);
		        	Subscription::subscribeTo($user, $this->inviter, false);
		        	
		        	Myinviterecord::saveNew($this->inviter->id, $user->id);
		        	
		        	// add 10 scores to the inviter since he invited cur_user
		        	User_grade::addScore($this->inviter->id, 10);
		        }
		        
		        if (! empty($this->g_inviter)) {
		        	User_group::fromInvitation($this->g_inviter->id, $user);
		        }
		        
//		        if (! empty($this->mailInvitation)) {
//		        	User_grade::addScore($this->mailInvitation->user_id, 10);
//		        }
            	
//		        if ($this->recruit) {
//	            	// 新手卡标记为已用过
//	            	$orig = clone($this->recruit);
//	            	$this->recruit->uid = $user->id;
//	            	$this->recruit->update($orig);
//		        }
//		        	
//            	// 再生成10个新手卡号
//            	$randomSource = sprintf('%03d', rand(0, 999));
//            	$ps = Popularize_source::getSource('100', $randomSource);
//            	if (! $ps) {
//            		$ps = Popularize_source::newSource('100', $randomSource, '系统注册推广');
//            	}
//            	$time = common_sql_now();
//            	$randomCursor = rand(0, 99999);
//            	for ($i = 0; $i < 10; $i ++) {
//            		$r = new Recruit();
//            		$r->source_id = $ps->id;
//            		$r->fullcode = '100' . $randomSource . sprintf('%05d', $randomCursor ++);
//            		$r->created = $time;
//            		// 多请求同步进行时，插入失败几率是客观存在的
//            		$rid = $r->insert();
//            		while (! $rid) {
//            			$r->fullcode = '100' . $randomSource . sprintf('%05d', $randomCursor ++);
//            			$rid = $r->insert();
//            		}
//            		
//            		$up = new User_popularize();
//            		$up->uid = $user->id;
//            		$up->rid = $rid;
//            		$up->insert();
//            	}
		        
            	
            	$this->cur_user = $user;
        		
                // success!
		        if (!common_set_user($user)) {
		            $this->serverError('设置用户时发生错误。');
		            return;
        		}
        		
        		// 发一条系统消息
        		System_message::saveNew(array($this->cur_user->id), 
					'亲爱的' . $this->cur_user->nickname . '，欢迎来到' . common_config('site', 'name') . '，祝您在这里找到曾经的老朋友、新的游戏朋友。', 
					'亲爱的' . $this->cur_user->nickname . '，欢迎来到' . common_config('site', 'name') . '，祝您在这里找到曾经的老朋友、新的游戏朋友。',
				0);
        		
        		Event::handle('EndRegistration', array($this, $this->cur_user));
            	
        		common_redirect(common_path('register/followsomepeople'), 303);
            } else {
                $this->showForm('暂时无法注册，请稍候再试');
            }
        }
    }
    
	/**
	 * Store an error and show the page
	 *
	 * This used to show the whole page; now, it's just a wrapper
	 * that stores the error in an attribute.
	 *
	 * @param string $error error, if any.
	 *
	 * @return void
	 */
	function showForm($error=null, $args=array())
	{
		if ($error != null) {
			$this->addPassVariable('register_error', $error);
		}
		if (common_have_session() && array_key_exists('ivid', $_SESSION)) {
			$this->addPassVariable('welcomeUser', User::staticGet('id', $_SESSION['ivid']));
		}
		$game_id = $this->trimmed('game');
        $game_big_zone_id = $this->trimmed('game_big_zone');
        $game_server_id = $this->trimmed('game_server');
        if ($game_id) {
        	$game = Game::staticGet('id', $game_id);
        	$this->addPassVariable('ggame', $game);
        	$this->addPassVariable('bigzones', $game->getBigzones());
        	
        	if ($game_big_zone_id) {
        		$this->addPassVariable('ggame_big_zone', Game_big_zone::staticGet('id', $game_big_zone_id));
        		$this->addPassVariable('servers', $game->getServers($game_big_zone_id));
        	}
        	if ($game_server_id) {
        		$this->addPassVariable('ggame_server', Game_server::staticGet('id', $game_server_id));
        	}
        }
		$this->displayWith('RegisterHTMLTemplate');
	}

}