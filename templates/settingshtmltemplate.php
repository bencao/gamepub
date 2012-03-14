<?php


if (!defined('SHAISHAI')) {
    exit(1);
}

class SettingsHTMLTemplate extends OwnerdesignHTMLTemplate 
{
	var $cur_user;
	var $cur_user_profile;
	
	function show($args) {
		$this->cur_user_profile = $args['settings_cur_user_profile'];
		$this->cur_user = $args['settings_cur_user'];
		parent::show($args);
    }
    
    function showCore() {
		$this->elementStart('div', array('id' => 'contents', 'class' => 'clearfix rounded5l tab'));
		$this->showContent();
		$this->elementEnd('div');
		
    	$this->elementStart('div', array('id' => 'widgets'));
		$this->showRightside();
		$this->elementEnd('div');
    }
    
	function showStyleSheets() {
    	parent::showStyleSheets();
    	$this->cssLink('css/settings.css','default','screen, projection');
    }
	
	/**
     * Show the local navigation menu
     *
     * This is the same for all settings, so we show it here.
     *
     * @return void
     */

    function showRightsidebar()
    {	
//		$this->tu->showOwnerInfoWidget($this->cur_user_profile);    	
    	
		$this->tu->showIntroWidget($this->title(), $this->showSettingsInstruction());
//    	$navs = new NavList_Settings();
//    	$this->tu->showNavigationWidget($navs->lists(), $this->trimmed('action'));
    }
    
    function showContent() {
        
        $this->tu->showTitleBlock($this->showSettingsTitle(), 'settings');
        
        $navs = new NavList_Settings($this->cur_user);
        
        $this->tu->showTabNav($navs->lists(), $this->trimmed('action'));
        
//        $this->tu->showPageInstructionBlock($this->showSettingsInstruction());

        $this->showSettingsTip();
        
    	$this->showSettingsContent();
    }
    
    function showSettingsContent() {}
    
    function showSettingsTitle() {}
    
    function showSettingsInstruction() {}
    
    function showSettingsTip()
    {
    	if ($this->arg('page_msg')) {
    		if ($this->arg('page_success')) {
    			$this->tu->showPageHighlightBlock($this->args['page_msg']);
    		} else {
    			$this->tu->showPageErrorBlock($this->args['page_msg']);
    		}
    	}
    }
}

?>