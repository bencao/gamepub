<?php
/**
 * LShai, the distributed microblogging tool
 *
 * Settings for email
 *
 * PHP version 5
 *
 * @category  Settings
 * @package   LShai
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR.'/lib/mail.php';
require_once INSTALLDIR . '/lib/validatetool.php';

/**
 * Settings for email
 *
 * @category Settings
 * @package  LShai
 *
 * @see      Widget
 */

class EmailsettingsAction extends SettingsAction
{
	function getViewName() {
		return 'EmailsettingsHTMLTemplate';
	}

    /**
     * Gets any existing email address confirmations we're waiting for
     *
     * @return Confirm_address Email address confirmation for user, or null
     */

    function getConfirmAddress()
    {
        $confirm = new Confirm_address();
        $confirm->user_id      = $this->cur_user->id;
        $confirm->address_type = 'email';
        if ($confirm->find(true)) {
            return $confirm->address;
        } else {
            return null;
        }
    }

    /**
     * Handle posts
     *
     * Since there are a lot of different options on the page, we
     * figure out what we're supposed to do based on which button was
     * pushed
     *
     * @return void
     */
    function handlePost()
    {
        if ($this->arg('save')) {
            $this->savePreferences();
        } else if ($this->arg('change')) {
            $this->addAddress();
        } else {
            $this->showForm('异常的表单提交。');
        }
    }

    /**
     * Save email preferences
     *
     * @return void
     */
    function savePreferences()
    {
        $emailnotifysub   = $this->boolean('emailnotifysub');
        $emailnotifyfav   = $this->boolean('emailnotifyfav');
        $emailnotifymsg   = $this->boolean('emailnotifymsg');
        $emailnotifynudge = $this->boolean('emailnotifynudge');
        $emailnotifyattn  = $this->boolean('emailnotifyattn');

        $this->cur_user_profile->query('BEGIN');

        $original = clone($this->cur_user_profile);

        $this->cur_user_profile->emailnotifysub   = $emailnotifysub;
        $this->cur_user_profile->emailnotifyfav   = $emailnotifyfav;
        $this->cur_user_profile->emailnotifymsg   = $emailnotifymsg;
        $this->cur_user_profile->emailnotifynudge = $emailnotifynudge;
        $this->cur_user_profile->emailnotifyattn  = $emailnotifyattn;

        $result = $this->cur_user_profile->update($original);

        if ($result === false) {
            common_log_db_error($user, 'UPDATE', __FILE__);
            $this->serverError('无法更新用户。');
            return;
        }

        $this->cur_user_profile->query('COMMIT');

        $this->showForm('偏好已保存。', true);
    }

    /**
     * Add the address passed in by the user
     *
     * @return void
     */

    function addAddress()
    {
        $email = strtolower($this->trimmed('email'));

        // Some validation

        if (! isValidEmail($email)) {
            $this->showForm('邮件地址不正确');
            return;
        }

        if ($this->cur_user_profile->email == $email) {
            $this->showForm('已经是您的Email地址了');
            return;
        }
        if (Profile::existEmail($email)) {
            $this->showForm('该Email地址已被其他用户使用');
            return;
        }
        
        $confirm = new Confirm_address();
        $confirm->address      = $email;
        $confirm->address_type = 'email';
        $confirm->user_id      = $this->cur_user->id;
        $confirm->code         = common_confirmation_code(64);

        $result = $confirm->insert();
        if ($result === false) {
            common_log_db_error($confirm, 'INSERT', __FILE__);
            $this->serverError('无法更新确认码');
            return;
        }
        
        mail_confirm_address($this->cur_user_profile, $confirm->code, $email);

        $msg = '确认码已发送至您指定的邮箱。请检查邮件，进行二次确认。';

        $this->showForm($msg, true);
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
        // 改变绑定的邮件时
        if ($this->trimmed('change') == 'true') {
        	$msg = '您的邮件地址已变更';
        	$success = true;
        }
        $this->addPassVariable('email_confirm_address', $this->getConfirmAddress());
        
        parent::showForm($msg, $success);
        
    }
}
