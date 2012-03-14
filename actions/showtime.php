<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class ShowtimeAction extends ShaiAction
{
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
	
	function handle($args) {
		parent::handle($args);
		$this->displayWith('ShowtimeHTMLTemplate');
	}
	
}