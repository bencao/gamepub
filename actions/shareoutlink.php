<?php
if (!defined('SHAISHAI')) {
	exit (1);
}

/**
 * Share the outside link
 *
 * @category Share
 * @package  Shaishai
 * @author   LIU Xiaodan <lxdfigo@163.com>
 * @author	 AGun Chan <agunchan@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */
class ShareoutlinkAction extends NewnoticeAction {

	function __construct() {
		parent :: __construct();
		$this->no_anonymous = false;
		$this->cache_allowed = false;
	}

	function isReadOnly($args) {
		return false;
	}

	/**
	 * Save a new notice, based on arguments
	 * Overrided method of NewnoticeAction
	 */
	function handleNewNotice($args)
	{
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			try {
				if (! $this->cur_user && ! $this->checkLogin()) {
					return false;
				}
				
				//如果登录成功则保存消息
				$this->saveNewNotice($args);
			} catch (Exception $e) {
				$this->showJsonResult(array('result' => 'false', 'msg' => $e->getMessage()));
				return false;
			}
			
			$stringer = new XMLStringer();
			$stringer->elementStart('div', 'success');
			$stringer->element('h1', null, '分享成功！');
			$stringer->elementStart('p');
			$stringer->raw('<span id="timeout">5</span>秒后窗口自动关闭，<a href="javascript:void(0);" onclick="window.opener=null;window.close();">点击这里</a>立即关闭');
			$stringer->elementEnd('p');
			$stringer->element('a', array('class' => 'reg', 'href' => common_path('home'), 'target' => '_blank'), '去我的空间');
			$stringer->elementEnd('div');
			$this->showJsonResult(array('result' => 'true', 'msg' => $stringer->getString()));
			return true;
		} else if ($_SERVER['REQUEST_METHOD'] == 'GET'){
			//如果是GET消息,获取传入的title与url
			$outsidelink_url = $this->trimmed('url');
			$outsidelink_title = $this->trimmed('title', '外站分享');
			$outsidelink_source = $this->trimmed('source', 'bookmark');
				
			$this->getWebContent($outsidelink_url);

			$this->addPassVariable('otlinktitle', $outsidelink_title);
			$this->addPassVariable('otlinkurl', $outsidelink_url);
			$this->addPassVariable('otlinksource', $outsidelink_source);
			$this->displayWith('ShareoutlinkHTMLTemplate');
			return true;
		}
	}

	/**
	 * Save a new notice, based on arguments
	 * Overrided method of NewnoticeAction
	 */
	function saveNewNotice($args)
	{
		$this->preHandle();

		$this->nnl = new NewnoticeLib();
		$this->handleMime();

		$from_source = $this->trimmed('source');

		$options = array(
        	'reply_to' => $this->reply_to ? $this->reply_to : null, 
        	'uri' => null, 
			'created' => null, 
			'addRendered' => $this->add_rendered, 
			'is_banned' => $this->is_banned, 
			'content_type' => $this->content_type, 
			'topic_type' => $this->topic_type, 
			'retweet_from' => $this->retweet_from ? $this->retweet_from : 0, 
			'reply_to_uname' => $this->reply_to_uname);

		$notice = Notice::saveNew($this->cur_user->id, $this->content_shortened, $this->add_content, $from_source, 1, $options);
		$this->postHandle($notice);
	}

	/**
	 * Get content of outside url
	 * 
	 * Proces picture,mp3,video separately
	 * 
	 * @param string outsidelink_url outside url
	 */
	function getWebContent($outsidelink_url)
	{
		$outsidelink_content = "";
		
		if (! $outsidelink_url) {
			$this->clientError('分享的网址不存在！');
			return false;
		}
		
		if(strrpos($outsidelink_url, common_path('')) != false){
			$this->clientError('此工具只对游戏酒馆站外的网页有效。');
			return false;
		} else if(NewnoticeLib::isPhotoUrl($outsidelink_url)){
			$outsidelink_imgarr = array ( array ('src' => $outsidelink_url) );
			$this->addPassVariable('photo', $outsidelink_url);
		} else if(NewnoticeLib::isAudioUrl($outsidelink_url)) {
			$outsidelink_imgarr = array ( array () );
			$this->addPassVariable('audio', $outsidelink_url);
		} else if(NewnoticeLib::isVideoUrl($outsidelink_url)) {
			$outsidelink_imgarr = array ( array () );
			$this->addPassVariable('video', $outsidelink_url);
		} else {
			try {
				$outsidelink_content = file_get_contents($outsidelink_url);
				//如果匹配失败则传入空数组
				$mode = "{<img[^>]*src=\"([^\">]*)\"[^>]*>}";
				if (!preg_match_all($mode, $outsidelink_content, $outsidelink_imgarr)) {
					$outsidelink_imgarr = array ( array () );
				}
			} catch (Exception $e) {
				$this->serverError($e->getMessage());
				return false;
			}
		}
			
		$this->addPassVariable('otlinkimgarr', $outsidelink_imgarr);
	}


	/**
	 * Check the login data
	 *
	 * Determines if the login data is valid. If so, logs the user
	 * in, and redirects to the 'with friends' page, or to the stored
	 * return-to URL.
	 *
	 * @return void
	 */
	function checkLogin() {
		$uname = strtolower($this->trimmed('uname'));

		$password = $this->arg('password');

		$profile = Profile::getByUNameAndPassword($uname, $password);
		//判断用户名与密码
		if (! $profile) {
			$this->showForm('用户名或密码无效。');
			return false;
		}

		if (strcmp(common_munge_password($password, $profile->id),
		$profile->password) != 0) {
			$this->showForm('用户名或密码无效。');
			return false;
		}

		//判断用户是否可用
		if ($profile->is_banned) {
			$this->showForm('你的恶意行为严重，已被封禁。');
			return false;
		}
		 
		$user = $profile->getUser();

		if (!common_set_user($user)) {
			$this->showForm('设置用户时发生错误。');
			return false;
		}

		if ($this->boolean('rememberme')) {
			Remember_me::remember($user);
		}

		$log = new Login_log();
		$log->logNew($user);

		$this->cur_user = $user;

		return true;
	}


	/**
	 * Store an error and show the page when login failed
	 *
	 * This used to show the whole page; now, it's just a wrapper
	 * that stores the error in an attribute.
	 *
	 * @param string $error error, if any.
	 *
	 * @return void
	 */
	function showForm($error = null) {
		if ($error) {
			$datas = array ('result' => 'false', 'msg' => $error);
		} else {
			$datas = array ('result' => 'true');
		}
		$this->showJsonResult($datas);
	}
}
?>
