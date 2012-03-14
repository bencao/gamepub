<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class GroupdesignHTMLTemplate extends RightsidebarHTMLTemplate
{
	var $cur_group;
	var $cur_group_design;
	var $is_group_admin;
	var $symbol = 0;
	var $has_bgmusic = false;
	
	function show($args) {
		$this->cur_group = $args['cur_group'];
		$this->cur_group_design = $args['cur_group_design'];
		$this->is_group_admin = $args['is_group_admin'];
		if($this->cur_group->backmusic && $this->cur_group->backmusic != '')
			$this->has_bgmusic = true;
		parent::show($args);
	}
	
	function getPage()
	{
		return 0;
	}
	
    
    function showCore() {
		$this->elementStart('div', array('id' => 'contents', 'class' => 'clearfix rounded5l tab'));
		$this->showContent();
		//只在公会主页显示音乐播放器  2010-10-8
		//if($this->has_bgmusic)
		//	$this->showGroupMusicPlayer();
		$this->elementEnd('div');
		
    	$this->elementStart('div', array('id' => 'widgets'));
		$this->showRightside();
		$this->elementEnd('div');
    }
    
    function showGroupMusicPlayer() {
    	if ($this->has_bgmusic) {
	    	$this->elementStart('div', 'gmusic');
	    	$this->element('a', array('id' =>'bgmusicbtn', 'href' => '#', 'url' => $this->cur_group->backmusic, 'class' => 'active'));
	    	$this->element('p', array('id' => 'playermp3', 'style' => 'display:none;'));
	    	$this->elementEnd('div');
    	}
    }
    
    function showGroupInfo() {
    	$original = $this->cur_group->homepage_logo;
    	if(!$original){
    		$original = User_group::defaultLogo(GROUP_LOGO_PROFILE_SIZE);
    	}
    	
    	$this->elementStart('div', array('class' => 'widget group_info'));
    	
    	$this->elementStart('div', array('class' => 'avatar'));
    	$this->element('img', array('src' => $original, 'alt' => $this->cur_group->uname));
    	$this->elementEnd('div'); 
    	
    	$this->elementStart('dl', array('class' => 'detail'));
    	$this->elementStart('dt');
    	$this->elementStart('a', array('href' => common_local_url('showgroup', array('id' => $this->cur_group->id))));
    	$this->text($this->cur_group->uname);
    	$this->elementEnd('a');
    	$this->elementEnd('dt');
    	$this->elementStart('dd');
    	$this->elementStart('p');
    	$this->element('strong', null, '全名：');
    	$this->text($this->cur_group->nickname);
    	$this->elementEnd('p');   
    	if(!$this->cur_group->groupclass){
    		$this->elementStart('p');
    		$this->element('strong', null, '分类：');
    		$this->text($this->cur_group->category . '-' . $this->cur_group->catalog);
    		$this->elementEnd('p');
    		$this->elementStart('p');
    		$this->element('strong', null, '群热度：');
    		$this->text($this->cur_group->heat);
    		$this->elementEnd('p');
    	}else{
    		$this->elementStart('p');
    		$this->element('strong', null, '游戏：');
    		$this->text($this->cur_group->getGame()->name);
    		$this->elementEnd('p');
    		$this->elementStart('p');
    		$this->element('strong', null, '群热度：');
    		$text = $this->cur_group->heat;
    		$rank = $this->cur_group->getRank();
    		if($rank > 0){
    			$text .= '（排名'.$rank.'）';
    		}
    		$this->text($text);
    		$this->elementEnd('p');
    	}  	
    	if(!is_null($this->cur_group->location) && $this->cur_group->location!=''){
    		$this->elementStart('p');
    		$this->element('strong', null, '聚集地：');
    		$this->text($this->cur_group->location);
    		$this->elementEnd('p');
    	}
    	$this->elementStart('p');
    	$this->element('strong', null, '创建者：');
    	$groupOwner = User::staticGet('id', $this->cur_group->ownerid);
    	$this->text($groupOwner->nickname);
    	$this->element('a', array('id' => 'disply_btn', 'href' => '#'), '[更多]');
    	$this->elementEnd('p');
    	 
		$this->elementStart('div', array('id' => 'detail_info', 'style' => 'display:none'));
    	$this->elementStart('p');
    	$this->element('strong', null, '创建时间：');
    	$this->text(date('Y-m-d', strtotime($this->cur_group->created)));
    	$this->elementEnd('p');   
    	$this->element('strong', null, '简介：');
    	$this->text($this->cur_group->description);
    	$this->element('a', array('id' => 'undisply_btn', 'href' => '#'), '[隐藏]');
    	if($this->symbol == 1){
    		$this->elementStart('p');
	    	if ($this->cur_group->isOwnedBy($this->cur_user)){
	    		$this->element('a', array('href' => common_local_url('groupleave', array('id' => $this->cur_group->id)), 
	    			'id' => 'deletegroup', 'class' => 'leavegroup deletegroup'), '删除' . GROUP_NAME() . '');
	    	} else {
	        	$this->element('a', array('href' => common_local_url('groupleave', array('id' => $this->cur_group->id)), 
	        		'id' => 'leavinggroup', 'class' => 'leavegroup leavinggroup'), '退出' . GROUP_NAME() . '');
	    	}
    		$this->elementEnd('p');
    	}
		$this->elementEnd('div');  
		$applyNum = Group_application::getApplyNum($this->cur_group->id);
    	if($this->is_group_admin && $applyNum){
    		$this->element('a', array('href' => common_local_url('groupapplication', array('id' =>$this->cur_group->id))), 
    			'有' . $applyNum . '个加入申请尚未处理');
    	}
    	$this->elementEnd('dd');
    	$this->elementEnd('dl');
    	
    	$this->elementEnd('div');
    }
    
    function showGroupNav() {
    	$this->elementStart('ul', array('id' => 'w_nav', 'class' => 'widget'));
    	if($this->getPage()==0)
    		$this->elementStart('li', array('class' => 'active'));
    	else
    		$this->elementStart('li');    		
    	$this->element('span');
    	$this->element('a', array('href' => common_local_url('showgroup', array('id' =>
        	$this->cur_group->id))), '' . GROUP_NAME() . '主页');
    	$this->elementEnd('li');
    	if($this->getPage()==1)
    		$this->elementStart('li', array('class' => 'active'));
    	else
    		$this->elementStart('li'); 
    	$this->element('span');
    	$this->element('a', array('href' => common_local_url('groupmembers', array('id' =>
        	$this->cur_group->id))), '' . GROUP_NAME() . '成员');
    	$this->elementEnd('li');
    	if($this->is_group_admin){
	    	if($this->getPage()==2)
	    		$this->elementStart('li', array('class' => 'active'));
	    	else
	    		$this->elementStart('li'); 
	    	$this->element('span');
	    	$this->element('a', array('href' => common_local_url('groupedit', array('id' =>
		    	$this->cur_group->id))), '编辑' . GROUP_NAME());
	    	$this->elementEnd('li');
    	}
    	$this->elementEnd('ul');
    }
	
    function showRightsidebar()
    {  	
		$this->groupAction();
    	
    	$this->showGroupInfo();
    	
    	$this->showGroupNav();
    	
    	$this->showGroupMembers();
    	
    	$this->showGroupHotTags();
    }
    
    function showGroupMembers() {
    	if(!(!$this->cur_group->hasMember($this->cur_user)&&$this->cur_group->grouptype)){
	    	$this->elementStart('dl', array('class' => 'widget grid-6'));
	    	$this->elementStart('dt');
	    	$this->text('' . GROUP_NAME() . '成员'. $this->cur_group->memberCount() . '人');
	    	$this->element('a', array('class' => 'unfold'));
	    	$this->elementEnd('dt');
	    	$this->elementStart('dd');
	    	$this->elementStart('ul', array('class' => 'clearfix'));
	    	$this->showMembers();
	    	$this->elementEnd('ul');
	    	$this->elementEnd('dd');
	    	$this->elementEnd('dl');
	    	
	    	$this->elementStart('dl', array('class' => 'widget grid-6'));
	    	$this->elementStart('dt');
	    	$this->text('' . GROUP_NAME() . '管理员');
	    	$this->element('a', array('class' => 'unfold'));
	    	$this->elementEnd('dt');
	    	$this->elementStart('dd');
	    	$this->elementStart('ul', array('class' => 'clearfix'));
	    	$this->showAdmins();
	    	$this->elementEnd('ul');
	    	$this->elementEnd('dd');
	    	$this->elementEnd('dl');
    	}
    }
    
    function showGroupHotTags() {
    	$this->elementStart('dl', array('class' => 'widget grid-6'));
    	$this->elementStart('dt');
    	$this->text('本' . GROUP_NAME() . '热门标签');
    	$this->elementEnd('dt');
    	$this->elementStart('dd');
    	$this->elementEnd('dd');
    	$this->elementEnd('dl');    
    	
    	$this->showGroupTagcloudWidget($this->cur_group->id, "white");
    }
    
	function showGroupTagcloudWidget($groupid, $theme="white") {
		$this->raw(common_stream('tu:tagcloud:' . $groupid . ':' . $theme, array($this, "_showGroupTagcloudWidget"), array($groupid, $theme), 1800));
	}
	
    // show hot tags
    function _showGroupTagcloudWidget($groupid, $theme){
		    	
        $tags = new Notice_tag();
        $second_tag = new Second_tag();
        $group_inbox = new Group_inbox();

        #Need to clear the selection and then only re-add the field
        #we are grouping by, otherwise it's not a valid 'group by'
        #even though MySQL seems to let it slide...
        $tags->joinAdd($second_tag);
        $tags->joinAdd($group_inbox);
        
        $tags->selectAdd();
        $tags->selectAdd('second_tag.name as tag');
        $tags->selectAdd('second_tag.id as tagid');

        #Add the aggregated columns...
        $tags->selectAdd('max(notice_tag.notice_id) as last_notice_id');
        $calc='sum(exp(-(now() - notice_tag.created)/%s)) as weight';
        $tags->selectAdd(sprintf($calc, common_config('tag', 'dropoff')));
        $tags->whereAdd('group_inbox.group_id=' . $groupid);
        $tags->whereAdd('second_tag.id >= 1000000');
        $tags->groupBy('tag');
        $tags->orderBy('weight DESC');

        $tags->limit(40);

        $cnt = $tags->find();
        
        $xs = new XMLStringer();
        $xs->elementStart('div', 'widget tags');
		$xs->elementStart('p', array('id' => 'tagcloud', 'style' => 'display:none;'));
		
    	$tw = array();
        $sum = 0;
        while ($tags->fetch()) {
            $tw[] = array('tag' => $tags->tag, 'id' => $tags->tagid, 'weight' => $tags->weight);
            $sum += $tags->weight;
        }

//        ksort($tw);

        foreach ($tw as $t) {
            $this->showGroupTag($xs, $t['tag'], $t['id'], $groupid, $t['weight'], $t['weight']/$sum, $theme);
        }
        $xs->elementEnd('p');
        $xs->elementEnd('div');
        
        return $xs->getString();
	}
	
	function showGroupTag($out, $tag, $id, $groupid, $weight, $relative, $theme) {
        if ($relative > 0.1) {
        	if ($theme == "white") {
        		// level 4 tag - extremly hot
				$color = '0xffffff';
        	} else if ($theme == "black") {
        		$color = "0x333333";
        	}
        } else if ($relative > 0.02) {
        	if ($theme == "white") {
        		// level 3 tag - very hot
				$color = '0xdddddd';
        	} else if ($theme == "black") {
        		$color = "0x444444";
        	}
        } else if ($relative > 0.005) {
        	if ($theme == "white") {
        		// level 2 tag - hot
				$color = '0xaaaaaa';
        	} else if ($theme  = "black") {
        		$color = "0x555555";
        	}
        } else {
        	if ($theme == "white") {
        		// level 1 tag - not so hot
				$color = '0x999999';
        	} else if ($theme == "black") {
        		$color = "0x666666";
        	}
        }
        
        $out->text(urlencode(('<a href="' . common_path('group/' . $groupid . '/tag/' . $id) . '" style="font-size:14px;" color="' . $color . '" hicolor="0xb8510c">'. $tag . '</a>')));
	}
    
    function showMembers()
    {
    	// only show 8 latest joined members
        $member = $this->cur_group->getMembers();

        if (!$member) {
            return;
        }
        // we don't plan to use ProfileMiniList($member, $this) currently

        while ($member->fetch()) { 
        	$avatar = $member->getAvatar(AVATAR_MINI_SIZE);       	
			$this->elementStart('li');
    		$this->elementStart('a', array('href' => $member->profileurl, 'title' => '访问' . $member->nickname . '的主页'));
    		$this->element('img', array('src' => ($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_PROFILE_SIZE, $member->id, $member->sex), 'alt' => '访问' . $member->nickname . '的主页'));
    		$this->elementEnd('a');
    		$this->elementEnd('li');
        }
    }
    function showAdmins()
    {
    	// only show 8 latest joined members
        $member = $this->cur_group->getAdmins(0, 5);

        if (!$member) {
            return;
        }
        // we don't plan to use ProfileMiniList($member, $this) currently

        while ($member->fetch()) { 
        	$avatar = $member->getAvatar(AVATAR_MINI_SIZE);       	
			$this->elementStart('li');
    		$this->elementStart('a', array('href' => $member->profileurl, 'title' => '访问' . $member->nickname . '的主页'));
    		$this->element('img', array('src' => ($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_PROFILE_SIZE, $member->id, $member->sex), 'alt' => '访问' . $member->nickname . '的主页'));
    		$this->elementEnd('a');
    		$this->elementEnd('li');
        }
    }
    
	function groupAction()
    {        
     	if ($this->cur_user) {            
            $inids = $this->cur_user->getGroupIds();
            if (in_array($this->cur_group->id, $inids)) {
            	$this->symbol = 1;
            } else if (!$this->cur_group->hasBlocked($this->cur_user) && !$this->cur_group->closed) {
            	if ($this->cur_group->grouptype) {
            		if ($this->cur_user->haveApplied($this->cur_group->id)) {
						$this->symbol = 2;
            		}else {
            			$this->symbol = 3;
            		}
            	}else {
            		$this->symbol = 4;
            	}
            }else if($this->cur_group->closed){
            		$this->symbol = 5;
            }
        }
    }
    
    function showScripts() {
		parent::showScripts();
		$this->script('js/lshai_group.js');
		if ($this->has_bgmusic) {
			$this->script('js/lshai_bgmusic.js');
		}
    	$this->script('js/lshai_tagcloud_min.js');
	}
	
	function showUAStylesheets()
    {
    	parent::showUAStylesheets();
    	if ($this->cur_group_design) {
    		$this->element('link', array('href' => $this->cur_group_design->cssurl . '?' . SHAISHAI_VERSION, 
    			'rel' => 'stylesheet', 'type' => 'text/css', 'media' => 'screen, projection, tv'));
    	}
    }

}