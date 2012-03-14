<?php
/**
 * ShaiShai
 * Collect users' feedback(bug, suggestions or issues)
 *
 * @author  Andray
 * @category  Feedback
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR . '/lib/common.php';
require_once INSTALLDIR . '/extlib/DB.php';

/**
 * Collect users' feedback
 *
 * the form for entering/saving feedback
 *
 * @category Feedback
 */

class UserfeedbackAction extends ShaiAction
{

	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
	
    /**
     * Prepare to run
     */

    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}

//        if (!common_current_user()) {
//            $this->clientError('请您先登陆，再向我们提出您遇到的问题或宝贵的建议，我们处理后会通过邮件给您回复。');
//            return false;
//        }

        $this->type        = $this->trimmed('type');
//        $this->priority    = $this->trimmed('priority');
//        $this->category    = $this->trimmed('category');
		$this->email = $this->trimmed('email');
		if (! $this->email && $this->cur_user) {
			$this->email = $this->cur_user->getProfile()->email;
		}
//        $this->subject        = $this->trimmed('subject');
        $this->description = $this->trimmed('description');
        
        return true;
    }

    /**
     * Handle the request
     *
     * On GET, show the form. On POST, try to save the message.
     *
     * @param array $args unused
     *
     * @return void
     */

    function handle($args)
    {
        parent::handle($args);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->trySave();
        } else {
            $this->showForm();
        }
    }
    
    function showForm($msg=null, $success=false)
    {
        $this->view = TemplateFactory::get('UserfeedbackHTMLTemplate');
        $this->view->show($this->paras, $msg, $success);
//		$this->displayWith('UserfeedbackHTMLTemplate');
    }

    function trySave()
    {
//    	if ($this->subject == '' || mb_strlen($this->subject, 'utf-8') > 100) {
//            $this->showForm('题目/简单描述不能为空，且在100个字以内。');
//            return false;
//        } else 
        if (mb_strlen($this->description, 'utf-8') > 3000) {
            $this->showForm('详细描述太长了，尝试缩短它，然后再保存！');
            return false;
        }
        
        $fb = new Feedback();
        $fb->ptype = $this->type;
//        $fb->priority = $this->priority;
//        $fb->category = $this->category;
//        $fb->subject = 'GamePub用户反馈';
        $fb->email = $this->email;
        $fb->description = $this->description;
        if (! $this->is_anonymous) {
        	$fb->report_user_id = $this->cur_user->id;
        }
        $fb->created = common_sql_now();
        
        $fb->insert();
        
        if (! $this->is_anonymous) {
	        // update the score and grade of the user
	        User_grade::addScore($this->cur_user->id, 3);
        }
        
    	$this->showForm('提交成功！我们会在三个工作日内确认您遇到的问题。非常感谢您的宝贵意见。', true);
    }
    
	

}
