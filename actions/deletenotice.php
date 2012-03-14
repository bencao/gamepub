<?php
/**
 * Shaishai, the distributed microblog
 *
 * Class for deleting a notice
 *
 * PHP version 5
 *
 * @category  Login
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Class for deleting a notice
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

require_once INSTALLDIR . '/lib/deleteaction.php';

class DeletenoticeAction extends DeleteAction
{
    function handle($args)
    {
        parent::handle($args);

        // can not delete flash notice
        if ($this->notice->content_type == 5) {
        	$this->showJsonResult(array('result' => 'false', 'msg' => '您不能删除小游戏的消息！'));
        	return false;
        }
        
        $this->notice->delete();
        
        //  deduct user 2 scores when notice is removed 
        User_grade::deductScore($this->notice->user_id, 2);
        
        if ($this->boolean('ajax')) {
        	$this->showJsonResult(array('result' => 'true', 'deleted' => $this->notice->id));
        } else {
        	common_redirect(common_path('home'), 303);
        }
    }
}