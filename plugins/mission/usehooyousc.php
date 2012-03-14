<?php

require_once INSTALLDIR . '/plugins/mission/basemission.php';

class Mission_Usehooyousc extends BaseMission {
	public function __construct($cur_user) {
		parent::__construct($cur_user);
		$this->clazz = 'usehooyousc';
	}
	
	public function precondition() {
		$status = Missions::getStatus($this->cur_user->id, 'usehooyou');
		
		return $status == 2 && parent::precondition();
	}
	
	public function missionPrecondition() {
		return '完成“与呼游的邂逅”任务';
	}
	
	public function missionTitle() {
		return '一键截屏上传';
	}
	
	public function missionAward() {
		return '3个铜G币';
	}
	
	public function missionBriefDescription() {
		return '通过实际操作，体验呼游强大的截屏上传功能。';
	}
	
	public function missionDescription() {
		return '现在就让我们来试一试神奇的一键截屏上传功能吧。<br />运行呼游桌面端，开启“一键截屏”功能(右下角的小闪电)，默认一键截屏的快捷键是Ctrl + Q(可在设置中修改)。进入游戏，按Ctrl + Q即可把当前活动窗口截图上传至GamePub。';
	}
	
	public function missionFinishCondition() {
		return '用呼游发出一条图片消息';
	}
	
	public function check() {
		$notices = new Notice();
		$notices->whereAdd('user_id = ' . $this->cur_user->id);
		$notices->whereAdd('is_banned = 0');
		$notices->whereAdd('content_type = 4');
		$notices->whereAdd('topic_type <> 4');
		$notices->whereAdd("source = 'hooyou'");
//        $notices->is_delete = 0;
        $cnt = (int) $notices->count('distinct id');
        
    	return $cnt > 0 && parent::check();
	}
	
	public function award() {
		User_grade::addScore($this->cur_user->id, 30);
		return '一键截屏在截屏之后不会在本地生成文件，直接就已经上传到您的个人发言里面，方便快捷！<br />恭喜您获得了3个铜G币的奖励！';
	}
}