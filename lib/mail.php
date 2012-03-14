<?php
/**
 * LShai, the distributed microblogging tool
 *
 * utilities for sending email
 *
 * PHP version 5
 *
 * @category  Mail
 * @package   LShai
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once 'Mail.php';
require_once "Mail/Queue.php";

/**
 * return the configured mail backend
 *
 * Uses the $config array to make a mail backend. Cached so it is safe to call
 * more than once.
 *
 * @return Mail backend
 */

function mail_backend($withqueue = false)
{
    static $queuebackend = null;
    static $backend = null;

    if ($withqueue) {
    	if (! $queuebackend) {
    		$db_options['type']       = 'db';
			$db_options['dsn']        = common_config('db', 'database');
			$db_options['mail_table'] = 'mail_queue';
	
			$mail_options['driver']   = common_config('mail', 'backend');
			$mail_options = array_merge($mail_options, (common_config('mail', 'params')) ?
	                                 common_config('mail', 'params') :
	                                 array());
			
	        $queuebackend = & Mail_Queue::factory($db_options, $mail_options);
    	 	if (PEAR::isError($queuebackend)) {
            	common_debug($queuebackend->getMessage());
        	}
    	}
    	return $queuebackend;
    } else {
    	if (! $backend) {
    		$backend = Mail::factory(common_config('mail', 'backend'),
                                 (common_config('mail', 'params')) ?
                                 common_config('mail', 'params') :
                                 array());
	    	if (PEAR::isError($backend)) {
	            common_debug($backend->getMessage());
	        }
    	}
    	return $backend;
    }
}

/**
 * send an email to one or more recipients
 *
 * @param array  $recipients array of strings with email addresses of recipients
 * @param array  $headers    array mapping strings to strings for email headers
 * @param string $body       body of the email
 *
 * @return boolean success flag
 */

function mail_send($recipients, $headers, $body, $inqueue=true)
{
	$headers['MIME-Version']= '1.0';
//	$headers['Sender'] = $headers['Return-Path'] = $headers['Errors-To'] = $headers['From'];
    $headers['Content-Type'] = 'text/html; charset=UTF-8;';
    
	if (common_config('mail', 'queue_enable') && $inqueue) {
		$backend = mail_backend(true);
		assert($backend); // throws an error if it's bad
		
		$sent = $backend->put($headers['From'], $recipients, $headers, $body);
	} else {
    	$backend = mail_backend();
    	assert($backend); // throws an error if it's bad
    	$sent = $backend->send($recipients, $headers, $body);
	}
    
    if (PEAR::isError($sent)) {
        common_log(LOG_ERR, 'Email error: ' . $sent->getMessage());
        return false;
    }
    return true;
}

// from mybb
function cleanup($string){
        $string = str_replace(array("\r", "\n", "\r\n"), "", $string);
        $string = trim($string);
        return $string;
}

function utf8_encode_c($string)
    {
        $charset = 'UTF-8';
        $encoded_string = $string;
        if(strtolower($charset) == 'utf-8' && preg_match('/[\x00-\x08\x0b\x0c\x0e-\x1f\x7f-\xff]/', $string))
        {
            // Define start delimimter, end delimiter and spacer
            $end = "?=";
            $start = "=?" . $charset . "?B?";
            $spacer = $end . ' ' . $start;

            // Determine length of encoded text within chunks and ensure length is even (should NOT use the my_strlen functions)
            $length = 75 - strlen($start) - strlen($end);
            $length = floor($length/4) * 4;

            // Encode the string and split it into chunks with spacers after each chunk
            $encoded_string = base64_encode($encoded_string);
            $encoded_string = chunk_split($encoded_string, $length, $spacer);

            // Remove trailing spacer and add start and end delimiters
            $spacer = preg_quote($spacer);
            $encoded_string = preg_replace("/" . $spacer . "$/", "", $encoded_string);
            $encoded_string = $start . $encoded_string . $end;
        }
        return cleanup($encoded_string);
    } 

/**
 * returns the configured mail domain
 *
 * Defaults to the server name.
 *
 * @return string mail domain, suitable for making email addresses.
 */

function mail_domain()
{
    $maildomain = common_config('mail', 'domain');
    if (!$maildomain) {
        $maildomain = common_config('site', 'server');
    }
    return $maildomain;
}

