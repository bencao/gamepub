<?php

if (!defined('SHAISHAI')) {
	exit(1);
}

class GroupeditpostHTMLTemplate extends GroupdesignHTMLTemplate
{

	function title()
	{
		return sprintf('编辑 %s ' . GROUP_NAME() . '公告', $this->cur_group->uname);
	}

	function getPage()
	{
		//右边导航的位置,从0开始: 公会主页, 公会成员, 编辑公会
		return 2;
	}
	
	function showContent()
	{
		$this->tu->showTitleBlock('编辑' . GROUP_NAME() . '', 'groups');
		
		$navs = new NavList_GroupEdit($this->cur_group);
        $this->tu->showTabNav($navs->lists(), $this->trimmed('action'));
		 
		if($this->trimmed('msg', false)) {
			if($this->trimmed('success', false))
				$this->element('div', 'success', $this->trimmed('msg'));
			else				
				$this->element('div', 'error', $this->trimmed('msg'));
		}
		
		$this->showFormContnet();
	}

	function showFormContnet()
	{
    	$this->elementStart('div');
    	$post = '';
    	if($this->args['post']!=null)
    		$post = $this->args['post'];
		$form = new GroupPostEditForm($this, $this->cur_group, $post);
		$form->show();
		$this->elementEnd('div');
	}
}

	
class GroupPostEditForm extends Form
{
	/**
	 * group for user to join
	 */

	var $group = null;
	var $post = null;

	/**
	 * Constructor
	 *
	 * @param Action     $out   output channel
	 * @param User_group $group group to join
	 */

	function __construct($out=null, $group=null, $post)
	{
		parent::__construct($out);

		$this->group = $group;
		$this->post = $post;
	}

 	function show()
    {
    	if($this->group){
	        $attributes = array('id' => $this->id(),
	            'class' => 'editing tagother',
	            'method' => 'post',
	            'action' => $this->action());
    	}else{
	        $attributes = array('id' => $this->id(),
	            'class' => 'editing tagother',
	            'method' => 'post',
	        	'style' => 'display:none',
	            'action' => $this->action());
    	}

        if (!empty($this->enctype)) {
            $attributes['enctype'] = $this->enctype;
        }
        $this->out->elementStart('form', $attributes);
        $this->out->elementStart('fieldset');
        $this->formLegend();
        $this->formData();
        $this->formActions();
        $this->sessionToken();
        $this->out->elementEnd('fieldset');
        $this->out->elementEnd('form');
    }
	
	/**
	 * ID of the form
	 *
	 * @return string ID of the form
	 */

	function id()
	{
		return 'form_grouppost_edit';
	}

	/**
	 * class of the form
	 *
	 * @return string of the form class
	 */

	function formClass()
	{
		return 'editing';
	}

	/**
	 * Action of the form
	 *
	 * @return string URL of the action
	 */

	function action()
	{
		return common_local_url('groupeditpost', array('id' =>$this->group->id));
	}

	/**
	 * Name of the form
	 *
	 * @return void
	 */

	function formLegend()
	{
		$this->out->element('legend', null, '创建或编辑' . GROUP_NAME() . '公告');
	}

	/**
	 * Data elements of the form
	 *
	 * @return void
	 */

	function formData()
	{
		$edit = false;
		if ($this->group) {
			$post = $this->group->post;
		} else {
			$post = '';
		}
		
		if(!$this->post){
			$this->post = $post;
		}
		 
		$this->out->elementStart('dl', array('class'=>'clearfix'));
		
    	
		
		$this->out->element('dt', null, GROUP_NAME() . '公告：');
		$this->out->elementStart('dd');		 
		$this->out->element('textarea', array('id' => 'post', 'class' => 'textarea376', 'type' => 'textarea',
    	   	'name' => 'post'), $this->post);
		$this->out->elementEnd('dd');	

	}

	/**
	 * Action elements
	 *
	 * @return void
	 */

	function formActions()
	{	
		$this->out->element('dt');
		$this->out->elementStart('dd');
		$this->out->element('input', array('class' => 'submit button76 green76', 'type' => 'submit', 'value' => '保存'));  
		$this->out->elementEnd('dd');
		$this->out->elementEnd('dl');
	}
}