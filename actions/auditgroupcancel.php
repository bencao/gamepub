<?php

/**
 * Shaishai, the distributed microblog
 *
 * Cancel audit group
 *
 * PHP version 5
 *
 * @category  Group
 * @package   Shaishai
 * @author    AGun Chan <agunchan@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
	exit(1);
}


class AuditgroupcancelAction extends ShaiAction
{
	var $group;
	
    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}
        
    	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			$this->clientError('不接受非POST方法。', 403);
			return;
		}
		
    	$this->group = User_group::staticGet('id', $this->trimmed('id'));
		if (!$this->group) {
			$this->clientError('此' . GROUP_NAME() . '已被删除。', 403);
		}
		
		if ($this->group->ownerid != $this->cur_user->id ) {
			$this->clientError('您不是此' . GROUP_NAME() . '的创建者，不能取消审核。', 403);
		}

        return true;
    }

	function handle($args){
		parent::handle($args);
		
		$result = $this->group->destroy();
		if($this->boolean('ajax')) {
			$this->showJsonResult(array('result' => $result ? 'true' : 'false' ));
		} else {
			common_redirect(common_path('groups/audit'), 303);
		}
	}

	
}
