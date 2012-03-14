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

//require_once INSTALLDIR . '/lib/deleteaction.php';

class DeletediscussAction extends ShaiAction
{ 
	var $discuss;
	var $root_notice;
	var $discuss_id;
    
	function prepare($args) {
		if (! parent::prepare($args)) {return false;}
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->clientError('不支持POST方法.');
            return false;
        }
        
		$this->discuss_id = $this->trimmed('id');
        $this->discuss = Discussion::staticGet('id', $this->discuss_id);
        if(! $this->discuss) {
            $this->clientError('没有此评论的相关记录。');
            return false;
        }
        $this->root_notice = Notice::staticGet('id', $this->discuss->notice_id);
        
        return true;
	}
	
    function handle($args)
    {
    	
        parent::handle($args);
        
        $this->discuss->query('BEGIN');
        
        if(! $this->discuss->delete()) {
        	$this->discuss->query('ROLLBACK');
        	$this->serverError('无法删除评论，有可能已经被删除。');
            return;
        }
        
        if (!empty($this->root_notice)) {
			//评论数减1
			$orig = clone($this->root_notice);
			if($this->root_notice->discussion_num > 0) { 
				$this->root_notice->discussion_num --;
			}
			$this->root_notice->update($orig);
		}
		$this->discuss->query('COMMIT');
        
        if($this->boolean('ajax')) {
        	$this->showJsonResult(array('did' => $this->discuss_id, 'result' => 'success'));
        } else {
			common_redirect(common_path('discussionlist/' . $this->root_notice->id), 303);
        }
    }
    
    
}