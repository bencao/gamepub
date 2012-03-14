<?php

if (!defined('SHAISHAI')) { exit(1); }

class TagotherAction extends ShaiAction
{
    var $userBeingTag = null;
    var $error = null;

    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}

        $to = $this->trimmed('to');
        // is subscribe or only tagother operation?
        $this->issub = $this->trimmed('issub', '0');
        
        if (! $to) {
            $this->clientError('没有to参数。');
            return false;
        }

        $this->userBeingTag = Profile::staticGet('id', $to);

        if (!$this->userBeingTag) {
            $this->clientError('该用户不存在。');
            return false;
        }

        return true;
    }

    function handle($args)
    {
        parent::handle($args);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->saveTags();
        } else {
            $this->showGroupDialog();
        }
    }
    
    function saveTags() {
    	if (array_key_exists('usertags', $_POST)) {
        	$usertags = $_POST['usertags'];
        } else {
        	$usertags = array();
        }
        
        Tagtions::setTagsById($this->cur_user->id, $this->userBeingTag->id, $usertags);
        
        if (count($usertags) == 0) {
        	$tagsString = '(空)';
        } else {
        	$tagsString = '';
        	foreach ($usertags as $tag) {
        		$ut = User_tag::staticGet('id', $tag);
        		$tagsString .= '#' . $ut->tag . ' ';
        	}
        }
        
        $this->view = TemplateFactory::get('JsonTemplate');
		$this->view->init_document();

        $this->view->show_json_objects(array('result' => 'true', 'to' => $this->userBeingTag->id, 'tags' => $tagsString, 'msg' => $this->issub ? '关注成功' : '修改分组成功'));
		$this->view->end_document();
    }

    function showGroupDialog()
    {
    	$avilableTags = User_tag::getTagsByTagger($this->cur_user->id);
        
        $tags = Tagtions::getTags($this->cur_user->id, $this->userBeingTag->id);
        
//    	$this->view = TemplateFactory::get('BasicHTMLTemplate');
//        $this->view->startHTML('text/xml;charset=utf-8');

        $stringer = new XMLStringer();
        
        $stringer->elementStart('div', array('class' => 'dialog_body'));
        $stringer->elementStart('form', array('action' => common_path('main/tagother'), 
        			'method' => 'POST', 'id' => 'tagotherfor' . $this->userBeingTag->id,
        			'class' => 'tagother'));
        $stringer->elementStart('fieldset');
        $stringer->element('legend', null, '加入分组');
        $stringer->element('input', array('name' => 'token', 'type' => 'hidden', 'value' => common_session_token()));
        $stringer->element('p', null, '将' . $this->userBeingTag->nickname . '加入分组');
        $stringer->elementStart('ul', array('class' => 'clearfix checkboxes'));
        $cnt = 0;
        foreach ($avilableTags as $t) {
        	$stringer->elementStart('li');
        	if (in_array($t['tag'], $tags)) {
        		$stringer->element('input', array('type' => 'checkbox', 'name' => 'usertags[]', 
        			'tagged' => $this->userBeingTag->id, 'value' => $t['id'], 'class' => 'checkbox',
        			'id' => $this->userBeingTag->id . '_' . $t['id'], 'checked' => 'checked'));
        	} else {
        		$stringer->element('input', array('type' => 'checkbox', 'name' => 'usertags[]',
        			'tagged' => $this->userBeingTag->id, 'value' => $t['id'], 'class' => 'checkbox',
        			'id' => $this->userBeingTag->id . '_' . $t['id']));
        	}
        	$stringer->element('label', array('for' => $this->userBeingTag->id . '_' . $t['id']), $t['tag']);
        	$stringer->elementEnd('li');
        	$cnt ++;
        }
        $stringer->elementEnd('ul');
        $stringer->element('a', array('class' => 'create', 'href' => '#'), '+创建新分组');
       	$stringer->elementStart('div', array('class' => 'create_new', 'style' => 'display:none;'));
       	$stringer->element('input', array('class' => 'text200', 'name' => 'tname', 'type' => 'text'));
       	$stringer->element('a', array('url' => common_path('ajax/edittaggroup'), 'class' => 'tocreate', 'href' => '#'), '创建');
        $stringer->element('a', array('class' => 'tocancel', 'href' => '#'), '取消');
       	$stringer->elementEnd('div');
        
        $stringer->elementStart('div', 'op');
        $stringer->element('input', array('class' => 'confirm button60', 'type' => 'submit', 'value' => '确定'));
        $stringer->element('a', array('href' => '#', 'class' => 'cancel button60'), '不分组');
        $stringer->elementEnd('div');
        
        $stringer->element('input', array('name' => 'to', 'type' => 'hidden', 'value' => $this->userBeingTag->id));
        $stringer->element('input', array('name' => 'issub', 'type' => 'hidden', 'value' => $this->issub));
        
        $stringer->elementEnd('fieldset');
        $stringer->elementEnd('form');
        $stringer->elementEnd('div');
        
        $this->showJsonResult(array('html' => $stringer->getString()));
    }

}

