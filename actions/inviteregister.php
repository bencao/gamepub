<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR . '/lib/mail.php';

class InviteregisterAction extends ShaiAction
{
	function handle($args) {
		parent::handle($args);
		
		if (array_key_exists('ivid', $_SESSION)) {
			$this->addPassVariable('welcomeUser', User::staticGet('id', $_SESSION['ivid']));
		}
		
		// 会话数据清理
//        foreach (array('ivid', 'givid') as $var) {
//        	if (array_key_exists($var, $_SESSION)) {
//                unset($_SESSION[$var]);
//                }
//        }
        if (!array_key_exists('havefollowed', $_SESSION)) {
        	$game_vip = Game::getVips($this->cur_user->game_id);
			$game_vip = array_diff($game_vip, array($this->cur_user->id,100066,100235));
			$game_bestusers = Notice::getMosttalkUsers(2, 'game',$this->cur_user->game_id);
			$gamebestuserids = array();
			foreach($game_bestusers as $gamebestuser)
			{
				$gamebestuserids[]=$gamebestuser['user_id'];
			}
			$gamebestuserids = array_diff($gamebestuserids, array($this->cur_user->id));
			
			$default_follow = array_merge($game_vip, $gamebestuserids, array(100066, 100235));
			
			foreach ($default_follow as $df) {
				Subscription::subscribeTo($this->cur_user, User::staticGet('id', $df));
			}
        }	
		$mail = Confirm_address::getConfirmEmailByUserId($this->cur_user->id);
		$this->addPassVariable('provider', mail_provider($mail));
		$this->addPassVariable('invite_link', common_path('register?ivid=' . $this->cur_user->id));
		
		$this->displayWith('InviteregisterHTMLTemplate');
	}
}