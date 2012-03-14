<?php
/**
 * copyright @ shaishai.com
 */

if (!defined('SHAISHAI')) {
    exit(1);
}


/**
 * Action for displaying the public stream
 *
 */

class HotnoticeAction extends ShaiAction
{
    /**
     * page of the stream we're on; default = 1
     */

    var $page = null;
    
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}

    function isReadOnly($args)
    {
        return true;
    }

    /**
     * Read and validate arguments
     *
     * @param array $args URL parameters
     *
     * @return boolean success value
     */

    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}

        common_set_returnto($this->selfUrl());

        return true;
    }

    /**
     * handle request
     *
     * Show the public stream, using recipe method showPage()
     *
     * @param array $args arguments, mostly unused
     *
     * @return void
     */

 	function handle($args)
    {
        parent::handle($args);
        $type = ($this->arg('type')) ? ($this->arg('type')) : 'video';
        	
        if($type == 'video')
        	$notice = Notice_heat::heatOrderStream(20,3);
        else if($type == 'music')
    		$notice = Notice_heat::heatOrderStream(20,2);
    	else if($type == 'photo')
    		$notice = Notice_heat::heatOrderStream(20,4);
    	else if($type == 'text')
    		$notice = Notice_heat::heatOrderStream(20,1);
        
    	$this->addPassVariable('notice', $notice);
    	$this->addPassVariable('type', $type);
        $this->displayWith('HotnoticeHTMLTemplate');
    }
}
