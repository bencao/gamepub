<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Join a group
 *
 * PHP version 5
 * @category  Group
 * @package   ShaiShai
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Join a group
 *
 * This is the action for joining a group. It works more or less like the subscribe action
 * for users.
 *
 * @category Group
 * @package  ShaiShai
 */

class GroupjoinAction extends GroupdesignAction
{

    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}
        
        if (! $this->cur_group->isadvanced 
        	&& $this->cur_group->memberCount() > 99) {
        	$this->clientError('对不起，这个' . GROUP_NAME() . '是普通' . GROUP_NAME() . '，它已经达到人数上限，无法进入。', 403);
            return false;
        }

        if ($this->cur_group->hasMember($this->cur_user)) {
            $this->clientError('您已经是此' . GROUP_NAME() . '的成员。', 403);
            return false;
        }

        if ($this->cur_group->hasBlocked($this->cur_user)) {
            $this->clientError('您已经被这个' . GROUP_NAME() . '的管理员屏蔽。', 403);
            return false;
        }
        
        if ($this->cur_group->closed) {
            $this->clientError('此' . GROUP_NAME() . '入口已被管理员关闭。', 403);
            return false;
        }

        return true;
    }


    function handle($args)
    {
        parent::handle($args);
        
        $groupOwner = User::staticGet('id', $this->cur_group->ownerid);
        if(!$this->cur_user->isSubscribed($groupOwner)){
        	if($groupOwner->hasBlocked($this->cur_user)) {
        		$this->clientError('对不起，您被该' . GROUP_NAME() . '主列入黑名单，不能加入此' . GROUP_NAME() . '。', 403);
		            return;
        	}else {
		        $result = Subscription::subscribeTo($this->cur_user, $groupOwner);
		        if($result != true) {
		            $this->clientError('对不起，您未能成功关注' . GROUP_NAME() . '主，加入' . GROUP_NAME() . '失败。', 403);
		            return;
		        }
	        }
        }

        if (! $this->cur_group->addMember($this->cur_user)) {
            $this->serverError('因技术原因，无法加入' . GROUP_NAME() . '，请稍后再试');
            return;
        }
        
    	if ($this->boolean('ajax')) {
    		$this->view = new HTMLTemplate();
            $this->view->startHTML('text/xml;charset=utf-8');
            $this->view->elementStart('head');
            $this->view->element('title', null, sprintf('用户加入了' . GROUP_NAME() . ' %s',
                                                  $this->cur_group->nickname));
            $this->view->elementEnd('head');
            $this->view->elementStart('body');
            $this->view->element('input', array('id'=>'url', 
                                          'type'=>'hidden', 
                                          'value'=>common_path('group/' . $this->cur_group->id)));
            $this->view->elementEnd('body');
            $this->view->endHTML();
        } else {
            common_redirect(common_path('group/' . $this->cur_group->id . '/members'),
                            303);
        }
    }
}