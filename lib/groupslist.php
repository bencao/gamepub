<?php

/**
 * ShaiShai, the distributed microblogging tool
 *
 * Groups List for basic group template.
 *
 * PHP version 5
 *
 * @category  Widget
 * @package   ShaiShai
 * @author    AGun Chan
 */
class GroupsList extends Widget{
    /** Current group, group query. */
    var $group = null;

    function __construct($group, $out=null)
    {
        parent::__construct($out);

        $this->group = $group;
    }

    function show()
    {
    	$this->out->elementStart('ol', array('class' => 'clearfix'));
        while ($this->group->fetch()) {
            $this->showGroup();
        }
		$this->out->elementEnd('ol');
		$this->group->free();	
    }
    
    function showGroup($showid=false)
    {
        if ($showid) {
        	$this->out->elementStart('li', array('class' => 'private', 'id' => 'group-' . $this->group->id));
        } else {
        	$this->out->elementStart('li', array('class' => 'private'));
        }
        
    	$logo = ($this->group->stream_logo) ? $this->group->stream_logo : User_group::defaultLogo(GROUP_LOGO_STREAM_SIZE);
    	$this->showImg($logo);
    	$this->out->elementStart('div', array('class' => 'profile'));
    	$this->out->elementStart('p', array('class' => 'nickname'));
    	$this->showName();
    	if($this->group->grouptype){
    		$this->out->element('span', array('class' => 'private', 'title' => '需要申请才可加入'), '' . $this->group->memberCount() . '人');
    	}else{    		
    		$this->out->element('span', array('title' => '可直接加入'), '' . $this->group->memberCount() . '人');	
    	}
    	$this->out->elementEnd('p');
    	$this->showInfo();
    	$this->out->elementEnd('div');
    	$this->out->elementEnd('li');
    }
    
    function showName()
    {
    	$this->out->elementStart('a', array('title' => '访问'.$this->group->nickname.GROUP_NAME().'主页', 
    		'href' => common_path('group/' . $this->group->id)));
    	$this->out->element('strong', null, $this->group->nickname);
    	$this->out->elementEnd('a');
    }
    
    function showImg($logo)
    {    	
    	$this->out->elementStart('div', array('class' => 'avatar'));
    	$this->out->elementStart('a', array('href' => common_path('group/' . $this->group->id)));
    	$this->out->element('img', array('title' => '访问'.$this->group->nickname.GROUP_NAME().'主页', 'src' =>$logo));
    	$this->out->elementEnd('a');
    	$this->out->elementEnd('div');
    }
    
    function showInfo()
    {
    	if($this->group->groupclass == 1){
	    	$groupinfo = $this->group->getGame()->name;
	    	$groupinfo .=' - ';
	    	$groupinfo .= $this->group->getGameServer()->name;
	    	$this->out->element('p', array('class' => 'org'), $groupinfo);
    	}else{
	    	$groupinfo = $this->group->category;
	    	$groupinfo .=' - ';
	    	$groupinfo .= $this->group->catalog;
	    	$this->out->element('p', array('class' => 'org'), $groupinfo);
    	}
    	$description = $this->group->description;
    	if(strlen($description) > 63){
    		//$description = substr($description, 0, 60);
    		$description = common_cut_string($description, 60);
    		$description .= '...';
    	}
    	$this->out->element('p', null, $description);
    }
}