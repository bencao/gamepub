<?php

if (!defined('SHAISHAI')) { exit(1); }

class EdittaggroupAction extends ShaiAction
{
	function handle ($args)
	{
		parent::handle($args);
		
		$tid = $this->trimmed('tid');
		$tname = $this->trimmed('tname');
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ($this->arg('del')) {
				$oldTag = User_tag::staticGet('id', $tid);
				if ($oldTag && User_tag::deleteTags($this->cur_user->id, array($tid))) {
					$this->showJsonResult(array('result' => 'true', 'op' => 'delete', 'oldtag' => $oldTag->tag));
				} else {
					$this->showJsonResult(array('result' => 'false'));
				}
			} else if ($this->arg('edit')) {
				if ($tname == '全部') {
					$this->showJsonResult(array('result' => 'false', 'msg' => '不能命名为"全部"，与默认分组重名。'));
				} else if ($tname == '未分组') {
					$this->showJsonResult(array('result' => 'false', 'msg' => '不能命名为"未分组"，与默认分组重名。'));
				} else if ($tname == '' || mb_strlen($tname, 'utf-8') > 8) {
					$this->showJsonResult(array('result' => 'false', 'msg' => '分组名不能为空或超过8个字'));
				} else {
					$oldTag = User_tag::staticGet('id', $tid);
					if ($oldTag && User_tag::updateATag($this->cur_user->id, $tid, $tname)) {
						$tagLink = common_local_url($this->cur_user->uname . '/subscriptions?tag=' . $tname);
						$this->showJsonResult(array('result' => 'true', 'op' => 'edit', 'oldtag' => $oldTag->tag, 'tag' => $tname, 'link' => $tagLink));
					} else {
						$this->showJsonResult(array('result' => 'false', 'msg' => '相同名字的分组已存在'));
					}
				}
			} else if ($this->arg('create')) {
				if ($tname == '全部') {
					$this->showJsonResult(array('result' => 'false', 'msg' => '不能创建名为"全部"的分组'));
				} else if ($tname == '' || mb_strlen($tname, 'utf-8') > 8) {
					$this->showJsonResult(array('result' => 'false', 'msg' => '分组名不能为空或超过8个字'));
				} else {
					$tid = User_tag::addATag($this->cur_user->id, $tname);
					if ($tid) {
						$tagLink = common_local_url($this->cur_user->uname . '/subscriptions?tag=' . $tname);
						$this->showJsonResult(array('result' => 'true', 'op' => 'new', 'tid' => $tid, 'tag' => $tname, 'link' => $tagLink));
					} else {
						$this->showJsonResult(array('result' => 'false', 'msg' => '相同名字的分组已存在'));
					}
				}
			} else if ($this->arg('addall')) {
				$selected = $_POST['sus'];
				
//				// 获取所有我关注的人
//				$taggedIds = Tagtions::getMyTaggedIds($this->cur_user->id);
				
				$theTag = User_tag::staticGet('id', $tid);
				
				$subs = new Subscription();
				$subs->whereAdd('subscriber = ' . $this->cur_user->id);
				$subs->find();
				
				while ($subs->fetch()) {
					// 排除掉已经被tag的人
					if (in_array($subs->subscribed, $selected)) {
						// 为剩下的所有id添加tagtions记录
						Tagtions::addTag($this->cur_user->id, $subs->subscribed, $theTag->id);
					}
				}
				
				$this->showJsonResult(array('result' => 'true', 'tag' => $theTag->tag));
			}
		} else {
			$this->handleGet();
		}
		
	}
	
	function handleGet() {
		$this->view = TemplateFactory::get('HTMLTemplate');
        $this->view->startHTML('text/xml;charset=utf-8');
		$this->view->elementStart('div', array('class' => 'replace_div'));
        
        $tags = User_tag::getTagsByTagger($this->cur_user->id);
        
        $this->view->elementStart('ul', 'tag_group_edit');
        foreach ($tags as $t)
        {
        	$this->view->elementStart('li');
        	$this->view->elementStart('form', array('method' => 'post',
        				'class' => 'tag_group_edit_form',
        				'action' => common_path('ajax/edittaggroup')));
        	$this->view->elementStart('fieldset');
        	
        	$this->view->hidden('token', common_session_token());
        	$this->view->hidden('tid', $t['id']);
        	
        	$this->view->elementStart('p', 'readonly');
        	$this->view->element('label', array('class' => 'label'), $t['tag']);
        	$this->view->element('button', array('class' => 'toedit tgbtn'), '编辑');
        	$this->view->element('input', 
        		array('type' => 'submit', 'name' => 'delete', 'value' => '删除', 'class' => 'tgbtn'));
        	$this->view->elementEnd('p');
        	
        	$this->view->elementStart('p', 'editable');
        	$this->view->element('input', array('type' => 'text', 
        					'name' => 'tname', 'class' => 'text',
        					'value' => $t['tag']));
        	$this->view->element('input', 
        		array('type' => 'submit', 'name' => 'edit', 'value' => '确定', 'class' => 'tgbtn'));
        	$this->view->element('button', array('class' => 'tocancel tgbtn'), '取消');
        	$this->view->elementEnd('p');
        	
        	$this->view->elementEnd('fieldset');
        	$this->view->elementEnd('form');
        	$this->view->elementEnd('li');
        }
        $this->view->elementEnd('ul');
        
        $this->view->element('button', array('class' => 'append_nl'), '添加新组');
        
        $this->view->elementEnd('div');
        $this->view->endHTML();
	}
}

?>