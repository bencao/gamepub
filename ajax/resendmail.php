<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR.'/lib/mail.php';

/**
 * New notice form
 *
 * @category Notice
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */
class ResendmailAction extends ShaiAction
{
    function handle($args)
    {

		$mail = $this->trimmed('newmail');
		
		if (empty($mail)) {
			$mail = Confirm_address::getConfirmEmailByUserId($this->cur_user->id);
			if (empty($mail)) {
				$this->showError('您的确认邮件记录已丢失，请用修改邮件并重新发送方式重发');
				return;
			}
		} else {
			$mail = strtolower($mail);
        	$u = Profile::staticGet('email', $mail);
        	if ($u) {
        		$this->showError('该邮件地址已被其他用户注册');
				return;
        	}
		}
		
     	$confirm = new Confirm_address();
        $confirm->code = common_confirmation_code(128);
        $confirm->user_id = $this->cur_user->id;
        $confirm->address = $mail;
        $confirm->address_type = 'email';

        $result = $confirm->insert();
        if (!$result) {
            common_log_db_error($confirm, 'INSERT', __FILE__);
            return false;
        }
		
		mail_confirm_address($this->cur_user->getProfile(), $confirm->code, $confirm->address);
		
		$this->view = TemplateFactory::get('JsonTemplate');
        $this->view->init_document($this->args);
        
    	$this->view->show_json_objects(array('result' => 'true'));
        
        $this->view->end_document();
		
    }
    
    function showError($errmsg) {
    	$this->view = TemplateFactory::get('JsonTemplate');
        $this->view->init_document($this->args);
        
    	$this->view->show_json_objects(array('result' => 'false', 'msg' => $errmsg));
        
        $this->view->end_document();
		return;
    }
}