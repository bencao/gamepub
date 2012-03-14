<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Form for editing a group
 *
 * PHP version 5
 *
 * @category  Form
 * @package   ShaiShai
 */

if (!defined('SHAISHAI')) {
	exit(1);
}

require_once INSTALLDIR.'/lib/form.php';

/**
 * Form for editing a group
 *
 * @category Form
 * @package  ShaiShai
 * @see      UnsubscribeForm
 */

class GroupGameEditForm extends Form
{
	/**
	 * group for user to join
	 */

	var $group = null;
	var $user = null;

	/**
	 * Constructor
	 *
	 * @param Action     $out   output channel
	 * @param User_group $group group to join
	 */

	function __construct($out=null, $group=null, $user=null)
	{
		parent::__construct($out);

		$this->group = $group;
		$this->user = $user;
	}
	
    function show()
    {
    	if($this->group){
	        $attributes = array('id' => $this->id(),
	            'class' => 'editing',
	            'method' => 'post',
	            'action' => $this->action());
    	}else{
	        $attributes = array('id' => $this->id(),
	            'class' => 'editing tagother',
	            'method' => 'post',
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
		return 'form_gamegroup_add';
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
		if ($this->group) {
			return common_path('group/' . $this->group->id . '/edit');
		} else {
			return common_path('groups/game/new');
		}
	}

	/**
	 * Name of the form
	 *
	 * @return void
	 */

	function formLegend()
	{
		$this->out->element('legend', null, '创建或编辑一个' . GROUP_NAME() . '');
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
			$id 		 = $this->group->id;
			$uname    	 = $this->group->uname;
			$nickname    = $this->group->nickname;
			$homepage    = $this->group->homepage;
			$description = $this->group->description;
			$location    = $this->group->location;
			$category    = $this->group->category;
			$catalog     = $this->group->catalog;
			$grouptype   = $this->group->grouptype;
			$isadvanced  = $this->group->isadvanced;
			$closed      = $this->group->closed;
			$backmusic	 = $this->group->backmusic;
			$edit		 = true;
		} else {
			$id = '';
			$uname = '';
			$nickname = '';
			$homepage = '';
			$description = '';
			$location = '';
			$category = '';
			$catalog = '';
			$grouptype = 0;
			$isadvanced = 0;
			$closed = 0;
		}
		 
		$this->out->elementStart('dl', array('class'=>'clearfix'));
		
		if(!$edit){
			$this->out->element('dt',  null, '共创人：');
			$this->out->elementStart('dd', 'creators');		 
			$this->out->element('input', array('id' => 'creator_one', 'class' => 'text74', 'type' => 'text',
	    	   	'name' => 'creator_one', 'tip' => '请输入已注册用户名', 'value' => ($this->out->arg('creator_one')) ? $this->out->arg('creator_one') : ''));
//			$this->out->element('input', array('id' => 'creator_two', 'class' => 'text74', 'type' => 'text',
//	    	   	'name' => 'creator_two', 'tip' => '请输入已注册用户名', 'value' => ($this->out->arg('creator_two')) ? $this->out->arg('creator_two') : ''));
//			$this->out->element('input', array('id' => 'creator_three', 'class' => 'text74', 'type' => 'text',
//	    	   	'name' => 'creator_three','tip' =>'请输入已注册用户名', 'value' => ($this->out->arg('creator_three')) ? $this->out->arg('creator_three') : ''));
//			$this->out->element('input', array('id' => 'creator_four', 'class' => 'text74', 'type' => 'text',
//	    	   	'name' => 'creator_four','tip' =>'请输入已注册用户名', 'value' => ($this->out->arg('creator_four')) ? $this->out->arg('creator_four') : ''));
			$this->out->element('a', array('class' => 'help', 'target' => '_blank', 
				'title' => '为保证您的' . GROUP_NAME() . '不被他人恶意抢创，设立共创人机制。共创人是在游戏里与您同一个' . GROUP_NAME() . '的玩家，他们可以证明您拥有创建跟游戏中同名' . GROUP_NAME() . '的权利。',
	    	   	'href' => common_path('doc/help/groups')), '什么是共创人?');
			$this->out->elementEnd('dd');
		}
		
		if($edit){
			$server = $this->group->getGameServer()->name;
		}else{
			$server = $this->user->getGameServer()->name;
		}
		 
		$this->out->element('dt', null, '名字：');
		$this->out->elementStart('dd');
		$this->out->element('input', array('id' => 'uname', 'class' => 'text200', 'type' => 'text',
    	   	'name' => 'uname', 'tip' =>'只包含小写字母和数字，最少2个字符', 'value' => ($this->out->arg('uname')) ? $this->out->arg('uname') : $uname));
		
		$this->out->element('input', array('id' => 'server', 'type' => 'hidden', 'name' => 'server', 'value' => $server));
		
		$this->out->elementEnd('dd');
		
		$this->out->element('dt', null, '全名：');
		$this->out->elementStart('dd');		 
		$this->out->element('input', array('id' => 'nickname', 'class' => 'text200', 'type' => 'text',
    	   	'name' => 'nickname', 'tip' =>'2位以上字符', 'value' => ($this->out->arg('nickname')) ? $this->out->arg('nickname') : $nickname));
		$this->out->elementEnd('dd');
		
		$this->out->element('dt', null, '聚集地：');
		$this->out->elementStart('dd');		 
		$this->out->element('input', array('class' => 'text200', 'type' => 'text',
    	   	'name' => 'location', 'id' => 'location_game', 'tip' =>'请输入聚集地', 'value' => ($this->out->arg('location')) ? $this->out->arg('location') : $location));
		$this->out->elementEnd('dd');
		
		if($edit){
			$this->out->element('dt', null, '背景音乐：');
			$this->out->elementStart('dd');		 
			$this->out->element('input', array('class' => 'text200', 'type' => 'text',
	    	   	'name' => 'backmusic', 'id' => 'backmusic', 'tip' =>'请输入背景音乐', 'value' => ($this->out->arg('backmusic')) ? $this->out->arg('backmusic') : $backmusic));
			$this->out->elementEnd('dd');
		}
		
		$this->out->element('dt', null, '简介：');
		$this->out->elementStart('dd', 'description');		 
		$this->out->element('textarea', array('id' => 'description', 'class' => 'textarea376', 'type' => 'textarea',
    	   	'name' => 'description', 'tip' =>'2位以上150位以下字符'), ($this->out->arg('description')) ? $this->out->arg('description') : $description);
		$this->out->elementEnd('dd');
		
		$this->out->element('dt');		
		$this->out->elementStart('dd');	
		$this->out->elementStart('p');
		if(($this->out->arg('isadvanced')) ? $this->out->arg('isadvanced') : $isadvanced){
			if($this->user->getUserGrade()<5)
				$this->out->element('input', array('class' => 'checkbox', 'name' => 'isadvanced', 
					'type' => 'checkbox', 'tip' =>'', 'checked' => 'checked', 'disabled' => 'disabled' ));
			else{
				$this->out->element('input', array('class' => 'checkbox', 'name' => 'isadvanced', 
					'type' => 'checkbox', 'tip' =>'', 'checked' => 'checked' ));
			}
		}else{
			if($this->user->getUserGrade()<5)
				$this->out->element('input', array('id' => 'isadvanced', 'class' => 'checkbox', 'name' => 'isadvanced', 
					'type' => 'checkbox', 'tip' =>'', 'disabled' => 'disabled' ));
			else{
				$this->out->element('input', array('id' => 'isadvanced', 'class' => 'checkbox', 'name' => 'isadvanced', 
					'type' => 'checkbox', 'tip' =>'' ));
			}
		}
		$this->out->element('label', array('for' => 'isadvanced', ), '设置为高级' . GROUP_NAME() . '（需要用户等级达到5级）');
		$this->out->elementEnd('p');
		$this->out->elementStart('p');
		if(($this->out->arg('grouptype')) ? $this->out->arg('grouptype') : $grouptype){
			$this->out->element('input', array('id' => 'grouptype', 'class' => 'checkbox', 'tip' =>'', 'name' => 'grouptype', 'type' => 'checkbox', 'checked' => 'checked'));
		}else{
			$this->out->element('input', array('id' => 'grouptype', 'class' => 'checkbox', 'tip' =>'', 'name' => 'grouptype', 'type' => 'checkbox'));
		}
		$this->out->element('label', array('for' => 'grouptype'), '设置为私有' . GROUP_NAME());
		$this->out->elementEnd('p');
		$this->out->elementStart('p');
		if($edit){
			if(($this->out->arg('closed')) ? $this->out->arg('closed') : $closed){
				$this->out->element('input', array('id' => 'closed', 'class' => 'checkbox', 'tip' =>'', 'name' => 'closed', 'type' => 'checkbox', 'checked' => 'checked'));
			}else{
				$this->out->element('input', array('id' => 'closed', 'class' => 'checkbox', 'tip' =>'', 'name' => 'closed', 'type' => 'checkbox'));
			}
			$this->out->element('label', array('for' => 'closed'), '关闭' . GROUP_NAME() . '入口');
		}
		$this->out->elementEnd('p');
		$this->out->elementEnd('dd');

	}

	/**
	 * Action elements
	 *
	 * @return void
	 */

	function formActions()
	{			
		$name = '创建';
		if ($this->group){
			$name = '保存';
		}
		$this->out->element('dt');
		$this->out->elementStart('dd');
		$this->out->element('input', array('class' => 'submit button76 green76', 'type' => 'submit', 'value' => $name));  
		$this->out->elementEnd('dd');
		$this->out->elementEnd('dl');
	}
}

class GroupLifeEditForm extends Form
{
	/**
	 * group for user to join
	 */

