<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class JsonStringer
{
	var $callback;
	var $buffer;
	
	function init_document($args = array())
    {
    	$this->buffer = '';
    	
        // Check for JSONP callback
        if (array_key_exists('callback', $args)) {
        	$this->callback = $args['callback'];
        	$this->buffer .= $callback . '(';
        }
    }

    function end_document()
    {
        // Check for JSONP callback
        if ($this->callback) {
            $this->buffer .= ')';
        }
    }
    
	function show_json_objects($objects)
    {
        $this->buffer .= json_encode($objects);
    }
    
    function flush() {
    	header('Content-Type: application/json; charset=utf-8');
    	print $this->buffer;
    }
}