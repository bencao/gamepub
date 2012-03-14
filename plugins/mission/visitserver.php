<?php

require_once INSTALLDIR . '/plugins/mission/basemission.php';

class Mission_Visitserver extends BaseMission {
	
	var $server;
	var $game;
	
	public function __construct($cur_user) {
		parent::__construct($cur_user);
		$this->clazz = 'visitserver';
		$this->game = $this->cur_user->getGame();
		$this->server = $this->cur_user->getGameServer();
	}
	
	public function missionTitle() {
		return '逛服务器专区';
	}
	
	public function missionAward() {
		return '1个铜G币';
	}
	
	public function missionBriefDescription() {
		return '想看看' . $this->server->name . '的玩家们都在聊什么么？';
	}
	
	public function missionDescription() {
		return '同服讨论区一大作用就是找到可以一起玩游戏的朋友。<br />如何导航：鼠标移动到网页顶端“' . $this->game->name . '”→点击弹出框中的“同服讨论区”。';
	}
	
	public function missionFinishCondition() {
		return '查看一次' . $this->server->name . '“同服讨论区”';
	}
	
	public function redirectURL() {
		return common_local_url('gameserver', array('gameserverid' => $this->server->id));
	}
	
	public function award() {
		User_grade::addScore($this->cur_user->id, 10);
		return '记得常看看服务器讨论区，获取有价值的信息哦。<br />恭喜您获得了1个铜G币的奖励！';
	}
}