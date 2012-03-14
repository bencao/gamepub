<?php

require_once INSTALLDIR . '/plugins/mission/basemission.php';

class Mission_Usetaggroup extends BaseMission {
	public function __construct($cur_user) {
		parent::__construct($cur_user);
		$this->clazz = 'usetaggroup';
	}
	
	public function missionTitle() {
		return '将关注者分组';
	}
	
	public function missionAward() {
		return '2个铜G币';
	}
	
	public function missionBriefDescription() {
		return '通过分组来管理您关注的人和他们发送的消息。';
	}
	
	public function missionDescription() {
		return '关注的人多了以后，每天收到的消息量也将增大。这时候，就需要有一种机制让我们能够根据消息的轻重缓急进行筛选。关注者分组功能就是为此而诞生的。<br />点击页面右上角“我关注的人”链接，即可对关注者进行管理。';
	}
	
	public function missionFinishCondition() {
		return '将至少一名您关注的人放入“朋友”分组';
	}
	
	public function redirectURL() {
		return common_local_url('subscriptions', array('uname' => $this->cur_user->uname));
	}
	
	public function check() {
		$ut = new User_tag();
		$ut->whereAdd('tagger = ' . $this->cur_user->id);
		$ut->whereAdd("tag = '朋友'");
		$ut->find();
		$ut->fetch();
		
		$tt = new Tagtions();
		$tt->whereAdd('tagger = ' . $this->cur_user->id);
		$tt->whereAdd('tagid = ' . $ut->id);
		$tt->find();
		
    	return $tt->fetch() && parent::check();
	}
	
	public function award() {
		User_grade::addScore($this->cur_user->id, 20);
		return '在我关注的人页面您可以进行很多操作，方便您管理好友，期待您的发现哦。<br />恭喜您获得了2个铜G币的奖励！';
	}
}