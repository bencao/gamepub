<?php
/**
 * Shaishai, the distributed microblog
 *
 * Class for deleting a fave group
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
 * Class for deleting a fave group
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */


class DeletefavorgroupAction extends ShaiAction
{ 
    var $user         = null;
    
    function handle($args)
    {
        parent::handle($args);
        
        $id     = $this->trimmed('id');
    	if($id && !is_numeric($id)) {
            $this->clientError('您访问的链接错误.', 403);
            return;
        }
        $favegroup = Fave_group::staticGet($id);
    	if(!$favegroup) {
        	$this->clientError('您要删除的收藏夹不存在.');
        	return false;
        }
        
        if (trim($favegroup->name) == '我的收藏') {
        	$this->clientError('无法删除默认收藏夹');
        	return false;
        }
        
    	$fave_user = User::staticGet('id', $favegroup->user_id);
        if ($this->cur_user->id != $favegroup->user_id) {
        	$this->clientError('您不能删除他人的收藏夹。');
            return false;
        }
        
        Fave_group::deleteFaveGroup($id);                
        $fave_user->blowFavesCache();
       
        //整个刷新页面
        common_redirect(common_path($this->cur_user->uname . '/showfavorites'));
    }
}