<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR . '/lib/mail.php';

class Missionstep4Action extends SettingsAction
{
	function prepare($args) {
		if (! parent::prepare($args)) {return false;}
		if ($this->cur_user_profile->getUserGrade() > 1) {
			$this->clientError('您的等级太高，无法做此任务了', 403);
			return false;
		}
		return true;
	}
	
	function getViewName() {
		return 'Missionstep4HTMLTemplate';
    }
    
	/**
     * Handle a post
     *
     * Validate input and save changes. Reload the form with a success
     * or error message.
     *
     * @return void
     */

    function handlePost()
    {
    	
    	// show result
    	
    	if ($this->checkStatus()) {
    		User_grade::awardForNewbieMission($this->cur_user_profile);
			
    		$this->addPassVariable('ok', 'finished');
    		
//    		$offsets = $this->cur_user->id - 100811;
//    		
//    		if ($offsets == 777) {
//    			$offsets ++;
//    		} else if ($offsets == 7777) {
//    			$offsets ++;
//    		} else if ($offsets == 77777) {
//    			$offsets ++;
//    		}
//    		
//			$valid = true;
//			
//			common_debug('USER_ID = ' . $this->cur_user->id);
//			common_debug('REMOTE_ADDR = ' . (array_key_exists('REMOTE_ADDR', $_SERVER) ? $_SERVER['REMOTE_ADDR'] : ''));
//			common_debug('HTTP_CLIENT_IP = ' . (array_key_exists('HTTP_CLIENT_IP', $_SERVER) ? $_SERVER['HTTP_CLIENT_IP'] : ''));
//			common_debug('HTTP_X_FORWARDED_FOR = ' . (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : ''));
//			common_debug('HTTP_VIA = ' . (array_key_exists('HTTP_VIA', $_SERVER) ? $_SERVER['HTTP_VIA'] : ''));
//			common_debug('HTTP_XROXY_CONNECTION = ' . (array_key_exists('HTTP_XROXY_CONNECTION', $_SERVER) ? $_SERVER['HTTP_XROXY_CONNECTION'] : ''));
//			common_debug('HTTP_PRAGMA = ' . (array_key_exists('HTTP_PRAGMA', $_SERVER) ? $_SERVER['HTTP_PRAGMA'] : ''));
//			
//			// cookie反重复注册
//			if ($this->trimmed('ah')) {
//				$valid = false;
//				$errorMsg = '您注册过于频繁，未能获得Q币';
//				common_debug('RejectReason = ' . 'Cookie');
//			}
//			
//			// 名称和email特征反重复注册
//			$nameFeature = array('/^lcl.*/', '/^zbs.*/', '/^zjbeat.*/', '/^[asdf123]+$/', '/^asd.*/', '/^zhengchao.*/',
//			 '/^shuaqb.*/', '/^liukexin.*/', '/^mixtape.*/', '/^mousec.*/', '/^yuxiao.*/', '/^esx.*/', '/^wzd.*/', '/liu/');
//			$nicknameFeature = array('/^wangyangjin.*/', '/^[asdf123]+$/', '/^asd.*/', '/^esx.*/', '/754021794/', '/996633/', '/liu/');
//			$emailFeature = array('/^zbs85.*/', '/^lcl.*/', '/^[^@]*@spam\.la$/', '/^zjb.*/', '/^zxa.*/', '/^xixik.*/', '/^www\..*/',
//				'/^oicpc.*/', '/^16464.*/', '/^hahas.*/', '/^a65751.*/', '/^[^@]*@bofthew.com/', '/^muronglanluo.*/', '/^b657518.*/', '/^ooh.*/', '/.*\.tk$/', '/.*sohu\.com$/');
//			
//			foreach ($nameFeature as $n) {
//				if (preg_match($n, $this->cur_user->uname)) {
//					$valid = false;
//					$errorMsg = '您注册过于频繁，未能获得Q币';
//					common_debug('RejectReason = ' . 'NameFeature');
//					break;
//				}
//			}
//			
//			if ($valid) {
//				if ($this->cur_user->nickname == $this->cur_user->uname) {
//					$valid = false;
//					$errorMsg = '您注册过于频繁，未能获得Q币';
//					common_debug('RejectReason = ' . 'NicknameUnameEqual');
//				}
//			}
//			
//			if ($valid) {
//				foreach ($nicknameFeature as $n) {
//					if (preg_match($n, $this->cur_user->nickname)) {
//						$valid = false;
//						$errorMsg = '您注册过于频繁，未能获得Q币';
//						common_debug('RejectReason = ' . 'NicknameFeature');
//						break;
//					}
//				}
//			}
//			
//			if ($valid) {
//				foreach ($emailFeature as $e) {
//					if (preg_match($e, $this->cur_user_profile->email)) {
//						$valid = false;
//						$errorMsg = '您注册过于频繁，未能获得Q币';
//						common_debug('RejectReason = ' . 'EmailFeature');
//						break;
//					}
//				}
//			}
//			
//			if ($valid) {
//				if (array_key_exists('HTTP_VIA', $_SERVER)
//					&& ! empty($_SERVER['HTTP_VIA'])) {
//					// 使用了代理
//					$valid = false;
//					$errorMsg = '您注册过于频繁，未能获得Q币';
//					common_debug('RejectReason = ' . 'UseProxy');
//				}
//			}
//			
//    		// 获取IP，防恶意注册
//	    	if (empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
//	    		$userip = $_SERVER['REMOTE_ADDR'];
//	    	} else {
//				$userip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
//				$userip = $userip[0];
//			}
//			
//			if ($valid) {
//				require_once INSTALLDIR . '/extlib/iplocation.class.php';
//				$ip_l = new ipLocation(INSTALLDIR . '/extlib/QQWry.Dat');
//				$address = $ip_l->getaddress($userip);
//		
//				$address["area1"] = iconv('GB2312','utf-8',$address["area1"]);
//				$address["area2"] = iconv('GB2312','utf-8',$address["area2"]);
//				$add=$address["area1"]." ".$address["area2"];
//				
//				common_debug('USER_ADDR = ' . $add);
//				
//				$valid = false;
//				$errorMsg = '您注册过于频繁，未能获得Q币';
//				
//				$provinces = array('广东', '江苏', '山东', '四川', '浙江', '辽宁', '河南', '湖北', '福建', '河北', '湖南', '上海', '北京', '黑龙江', '天津', '重庆', '江西', '山西', '安徽', '陕西', '海南', '云南', '甘肃', '内蒙古', '贵州', '新疆', '西藏', '青海', '广西', '宁夏', '吉林', '大学');
//				
//				foreach ($provinces as $p) {
//					if (preg_match('/' . $p . '/', $add)) {
//						$valid = true;
//						break;
//					}
//				}
//				
//				if (! $valid) {
//					common_debug('RejectReason = ' . 'AbroadIP');
//				}
//			}
//			
//			$digits = explode('.', $userip);
//			
//			if ($valid && ! empty($userip)) {
//				// 同IP子段反重复注册(192.168.0.1和192.168.0.2，只要出现过就是重复的)
//				$count = Qq_card_one_yuan::getCountByIPFeature($digits[0] . '.' . $digits[1] . '.' . $digits[2] . '.%');
//				if ($count > 0) {
//					$valid = false;
//					$errorMsg = '您注册过于频繁，未能获得Q币';
//					common_debug('RejectReason = ' . 'IPDuplicateLevel3');
//				}
//			}
//			
//			if ($valid && ! empty($userip)) {
//				// IP段智能反重复注册(192.168.0.1和192.168.1.1，出现四条认为重复)
//				$count = Qq_card_one_yuan::getCountByIPFeature($digits[0] . '.' . $digits[1] . '.%');
//				if ($count > 3) {
//					$valid = false;
//					$errorMsg = '您注册过于频繁，未能获得Q币';
//					common_debug('RejectReason = ' . 'IPDuplicateLevel2');
//				}
//			}
//			
//			if ($valid) {
//	    		$card = Qq_card_one_yuan::fetchACard($this->cur_user->id, $userip);
//	    		
//	    		if ($card) {
////		    		System_message::saveNew(array($this->cur_user->id), 
////						'您完成了新手任务，恭喜您获得1Q币的奖励！请到领奖页面充值，您的卡号是'. $card->card_no . '密码是' . $card->card_password . '。邀请好友加入，一起拿Q币。当超过10个好友接受你的邀请，还有额外的Q币赠送哦。具体活动说明请见GamePub活动公告。 ', 
////						'您完成了新手任务，恭喜您获得1Q币的奖励！请到<a href="http://www.jcard.cn/Charge/UCardDirectCharge.aspx?category=AATXVVQQBK" target="_blank">领奖页面</a>充值，您的卡号是'. $card->card_no . '密码是' . $card->card_password . '。<a href="' . common_path('main/invite') . '" target="_blank">邀请好友加入</a>，一起拿Q币。具体活动说明请见<a href="http://tieba.baidu.com/f?kz=833364714">《GamePub吧活动公告》</a>。', 
////					0);
//					
//					System_message::saveNew(array($this->cur_user->id), 
//						'您完成了新手任务，恭喜您获得1Q币的奖励！请到领奖页面充值，您的卡号是'. $card->card_no . '密码是' . $card->card_password . '。 ', 
//						'您完成了新手任务，恭喜您获得1Q币的奖励！请到<a href="http://www.jcard.cn/Charge/UCardDirectCharge.aspx?category=AATXVVQQBK" target="_blank">领奖页面</a>充值，您的卡号是'. $card->card_no . '密码是' . $card->card_password . '。', 
//					0);
//	    		} else {
//	    			Qq_card_waiting::saveNew($this->cur_user->id, $userip);
//	    			System_message::saveNew(array($this->cur_user->id), 
//						'您完成了新手任务，恭喜您获得1Q币的奖励！非常抱歉，因为Q币库存用尽，我们将在24小时内补充库存并为您补发奖励，敬请保持关注，谢谢！', 
//						'您完成了新手任务，恭喜您获得1Q币的奖励！非常抱歉，因为Q币库存用尽，我们将在24小时内补充库存并为您补发奖励，敬请保持关注，谢谢！', 
//					0);
//	    		}
//	    		parent::showForm('您是本次活动第' . $offsets . '名注册用户！Q币充值卡号已经通过系统消息发给您，注意查收！系统消息在“我的首页”的右侧查看。', true);
//			} else {
//				System_message::saveNew(array($this->cur_user->id), 
//					$errorMsg, 
//					$errorMsg, 
//				0);
//				parent::showForm('您是本次活动第' . $offsets . '名注册用户！' . $errorMsg, true);
//			}
			parent::showForm('恭喜您完成了新手任务，系统已将您直升二级，可以建立自己的' . GROUP_NAME() . '了！', true);
    	} else {
    		$this->addPassVariable('ok', false);
    		parent::showForm('您尚未达成任务完成条件');
    	}
    }
    
