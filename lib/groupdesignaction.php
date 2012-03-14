<?php
/**
 * Shaishai, the distributed microblogging tool
 *
 * Base class for actions that use the current user's design
 *
 * PHP version 5
 *
 * @category  Action
 * @package   ShaiShai
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Base class for actions that use a group's design
 *
 * Pages related to groups can be themed with a design.
 * This superclass returns that design.
 *
 * @category Action
 * @package  LShai
 *
 */
class GroupDesignAction extends ShaiAction {

    /** The group in question */
    var $cur_group = null;
    var $cur_group_design = null;
    var $is_group_admin;
    
    function prepare($args) {
    	if (! parent::prepare($args)) {return false;}
    	
        $id = $this->trimmed('id');
        
        if ($id) {
            $this->cur_group = User_group::staticGet('id', $id);
	        if (! $this->cur_group) {
	            $this->clientError('指定的' . GROUP_NAME() . '不存在', 404);
	            return false;
	        }
        } else {
            $this->clientError('指定的' . GROUP_NAME() . '不存在', 404);
            return false;
        }
        
        if(!$this->cur_group->validity){        	
            $this->clientError('指定的' . GROUP_NAME() . '处于无效状态', 404);
            return false;
        }
        
        $this->is_group_admin = $this->cur_user && $this->cur_group->hasAdmin($this->cur_user);
        
        $this->cur_group_design = $this->cur_group->getDesign();
    	
    	return true;
    }
     
    function handle($args)
    {
        parent::handle($args);
        $this->addPassVariable('cur_group', $this->cur_group);
        $this->addPassVariable('cur_group_design', $this->cur_group_design);
        $this->addPassVariable('is_group_admin', $this->is_group_admin);
    }
}
