<?php

if (!defined('SHAISHAI')) { exit(1); }

require_once INSTALLDIR . '/plugins/mission/classes/Missions.php';

class StartmissionAction extends ShaiAction
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
    	
    	require_once INSTALLDIR . '/plugins/mission/' . $clazz . '.php';
		$mission_clazz = 'Mission_' . ucfirst($clazz);
		$missionObj = new $mission_clazz($this->cur_user);
    	
		if ($missionObj->precondition()) {
			Missions::updateStatus($this->cur_user->id, $clazz, 1);	
			$this->showJsonResult(array('result' => 'true', 'url' => $missionObj->redirectURL(), 'fmsg' => $missionObj->missionFinishCondition()));
		} else {
			$this->showJsonResult(array('result' => 'false', 'msg' => '不满足任务前置条件'));
		}
    	
    	
    	
    }
}