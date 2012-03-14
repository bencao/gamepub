<?php

require_once INSTALLDIR . '/plugins/plugin.php';
require_once INSTALLDIR . '/plugins/feature/lib/featuremanager.php';

class Feature_plugin extends Plugin {
	
	var $noteClazz;
	var $note;
	var $valid;
	
	function initialize($args)
    {
    	parent::initialize($args);
    	
    	$targetActions = array('home');
    	
		$user = common_current_user();
		if ($user && in_array($this->args['action'], $targetActions)) {
			$fm = new FeatureManager(common_current_user());
			$this->noteClazz = $fm->getANote();
			if ($this->noteClazz 
				&& file_exists(INSTALLDIR . '/plugins/feature/' . $this->noteClazz . '.php')) {
				require_once INSTALLDIR . '/plugins/feature/' . $this->noteClazz . '.php';
				$noteClassName = 'Feature_' . ucfirst($this->noteClazz);
				$this->note = new $noteClassName();
				$this->valid = true;
			} else {
				$this->valid = false;
			}
		} else {
			$this->valid = false;
		}
		
        return true;
    }
    
    function onEndRegistration($out, $user) {
    	require_once INSTALLDIR . '/plugins/feature/classes/User_feature_notes.php';
    	foreach (common_config('site', 'features') as $clz) {
	    	$ufn = new User_feature_notes();
	    	$ufn->uid = $user->id;
	    	$ufn->clazz = $clz;
	    	$ufn->insert();
    	}
    }
	
	function onEndShowNoticeForm($out, $user) {
		if ($this->valid
			&& $user->getUserGrade() > 1) {
			$this->note->showHTML($out);
		}
	}
	
	function onEndShowScripts($out) {
		if ($this->valid) {
			$out->script('plugins/feature/js/feature.js');
			$this->note->showScripts($out);
		}
	}
	
	function onEndShowStyles($out) {
		if ($this->valid) {
			$this->note->showStylesheets($out);
		}
	}
	
	function addRouter($m) {
		require_once INSTALLDIR . '/plugins/feature/ajax/ignorenote.php';
        $m->connect('ajax/ignorenote', array('action' => 'ignorenote'));
	}
}