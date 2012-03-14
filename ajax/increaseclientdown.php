<?php

if (!defined('SHAISHAI')) { exit(1); }

class IncreaseclientdownAction extends ShaiAction
{
	var $client;
	var $url;
	
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
	
	function prepare($args)
	{
		if (! parent::prepare($args)) {return false;}
		$this->client = $this->trimmed('c');
		$this->url = $this->trimmed('url');
		return true;
	}
	
    function handle($args)
    {
    	parent::handle($args);
    	if(Variables::increaseIntValueByName($this->client . 'Downs'))
    		$this->showJsonResult(array('result' => 'true', 'url' => $this->url));
    	else
    		$this->showJsonResult(array('result' => 'false'));
    }
}

?>