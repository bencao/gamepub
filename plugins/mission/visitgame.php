<?php

require_once INSTALLDIR . '/plugins/mission/basemission.php';

class Mission_Visitgame extends BaseMission {
	
	var $game;
	
	public function __construct($cur_user) {
		parent::__construct($cur_user);
		$this->clazz = 'visitgame';
		$this->game = $this->cur_user->getGame();
	}
	
	public function missionTitle() {
		return '逛游戏专区';
	}
	
	public function missionAward() {
		return '1个铜G币';
	}
	
	public function missionBriefDescription() {
		return '想看看' . $this->game->name . '的玩家们都在聊什么么？';
	}
	
	public function missionDescription() {
		return 'GamePub汇集了众多玩' . $this->game->name . '的玩家，很可能您的朋友也在其中！游戏讨论区有很多有用的攻略信息、趣事分享。<br />如何导航：鼠标移动到网页顶端“' . $this->game->name . '”→点击弹出框中的“游戏讨论区”。';
	}
	
	public function missionFinishCondition() {
		return '查看一次' . $this->game->name . '“游戏讨论区”';
	}
	
	public function redirectURL() {
		return common_local_url('game', array('gameid' => $this->game->id));
	}
	
	public function award() {
		User_grade::addScore($this->cur_user->id, 10);
		return '记得常看看游戏讨论区，获取有价值的信息哦。<br />恭喜您获得了1个铜G币的奖励！';
	}
}