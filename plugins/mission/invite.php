<?php

require_once INSTALLDIR . '/plugins/mission/basemission.php';

class Mission_Invite extends BaseMission {
	public function __construct($cur_user) {
		parent::__construct($cur_user);
		$this->clazz = 'invite';
	}
	
	public function missionTitle() {
		return '邀请五名好友';
	}
	
	public function missionAward() {
		return '5个铜G币';
	}
	
	public function missionBriefDescription() {
		return '邀请好友加入，得丰厚奖励。';
	}
	
	public function missionDescription() {
		return '已经发现游戏酒馆是一个好地方的您，是不是想要把游戏中、现实中的好友都聚集到游戏酒馆中呢？用您的邀请码来邀请好友加入游戏酒馆，不但可以和朋友们相聚在一起，更可以获得游戏酒馆提供的丰厚奖励哦！<br />点击页面右上“邀请”链接即可进入邀请页面。';
	}
	
	public function missionFinishCondition() {
		return '邀请到五名好友注册';
	}
	
	public function redirectURL() {
		return common_local_url('invite');
	}
	
	public function check() {
		$m = new Myinviterecord();
		$m->whereAdd('inviter_id = ' . $this->cur_user->id);
		return $m->count() >= 5 && parent::check();
	}
	
	public function award() {
		User_grade::addScore($this->cur_user->id, 50);
		return '通过邀请功能，您可以轻松和朋友相约游戏酒馆。为了您玩的开心，为了我们玩家网上家园的繁华，多邀请几个朋友加入游戏酒馆吧。<br />恭喜您获得了5个铜G币的奖励！';
	}
}