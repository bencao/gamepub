<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class NavList_Mission extends NavList {
	
	function __construct() {
		$this->init();
	}
	
	function init() {
		$this->addElement('all', common_local_url('missionlist', array('mtype' => 'all')), '我的任务');
		$this->addElement('new', common_local_url('missionlist', array('mtype' => 'new')), '可以开始');
		$this->addElement('started', common_local_url('missionlist', array('mtype' => 'started')), '进行中');
		$this->addElement('finished', common_local_url('missionlist', array('mtype' => 'finished')), '已完成');
	}
}

class MissionlistHTMLTemplate extends OwnerdesignHTMLTemplate
{
	var $missions;
	var $mtype;
	
	function show($args) {
		$this->missions = $args['missions'];
		$this->mtype = $args['mtype'];
		parent::show($args);
    }
    
	function showCore() {
		$this->elementStart('div', array('id' => 'contents', 'class' => 'clearfix rounded5l tab'));
		$this->showContent();
		$this->elementEnd('div');
		
    	$this->elementStart('div', array('id' => 'widgets'));
		$this->showRightside();
		$this->elementEnd('div');
    }
    
	function showContent() {
		if ($this->mtype == 'started') {
			$title = '进行中的任务';
		} else if ($this->mtype == 'new') {
			$title = '可以开始的任务';
		} else if ($this->mtype == 'finished') {
			$title = '已完成的任务';
		} else {
			$title = '我的任务';
		}
		$this->tu->showTitleBlock($title, 'settings');
		
		$navs = new NavList_Mission();
        
        $this->tu->showTabNav($navs->lists(), $this->mtype);
        
        if ($this->missions && $this->missions->N > 0) {
        	$mission_list = new MissionList($this, $this->missions, $this->cur_user);
        	$mission_list->show();
        } else {
        	$this->showEmptyMessage();
        }
	}
	
	function showEmptyMessage() {
		$this->tu->showEmptyListBlock('目前还没有任务');
	}
	
	function showRightsidebar() {
		$page_owner_profile = $this->owner->getProfile();
		
		$this->tu->showOwnerInfoWidget($page_owner_profile);
    	$this->tu->showSubInfoWidget($page_owner_profile, $this->is_own);
    	$this->tu->showToolbarWidget($page_owner_profile);
        
    	$this->tu->showGroupsWidget($page_owner_profile, 6);
    	$subscriptions = $page_owner_profile->getSubscriptions(0, 18);
    	$this->tu->showUserListWidget($subscriptions, '我关注的人');
    	$this->tu->showInviteWidget();
    	
    	$this->tu->showTagcloudWidget();
    	
    	$this->tu->showActivityWidget();
	}
	
	function showShaishaiStylesheets() {
		parent::showShaishaiStylesheets();
		$this->cssLink('plugins/mission/css/missionlist.css');
	}
	
	function showShaishaiScripts() {
		parent::showShaishaiScripts();
		$this->script('plugins/mission/js/missionlist.js');
	}
}

class MissionList {
	var $missions;
	var $cur_user;
	var $out;
	
	function __construct($out, $missions, $cur_user) {
		$this->missions = $missions;
		$this->out = $out;
		$this->cur_user = $cur_user;
	}
	
