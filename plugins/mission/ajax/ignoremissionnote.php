<?php

if (!defined('SHAISHAI')) { exit(1); }

require_once INSTALLDIR . '/plugins/mission/classes/Missions.php';

class IgnoremissionnoteAction extends ShaiAction
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
    	
    	$_SESSION['imn'] = 't';
//    	setcookie('imn', 't', 0, '/');
//		echo 'true';
    }
}