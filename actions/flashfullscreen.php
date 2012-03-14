<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class FlashfullscreenAction extends ShaiAction
{
	var $fid;
	
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
		$this->cache_allowed = false;
	}
	
	function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}
        
        $this->fid = $this->trimmed('fid');
        $this->flash = Flash::staticGet('id', $this->fid);
        
        if (! $this->flash) {
        	$this->clientError('请求的文件不存在');
        	return false;
        }
        return true;
    }
    
    function handle($args) {
    	parent::handle($args);
    	
    	$this->addPassVariable('flash', $this->flash);
    	$this->displayWith('FlashfullscreenHTMLTemplate');
    }
}