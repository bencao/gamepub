<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Group main page
 *
 * @category Group
 * @package  ShaiShai
 */

class GroupdeletepostAction extends GroupAdminAction
{
    function handle($args)
    {
        parent::handle($args);
        
        $this->cur_group->updatePost('');
        
        if ($this->boolean('ajax')) {
        	$this->showJsonResult(array('result'=>'successful')); 					
		} else {
            common_redirect(common_path('group/' . $this->cur_group->id), 303);
    	}
    }

}