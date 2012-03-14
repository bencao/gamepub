<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR . '/plugins/mission/classes/Missions.php';

class MissionlistAction extends OwnerdesignAction
{
	
	var $mtype;
	var $pageLimit = 11;
	var $total;
	
	function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}
        
        if (array_key_exists('mtype', $args)) {
        	$this->mtype = $args['mtype'];	
        } else {
        	$this->mtype = 'all';
        }
        
    	return true;
    }
    
    function handle($args)
    {
        parent::handle($args);
        
        $this->total = Missions::getMissionsCountByUserid($this->cur_user->id);
        
        if (! array_key_exists('page', $_REQUEST)) {
			// 做一个检查，页面中的任务是否已经全部已完成
        	$index = Missions::getFirstNotFinishedIndex($this->cur_user->id);
        	$this->cur_page = (int) (($index + 1)/$this->pageLimit + 1);
        }
        
        if ($this->mtype == 'new') {
        	$missionlist = Missions::getNewMissionsByUserid($this->cur_user->id);
        } else if ($this->mtype == 'started') {
        	$missionlist = Missions::getStartedMissionsByUserid($this->cur_user->id);
        } else if ($this->mtype == 'finished') {
        	$missionlist = Missions::getFinishedMissionsByUserid($this->cur_user->id);
        } else {
        	$missionlist = Missions::getAllMissionsByUserid($this->cur_user->id, 
        		$this->pageLimit * ($this->cur_page - 1), $this->pageLimit);
        }
        
        $this->view = new XMLStringer();
        $this->view->element('a', array('href' => '#', 'class' => 'close', 'title' => '关闭窗口'));
        
        $this->view->element('span', 'cp', '第' . $this->cur_page . '页，共' . (int) (($this->total/$this->pageLimit) + 1) . '页');
        
        if ($this->total > $this->cur_page * $this->pageLimit) {
        	$this->view->element('a', array('href' => common_local_url('missionlist', null, array('page' => $this->cur_page + 1)), 'class' => 'next'), '下一页');
        }
        if ($this->cur_page > 1) {
        	$this->view->element('a', array('href' => common_local_url('missionlist', null, array('page' => $this->cur_page - 1)), 'class' => 'prev'), '上一页');
        }
        $this->view->elementStart('ul');
        
        while ($missionlist->fetch()) {
        	require_once INSTALLDIR . '/plugins/mission/' . $missionlist->mission_clazz . '.php';
        	$mission_clazz = 'Mission_' . ucfirst($missionlist->mission_clazz);
			$missionObj = new $mission_clazz($this->cur_user);
			$this->view->elementStart('li');
        	if ($missionlist->status == 1) {
        		$aclass = 'ing';
        		$title = '点击领取奖励';
        	} else if ($missionlist->status == 2) {
        		$aclass = 'fin';
        		$title = '点击查看详情';
        	} else {
        		$aclass = 'able';
        		$title = '点击查看详情';
			}
        	$this->view->elementStart('a', array('href' => '#',
        		'desc' => $missionObj->missionDescription(),
        		'lim' => $missionObj->missionPrecondition(),
        		'award' => $missionObj->missionAward(),
        		'cz' => $missionlist->mission_clazz,
        		'class' => $aclass,
        		'title' => $title));
        	$this->view->element('strong', null, $missionObj->missionTitle());
       		$this->view->element('span', 'desc', $missionObj->missionBriefDescription());
        	$this->view->elementStart('span', 'status');
       		if ($missionlist->status == 1) {
       			if ($missionObj->check()) {
        			$this->view->text('(可领奖)');
       			} else {
        			$this->view->text('(进行中)');	
        		}
        	} else if ($missionlist->status == 2) {
				$this->view->text('(已完成)');
        	} else {
				$this->view->text('(未开始)');
			}
       		$this->view->elementEnd('span');
       		$this->view->elementEnd('a');
        	$this->view->elementEnd('li');
        }
        
        $this->view->elementEnd('ul');
        
        $this->showJsonResult(array('result' => 'true', 'html' => $this->view->getString()));
    }
}