<?php
/**
 * ShaiShai
 * Add a new group
 *
 * @category  Group
 */

if (!defined('SHAISHAI')) {
	exit(1);
}

/**
 * Add a new group
 *
 * This is the form for adding a new group
 *
 * @category Group
 */

class NewgamegroupAction extends ShaiAction
{
	function handle($args)
	{
		parent::handle($args);
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->trySave();
		} else {
			if ($this->_permit(false)) {
				$this->showForm();
			}
		}
	}
	
	function _permit($issubmit)
	{
		$user_grade = $this->cur_user->getUserGrade();
		
		if ($user_grade < 2){
        	$this->showForm('要创建一个游戏' . GROUP_NAME() . '，您必须达到2级。', $issubmit);
            return false;
        }
        
        $owned_group_num = $this->cur_user->getOwnedGameGroupsNum();
        if ($owned_group_num == 1 && $user_grade < 4){
        	$this->showForm('您已经拥有一个游戏' . GROUP_NAME() . '，需要达到4级才能创建第二个游戏' . GROUP_NAME() . '。', $issubmit);
        	return false;
        } 
        if ($owned_group_num == 2 && $user_grade < 6){
        	$this->showForm('您已经拥有两个游戏' . GROUP_NAME() . '，需要达到6级才能创建第三个游戏' . GROUP_NAME() . '。', $issubmit);
        	return false;
        }
        if ($owned_group_num == 3 && $user_grade < 8){
        	$this->showForm('您已经拥有三个游戏' . GROUP_NAME() . '，需要达到8级才能创建第四个游戏' . GROUP_NAME() . '。', $issubmit);
        	return false;
        }
        if ($owned_group_num > 3){
        	$this->showForm('您已经拥有四个游戏' . GROUP_NAME() . '，不能再创建新的游戏' . GROUP_NAME() . '。', $issubmit);
        	return false;
        }
		// user need grade 5 to create advanced group
		if ($this->trimmed('isadvanced') == true && $user_grade < 5){
			$this->showForm('您尚未拥有足够的等级来创建高级' . GROUP_NAME() . '。');
			return false;
		}
        
        return true;
	}

	function showForm($msg=null, $issubmit=true)
	{
		if ($this->boolean('ajax')) {
			$this->view = TemplateFactory::get('JsonTemplate');
			$this->view->init_document($this->paras);
			$this->view->show_json_objects(array('qualified'=>true));
			$this->view->end_document();
		}else {
			$this->addPassVariable('page_msg', $msg);
			$this->addPassVariable('issubmit', $issubmit);
			$this->displayWith('NewgamegroupHTMLTemplate');
		}
	}

	function trySave()
	{
		if (!$this->_permit(true)) {
			return;
		}
		
		$groupclass	 = 1;
		 
		$uname   	 = $this->trimmed('uname');
		$nickname    = $this->trimmed('nickname');
		$description = $this->trimmed('description');
		$location    = $this->trimmed('location');
		$category    = $this->trimmed('category');
		$catalog     = $this->trimmed('catalog');
		$creator_one = $this->trimmed('creator_one');
//		$creator_two = $this->trimmed('creator_two');
//		$creator_three = $this->trimmed('creator_three');
//		$creator_four = $this->trimmed('creator_four');
		if($this->trimmed('grouptype') == true){
			$grouptype = 1;
		}else{
			$grouptype = 0;
		}
		if($this->trimmed('isadvanced') == true){
			$isadvanced = 1;
		}else{
			$isadvanced = 0;
		}
		if($this->trimmed('closed') == true){
			$closed = 1;
		}else{
			$closed = 0;
		}
     

		if (mb_strlen($uname, 'utf-8')<1 || mb_strlen($uname, 'utf-8')>18 || ! User_group::validName($uname)) {
			$this->showForm('' . GROUP_NAME() . '名字在6个汉字或18个英文以内，支持大小写英文，数字和中文。');
			return;
		} else if (User_group::existUname($uname)) {
			$this->showForm('这个名字已被使用，请尝试其它名字。');
			return;
		} else if (!User_group::alloweduname($uname)) {
			$this->showForm('不是一个合法的名字');
			return;
		} else if (!is_null($nickname) && mb_strlen($nickname, 'utf-8') > 24) {
			$this->showForm('全名太长了(超过了24个字符)。');
			return;
		} else if (!is_null($location) && mb_strlen($location, 'utf-8') > 100) {
			$this->showForm('聚集地太长了(超过了100个字符) 。');
			return;
		} else if (mb_strlen($description, 'utf-8') < 1) {
			$this->showForm('' . GROUP_NAME() . '简介不能为空。');
			return;
		} else if (mb_strlen($description, 'utf-8') > 150) {
			$this->showForm('' . GROUP_NAME() . '简介太长了(超过了150个字符)。');
			return;
		} 
		if(!is_null($location)&&mb_strlen($location, 'utf-8') == 0)
			$location = null;			
		$validity = 0;
		if(mb_strlen($creator_one, 'utf-8')<1 /*||mb_strlen($creator_two, 'utf-8')<1 ||
		mb_strlen($creator_three, 'utf-8')<1 ||mb_strlen($creator_four, 'utf-8')<1*/){
			$this->showForm('' . GROUP_NAME() . '共创者的用户名不能为空。');
			return;
		}
		$user1 = User::staticGet('uname', $creator_one);
//		$user2 = User::staticGet('uname', $creator_two);
//		$user3 = User::staticGet('uname', $creator_three);
//		$user4 = User::staticGet('uname', $creator_four);
		if(!$user1){
			$this->showForm('用户名'. $creator_one .'不存在。');
			return;
		}
//		if(!$user2){
//			$this->showForm('用户名'. $creator_two .'不存在。');
//			return;
//		}
//		if(!$user3){
//			$this->showForm('用户名'. $creator_three .'不存在。');
//			return;
//		}
//		if(!$user4){
//			$this->showForm('用户名'. $creator_four .'不存在。');
//			return;
//		}
		
//		if($user1==$user2 || $user1==$user3 || $user1==$user4 || 
//			$user2==$user3 || $user2==$user4 || $user3==$user4){
//			$this->showForm('共创者必须为四个不同的注册用户。');
//			return;			
//		}
		
		if($user1->uname==$this->cur_user->uname){
			$this->showForm('自己不能作为共创者。');
			return;	
		}	
		
//		
//		$this->showForm('暂时关闭创建' . GROUP_NAME() . '。');
//		return;

		$this->cur_user->query('BEGIN');
		$group = User_group::saveNew(array(
			        	'uname' => $uname,
			        	'nickname' => $nickname,
			        	'description' => $description,
			        	'location' => $location,
					    'category' => '游戏',
					    'catalog' => '游戏',
        				'game_id' => $this->cur_user->game_id,
        				'game_server_id' => $this->cur_user->game_server_id,
			        	'grouptype' => $grouptype,
			        	'ownerid' => $this->cur_user->id,
			        	'isadvanced' => $isadvanced,
        				'groupclass' => $groupclass,
        				'closed' => $closed,
						'validity' => $validity
		));

		if (!$group) {
			$this->cur_user->query('ROLLBACK');
			$this->showForm('因技术原因无法创建' . GROUP_NAME() . '，请稍候再试。');
			return;
		}

		if (!$group->addMember($this->cur_user, true)) {
			$this->cur_user->query('ROLLBACK');
			$this->showForm('因技术原因无法添加' . GROUP_NAME() . '成员。');
			return;
		}

		if ($this->_sendInvite($group, $user1) === false /*|| $this->_sendInvite($group, $user2) === false
		|| $this->_sendInvite($group, $user3) === false || $this->_sendInvite($group, $user4) === false*/) {
			$this->cur_user->query('ROLLBACK');
			$this->showForm('因技术原因无法向' . GROUP_NAME() . '共创者发送邀请。');
			return;
		}

		$this->cur_user->query('COMMIT');

		common_redirect(common_path('groups/audit/ok'), 303);
		
	}

	function _sendInvite($group, $user){
		$inv_code = Group_invitation::saveNew($group->id, $user->id);
		if (!$inv_code) {
            return false;
        }

        // send a system message to the user who is invited
		$sysmsg_id =  System_message::create_guid(); 
		
        $contentFormat = '用户 %s 邀请您共同创建游戏'. GROUP_NAME() .' %s（游戏：' . $group->getGame()->name . '，  简介：' .
        	$group->description . '），请选择 %s 或者 %s';
        	
		$content = sprintf($contentFormat,
							$this->cur_user->nickname, 
							$group->nickname,
							'接受',
							'拒绝');
		
		$rendered = sprintf($contentFormat,
							common_user_linker($this->cur_user->id), 
							common_group_linker($group),
							GroupinviteAction::groupinv_acc_link($inv_code, $sysmsg_id, $group->id, $user->id),
        					GroupinviteAction::groupinv_rej_link($inv_code, $sysmsg_id, $group->id, $user->id));
        					
		$result = System_message::saveNew($user->id, $content, $rendered, 4, $sysmsg_id);
		
		return $result;
	}
}

