<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR . '/plugins/feature/classes/User_feature_notes.php';

class FeatureManager {
	var $user;
	var $featureNotes;
	
	public function __construct($user) {
		$this->user = $user;
		$this->featureNotes = User_feature_notes::getAvailableFeatureNotesByUserid($this->user->id);
	}
	
	public function getANote() {
		if (empty($this->featureNotes)) {
			return false;
		}
		return $this->featureNotes[0];
	}
	
	public function ignoreANote($clazz) {
		return User_feature_notes::ignoreFeatureNote($this->user->id, $clazz);
	}
}