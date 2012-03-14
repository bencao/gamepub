<?php

if (!defined('SHAISHAI')) { exit(1); }

abstract class BaseMission {
	
	var $cur_user;
	var $clazz = null;
	
	public function __construct($cur_user) {
		$this->cur_user = $cur_user;
	}
	
	public function precondition() {
		$status = Missions::getStatus($this->cur_user->id, $this->clazz);
    	
		return is_numeric($status) && ($status == 0 || $status == 1);
	}
	
	public abstract function missionTitle();
	
	public abstract function missionBriefDescription();
	
	public abstract function missionDescription();
	
	public abstract function missionFinishCondition();
	
	public abstract function missionAward();
	
	public function missionPrecondition() {
		return '无';
	}
	
	public function redirectURL() {
		return '#';
	}
	
	public function showHTML($out) {
	
	}
	
	public function showScripts($out) {
	
	}
	
	public function showStylesheets($out) {
	
	}
	
	public function check() {
		$status = Missions::getStatus($this->cur_user->id, $this->clazz);
		return $status == 1;
	}
	
	public abstract function award();
} 