	var $group = null;
	var $user = null;

	/**
	 * Constructor
	 *
	 * @param Action     $out   output channel
	 * @param User_group $group group to join
	 */

	function __construct($out=null, $group=null, $user=null)
	{
		parent::__construct($out);

		$this->group = $group;
		$this->user = $user;
	}
	
	/**
	 * ID of the form
	 *
	 * @return string ID of the form
	 */

	function id()
	{
		return 'form_lifegroup_add';
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
		if ($this->group) {
			return common_path('group/' . $this->group->id . '/edit');
		} else {
			return common_path('groups/life/new');
		}
	}

	/**
	 * Name of the form
	 *
	 * @return void
	 */

	function formLegend()
	{
		$this->out->element('legend', null, '创建或编辑一个' . GROUP_NAME() . '');
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
			$id 		 = $this->group->id;
			$uname    	 = $this->group->uname;
			$nickname    = $this->group->nickname;
			$homepage    = $this->group->homepage;
			$description = $this->group->description;
			$location    = $this->group->location;
			$backmusic	 = $this->group->backmusic;
			$category    = $this->group->category;
			$catalog     = $this->group->catalog;
			$grouptype   = $this->group->grouptype;
			$isadvanced  = $this->group->isadvanced;
			$closed      = $this->group->closed;
			$edit		 = true;
		} else {
			$id = '';
			$uname = '';
			$nickname = '';
			$homepage = '';
			$description = '';
			$location = '';
			$category = '';
			$catalog = '';
			$grouptype = 0;
			$isadvanced = 0;
			$closed = 0;
		}
		 
