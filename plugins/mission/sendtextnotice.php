<?php

require_once INSTALLDIR . '/plugins/mission/basemission.php';

class Mission_Sendtextnotice extends BaseMission {
	public function __construct($cur_user) {
		parent::__construct($cur_user);
		$this->clazz = 'sendtextnotice';
	}
	
	public function missionTitle() {
		return '发文字消息';
	}
	
	public function missionAward() {
		return '1个铜G币';
	}
	
	public function missionBriefDescription() {
		return '用文字告诉大家，“我在做什么”？';
	}
	
	public function missionDescription() {
		return '看了这么多游友的互动，想来您已经心痒难挠了吧？让我们行动起来，发出您的第一条文字消息。<br />点击“我的空间”，回到您的空间，在输入框里输入您想说的话，点击发送就可以了。很简单吧？看到输入框上面“您的话题”了吗？任意选择您想要发表的一个话题都可以为您增加G币。';
	}
	
	public function missionFinishCondition() {
		return '发出一条文字消息';
	}
	
	public function redirectURL() {
		return common_local_url('home');
	}
	
	public function check() {
		$notices = new Notice();
		$notices->whereAdd('user_id = ' . $this->cur_user->id);
		$notices->whereAdd('is_banned = 0');
		$notices->whereAdd('content_type = 1');
		$notices->whereAdd('topic_type <> 4');
//        $notices->is_delete = 0;
        $cnt = (int) $notices->count('distinct id');
    	
		return $cnt >= 2 && parent::check();
	}
	
	public function award() {
		User_grade::addScore($this->cur_user->id, 10);
		return '在游戏酒馆发言是最基本，也是最使用的技能，努力把熟练度练高吧。<br />恭喜您获得了1个铜G币的奖励！';
	}
}