<?php

class Plugin {
	var $args;
	
	function install()
    {
    	Event::addHandler('StartRouterInitialize', array($this, 'addRouter'));
        Event::addHandler('InitializePlugin', array($this, 'initialize'));
        Event::addHandler('CleanupPlugin', array($this, 'cleanup'));

        foreach (get_class_methods($this) as $method) {
            if (mb_substr($method, 0, 2) == 'on') {
                Event::addHandler(mb_substr($method, 2), array($this, $method));
            }
        }
    }

    function initialize($args)
    {
    	$this->args = $args;
        return true;
    }

    function cleanup()
    {
        return true;
    }
    
    function addRouter($m) {
    	return true;
    }
}