<?php

if (!defined('SHAISHAI')) { exit(1); }

# You have 24 hours to claim your password

define('MAX_RECOVERY_TIME', 24 * 60 * 60);

require_once INSTALLDIR.'/lib/mail.php';

class RecoverpasswordAction extends ShaiAction
{
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}

    function handle($args)
    {
        parent::handle($args);
        
        $this->view = TemplateFactory::get('RecoverPasswordHTMLTemplate');
        
        if (common_current_user()) {
            $this->clientError('您已经登录！');
            return;
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->arg('recover')) {
                $this->recoverPassword();
            } else if ($this->arg('reset')) {
                $this->resetPassword();
            } else {
                $this->clientError('异常的表单提交操作。');
            }
        } else {
            if ($this->trimmed('code')) {
                $this->checkCode();
            } else {
                $this->prepareForm('recover');
            }
        }
    }

    function checkCode()
    {

        $code = $this->trimmed('code');
        $confirm = Confirm_address::staticGet('code', $code);

        if (!$confirm) {
            $this->clientError('恢复码无效。');
            return;
        }
        if ($confirm->address_type != 'recover') {
            $this->clientError('恢复码无效。');
            return;
        }

        $user = User::staticGet($confirm->user_id);

        if (!$user) {
            $this->serverError('恢复码无效。');
            return;
        }

        $touched = strtotime($confirm->modified);
        $email = $confirm->address;

        # Burn this code

        $result = $confirm->delete();

        if (!$result) {
            common_log_db_error($confirm, 'DELETE', __FILE__);
            $this->serverError('确认码无效。');
            return;
        }

        # These should be reaped, but for now we just check mod time
        # Note: it's still deleted; let's avoid a second attempt!

        if ((time() - $touched) > MAX_RECOVERY_TIME) {
            common_log(LOG_WARNING,
                       'Attempted redemption on recovery code ' .
                       'that is ' . $touched . ' seconds old. ');
            $this->clientError('确认码已过期，请重新发起找回密码请求。');
            return;
        }

        # If we used an outstanding confirmation to send the email,
        # it's been confirmed at this point.

        $profile = $user->getProfile();
        if (! $profile->email) {
            $orig = clone($profile);
            $profile->email = $email;
            $result = $profile->updateKeys($orig);
            if (!$result) {
                common_log_db_error($user, 'UPDATE', __FILE__);
                $this->serverError('无法用确认的邮件地址更新用户信息。');
                return;
            }
        }

        # Success!

        $this->setTempUser($user);
        
        $this->prepareForm('reset',null, array('recoverpass_sessiontoken' => common_session_token()));
    }

    function setTempUser(&$user)
    {
        common_ensure_session();
        $_SESSION['tempuser'] = $user->id;
    }

    function getTempUser()
    {
        common_ensure_session();
        $user_id = $_SESSION['tempuser'];
        if ($user_id) {
            $user = User::staticGet($user_id);
        }
        return $user;
    }

    function clearTempUser()
    {
        common_ensure_session();
        unset($_SESSION['tempuser']);
    }

    function recoverPassword()
    {
        $nore = $this->trimmed('unameoremail');
        if (!$nore) {
            $this->prepareForm('recover', '请输入邮件地址。');
            return;
        }

        $profile = Profile::staticGet('email', strtolower($nore));

        if (!$profile) {
            $profile = Profile::staticGet('uname', strtolower($nore));
        }

        # See if it's an unconfirmed email address

        $confirm_email = null;
        
        if (!$profile) {
            $confirm_email = Confirm_address::staticGet('address', strtolower($nore));
            if ($confirm_email && $confirm_email->address_type == 'email') {
                $profile = Profile::staticGet($confirm_email->user_id);
            }
        }

        if (!$profile) {
            $this->prepareForm('recover', '您输入的用户名或邮件不存在。');
            return;
        }

        # Try to get an unconfirmed email address if they used a user name
        if (! $profile->email && !$confirm_email) {
            $confirm_email = Confirm_address::staticGet('user_id', $profile->id);
            if ($confirm_email && $confirm_email->address_type != 'email') {
                # Skip non-email confirmations
                $confirm_email = null;
            }
            $this->prepareForm('recover', '没有指定电子邮箱， 无法找回密码。请与客服联系。客服QQ是1279106166。');
            return;
        }

        # Success! We have a valid user and a confirmed or unconfirmed email address

        $confirm = new Confirm_address();
        $confirm->code = common_confirmation_code(128);
        $confirm->address_type = 'recover';
        $confirm->user_id = $profile->id;
        $confirm->address = (isset($profile->email)) ? $profile->email : $confirm_email->address;

        if (!$confirm->insert()) {
            common_log_db_error($confirm, 'INSERT', __FILE__);
            $this->serverError('保存地址信息出错。');
            return;
        }

		mail_recover_password($profile, $confirm);

        $provider = mail_provider($profile->email);
        
        $this->prepareForm('sent', '找回密码所需要的信息已送达您的' . $provider['name'] . '邮箱。',
        			array('recoverpass_reset' => true, 'mail_link' => $provider['link']));
    }

    function resetPassword()
    {
        $user = $this->getTempUser();

        if (!$user) {
            $this->clientError('异常的密码重设请求。');
            return;
        }

        $newpassword = $this->trimmed('newpassword');
        $confirm = $this->trimmed('confirm');
        	
        if (!$newpassword || strlen($newpassword) < 6) {
            $this->prepareForm('reset', '密码必须为6位或是更长。');
            return;
        }
        if ($newpassword != $confirm) {
        	
            $this->prepareForm('reset', '密码与确认密码不一致。');
            return;
        }

        # OK, we're ready to go
        
        $profile = $user->getProfile();

        $original = clone($profile);

        $profile->password = common_munge_password($newpassword, $profile->id);

        if (!$profile->update($original)) {
            common_log_db_error($profile, 'UPDATE', __FILE__);
            $this->serverError('无法保存新密码。');
            return;
        }

        $this->clearTempUser();

        if (!common_set_user($profile->uname)) {
            $this->serverError('设置用户出错。');
            return;
        }

        $this->prepareForm('saved', '新密码已保存，您现在已成功登录。八秒后自动跳转至您的个人主页。', array('recoverpass_success' => true));
    }
    
	function prepareForm($mode,$msg=null,$extraargs = array())
	{	
		$paras = array_merge($this->paras, $extraargs);
		$paras['recoverpass_msg'] = $msg;
        $paras['recoverpass_mode'] = $mode;
        $this->view->show($paras);
	}
    
}
