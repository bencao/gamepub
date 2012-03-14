<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class RightsidebarHTMLTemplate extends BasicHTMLTemplate
{
	function showCore() {
//		$this->elementStart('div', 'wrap_r');
//		$this->elementStart('div', 'wrap_l');
		$this->elementStart('div', array('id' => 'contents', 'class' => 'clearfix rounded5l'));
		$this->showContent();
		$this->elementEnd('div');
		
    	$this->elementStart('div', array('id' => 'widgets'));
		$this->showRightside();
		$this->elementEnd('div');
		
//		$this->elementEnd('div');
//		$this->elementEnd('div');
	}
	
	function showContent() {
		
	}
	
	
	function showRightside() {
		$this->showRightsidebar();
		$this->showSections();
	}
	
	function showRightsidebar() {
		
	}
	
	function showSections(){
		
	}
}
?>