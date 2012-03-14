<?php
if (!defined('SHAISHAI')) {
    exit(1);
}

class BlacklistHTMLTemplate extends GalleryHTMLTemplate
{
	function title()
    {
    	return '我的黑名单';
    }
    
    function showGalleryTitle() {
    	return '我的黑名单';
    }
    
    function showGalleryInstruction() {
    	return '查看我的黑名单';
    }
    
    function showGalleryPagination() {
    	$this->numpagination($this->total, 'blacklist', array('uname' => $this->owner->uname), array(), PROFILES_PER_PAGE);
//        $this->numpagination($this->cur_page > 1, $this->cnt > PROFILES_PER_PAGE,
//                          $this->cur_page, 'blacklist',
//                          array('uname' => $this->owner_profile->uname), $this->total, '');
    }

    function showGalleryList()
    {
        $blacklist = new ProfileList($this, $this->subs, $this->owner, $this->cur_user);
        $this->cnt = $blacklist->show();
    }

    function showEmptyList()
    {

        $message = '目前还没有用户被您列入黑名单。';
        
        $this->tu->showEmptyListBlock($message);
        
    }
    
}

?>