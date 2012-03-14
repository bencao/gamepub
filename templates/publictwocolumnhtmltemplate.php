<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class PublictwocolumnHTMLTemplate extends PublicbaseHTMLTemplate
{
	function showCore() {
		$this->showLeftNav();
		
		$this->elementStart('div', array('id' => 'public_contents', 'style' => 'width: 750px;'));
		$this->showContent();
		$this->elementEnd('div');
	}
	
	function showContent() {
		
	}
	
	function showScripts() {
    	parent::showScripts();
 
	    $this->script('js/lshai_player.js');
	    $this->script('js/lshai_relation.js');
    }
}
?>