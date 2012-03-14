<?php
/**
 * Shaishai, the distributed microblog
 *
 * Disfavor action
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
 * Disfavor action
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class DisfavorAction extends ShaiAction
{ 
	var $disfavorView = null;
	
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
//        if (!common_current_user()) {
//            $this->clientError('您还没有登陆.');
//            return;
//        }
        $user = common_current_user();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            common_redirect(common_path($user->uname . '/showfavorites'));
            return;
        }
        $id     = $this->trimmed('nid');
    	if($id && !is_numeric($id)) {
            $this->clientError('您访问的链接错误.', 403);
            return;
        }
        $notice = Notice::staticGet($id);
        
        $fave            = new Fave();
        $fave->query('BEGIN');
        $fave->user_id   = $user->id;
        $fave->notice_id = $notice->id;
        if (!$fave->find(true)) {
            $this->clientError('此消息未被收藏!');
            return;
        }
        $favegroupid = $fave->favegroup_id;
        $result = $fave->delete();
        if (!$result) {
            common_log_db_error($fave, 'DELETE', __FILE__);
            $this->serverError('无法删除此收藏.');
            return;
        }
        
    	Notice_heat::addHeat($notice->id, -3);
		$fave->query('COMMIT');    	
    	
        $user->blowFavesCache();
        $favgroup = Fave_group::staticGet('id', $favegroupid);
    	$favgroup->blowFavesCache();
        
        if ($this->boolean('ajax')) {
        	$stringer = new XMLStringer();
            $stringer->elementStart('li', array('class' => 'favor' , 
							'id' => 'notice_favor-' . $id));
    		$stringer->elementStart('a', array('href' => common_path('notice/favor'),
                   'title' => '收藏', 'nid' => $notice->id));
            $stringer->text('收藏');
            $stringer->elementEnd('a');
    		$stringer->elementEnd('li');
   			
    		$this->showJsonResult(array('result' => 'true', 'html' => $stringer->getString()));
        } else {
            common_redirect(common_path($user->uname . '/showfavorites'),
                            303);
        }
    }
}