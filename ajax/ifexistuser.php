<?php

if (!defined('SHAISHAI')) { exit(1); }

class IfexistuserAction extends ShaiAction
{
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
	
	function nameExists($uname)
    {
        $other = User::staticGet('uname', $uname);
        return $other != null;
    }
    
    function handle($args)
    {
    	parent::handle($args);
    	
    	$username = $this->trimmed('uname');
    	
    	$this->view = TemplateFactory::get('JsonTemplate');
        $this->view->init_document($this->paras);
        $this->view->show_json_objects(! $this->nameExists($username));
        $this->view->end_document();
        
    	
    }
}

?>