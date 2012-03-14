<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Edit the group
 *
 *
 * @category Group
 */
class GroupeditAction extends GroupAdminAction
{
    function handle($args)
    {
        parent::handle($args);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->trySave();
        } else {
            $this->showForm();
        }
    }

    function showForm($msg=null, $success=null)
    {
        $this->addPassVariable('group', $this->cur_group);
        $this->addPassVariable('msg', $msg);
        if ($success){
            $this->addPassVariable('success', $success);
        }else{
            $this->addPassVariable('success', false);
        }
        $this->displayWith('GroupeditHTMLTemplate');
    }

    function trySave()
    {
		$uname   	 = $this->trimmed('uname');
        $nickname    = $this->trimmed('nickname');
        $description = $this->trimmed('description');
        $location    = $this->trimmed('location');
        $backmusic	 = $this->trimmed('backmusic');
        if($this->cur_group->groupclass == 0){
	        $category    = $this->trimmed('category');
	        $catalog     = $this->trimmed('catalog');
        }
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

        if (!is_null($nickname) && mb_strlen($nickname, 'utf-8') > 24) {
            $this->showForm('全名太长了(超过了24个字符)。');
            return;
        }  else if (User_group::existUname($uname, $this->cur_group->uname)) {
			$this->showForm('这个名字已被使用，请尝试其它名字。');
			return;
		} else if (!User_group::alloweduname($uname)) {
			$this->showForm('不是一个合法的名字');
			return;
		} else if (mb_strlen($description, 'utf-8')<1) {
            $this->showForm('' . GROUP_NAME() . '简介可以方便游友们了解本' . GROUP_NAME() . '，不能为空。');
            return;
        } else if (!is_null($description) && mb_strlen($description, 'utf-8') > 150) {
            $this->showForm('简介太长了(超过了150个字符)。');
            return;
        } else if (!is_null($location) && mb_strlen($location, 'utf-8') > 100) {
            $this->showForm('聚集地太长了 (超过了100个字符)。');
            return;
        } else if (!is_null($backmusic) && (mb_strlen($backmusic, 'utf-8') > 0) &&
                   !Validate::uri($backmusic, array('allowed_schemes' => array('http', 'https')))){
            $this->showForm('背景音乐地址不合法。');  
            return; 	
        }
        if($this->cur_group->groupclass == 0){
	        if (mb_strlen($category, 'utf-8')<1 || (!is_null($category) && mb_strlen($category, 'utf-8') > 8)) {
	            $this->showForm('' . GROUP_NAME() . '类别不能为空，且在8个字以内。');
	            return;
	        } else if (mb_strlen($catalog, 'utf-8')<1 || (!is_null($catalog) && mb_strlen($catalog, 'utf-8') > 8)) {
	            $this->showForm('' . GROUP_NAME() . '子类别不能为空，且在8个字以内。');
	            return;
	        }
        }
        
        // user need grade 8 to create advanced group
        if ($isadvanced ==1 && $this->cur_user->getUserGrade()<8){
        	$this->showForm('您尚未拥有足够的财富来创建高级' . GROUP_NAME() . '。');
            return;
        }

        $this->cur_group->query('BEGIN');

        $orig = clone($this->cur_group);
        $this->cur_group->uname    = $uname;
        $this->cur_group->nickname    = $nickname;
        $this->cur_group->description = $description;
        $this->cur_group->location    = $location;
        $this->cur_group->backmusic   = $backmusic;
        if($this->cur_group->groupclass == 0){
	        $this->cur_group->category    = $category;
	        $this->cur_group->catalog     = $catalog;
        }
        // add there two ifs to fix the bug which results in error when no change
        if ($this->cur_group->grouptype != $grouptype){
            $this->cur_group->grouptype = $grouptype;
        }
        if ($this->cur_group->isadvanced != $isadvanced){
            $this->cur_group->isadvanced = $isadvanced;
        }
        if ($this->cur_group->closed != $closed) {
        	$this->cur_group->closed = $closed;
        }

        $result = $this->cur_group->update($orig);

        if (!$result) {
            common_log_db_error($this->cur_group, 'UPDATE', __FILE__);
            $this->serverError('不能更新' . GROUP_NAME() . '的信息。');
        }

        $this->cur_group->query('COMMIT');

        $this->showForm('信息已保存！', true);

    }

    function unameExists($uname)
    {
        $group = User_group::staticGet('uname', $uname);

        if (!empty($group) &&
            $group->id != $this->cur_group->id) {
            return true;
        }

        return false;
    }
}

