<?php
if (!defined('SHAISHAI')) {
    exit(1);
}

class FlashfullscreenHTMLTemplate extends BasicHTMLTemplate
{
	var $flash;
	
    function title() {
    	return 'GamePub小游戏全屏玩 - ' . $this->flash->title;
    }
    
    function show($args) {
    	$this->flash = $args['flash'];
    	parent::show($args);
    }
    
	function showStylesheets() {
    	$this->cssLink('css/fullscreen.css','default');
    }
    
    function showUAStylesheets() {}
    
	function showBody()
    {
        $this->elementStart('body', array('scroll' => 'no'));
        
        $this->element('embed', array('src' => $this->flash->path, 'quality' => 'high',
    		'type' => 'application/x-shockwave-flash', 'width' => '100%', 'height' => '99%'
    	));
        $this->elementEnd('body');
    }
}