<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class GalleryHTMLTemplate extends OwnerdesignHTMLTemplate
{
	var $owner_profile = null;
    var $tag = null;
    var $total = null;
    var $subs = null;
    
    function show($args) {
    	$this->owner_profile =  $args['owner_profile'];
    	$this->total = $args['g_sub_total'];
    	$this->subs = $args['g_subs'];
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
    
	function showRightsidebar()
    {	
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
    		$this->tu->showOwnerInfoWidget($this->owner_profile);
    		$this->tu->showSubInfoWidget($this->owner_profile, $this->is_own);
    		$this->tu->showToolbarWidget($this->owner_profile);
    	} else {
    		$this->tu->showProfileDetailWidget($this->owner_profile);
    		$this->tu->showSubInfoWidget($this->owner_profile, $this->is_own);
    		$navs = new NavList_Visitor($this->owner_profile, $this->is_own);
    	    $this->tu->showNavigationWidget($navs->lists(), $this->trimmed('action'));
    	}
    }
    
	function showScripts()
    {
    	parent::showScripts();
    	$this->script('js/lshai_relation.js');
    }
    
    function showContent() {
    	// show title
    	$this->tu->showTitleBlock($this->showGalleryTitle(), 'galleries');
    	
//    	$this->tu->showPageInstructionBlock($this->showGalleryInstruction());
    	
    	$navs = new NavList_Relation($this->owner_profile, $this->is_own);
        
        $this->tu->showTabNav($navs->lists(), $this->trimmed('action'));
        
        $this->showGallerySubOp();
        
//        $this->showGalleryExtraHead();
    	
    	// show profiles
        if ($this->subs && $this->subs->N > 0) {
            $this->showGalleryList();
        } else {
        	$this->cnt = 0;
        	$this->showEmptyList();
        }
        $this->subs->free();
    	
    	$this->showGalleryPagination();
    	
    }
    
    function showGallerySubOp() {
    	$this->elementStart('div', array('id' => 'sub_op'));
    	$this->element('strong', null, $this->showGalleryTitle());
    	$this->text('共有' . $this->total . '人');
    	$this->elementEnd('div');
    }
    
    function showGalleryTitle() {
    	return '';
    }
    
    function showGalleryInstruction() {
    	return '';
    }
    
//    function showGalleryExtraHead() {}
    
    function showGalleryPagination() {}
    
    function showGalleryList() {}
    
    function showEmptyList() {}
    
}

?>