		$this->out->elementStart('dl', array('class'=>'clearfix'));
		 
		$this->out->element('dt', null, '名字：');
		$this->out->elementStart('dd');	
		$this->out->element('input', array('id' => 'uname', 'class' => 'text200', 'type' => 'text',
    	   	'name' => 'uname', 'tip' =>'只包含小写字母和数字，最少2个字符', 'value' => ($this->out->arg('uname')) ? $this->out->arg('uname') : $uname));
		
		$this->out->elementEnd('dd');
		
		$this->out->element('dt', null, '全名：');
		$this->out->elementStart('dd');		 
		$this->out->element('input', array('id' => 'nickname', 'class' => 'text200', 'type' => 'text',
    	   	'name' => 'nickname', 'tip' =>'2位以上字符', 'value' => ($this->out->arg('nickname')) ? $this->out->arg('nickname') : $nickname));
		$this->out->elementEnd('dd');
		
    	$this->out->element('dt', array('class' => 'life_catalog'), '' . GROUP_NAME() . '分类:');
		$this->out->elementStart('dd',  array('class' => 'life_catalog'));
		$cgy = ($this->out->arg('category')) ? $this->out->arg('category') : $category;
		$clg = ($this->out->arg('catalog')) ? $this->out->arg('catalog') : $catalog;
		$cates = $cgy? ($cgy. ' - '. $clg):'';

		$this->out->element('input', array('id' => 'category', 'class' => 'text200', 'name' => 'category' , 'type' => 'hidden',
						 'tip' =>'', 'value' => $cgy));
		$this->out->element('input', array('id' => 'catalog', 'class' => 'text200', 'name' => 'catalog' , 'type' => 'hidden',
						 'tip' =>'', 'value' => $clg));
		if($edit){
			$this->out->element('input', array('id' => 'cates', 'class' => 'text200', 'name' => 'cates' ,'type' => 'text',
							 'tip' =>'请选择分类', 'value' => $cates));
		}else{
			$this->out->element('input', array('id' => 'cates', 'class' => 'text200', 'name' => 'cates' ,'type' => 'text',
							 'tip' =>'请选择分类', 'value' => $cates));
		}
    	$this->out->elementEnd('dd');
		