/**
 * returns a good address for sending email from this server
 *
 * Uses either the configured value or a faked-up value made
 * from the mail domain.
 *
 * @return string notify from address
 */

function mail_notify_from()
{
//	$domain = mail_domain();
	$from = common_config('mail', 'notifyfrom');
    $notifyfrom = utf8_encode_c(common_config('site', 'name')) .'<' . $from . '>';
    return $notifyfrom;
}

/**
 * sends email to a user
 *
 * @param User   &$user   user to send email to
 * @param string $subject subject of the email
 * @param string $body    body of the email
 * @param string $address optional specification of email address
 *
 * @return boolean success flag
 */

function mail_to_user(&$profile, $subject, $body, $address=null, $inqueue=true)
{
    if (!$address) {
        $address = $profile->email;
    }

    $recipients = $address;
    
    $headers['From']    = mail_notify_from();
    $headers['To']      = utf8_encode_c($profile->nickname) . ' <' . $address . '>';
    $headers['Subject'] = utf8_encode_c($subject);

    return mail_send($recipients, $headers, $body, $inqueue);
}

/**
 * Send an email to confirm a user's control of an email address
 *
 * @param User   $user     User claiming the email address
 * @param string $code     Confirmation code
 * @param string $uname uname of user
 * @param string $address  email address to confirm
 *
 * @see common_confirmation_code()
 *
 * @return success flag
 */

function mail_confirm_address($profile, $code, $address)
{
    $subject = sprintf("确认邮件地址");
                    
    $body = '
<html>
<head>
<title>确认邮件地址</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
</head>
<body>
<p style="margin-bottom:40px;">' . $profile->nickname .'，您好！</p>
<p>有人在' . common_config('site', 'name') . '输入了此邮件地址，</p>
<p>如果是您提交的，并且您想绑定此邮箱作为您在' . common_config('site', 'name') . '的注册邮箱，请点击以下链接：</p>
<p style="margin-left:50px;line-height:30px;"><a href="' . common_local_url('confirmaddress', null, array('code' => $code)) . '">确认地址</a></p>
<p>如果按钮无法点击，请手动复制以下地址到浏览器并访问：</p>
<p style="margin-left:50px;line-height:30px;">' . common_local_url('confirmaddress', null, array('code' => $code)) . '</p>  
<p>如果不是您提交的，请忽略此邮件。谢谢！</p>
<p>最后，请您把我们的电子邮件地址noreply@gamepub.cn存入您的地址簿。这样您才能确保收到其他玩家对您的关注和回复通知。</p>
<p>如果您对此邮件有任何疑问，请与我们联系，联系电话400-6758-365。</p>
<p style="margin:40px 0 0 32px;">此致</p>
<p>敬礼</p>
<p style="margin-left:150px;font-weight:bold;color:#666;">' . common_config('site', 'name') . '</p>
</body>
</html>
    ';
    return mail_to_user($profile, $subject, $body, $address, false);
}

/**
 * Send an email to confirm a user's qq ownership
 *
 * @param User   $profile     User claiming the email address
 * @param string $code     Confirmation code
 * @param string $uname uname of user
 * @param string $address  qq number to confirm
 *
 * @return success flag
 */

function mail_confirm_qq($profile, $code, $address)
{
    $subject = '确认绑定QQ和您的' . common_config('site', 'name') . '账号';
    
    $body = '
    <html>
<head>
<title>绑定您的QQ号</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
</head>
<body>
<p style="margin-bottom:40px;">' . $profile->nickname .'，您好！</p>
<p>有游友在' . common_config('site', 'name') . '申请将此QQ号与其账号进行绑定。</p>
<p>如果是您提交的，并且您想绑定此QQ号，请点击以下按钮：</p>
<p style="margin-left:50px;line-height:30px;"><a href="' . common_local_url('confirmaddress', null, array('code' => $code)) . '">确认绑定QQ</a></p>
<p>如果按钮无法点击，请手动复制以下地址到浏览器并访问：</p>
<p style="margin-left:50px;line-height:30px;">' . common_local_url('confirmaddress', null, array('code' => $code)) . '</p>
<p style="border:2px solid #3e3;background-color:#ccc;">小提示：绑定后您可以使用QQ号码来登录' . common_config('site', 'name') . '</p>
<p>如果不是您提交的，请忽略此邮件。谢谢！</p>
<p>最后，请您把我们的电子邮件地址noreply@gamepub.cn存入您的地址簿。这样您才能确保收到其他玩家对您的关注和回复通知。</p>
<p>如果您对此邮件有任何疑问，请与我们联系，联系电话400-6758-365。</p>
<p style="margin:40px 0 0 32px;">此致</p>
<p>敬礼</p>
<p style="margin-left:150px;font-weight:bold;color:#666;">' . common_config('site', 'name') . '</p>
</body>
</html>
    ';
    return mail_to_user($profile, $subject, $body, $address.'@qq.com', false);
}

