<?php
/**
 * Shaishai, the distributed microblog
 *
 * Rename fave group action
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
 * Rename fave group action
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class RenamefavorgroupAction extends ShaiAction
{ 
    /**
     * Class handler.
     *
     * @param array $args query arguments
     *
     * @return void
     */
    function handle($args)
    {
        parent::handle($args);

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            common_redirect(common_path($this->cur_user->uname . '/showfavorites'));
            return;
        }
        
        $id = $this->trimmed('id');
    	if($id && !is_numeric($id)) {
            $this->clientError('您访问的链接错误.', 403);
            return;
        }
        //提取ID
        $favegroup = Fave_group::staticGet($id);
    	if(!$favegroup) {
        	$this->clientError('您要删除的收藏夹不存在.');
        }
        
    	$fave_user = User::staticGet('id', $favegroup->user_id);
        if ($this->cur_user->id != $favegroup->user_id) {
        	$this->clientError('此用户的收藏夹设置为隐藏, 您不能重命名他的收藏夹。');
            return false;
        }

        $favegroupName = $this->trimmed('name');                
        Fave_group::renameFaveGroup($id, $favegroupName);
        
        if ($this->boolean('ajax')) {
//			$favorView = TemplateFactory::get('RenamefavorgroupHTMLTemplate');
//			$favorView->ajaxShowNotice($args, $favegroup, $favegroupName);
			$this->showJsonResult(array('name' => $favegroupName));
        } else {
            common_redirect(common_path($user->uname . '/showfavorites'), 303);
        }
    }
}