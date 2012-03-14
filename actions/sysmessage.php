<?php
/**
 * Shaishai, the distributed microblog
 *
 * action handler for system message
 *
 * PHP version 5
 *
 * @category  Login
 * @package   Shaishai
 * @author    Andray ma <andray09@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * action handler for system message
 *
 * @category Personal
 * @package  Shaishai
 * @author    Andray Ma <andray09@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class SysmessageAction extends OwnerdesignAction
{
    var $page = null;
    
    /**
     * Sysmessage action is read only
     *
     * @param array $args other arguments
     *
     * @return boolean
     */

    function isReadOnly($args)
    {
         return true;
    }
	
    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}
        
        common_set_returnto($this->selfUrl());
        
        return true;
    }
	
    function handle($args)
    {
        parent::handle($args);

        $sysmes = $this->cur_user->getSysmes(($this->cur_page -1) * NOTICES_PER_PAGE,
                                          NOTICES_PER_PAGE + 1);
        $total = $this->cur_user->sysmesCount();     	                        		
		$this->addPassVariable('total', $total);                                  
        $this->addPassVariable('sysmes', $sysmes);
		$this->displayWith('SysmessageHTMLTemplate');
    }
}