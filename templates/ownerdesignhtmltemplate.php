<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class OwnerdesignHTMLTemplate extends RightsidebarHTMLTemplate
{	
	var $owner_design;
	
	var $owner_game;
	
	var $owner_game_server;
	
	var $is_own;
	
	var $owner;
	
	function show($args) {
		$this->owner = $args['owner'];
		$this->owner_design = $args['owner_design'];
		$this->owner_game = $args['owner_game'];
		$this->owner_game_server = $args['owner_game_server'];
		$this->is_own = $args['is_own'];
		
		parent::show($args);	
	}
	
	function showUAStylesheets()
    {
    	parent::showUAStylesheets();
    	if ($this->owner_design && $this->owner_design->cssurl) {
    		$this->element('link', array('href' => $this->owner_design->cssurl . '?' . SHAISHAI_VERSION, 
    			'rel' => 'stylesheet', 'type' => 'text/css', 'media' => 'screen, projection, tv'));
    	}
    }
}