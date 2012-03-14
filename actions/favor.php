<?php
/**
 * Shaishai, the distributed microblog
 *
 * Favor action
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

require_once INSTALLDIR.'/lib/mail.php';

/**
 * Favor action
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class FavorAction extends ShaiAction
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
        
        $id     = $this->trimmed('nid');
    	if(!$id || !is_numeric($id)) {
            $this->clientError('您访问的链接错误.', 403);
            return;
        }
        $notice = Notice::staticGet($id);
	        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {           
	        $err = null;

	        //只需要获得名字就可以了, 相同名字保存在同一收藏夹下, 不同的名字新建一个收藏夹
        	$favegroupName = $this->trimmed('favorgroup');
        	$favegroupId = $this->trimmed('favorselect');

        	$favegroupId = 0;
        	//add commit
	        $faveGroups = Fave_group::getFaveGroup($this->cur_user->id, $favegroupName);
	        if (!empty($faveGroups)) {
				$favegroup = $faveGroups[0];
				$favegroupId = $favegroup->id;
	        } else {
	        	$favegroup = Fave_group::addNew($this->cur_user, $favegroupName);
	        	$favegroupId = $favegroup->id;
	        }
		    	        
	        if ($this->cur_user->hasFave($notice)) {
	        	$err = '这条消息已收藏!';
	            return;
	        }	
	        $fave = Fave::addNew($this->cur_user, $notice, $favegroupId);
	        if (!$fave) {
	        	$err = '无法创建收藏.';
	            return;
	        }
	        
	        // adjust user score, notice owner +3
	        User_grade::addScore($notice->user_id, 3);
	        
	        $this->notify($notice, $this->cur_user);
	        
	        //清理缓存
	        $this->cur_user->blowFavesCache();
	        if ($this->boolean('ajax')) {
				$stringer = new XMLStringer();
				$stringer->elementStart('li', array('class' => 'divfavor' , 
					'id' => 'notice_disfavor-' . $notice->id));
		        $stringer->elementStart('a', array('href' => common_path('notice/disfavor'), 'title' => '取消收藏', 'nid' => $notice->id));
		        $stringer->text('取消收藏');
		    	$stringer->elementEnd('a');
		    	$stringer->elementEnd('li');
		    	$this->showJsonResult(array('result' => 'true', 'html' => $stringer->getString()));
	        } else {
	            common_redirect(common_path($this->cur_user->uname . '/showfavorites'),
	                            303);
	        }
        } else {
        	$favegroups = Fave_group::getFaveGroup($this->cur_user->id);
        	
        	$stringer = new XMLStringer();
			
			$stringer->elementStart('div', 'dialog_body');
	
			$noticeOwnerProfile = Profile::staticGet('id', $notice->user_id);
	
			$favor = new FavorForm($stringer, $notice, $favegroups);
			$favor->show();
			$stringer->elementEnd('div');
		
            $this->showJsonResult(array('result' => 'true', 'html' => $stringer->getString()));;
        }
    }
    

    //某条消息被收藏时, 可以使用邮件通知, 或者通过系统通知.
    function notify($notice, $user)
    {
        $other = User::staticGet('id', $notice->user_id);
        if ($other && $other->id != $user->id) {
        	$otherProfile = $other->getProfile();
            if ($otherProfile->email && $otherProfile->emailnotifyfav) {
                mail_notify_fave($otherProfile, $user->getProfile(), $notice);
            }
            // XXX: notify by IM
            // XXX: notify by SMS
        }
    }
}