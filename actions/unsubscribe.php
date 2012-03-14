<?php
/*
 * 
 */

if (!defined('SHAISHAI')) { exit(1); }

class UnsubscribeAction extends ShaiAction
{

    function handle($args)
    {
        parent::handle($args);

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            common_redirect(common_path($this->cur_user->uname . '/subscriptions'));
            return;
        }
        
        $other_id = $this->arg('to');

        if (!$other_id) {
            $this->clientError('请求中缺少参数。');
            return;
        }
        
    	if ($this->cur_user->id == $other_id) {
        	$this->clientError('不能关注自己');
        	return;
        }

        $other = Profile::staticGet('id', $other_id);

        if (!$other_id) {
            $this->clientError('该用户不存在。');
            return;
        }

        $result = Subscription::unsubscribeTo($this->cur_user, $other);

        if ($result != true) {
            $this->clientError($result);
            return;
        }

        if ($this->boolean('ajax')) {
            $this->view = TemplateFactory::get('JsonTemplate');
        	$this->view->init_document($this->paras);
        	$this->view->show_json_objects(array('pid' => $other->id, 'action' => common_path('main/subscribe')));
        	$this->view->end_document();
        } else {
            common_redirect(common_path($this->cur_user->uname . '/subscriptions'),
                            303);
        }
    }
}
