<?php

if (!defined('SHAISHAI')) { exit(1); }

require_once INSTALLDIR . '/plugins/feature/lib/featuremanager.php';

class IgnorenoteAction extends ShaiAction
{
	function prepare($args) {
		if (! parent::prepare($args)) {return false;}
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			$this->clientError('只接受POST请求', 404);
			return false;
		}
		return true;
	}
	
    function handle($args)
    {
    	parent::handle($args);
    	
    	$clazz = $this->trimmed('cz');
    	
    	$fm = new FeatureManager($this->cur_user);
    	$result = $fm->ignoreANote($clazz);
		
		$this->showJsonResult(array('result' => $result ? 'true' : 'false'));
    }
}