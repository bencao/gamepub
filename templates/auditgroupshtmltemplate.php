<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class AuditgroupsHTMLTemplate extends GroupsbaseHTMLTemplate
{
    function show($args = array()) {
		$this->groups_audit = $args['groups_audit'];
		$this->groups_apply = $args['groups_apply'];
		$this->newgroupok = $args['newgroupok'];
    	parent::show($args);
    }

	function title()
    {
        return "待审核" . GROUP_NAME();
    }

    function showContent()
    {  
    	$this->element('input', array('name' => 'newgamegroupok',
                                      'type' => 'hidden',
                                      'id' => 'newgamegroupok',
                                      'value' => $this->newgroupok));
    	$this->elementStart('h2');
    	$this->text('待审核' . GROUP_NAME());
    	$this->element('span', null, '-- 需要审核的' . GROUP_NAME());
    	$this->elementEnd('h2');


    	$this->showGroupSearchForm();
    	
    	$this->elementStart('dl', array('class' => 'grid-3'));
    	$this->element('dt', null, '待确认的新创建'  . GROUP_NAME());
    	$this->elementStart('dd');
    	$this->showAuditGroups();	
    	$this->elementEnd('dd');
    	$this->elementEnd('dl');     	
    	
    	$this->elementStart('dl', array('class' => 'grid-3'));
    	$this->element('dt', null, '待批准的新加入'  . GROUP_NAME());
    	$this->elementStart('dd');
    	$this->showApplyGroups();  	
    	$this->elementEnd('dd');
    	$this->elementEnd('dl');
    	
    }
    
    function showAuditGroups()
    {
    	if($this->groups_audit && $this->groups_audit->N > 0){
    		$gl = new AuditGroupsList($this->groups_audit, $this);
            $gl->show();
    	}else{
    	 	$this->tu->showEmptyListBlock('您没有待确认的' . GROUP_NAME() . '。');
    	}
    }

    function showApplyGroups()
    {
    	if($this->groups_apply && $this->groups_apply->N > 0) {
    		$gl = new ApplyGroupsList($this->groups_apply, $this);
            $cnt = $gl->show();
    	}else {
    	 	$this->tu->showEmptyListBlock('您没有待批准的' . GROUP_NAME() . '。');
    	}
    }
    
    function showScripts() {
		parent::showScripts();
		$this->script('js/lshai_auditgroup.js');
		$this->script('js/lshai_newgroupok.js');
	}
}

class AuditGroupsList extends GroupsList
{
	function show()
    {
    	$this->out->elementStart('ol', array('class' => 'clearfix'));
        while ($this->group->fetch()) {
            $this->showGroup(true);
        }
		$this->out->elementEnd('ol');
		$this->group->free();	
    }
    
    function showImg($logo)
    {    	
    	$this->out->elementStart('div', array('class' => 'avatar'));
    	$this->out->element('img', array('alt' => $this->group->nickname, 'src' =>$logo));
    	$this->out->elementEnd('div');
    }
    
    function showInfo()
    {
    	$description = $this->group->description;
    	if(strlen($description) > 63){
    		$description = common_cut_string($description, 60);
    		$description .= '...';
    	}
    	   		
    	$this->out->elementStart('p', array('class' => 'org'));
	    $groupinv = $this->group->getInvites();
	    $audit_num = $groupinv->N;
	    $invname = '';
	    while($groupinv->fetch()) {
    		$invname .= $groupinv->nickname;
    		$invname .= ',';	
    	}
    	$groupinv->free();
    	if($invname != ''){
    		$invname = substr($invname, 0, strlen($invname)-1);
    		$invname .= '尚未确认';
    	}
    		
    	$this->out->element('strong', array('title' => $invname), $audit_num . ' 个共创人尚未确认');
    	if ($this->group->isOwnedBy($this->out->cur_user))
	    	$this->out->element('a',array('class'=>'cancelaudit', 'gid'=>$this->group->id, 'href' => common_local_url('auditgroupcancel')), '(取消)');
    	$this->out->elementEnd('p');
    	$this->out->element('p', null, '简介：' . $description);
    }

    function showName()
    {
    	$this->out->element('strong', null, $this->group->nickname);
    }
}

class ApplyGroupsList extends GroupsList
{
	function showInfo()
    {
    	$description = $this->group->description;
    	if(strlen($description) > 63){
    		$description = common_cut_string($description, 60);
    		$description .= '...';
    	}
    	
    	$this->out->elementStart('p', array('class' => 'org'));
    	$this->out->element('strong', null, '申请待审批');
    	$this->out->elementEnd('p');
    	$this->out->element('p', null, '简介：' . $description);
    }
}