<?php

require_once INSTALLDIR . '/plugins/mission/basemission.php';

class Mission_Usehooyou extends BaseMission {
	public function __construct($cur_user) {
		parent::__construct($cur_user);
		$this->clazz = 'usehooyou';
	}
	
	public function missionTitle() {
		return '与呼游的邂逅';
	}
	
	public function missionAward() {
		return '5个铜G币';
	}
	
	public function missionBriefDescription() {
		return '游戏辅助利器，极速信息分享 -- 它就是呼游。';
	}
	
	public function missionDescription() {
		return '想要随时知道游戏酒馆是否有新消息，是否有人关注您，是否有人给您回复，却又觉得老是刷新网页太麻烦？下载“呼游”，轻松解决您的困扰。<br />点击网页右上方“更多玩法”，然后点击“呼游”一行的下载按钮。';
	}
	
	public function missionFinishCondition() {
		return '下载安装呼游并使用呼游发出一条消息';
	}
	
	public function redirectURL() {
		return common_path('clients/hooyou');
	}
	
	public function check() {
		$notices = new Notice();
		$notices->whereAdd('user_id = ' . $this->cur_user->id);
		$notices->whereAdd('is_banned = 0');
		$notices->whereAdd('topic_type <> 4');
		$notices->whereAdd("source = 'hooyou'");
//        $notices->is_delete = 0;
        $cnt = (int) $notices->count('distinct id');
        
    	return $cnt > 0 && parent::check();
	}
	
	public function award() {
		User_grade::addScore($this->cur_user->id, 50);
		return '呼游桌面端不仅可以随时提示您网页上出现的最新消息，还可以实现一键截屏等奇妙功能。绝对是畅游游戏酒馆，轻松展现自我的神兵利器！<br />恭喜您获得了5个铜G币的奖励！';
	}
}