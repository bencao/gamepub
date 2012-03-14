<?php
if (!defined('SHAISHAI')) {
    exit(1);
}

class GametwocolumnHTMLTemplate extends GameBaseHTMLTemplate
{   
	function showCore() {
		$this->showLeftNav();
		
		$this->elementStart('div', array('id' => 'public_contents', 'style' => 'width: 750px;'));
		$this->showContent();
		$this->elementEnd('div');
	}
	
	function showContent() {
		
	}	
}

?>