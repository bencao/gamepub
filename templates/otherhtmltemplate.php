<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class OtherHTMLTemplate extends BasicHTMLTemplate
{
	function showBody()
    {
        $this->elementStart('body', array('token' => common_session_token(), 'anonymous' => $this->cur_user ? '0' : '1'));
        if (Event::handle('StartShowHeader', array($this))) {
            $this->showHeader();
            Event::handle('EndShowHeader', array($this));
        }
        
		$this->elementStart('div', array('id' => 'other_wrap'));
        $this->showCore();
        $this->elementEnd('div');
        
        if (Event::handle('StartShowFooter', array($this))) {
            $this->showFooter();
            Event::handle('EndShowFooter', array($this));
        }
        
        $this->showFloatBar();
        
        $this->showWaiter();
        
        $this->elementEnd('body');
    }
    
	function showFooter()
    {
        $this->elementStart('div', array('id' => 'footer'));
        $this->showFooterSpans();
        $this->showSecondaryNav();
        $this->elementEnd('div');
    }
    
    function othertitle() {}
    
	function showCore() {
		$this->elementStart('h2');
		$this->text($this->othertitle());
		$this->elementEnd('h2');
		$this->elementStart('div', 'body clearfix');
		$this->elementStart('div', array('class' => 'contents'));
		$this->showContent();
		$this->elementEnd('div');
		
    	$this->elementStart('div', array('id' => 'other_widgets'));
		$this->showRightsidebar();
		$this->elementEnd('div');
		$this->elementEnd('div');
	}
	
	function showContent() {
		
	}
	
	function showRightsidebar() {
		
	}
	
	function showStyleSheets() {
    	parent::showStyleSheets();
    	$this->cssLink('css/other.css','default','screen, projection');
    }
}
?>