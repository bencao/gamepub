<?php

require_once INSTALLDIR . '/plugins/mission/basemission.php';

class Mission_Firstreply extends BaseMission {
	public function __construct($cur_user) {
		parent::__construct($cur_user);
		$this->clazz = 'firstreply';
	}
	
	public function precondition() {
		$r = new Reply();
		$r->whereAdd('user_id = ' . $this->cur_user->id);
		$cnt = $r->count();
		
		return $cnt > 0 && parent::precondition();
	}
	
	public function missionPrecondition() {
		return '收到至少一条回复';
	}
	
	public function missionTitle() {
		return '第一次回复';
	}
	
	public function missionAward() {
		return '2个铜G币';
	}
	
	public function missionBriefDescription() {
		return '有人对您说话了，快回复TA吧。';
	}
	
	public function missionDescription() {
		return '通过回复可以与对方展开会话，存在的会话的消息右上，会有一个“查看相关会话”链接，您可以点击查看会话的完整内容。<br />点击右侧面板“回复”，即可查看您收到的所有回复。';
	}
	
	public function missionFinishCondition() {
		return '回复至少一次';
	}
	
	public function check() {
		$n = new Notice();
		$r = new Reply();
		$n->joinAdd($r);
		$n->whereAdd('notice.user_id = ' . $this->cur_user->id);
		$cnt = $n->count();
		
    	return $cnt > 0 && parent::check();
	}
	
	public function award() {
		User_grade::addScore($this->cur_user->id, 20);
		return '您真厉害，已经深谙回复的技巧了。<br />恭喜您获得了2个铜G币的奖励！';
	}
}