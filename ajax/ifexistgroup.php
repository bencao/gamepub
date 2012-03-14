<?php

if (!defined('SHAISHAI')) { exit(1); }

class IfexistgroupAction extends ShaiAction
{
	
	function nameExists($uname)
    {
        $other = User_group::staticGet('uname', $uname);
        return $other != null;
    }
    
    function handle($args)
    {
    	parent::handle($args);
    	
    	$uname = $this->trimmed('uname');
    	
    	$this->view = TemplateFactory::get('JsonTemplate');
        $this->view->init_document($this->paras);
        $this->view->show_json_objects(! $this->nameExists($uname));
        $this->view->end_document();
    	
    }
}

?>