/**
 * notify a user of subscription by a profile (remote or local)
 *
 * This function checks to see if the listenee has an email
 * address and wants subscription notices.
 *
 * @param User    $listenee user who's being subscribed to
 * @param Profile $other    profile of person who's listening
 *
 * @return void
 */

function mail_subscribe_notify($subscribed, $subscriber)
{
    if ($subscribed->email && $subscribed->emailnotifysub) {
    	
        $name = $subscribed->nickname;

        $long_name = ($subscriber->nickname) ?
          ($subscriber->nickname . ' (' . $subscriber->uname . ')') : $subscriber->uname;

        $recipients = $subscribed->email;

        $headers['From']    = mail_notify_from();
        $headers['To']      = utf8_encode_c($name) . ' <' . $subscribed->email . '>';
        $headers['Subject'] = utf8_encode_c(sprintf('%1$s开始在%2$s上关注您了',
                                      $subscriber->nickname,
                                      common_config('site', 'name')));

		$body = '
<html>
<head>
<title>游友关注提示</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
</head>
<body>
<p style="margin-bottom:40px;">您好！</p>
<p>' . $long_name . '开始在' . common_config('site', 'name') . '上关注您了。以下是TA的一些信息：</p>
<p style="margin-left:50px;">性别:' . ($subscriber->sex == 'M' ? '男' : '女') . '</p>'
. ($subscriber->location ? ('<p style="margin-left:50px;">所在地:' . $subscriber->location . '</p>') : '')
. ($subscriber->bio ? ('<p style="margin-left:50px;">个人简介:' . $subscriber->bio . '</p>') : '')
. '<p>您可以点击以下链接访问TA的个人主页:</p>
<p style="margin-left:50px;line-height:30px;"><a href="' . $subscriber->profileurl . '">访问' . $long_name . '的个人主页</a></p> 
<p>如果您不想再收到提示邮件，可点击以下链接修改您的邮件设置：</p>
<p style="margin-left:50px;line-height:30px;"><a href="' . common_local_url('emailsettings') . '">设置邮件选项</a></p>  
<p>最后，请您把我们的电子邮件地址noreply@gamepub.cn存入您的地址簿。这样您才能确保收到其他玩家对您的关注和回复通知。</p>
<p>如果您对此邮件有任何疑问，请与我们联系，联系电话400-6758-365。</p>
<p style="margin:40px 0 0 32px;">此致</p>
<p>敬礼</p>
<p style="margin-left:150px;font-weight:bold;color:#666;">' . common_config('site', 'name') . '</p>
</body>
</html>
		';
        mail_send($recipients, $headers, $body);
    }
}

/**
 * send a message to notify a user of a direct message (DM)
 *
 * This function checks to see if the recipient wants notification
 * of DMs and has a configured email address.
 *
 * @param Message $message message to notify about
 * @param User    $from    user sending message; default to sender
 * @param User    $to      user receiving message; default to recipient
 *
 * @return boolean success code
 */

function mail_notify_message($message, $from=null, $to=null)
{
    if (is_null($from)) {
        $from = User::staticGet('id', $message->from_user);
    }

    if (is_null($to)) {
        $to = User::staticGet('id', $message->to_user);
    }

    if (is_null($to->email) || !$to->emailnotifymsg) {
        return true;
    }
    $subject = sprintf('%s向您发送了悄悄话', $from->nickname);

    $body = '
<html>
<head>
<title>游友悄悄话提示</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
</head>
<body>
<p style="margin-bottom:40px;">您好！</p>
<p>' . $from->nickname . '(' . $from->uname . ')' . '开始在' . common_config('site', 'name') . '上向您发送了悄悄话：</p>
<p>------------------------------------------------------</p>
<p>' . $message->content . '</p>
<p>------------------------------------------------------</p>
<p>您可点击以下链接直接回复TA:</p>
<p style="margin-left:50px;line-height:30px;"><a href="' . common_local_url('inbox', array('uname' => $to->uname)) .'">回复' . $from->nickname . '</a></p>
<p>如果您不想再收到提示邮件，可点击以下链接修改您的邮件设置：</p>
<p style="margin-left:50px;line-height:30px;"><a href="' . common_local_url('emailsettings') . '">设置邮件选项</a></p>  
<p>最后，请您把我们的电子邮件地址noreply@gamepub.cn存入您的地址簿。这样您才能确保收到其他玩家对您的关注和回复通知。</p>
<p>如果您对此邮件有任何疑问，请与我们联系，联系电话400-6758-365。</p>
<p style="margin:40px 0 0 32px;">此致</p>
<p>敬礼</p>
<p style="margin-left:150px;font-weight:bold;color:#666;">' . common_config('site', 'name') . '</p>
</body>
</html>
	';
    
    return mail_to_user($to, $subject, $body);
}