	function show() {
		$this->out->elementStart('ol', array('id' => 'missions'));
		while ($this->missions->fetch()) {
			require_once INSTALLDIR . '/plugins/mission/' . $this->missions->mission_clazz . '.php';
			$mission_clazz = 'Mission_' . ucfirst($this->missions->mission_clazz);
			$missionObj = new $mission_clazz();
			if ($this->missions->status == 1) {
				$this->out->elementStart('li', array('class' => 'mission active'));
			} else {
				$this->out->elementStart('li', array('class' => 'mission'));
			}
            $this->showMissionPicture($missionObj->missionTitle(), $missionObj->missionIcon());
            $this->showMissionTitle($missionObj->missionTitle());
//            $this->showMissionDesc($missionObj->missionDescription());
//            $this->showMissionAward($missionObj->missionAward());
			$this->out->elementStart('p', 'status');
            if ($this->missions->status == 0) {
				$this->out->text('可以开始');
			} if ($this->missions->status == 1) {
				$this->out->text('进行中');
			} if ($this->missions->status == 2) {
				$this->out->text('已完成');
			}
			$this->out->elementEnd('p');
            $this->out->elementStart('div', 'op');
//            $this->showOperations($missionObj->missionDescription(), $missionObj->missionFinishCondition(), $this->missions);
            
            if ($this->missions->status == 1) {
            	$this->out->elementStart('a',array('class' => 'unfold','title' => '查看任务详情','href' => '#'));
            } else {
            	$this->out->elementStart('a',array('class' => 'fold','title' => '查看任务详情','href' => '#'));
            }
            $this->out->text('详细');
    		$this->out->elementEnd('a');
    		
            $this->out->elementEnd('div');
            if ($this->missions->status == 1) {
            	$this->out->elementStart('div',array('class' => 'detail'));
            } else {
            	$this->out->elementStart('div',array('class' => 'detail', 'style' => 'display:none;'));
            }
            $this->out->element('p', 'desc', $missionObj->missionDescription());
            $this->out->elementStart('div', 'oinfo');
            $this->out->elementStart('p');
            $this->out->element('strong', null, '奖励：');
            $this->out->text($missionObj->missionAward());
            $this->out->elementEnd('p');
            $prec = $missionObj->missionPrecondition();
            if (! empty($prec)) {
            	$this->out->elementStart('p');
            	$this->out->element('strong', null, '限制条件：');
            	$this->out->text($prec);
            	$this->out->elementEnd('p');
            }
            
            $this->out->elementStart('p', 'ops');
			if ($this->missions->status == 0) {
				$this->out->element('a', array('class' => 'mission_accept button76 orange76', 'title' => '开始任务', 'cz' => $this->missions->mission_clazz), '开始任务');
			} else if ($this->missions->status == 1) {
				$this->out->element('a', array('class' => 'mission_award button76 green76', 'title' => '我完成了，想领取奖励', 'cz' => $this->missions->mission_clazz), '领取奖励');
			} else if ($this->missions->status == 2) {
				$this->out->text('已完成');
			}
            $this->out->elementEnd('p');
            $this->out->elementEnd('div');
            
            $this->out->element('div', 'clear');
            
            $this->out->elementEnd('div');
            
            $this->out->elementEnd('li');
		}
		
		$this->out->elementEnd('ol');
	}
	
	function showMissionPicture($mission_name, $mission_pic) {
		$this->out->elementStart('div', 'avatar');
    	$this->out->element('img', array('height' => '32', 'width' => '32', 
    		'src' => $mission_pic, 
    		'alt' => $mission_name));
    	$this->out->elementEnd('div');
	}
	
	function showMissionTitle($mission_title) {
		$this->out->elementStart('p', 'nickname');
    	$this->out->elementStart('strong');
    	$this->out->text($mission_title);
    	$this->out->elementEnd('strong');
    	$this->out->elementEnd('p');
	}
	
//	function showOperations($mission_desc, $mission_fin, $mission) {
//		if ($mission->status == 0) {
//			$this->out->element('a', array('class' => 'mission_detail button76 orange76', 'title' => '查看任务详情', 'desc' => $mission_desc, 'fin' => $mission_fin, 'cz' => $mission->mission_clazz), '查看详情');
//		} else if ($mission->status == 1) {
//			$this->out->element('a', array('class' => 'mission_award button76 green76', 'title' => '我完成了，想领取奖励', 'desc' => $mission_desc, 'fin' => $mission_fin, 'cz' => $mission->mission_clazz), '领取奖励');
//		} else if ($mission->status == 2) {
//			$this->out->element('已完成');
//		}
//	}
	
	
}