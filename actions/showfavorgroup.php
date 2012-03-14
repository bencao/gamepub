<?php
/**
 * Shaishai, the distributed microblog
 *
 * List of a favorites group
 *
 * PHP version 5
 *
 * @category  Personal
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * List of a favorites group
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ShowfavorgroupAction extends OwnerDesignAction
{	
	function isReadOnly($args)
    {
        return true;
    }
    
   /**
     * Prepare the object
     *
     * Check the input values and initialize the object.
     * Shows an error page on bad input.
     *
     * @param array $args $_REQUEST data
     *
     * @return boolean success flag
     */

    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}
        
        common_set_returnto($this->selfUrl());

        return true;
    }

    function handle($args)
    {
        parent::handle($args);
        
		$id = $this->trimmed('id');
   		if($id && !is_numeric($id)) {
            $this->clientError('您访问的链接错误.', 403);
            return;
        }
        
        $favegroup = Fave_group::staticGet('id', $id);
        if(!$favegroup) {
        	$this->clientError('您访问的收藏夹不存在.');
        }
        $fave_user = User::staticGet('id', $favegroup->user_id);
        $fave_profile = $fave_user->getProfile();
        if ($this->cur_user->id != $favegroup->user_id && $fave_profile->sharefavorites == 0) {
        	$this->clientError('此用户的收藏夹设置为隐藏, 您不能查看他的收藏夹。');
            return false;
        }
        
    	$notice = Fave::getFaveGroupById($id, 
					($this->cur_page-1)*NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1);				

	    if (empty($notice)) {
            $this->serverError('无法获得收藏的消息.');
            return;
        }
        $this->addPassVariable('id', $id);
        $this->addPassVariable('notice', $notice);

		if ($this->boolean('ajax')) {
	    	
	        if ($notice->N > 0) {
	       		$xs = new XMLStringer();
	        	$nl = new NoticeList($notice, $xs);
	       		$cnt = $nl->show();
	        	
	       		$xs1 = new XMLStringer();
	       		if ($cnt > NOTICES_PER_PAGE) {
		        	$xs1->element('a', array('href' => common_path('main/showfavorgroup/' . $id . '?page=' . ($this->cur_page + 1)),
		                                   'id' => 'notice_more', 'rel' => 'nofollow'));
	        	}
	        	
	        	$resultArray = array('result' => 'true', 'notices' => $xs->getString(), 'pg' => $xs1->getString()); 	
	        } else {
	        	$resultArray = array('result' => 'true', 'notices' => '');
	        }
	       	      	 	       	 	
	        $this->showJsonResult($resultArray);
		} else {
			common_redirect(common_path($this->cur_user->uname . '/showfavorites'));		
		}
    }
}