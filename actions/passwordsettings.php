<?php
/**
 * LShai, the distributed microblogging tool
 *
 * Change user password
 *
 * @category  Settings
 * @package   LShai
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR . '/lib/validatetool.php';

/**
 * Change password
 *
 * @category Settings
 * @package  LShai
 */

class PasswordsettingsAction extends SettingsAction
{
    
	function getViewName() {
		return 'PasswordsettingsHTMLTemplate';
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
        if ($this->_checkNewPassword()
        	&& $this->_checkOldPassword()
        	&& $this->_updatePassword()) {
        	$this->showForm('密码已保存。', true);
        } else {
        	$this->showForm($this->errorMessage, false);
        }
    }
    
    function _checkNewPassword() {
        $this->newpassword = $this->arg('newpassword');
        $confirm     = $this->arg('confirm');

        # Some validation
        if (! isValidPassword($this->newpassword)) {
            $this->errorMessage = '密码长度必须为5~64位之间。';
            return false;
        } else if ($this->newpassword != $confirm) {
            $this->errorMessage = '新密码与确认密码不匹配。';
            return false;
        }
        return true;
    }
    
    function _checkOldPassword() {
    	if ($this->cur_user_profile->password) {
            $oldpassword = $this->arg('oldpassword');

            $dbprofile = Profile::getByUNameAndPassword($this->cur_user->uname, $oldpassword);
            if ($dbprofile == false || 0 != strcmp(common_munge_password($oldpassword, $dbprofile->id),
	                        $dbprofile->password)) {
                $this->errorMessage = '旧密码不正确。';
                return false;
            }
        }
        return true;
    }
    
    function _updatePassword() {
    	$original = clone($this->cur_user_profile);

        $this->cur_user_profile->password = common_munge_password($this->newpassword, $this->cur_user->id);

        $val = $this->cur_user_profile->validate();
        if ($val !== true) {
            $this->errorMessage = '无效用户。';
            return false;
        }

        if (!$this->cur_user_profile->update($original)) {
            $this->errorMessage = '因技术原因，无法保存密码，请稍候再试。';
            return false;
        }
        return true;
    }
}
