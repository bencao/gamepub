<?php

if (!defined('SHAISHAI')) {
	exit(1);
}

/**
 * Invite people to a group
 *
 * This is the form for inviting people to group
 *
 * @category Group
 */

class GroupbatchinviteAction extends GroupinviteAction
{

	function inviteHandle()
	{
		$ids = explode('-', $this->trimmed('batchid'));
		foreach($ids as $pid){
			$this->trySendInvite($pid);
		}

		if ($this->boolean('ajax')) {
			$this->showJsonResult(array('result'=>'successful'));
		} else {
			common_redirect(common_path('group/' . $this->cur_group->id . '/invitation'), 303);
		}
	}
}

