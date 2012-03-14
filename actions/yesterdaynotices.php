<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class YesterdaynoticesAction extends ShaiAction
{ 
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
	
	function handle($args) {
		parent::handle($args);
		
		$notice = Notice::getYesterdayNotices();
		
		$this->addPassVariable('notice', $notice);
		
		$this->displayWith('YesterdaynoticesHTMLTemplate');
	}
}