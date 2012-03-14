<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class SubscriptionsHTMLTemplate extends GalleryHTMLTemplate
{
	var $tag;
	var $tag_id;
	
	function show($args)
	{
		$this->tag = $args['g_tag'];
		$this->tag_id = $args['g_tag_id'];
		parent::show($args);
	}
	
	function title()
    {
    	if ($this->is_own) {
    		return '我关注的人';
    	} else {
    		return $this->owner->nickname . '关注的人';
    	}
    }
    
	function showRightsidebar()
    {
//    	$this->tu->showOwnerInfoWidget($this->owner_profile);
//    	$this->tu->showSubInfoWidget($this->owner_profile, $this->is_own);
//    	if ($this->is_own) {
//    		$this->tu->showToolbarWidget($this->owner_profile);
//    	}
    	
    	$this->showRightSection();
    	    	
    	if ($this->cur_user) {
	    	$recommends = Profile::getRecommendProfileToFollow(0, 9);
	    	if(! empty($recommends)){
		    	$this->tu->showUserListWidget($recommends, '您可能感兴趣的人');
	    	}
    	}
    	
    	$excellents = Profile::getPopularProfileToFollow(0, 12);
    	if (($excellents->N)>0) {
	    	$this->tu->showUserListWidget($excellents, '优秀用户推荐');
    	}
    }
    
    function showRightSection() {
    	if ($this->is_own) {
    		$this->tu->showIntroWidget('我关注的人分组', '您可以对您关注的游友进行分组 ，方便您在空间按分组查看不同游友的消息。下面显示的是您现有的分组：');
			$this->tu->showTagNavigationWidget($this->cur_user, $this->trimmed('action'), $this->trimmed('gtag'), array('uname' => $this->cur_user->uname));
    		$this->showCreatePeopleGroup(); 
    		$this->tu->showIntroWidget('小提示', '分组信息仅您自己可见。');
    	} else {
    		$this->tu->showProfileDetailWidget($this->owner_profile);
    		$this->tu->showSubInfoWidget($this->owner_profile, $this->is_own);
    		$navs = new NavList_Visitor($this->owner_profile, $this->is_own);
    	    $this->tu->showNavigationWidget($navs->lists(), $this->trimmed('action'));
    	}
    }
    
	function showCreatePeopleGroup() {
    	$this->elementStart('div', array('id' =>'w_nav_create', 'class' => 'widget'));
    	$this->element('a', array('href' => common_path('ajax/edittaggroup'), 'alt' => '创建新的分组'), '+ 创建新的分组');
    	$this->elementEnd('div');
    }
    
    function showGalleryTitle() {
    	if ($this->is_own) {
    		return '我关注的人';
    	} else {
    		return $this->owner->nickname . '关注的人';
    	}
    }
    
    function showGalleryInstruction() {
    	if (! $this->is_own) {
    		return '以下为'. $this->owner->nickname. '关注的人，看看里面有没有您感兴趣的人哦~';
    	} else {
    		return '您关注的人的情况';
    	}
    }
    
    function showGalleryPagination() {
    	// show page switcher
        if($this->tag) {
            $this->numpagination($this->total, 'subscriptions', array('uname' => $this->owner->uname), 
				array('gtag' => $this->tag), PROFILES_PER_PAGE);
        } else { 
            $this->numpagination($this->total, 'subscriptions', array('uname' => $this->owner->uname), 
				array(), PROFILES_PER_PAGE);      	
        }
    }
    
	function showGallerySubOp() {
    	
    	if ($this->tag) {
	    	$this->elementStart('div', array('id' => 'sub_op'));
	    	
    		if ($this->tag != '未分组') {
	    		$this->elementStart('ul', 'op');
		    	$this->elementStart('li');
		    	$this->element('a', array('href' => '#', 'title' => '添加未分组的人至本组', 'class' => 'add', 'gtag' => $this->trimmed('gtag'),
		    		'tid' => $this->tag_id), '添加关注的人至本组');
		    	$this->elementEnd('li');
	    		$this->elementStart('li');
		    	$this->element('a', array('href' => common_local_url('edittaggroup'), 'title' => '重命名', 'class' => 'edit',
		    		'tid' => $this->tag_id), '重命名');
		    	$this->elementEnd('li');
		    	$this->elementStart('li');
		    	$this->element('a', array('href' => common_local_url('edittaggroup'), 'title' => '删除该组', 'class' => 'delete',
		    		'tid' => $this->tag_id), '删除该组');
		    	$this->elementEnd('li');
		    	$this->elementEnd('ul');
    		}
    		$this->element('strong', null, $this->tag);
    		$this->text('共有' . $this->total . '人');
    	$this->elementEnd('div');
    	} else {
    		parent::showGallerySubOp();	
    	}
    }

    function showGalleryList()
    {
        $subscriptions_list = new SubscriptionsList($this, $this->subs, $this->owner, $this->cur_user);
        $this->cnt = $subscriptions_list->show();
    }

    function showEmptyList()
    {

        if ($this->is_own) {
            $message = '目前在这个分组内您还没有关注任何人，使用 <a href="' . common_local_url('peoplesearch') . '" title="搜索用户">[搜索用户]</a>， 找找看有没有您听说过的人在这里。 ';
        } else {
            $message = sprintf('%s 目前没有关注其他人。', $this->owner->uname);
        }
        
        $this->tu->showEmptyListBlock($message);
        
    }
    
	function showScripts()
    {
    	parent::showScripts();
    	$this->script('js/lshai_taggroupedit.js');
    }
}

class SubscriptionsList extends ProfileList
{
	function showInfos($profile) {
		if (! $this->is_own) {
			parent::showInfos($profile);
		} else {
			$this->out->element('p', null, '所在地: ' . (empty($profile->location) ? '迷路中...' : $profile->location));
		
			$tagString = '';
			
			$tags = Tagtions::getTags($this->owner->id, $profile->id);
			
	        if ($tags) {
	            foreach ($tags as $tag) {
	                $tagString .= ' #' . $tag;
	            }
	        } else {
	            $tagString = ' (空) ';
	        }
			$this->out->element('p', 'pgroup', '所属分组: ' . $tagString);	
		}
		
	}
	
	function _endMoreButton($profile) {
		$this->out->elementStart('li');
    	$this->out->element('a', array('class' => 'tagother', 'title' => '修改分组', 'href' => '#', 'to' => $profile->id, 'url' => common_local_url('tagother')), '修改分组');
    	$this->out->elementEnd('li');
    	
    	$this->out->elementEnd('ul');
    }
}

?>