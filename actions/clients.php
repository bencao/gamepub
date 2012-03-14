<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class ClientsAction extends ShaiAction
{
	var $client;
	
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
	
	function prepare($args)
	{
		if (! parent::prepare($args)) {return false;}
		$this->client = $this->trimmed('client');
			
		return true;
	}
	
	function handle($args)
	{
		parent::handle($args);
		
		if ($this->client) {
			if ($this->client == 'bookmark') {
				$this->displayWith('ClientsdetailbookmarkHTMLTemplate');
			} else {
				$this->addPassVariable('download_times', Variables::getValueByName($this->client . 'Downs'));
				$this->displayWith('Clientsdetail' . $this->client . 'HTMLTemplate');
			}
		} else {
			$this->displayWith('ClientsHTMLTemplate');
		}
	}
}