<?php

if (!defined('SHAISHAI')) {
	exit(1);
}

class RegisterWizardHTMLTemplate extends BasicHTMLTemplate
{
//    function showHeader() {
//    	$this->elementStart('div', array('id' => 'header'));
//    	$this->elementStart('div', 'h_logo');
//        $this->elementStart('address');
//        if (Event::handle('StartAddressData', array($this))) {
//            $this->element('img', array('width' => '121', 'height' => '69', 'src' => common_path('images/logo_blue.png'), 'id' => 'logo'));
//            Event::handle('EndAddressData', array($this));
//        }
//        $this->elementEnd('address');
//		
//		$this->elementEnd('div');
//        $this->element('div', 'cb');
//        $this->elementEnd('div');
//    }
    
    function showBody() {
    	$this->elementStart('body', array('token' => common_session_token(), 'anonymous' => $this->cur_user ? '0' : '1', 'class' => 'reg'));
        if (Event::handle('StartShowHeader', array($this))) {
            $this->showHeader();
            Event::handle('EndShowHeader', array($this));
        }
        $this->elementStart('div', array('id' => 'register_outer_wrap'));
        $this->element('div', array('id' => 'register_head'));
		$this->elementStart('div', array('id' => 'register_wrap'));
        $this->showCore();
        $this->elementEnd('div');
        $this->element('div', array('id' => 'register_foot'));
        $this->elementEnd('div');
        
        if (Event::handle('StartShowFooter', array($this))) {
//            $this->showFooter();
            Event::handle('EndShowFooter', array($this));
        }
        
        $this->elementEnd('body');
    }
    
	function showCore()
	{
		$this->showGreeting();
		$this->showContent();
	}
	
	function showGreeting() {
		$this->elementStart('div', 'greet');
		$this->elementStart('div', 'avatar');
		$this->element('img', array('src' => common_path('images/welcomeAnimal.png')));
		$this->elementEnd('div');
		$this->elementStart('p');
		$this->text($this->greeting());
		$this->element('span', 'pointer');
		$this->elementEnd('p');
		$this->elementEnd('div');
	}
	
	function greeting() { return ''; }
	
	function showContent() {}
	
//	function showFooter()
//    {
//        $this->elementStart('div', array('id' => 'footer', 'class' => 'footer_center'));
////        $this->showSecondaryNav();
//        $this->showLicenses();
//        $this->elementEnd('div');
//    }
    
    function showStylesheets() {
    	parent::showStylesheets();
    	$this->cssLink('css/settings.css','default','screen, projection');
    }
}