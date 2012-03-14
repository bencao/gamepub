<?php
/*
    Action to subscribe an user
 */

if (!defined('SHAISHAI')) { exit(1); }

class SubscribeAction extends ShaiAction 
{
	/**
	 * an User Object Instance 
	 * which specify the other user who is now being subscribed
	 */
	var $userBeingSubscribed;
	
	function prepare($args)
	{
		if (! parent::prepare($args)) {return false;}
		// 校验
		
		// 不接受非 POST的请求
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            common_redirect(common_path($this->cur_user->uname . '/subscriptions'));
            return;
        }
        
		$other_id = $this->arg('subscribeto');
        
        if ($this->cur_user->id == $other_id) {
        	$this->clientError('不能关注自己');
        	return;
        } else {
        	$this->userBeingSubscribed = User::staticGet('id', $other_id);
        	if (! $this->userBeingSubscribed) {
            	$this->clientError('您不是本地用户。');
            	return;
        	}
        }
        
        return true;
	}
	
    function handle($args)
    {
        parent::handle($args);
        
        // 执行 update
        $result = Subscription::subscribeTo($this->cur_user, $this->userBeingSubscribed);
        
        if($result != true) {
            $this->clientError($result);
            return;
        }
        
        // 生成视图
        if ($this->arg('ajax')) {
            $this->view = TemplateFactory::get('JsonTemplate');
       	 	$this->view->init_document($this->paras);
        	$this->view->show_json_objects(array('pid' => $this->userBeingSubscribed->id, 
        			'tagotherurl' => common_path('main/tagother?id=' . $this->userBeingSubscribed->id),
        			'action' => common_path('main/unsubscribe')));
        	$this->view->end_document();
        } else {
            common_redirect(common_path($this->cur_user->uname . '/subscriptions'),
                            303);
        }
    }
    
}
?>