    function checkStatus() {
    	$isOk = true;
    	$html = '';
    	// do check
    	// 头像
    	$avatar = $this->cur_user_profile->getOriginalAvatar();
    	if (! $avatar) {
    		$isOk = false;
    		$this->addPassVariable('noavatar', true);
    	}
    	
    	// 简介
    	$bio = $this->cur_user_profile->bio;
    	if (! $bio) {
    		$isOk = false;
    		$this->addPassVariable('nobio', true);
    	}
    	
    	// 游戏组织
//    	$org = $this->cur_user->game_org;
//    	if (! $org) {
//    		$isOk = false;
//    		$this->addPassVariable('noorg', true);
//    	}
    	
    	// 居住地
    	$location = $this->cur_user_profile->location;
    	if (! $location) {
    		$isOk = false;
    		$this->addPassVariable('nolocation', true);
    	}
    	
    	// 确认邮件
    	$email = $this->cur_user_profile->email;
    	
    	if (! $email) {
    		$isOk = false;
    		$this->addPassVariable('noemail', true);
    		$mail = Confirm_address::getConfirmEmailByUserId($this->cur_user->id);
    		$this->addPassVariable('mail_link', mail_provider($mail));
    	}
    	
    	// 兴趣
    	$sdi = User_interest::getSelfDefinedInterestByUser($this->cur_user->id);
    	$cdi = User_interest::getClassifiedInterestByUser($this->cur_user->id);
    	
    	if (! $sdi && ! $cdi) {
    		$isOk = false;
    		$this->addPassVariable('nointerest', true);
    	}
    	
    	return $isOk;
    }
    
	/**
     * show the settings form
     *
     * @param string $msg     an extra message for the user
     * @param string $success good message or bad message?
     *
     * @return void
     */

    function showForm($msg=null, $success=false)
    {
    	if ($this->checkStatus()) {
    		$this->addPassVariable('ok', 'confirm');
    		parent::showForm('您已满足任务结束条件，点击“领取奖励”，马上获得大笔G币，直升二级！', true);
    	} else {
    		$this->addPassVariable('ok', false);
    		parent::showForm('您尚未达成任务完成条件');
    	}
    	
//    	$this->addPassVariable('interest_categories', Interest_category::getCategories());
//    	$this->addPassVariable('interest_currinterests', User_interest::getClassifiedInterestByUser($this->cur_user->id));
//    	$this->addPassVariable('interest_self_define', User_interest::getSelfDefinedInterestStringByUser($this->cur_user->id));
    	
//    	parent::showForm($msg, $success);
    }
    
    
}

?>