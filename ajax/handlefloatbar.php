<?php
if (!defined('SHAISHAI')) { exit(1); }

class HandlefloatbarAction extends ShaiAction
{
    function handle($args)
    {
    	parent::handle($args);
    	
    	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    		if($this->trimmed('close') == 'true') {
    			$_SESSION['floatbar'] = 0;
    		} else if($this->trimmed('close') == 'false') {
    			$_SESSION['floatbar'] = 1;
    		}
    	} 
    }
}