		$this->out->element('dt', null, '聚集地：');
		$this->out->elementStart('dd');		 
		$this->out->element('input', array('class' => 'text200', 'type' => 'text',
    	   	'name' => 'location', 'id' => 'location_life', 'tip' =>'请输入聚集地', 'value' => ($this->out->arg('location')) ? $this->out->arg('location') : $location));
		$this->out->elementEnd('dd');
		
		if($edit){
			$this->out->element('dt', null, '背景音乐：');
			$this->out->elementStart('dd');		 
			$this->out->element('input', array('class' => 'text200', 'type' => 'text',
	    	   	'name' => 'backmusic', 'id' => 'backmusic', 'tip' =>'请输入背景音乐', 'value' => ($this->out->arg('backmusic')) ? $this->out->arg('backmusic') : $backmusic));
			$this->out->elementEnd('dd');
		}
		
		$this->out->element('dt', null, '公会简介：');
		$this->out->elementStart('dd', 'description');		 
		$this->out->element('textarea', array('id' => 'description', 'class' => 'textarea376', 'type' => 'textarea',
    	   	'tip' =>'', 'name' => 'description'), ($this->out->arg('description')) ? $this->out->arg('description') : $description);
		$this->out->elementEnd('dd');
		
		$this->out->element('dt');		
		$this->out->elementStart('dd');	
		$this->out->elementStart('p');
		if(($this->out->arg('isadvanced')) ? $this->out->arg('isadvanced') : $isadvanced){
			if($this->user->getUserGrade()<5)
				$this->out->element('input', array('class' => 'checkbox', 'name' => 'isadvanced', 
					'tip' =>'', 'type' => 'checkbox', 'checked' => 'checked', 'disabled' => 'disabled' ));
			else{
				$this->out->element('input', array('class' => 'checkbox', 'name' => 'isadvanced', 
					'tip' =>'', 'type' => 'checkbox', 'checked' => 'checked' ));
			}
		}else{
			if($this->user->getUserGrade()<5)
				$this->out->element('input', array('id' => 'isadvancedl', 'class' => 'checkbox', 'name' => 'isadvanced', 
					'tip' =>'', 'type' => 'checkbox', 'disabled' => 'disabled' ));
			else{
				$this->out->element('input', array('id' => 'isadvancedl', 'class' => 'checkbox', 'name' => 'isadvanced', 
					'tip' =>'', 'type' => 'checkbox' ));
			}
		}
		$this->out->element('label', array('for' => 'isadvancedl'), '设置为高级' . GROUP_NAME() . '（需要用户等级达到5级）');
		$this->out->elementEnd('p');
		$this->out->elementStart('p');
		if(($this->out->arg('grouptype')) ? $this->out->arg('grouptype') : $grouptype){
			$this->out->element('input', array('id' => 'grouptypel', 'class' => 'checkbox', 'name' => 'grouptype', 'type' => 'checkbox', 'tip' =>'', 'checked' => 'checked'));
		}else{
			$this->out->element('input', array('id' => 'grouptypel', 'class' => 'checkbox', 'name' => 'grouptype', 'tip' =>'', 'type' => 'checkbox'));
		}
		$this->out->element('label', array('for' => 'grouptypel'), '设置为私有' . GROUP_NAME());
		$this->out->elementEnd('p');
		$this->out->elementStart('p');
		if($edit){
			if(($this->out->arg('closed')) ? $this->out->arg('closed') : $closed){
				$this->out->element('input', array('id' => 'closedl', 'class' => 'checkbox', 'name' => 'closed', 'type' => 'checkbox', 'tip' =>'', 'checked' => 'checked'));
			}else{
				$this->out->element('input', array('id' => 'closedl', 'class' => 'checkbox', 'name' => 'closed', 'tip' =>'', 'type' => 'checkbox'));
			}
			$this->out->element('label', array('for' => 'closedl'), '关闭' . GROUP_NAME() . '入口');
		}
		$this->out->elementEnd('p');
		$this->out->elementEnd('dd');

	}

	/**
	 * Action elements
	 *
	 * @return void
	 */

	function formActions()
	{	
		$name = '创建';
		if ($this->group){
			$name = '保存';
		}
		$this->out->element('dt');
		$this->out->elementStart('dd');
		$this->out->element('input', array('class' => 'submit button76 green76', 'type' => 'submit', 'value' => $name));  
		$this->out->elementEnd('dd');
		$this->out->elementEnd('dl');
	}
}
