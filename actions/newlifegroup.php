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

class NewlifegroupAction extends ShaiAction
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
        	$this->showForm('要创建一个生活' . GROUP_NAME() . '，您必须达到2级。', $issubmit);
            return false;
        }
        
        $owned_group_num = $this->cur_user->getOwnedLifeGroupsNum();
        if ($owned_group_num == 1 && $user_grade < 7){
        	$this->showForm('您已经拥有一个生活' . GROUP_NAME() . '，需要达到7级才能创建第二个生活' . GROUP_NAME() . '。', $issubmit);
            return false;
        }
        if ($owned_group_num > 1){
        	$this->showForm('您已经拥有两个个生活' . GROUP_NAME() . '，不能再创建新的生活' . GROUP_NAME() . '。', $issubmit);
            return false;
        }
		// user need grade 5 to create advanced group
		if ($this->trimmed('isadvanced') == true && $user_grade < 5){
			$this->showForm('您尚未拥有足够的等级来创建高级' . GROUP_NAME() . '。', $issubmit);
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
			$this->displayWith('NewlifegroupHTMLTemplate');
		}
	}
	
	function trySave()
	{
		if (!$this->_permit(true)) {
			return;
		}
		
		//groupclass: kind of group , 0 - life, 1 - game
		$groupclass = 0;	
		 
		$uname   	 = $this->trimmed('uname');
		$nickname    = $this->trimmed('nickname');
		$description = $this->trimmed('description');
		$location    = $this->trimmed('location');
		$category    = $this->trimmed('category');
		$catalog     = $this->trimmed('catalog');

		//grouptype: 0 - public, 1- private
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
		} else if (mb_strlen($description, 'utf-8')<1) {
			$this->showForm('' . GROUP_NAME() . '简介不能为空。');
			return;
		} else if (mb_strlen($description, 'utf-8') > 150) {
			$this->showForm('' . GROUP_NAME() . '简介太长了(超过了150个字符)。');
			return;
		}
		if(!is_null($location)&&mb_strlen($location, 'utf-8') == 0)
			$location = null;
		$validity = 1;

		$this->cur_user->query('BEGIN');
		
		$group = User_group::saveNew(array(
			        	'uname' => $uname,
			        	'nickname' => $nickname,
			        	'description' => $description,
			        	'location' => $location,
					    'category' => $category,
					    'catalog' => $catalog,
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
			$this->serverError('因技术原因无法创建' . GROUP_NAME() . '，请稍候再试。');
			return;
		}
		$group->blowLifeGroupsCache();
		if (!$group->addMember($this->cur_user, true)) {
			$this->cur_user->query('ROLLBACK');
			$this->serverError('因技术原因无法添加' . GROUP_NAME() . '成员。');
			return;
		}
		$this->cur_user->query('COMMIT');

		common_redirect(common_path('group/' . $group->id . '/invitation/ok'), 303);
		
	} 

}

