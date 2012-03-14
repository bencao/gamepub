<?php
if (!defined('SHAISHAI')) {
    exit(1);
}

class ShowmplayerHTMLTemplate extends BasicHTMLTemplate
{
    function title() {
    	return common_config('site', 'name') . '音乐播放器';
    }
    
    function showScripts() {
//    	$this->element('script', array('type' => 'text/javascript', 'src' => common_path('mplayer/AC_OETags.js')));
//    	$this->element('script', array('type' => 'text/javascript', 'src' => common_path('mplayer/history/history.js')));
//    	$this->element('script', array('type' => 'text/javascript'), 
//    		'var requiredMajorVersion = 9;var requiredMinorVersion = 0;var requiredRevision = 124;');
		
//    	$this->element('script', array('src' => 'http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js', 'type' => 'text/javascript'));
    	$this->script('js/swfobject.js');
    	$this->element('script', array('type' => 'text/javascript'), 
    		"swfobject.embedSWF('/mplayer/GamePubMusicPlayer.swf?" . SHAISHAI_VERSION . "', 'r', "
    			. "'800', '600', '9', '/js/expressInstall.swf', "
    			. "{recent : '/ajax/recentmusics', random : '/ajax/randommusics', add : '/ajax/addmusic', del : '/ajax/delmusic', fav : '/ajax/favormusic', unfav : '/ajax/unfavormusic', more : '/hotnotice?type=music'}," 
    			. "{allowScriptAccess : 'sameDomain', wmode : 'transparent',  align : 'middle', id : 'GamePubMusicPlayer', name : 'GamePubMusicPlayer', quality : 'high', bgcolor : '#869ca7'});");
    }
    
    function showStylesheets() {
    	
    }
    
    function showUAStylesheets() {}
    
	function showBody()
    {
        $this->elementStart('body', array('scroll' => 'no', 'style' => 'margin: 0px; overflow:hidden;'));
        
        $this->element('div', array('id' => 'r'));
        $this->elementEnd('body');
    }
}