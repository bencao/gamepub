<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class JsonTemplate
{
	var $callback;
	
	function init_document($args = array())
    {
        header('Content-Type: application/json; charset=utf-8');

        // Check for JSONP callback
        if (array_key_exists('callback', $args)) {
        	$this->callback = $args['callback'];
        	print $callback . '(';
        }
    }

    function end_document()
    {
        // Check for JSONP callback
        if ($this->callback) {
            print ')';
        }
    }
    
	function show_json_objects($objects)
    {
        print(json_encode($objects));
    }
}
?>