/**
 * notify a user that one of their notices has been chosen as a 'fave'
 *
 * Doesn't check that the user has an email address nor if they
 * want to receive notification of faves. Maybe this happens higher
 * up the stack...?
 *
 * @param User   $other  The user whose notice was faved
 * @param User   $profile   The profile who faved the notice
 * @param Notice $notice The notice that was faved
 *
 * @return void
 */

function mail_notify_fave($other, $profile, $notice)
{

    $subject = $profile->nickname . '收藏了您的消息';

    $body = '
<html>
<head>
<title>游友收藏提示</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
</head>
<body>
<p style="margin-bottom:40px;">' . $other->nickname . '，您好！</p>
<p>' . $profile->nickname . '将您在' . common_config('site', 'name') . '上的消息加入了TA的收藏夹。</p>
<p>您被收藏的消息地址是:</p>
<p style="margin-left:50px;line-height:30px;"><a href="' . common_local_url('replylist', array('id' => $notice->reply_to)) . '">' . common_local_url('replylist', array('id' => $notice->reply_to)). '</a></p>
<p>消息内容为：</p>
<p>------------------------------------------------------</p>
<p>' . $notice->content . '</p>
<p>------------------------------------------------------</p>
<p>您也可以通过以下地址来查看TA的收藏:</p>
<p style="margin-left:50px;line-height:30px;"><a href="' . common_local_url('checkfavorites', array('uname' => $profile->uname)) . '">' . $profile->nickname . '的收藏夹</a></p>
<p></p>
<p>如果您不想再收到提示邮件，可点击以下链接修改您的邮件设置：</p>
<p style="margin-left:50px;line-height:30px;"><a href="' . common_local_url('emailsettings') . '">设置邮件选项</a></p>  
<p>最后，请您把我们的电子邮件地址noreply@gamepub.cn存入您的地址簿。这样您才能确保收到其他玩家对您的关注和回复通知。</p>
<p>如果您对此邮件有任何疑问，请与我们联系，联系电话400-6758-365。</p>
<p style="margin:40px 0 0 32px;">此致</p>
<p>敬礼</p>
<p style="margin-left:150px;font-weight:bold;color:#666;">' . common_config('site', 'name') . '</p>
</body>
</html>
	';
//    common_init_locale();
    mail_to_user($other, $subject, $body);
}

/**
 * notify a user that they have received an "attn:" message AKA "@-reply"
 *
 * @param User   $profile   The user who recevied the notice
 * @param Notice $notice The notice that was sent
 *
 * @return void
 */

