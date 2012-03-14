<?php

require_once INSTALLDIR . '/plugins/plugin.php';
require_once INSTALLDIR . '/plugins/mission/classes/Missions.php';

class Mission_plugin extends Plugin {
	
	var $missionObj;
	var $valid;
	
	function initialize($args)
    {
    	parent::initialize($args);
    	
    	if (array_key_exists('mission', $this->args)
			&& $this->args['mission'] == 'on'
			&& array_key_exists('mission_name', $this->args)) {
			$mission_name = $this->args['mission_name'];
			if (file_exists(INSTALLDIR . '/plugins/mission/' . $mission_name . '.php')) {
				require_once INSTALLDIR . '/plugins/mission/' . $mission_name . '.php';
				$missionClass = 'Mission_' . ucfirst($mission_name);
				$this->missionObj = new $missionClass(common_current_user());
				$this->valid = $this->missionObj->precondition();
			} else {
				$this->valid = false;
			}
		}
        return true;
    }
    
	function onEndRegistration($out, $user) {
    	foreach (common_config('site', 'missions') as $clz) {
	    	$ms = new Missions();
	    	$ms->uid = $user->id;
	    	$ms->mission_clazz = $clz;
	    	$ms->status = '0';
	    	$ms->modified = common_sql_now();
	    	$ms->created = $ms->modified;
	    	$ms->insert();
    	}
    }
	
	function onStartShowHeader($out) {
		if ($this->valid) {
			$this->missionObj->showHTML($out);
		}
	}
	
	function onEndShowScripts($out) {
		if ($this->valid) {
			$this->missionObj->showScripts($out);
		}
	}
	
	function onEndShowStyles($out) {
		if ($this->valid) {
			$this->missionObj->showStylesheets($out);
		}
	}
	
	function onEndHomeContentInfo($out, $user) {
		$out->elementStart('div', 'mymissions');
    	$out->element('div', 'bg');
    	$out->element('a', array('href' => common_local_url('missionlist'), 'title' => '做任务，赚财富啦', 'class' => 'outbound'), '我的任务');
//    	common_debug($_COOKIE);
    	if (! (array_key_exists('imn', $_SESSION)
    		&& $_SESSION['imn'] == 't')) {
	    	$total = count(common_config('site', 'missions'));
	    	$index = Missions::getFirstNotFinishedIndex($user->id);
	    	if ($index < $total) {
		    	$out->elementStart('div', 'pop');
		    	$out->element('div', 'pointer');
		    	$out->element('p', null, '您有新的任务！');
		    	$out->element('a', array('href' => '#', 'class' => 'pop_close'), 'X');
		    	$out->elementEnd('div');
	    	}
    	}
    	$out->elementEnd('div');
	}
	
	function onEndHomeStylesheets($out) {
		$out->cssLink('plugins/mission/css/missionlist.css');
	}
	
	function onEndHomeScripts($out) {
		$out->script('plugins/mission/js/missionlist.js');
	}
	
	function addRouter($m) {
		require_once INSTALLDIR . '/plugins/mission/action/missionlist.php';
		require_once INSTALLDIR . '/plugins/mission/ajax/awardmission.php';
		require_once INSTALLDIR . '/plugins/mission/ajax/startmission.php';
		require_once INSTALLDIR . '/plugins/mission/ajax/ignoremissionnote.php';
        $m->connect('main/missionlist', array('action' => 'missionlist'));
        $m->connect('ajax/startmission', array('action' => 'startmission'));
        $m->connect('ajax/awardmission', array('action' => 'awardmission'));
        $m->connect('ajax/ignoremissionnote', array('action' => 'ignoremissionnote'));
	}
}