function mail_notify_attn($other, $profile, $notice)
{
    if (!$profile->email || !$profile->emailnotifyattn) {
        return;
    }

    $subject = sprintf('%s在' . common_config('site', 'name') . '回复了您的消息', $other->nickname);

    $body = '
<html>
<head>
<title>游友回复提示</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
</head>
<body>
<p style="margin-bottom:40px;">您好！</p>
<p>' . $other->nickname . '回复了您在' . common_config('site', 'name') . '上的消息。</p>
<p>回复内容为：</p>
<p>-------------------华丽的分割线-----------------------------------</p>
<p style="margin-left:50px;line-height:30px;">' . $notice->content . '</p>
<p>-------------------华丽的分割线-----------------------------------</p>
<p>您可以点击以下链接来查看整个会话:</p>
<p style="margin-left:50px;line-height:30px;"><a href="' . common_local_url('conversation', array('id' => $notice->id)). '">查看会话</a></p>
<p></p>
<p>想查看您收到的所有回复，可访问:</p>
<p style="margin-left:50px;line-height:30px;"><a href="' . common_local_url('replies', array('uname' => $profile->uname)) . '">查看我的回复</a></p>
<p></p>
<p>如果您不想再收到提示邮件，可点击以下链接修改您的邮件设置：</p>
<p style="margin-left:50px;line-height:30px;"><a href="' . common_local_url('emailsettings') . '">设置邮件选项</a></p> 
<p>最后，请您把我们的电子邮件地址noreply@gamepub.cn存入您的地址簿。这样您才能确保收到其他玩家对您的关注和回复通知。</p>
<p>如果您对此邮件有任何疑问，请与我们联系，联系电话400-6758-365。</p>
<p style="margin:40px 0 0 32px;">此致</p>
<p>敬礼</p>
<p style="margin-left:150px;font-weight:bold;color:#666;">' . common_config('site', 'name') . '</p>
</body>
</html>
	';
    
//    common_init_locale();
    mail_to_user($profile, $subject, $body);
}

function mail_recover_password($profile, $confirm) {
	
	$body = '
<html>
<head>
<title>找回密码</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
</head>
<body>
<p style="margin-bottom:40px;">您好！</p>
<p>有一位用户在' . common_config('site', 'name') . '提交了重置此帐户密码的请求。</p>
<p>如果是您本人，并且您想确认此次操作，请访问以下链接：</p>
<p style="margin-left:50px;line-height:30px;"><a href="' . common_local_url('recoverpassword', null,
                                   array('code' => $confirm->code)) . '">找回密码</a></p>
<p>如果不是您本人，则可忽略此信息。</p>
<p>如果您对此邮件有任何疑问，请与我们联系，联系电话400-6758-365。</p>
<p style="margin:40px 0 0 32px;">此致</p>
<p>敬礼</p>
<p style="margin-left:150px;font-weight:bold;color:#666;">' . common_config('site', 'name') . '</p>
</body>
</html>
	';

    mail_to_user($profile, '找回您在'. common_config('site', 'name').'的密码', $body, $confirm->address, false);
}

function mail_provider($email) {
	$provider = array(
		'name' => '未知服务商',
		'link' => '#'
	);
	
	if (! $email) {
		return $provider;
	}
            
    preg_match('/^(?<username>[^@]*)@(?<domain>[^\.]*)\.(?<postfix>.*)/i', $email, $matches);
            
    $provider['name'] = strtolower($matches['domain']);
    if ($provider['name'] == 'qq') {
        $provider['name'] = '腾讯';
        $provider['link'] = "http://mail.qq.com";
    } else if ($provider['name'] == 'gmail') {
        $provider['name'] = 'google';
        $provider['link'] = "http://mail.google.com";
    } else if ($provider['name'] == 'hotmail' || $provider['name'] == 'live' || $provider['name'] == 'msn') {
        $provider['name'] = '微软';
        $provider['link'] = "http://mail.live.com";
    } else if ($provider['name'] == '163' || $provider['name'] == '126' || $provider['name'] == 'yeah') {
        $provider['name'] = '网易';
        $provider['link'] = "http://mail." . strtolower($matches['domain']) . "." . strtolower($matches['postfix']);
    } else if ($provider['name'] == 'iscas') {
		$provider['name'] = '中科院';
		$provider['link'] = 'http://webmails.iscas.ac.cn';
    } else {
        $provider['link'] = "http://mail." . strtolower($matches['domain']) . "." . strtolower($matches['postfix']);
    }
    return $provider;
}

function mail_send_invitation($email, $profile)
{
	if (Profile::existEmail($email)
		|| Invitation::existsInvitation($email)) {
		// 重复邀请检测，一是防止被成为垃圾邮件发送商，二是使被邀请用户体验好一点
		return;
	}
	
	// 为其生成一个新手卡号
	$randomSource = sprintf('%03d', rand(0, 999));
    $ps = Popularize_source::getSource('101', $randomSource);
    if (! $ps) {
        $ps = Popularize_source::newSource('101', $randomSource, '系统邮箱推广');
    }
    $time = common_sql_now();
    $randomCursor = rand(0, 99999);
    $r = new Recruit();
    $r->source_id = $ps->id;
    $r->fullcode = '101' . $randomSource . sprintf('%05d', $randomCursor ++);
    $r->created = $time;
    // 多请求同步进行时，插入失败几率是客观存在的
    $rid = $r->insert();
    while (! $rid) {
        $r->fullcode = '101' . $randomSource . sprintf('%05d', $randomCursor ++);
        $rid = $r->insert();
    }
    
	$invite = new Invitation();
    $invite->address = $email;
    $invite->address_type = 'email';
    $invite->code = common_confirmation_code(128);
    $invite->user_id = $profile->id;
    $invite->rcode = $r->fullcode;
    $invite->created = common_sql_now();

    if (! $invite->insert()) {
        common_log_db_error($invite, 'INSERT', __FILE__);
        return false;
    }
	
    $bestname = $profile->nickname;

    $sitename = common_config('site', 'name');
        
	$registerlink = common_local_url('register', null, array('mailcode' => $invite->code));
	
    $recipients = array($email);

    $headers['From'] = mail_notify_from();
    $headers['To'] = $email;
    $headers['Subject'] = utf8_encode_c(sprintf('%1$s邀请您加入%2$s', $bestname, $sitename));
    $body = '
<html>
<head>
<title>加入' . common_config('site', 'name') . '</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
</head>
<body>
<p style="margin-bottom:22px;">Hi，</p>
<p style="text-indent:28px;">我是' . $bestname . '，咱们好久没联系了。</p>
<p style="text-indent:28px;">最近我在玩一个网站叫' . $sitename . '，
在里边可以非常方便的分享我们在游戏中的点点滴滴，有值得纪念的图片、动听的音乐、搞笑的视频。</p>
<p style="text-indent:28px;">我用它来记录自己的游戏旅程，你是不是也来用用看呢？现在注册就能100%获得1Q币哦！</p>
<p style="text-indent:28px;">访问以下链接就可以注册:</p>
<p style="text-indent:56px;"><a href="' . $registerlink . '">注册' . $sitename . '</a></p>
<p>如果您对此邮件有任何疑问，请与我们联系，联系电话是400-6758-365。</p>
<p style="margin:15px 0 0 28px;">祝万事顺意</p>
<p style="margin-left:130px;">' . $bestname . '</p>
<p style="margin-left:130px;">' . date("Y-m-d") . '</p>
</body>
</html>
	';
    return mail_send($recipients, $headers, $body);	
}

function mail_resend_invitation($email, $profile_id, $code, $daysBefore)
{
	if (emailExists($email)) {
		return;
	}
	if ($daysBefore == 7) {
		$dayDesc = '上周';
	} else if ($daysBefore == 30) {
		$dayDesc = '上月';
	} else {
		$dayDesc = $daysBefore . '天前';
	}
	$profile = Profile::staticGet('id', $profile_id);
	
    $bestname = $profile->getBestName();

    $sitename = common_config('site', 'name');

	$registerlink = common_local_url('register', null, array('mailcode' => $code));
	
    $recipients = array($email);

    $headers['From'] = mail_notify_from();
    $headers['To'] = $email;
    $headers['Subject'] = utf8_encode_c(sprintf('%1$s邀请您加入%2$s', $bestname, $sitename));
    $body = '
<html>
<head>
<title>加入' . common_config('site', 'name') . '</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
</head>
<body>
<p style="margin-bottom:22px;">Hi，</p>
<p style="text-indent:28px;">我是' . $bestname . '，' . $dayDesc . '我给你发了一份' . $sitename . '的邀请，不知你有没有注意？</p>
<p style="text-indent:28px;">最近我在玩一个网站叫' . $sitename . '，
在里边可以非常方便的分享我们在游戏中的点点滴滴，有值得纪念的图片、动听的音乐、搞笑的视频。</p>
<p style="text-indent:28px;">我用它来记录自己的游戏旅程，你是不是也来用用看呢？</p>
<p style="text-indent:28px;">访问以下链接就可以注册:</p>
<p style="text-indent:56px;"><a href="' . $registerlink . '">注册' . $sitename . '</a></p>
<p style="text-indent:28px;>如果您对此邮件有任何疑问，请与我们联系，联系电话400-6758-365。</p>
<p style="margin:15px 0 0 28px;">祝万事顺意</p>
<p style="margin-left:130px;">' . $bestname . '</p>
<p style="margin-left:130px;">' . date("Y-m-d") . '</p>
</body>
</html>
	';
     return mail_send($recipients, $headers, $body);	
}