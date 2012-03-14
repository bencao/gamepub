<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class SubscribersHTMLTemplate extends GalleryHTMLTemplate
{
    function title()
    {
    	if ($this->is_own) {
    		return '关注我的人';
    	} else {
    		return '关注' . $this->owner->nickname . '的人';
    	}
    }
    
    function showGalleryTitle() {
    	if ($this->is_own) {
    		return '关注我的人';
    	} else {
    		return '关注' . $this->owner->nickname . '的人';
    	}
    }
    
    function showGalleryInstruction() {
    	return '以下为关注'. $this->owner_profile->nickname. '的人，看看里面有没有您认识的人哦~';
    }

    function showGalleryList()
    {
        $subscribers_list = new ProfileList($this, $this->subs, $this->owner, $this->cur_user);
        $this->cnt = $subscribers_list->show();
    }
    
    function showGalleryPagination() {
//    	$this->numpagination($this->cur_page > 1, $this->cnt > PROFILES_PER_PAGE,
//                          $this->cur_page, 'subscribers',
//                          array('uname' => $this->owner->uname), $this->total, '');
                          
        $this->numpagination($this->total, 'subscribers', array('uname' => $this->owner->uname), 
				array(), PROFILES_PER_PAGE);
    }

    function showEmptyList()
    {
        if (! $this->is_anonymous) {
            if ($this->is_own) {
                $message = '您没有关注者。先去关注您认识的人，他们很可能也会回过头来关注您哦。';
            } else {
                $message = sprintf('%s没有关注者，想成第一个吗？', $this->owner->uname);
            }
        } else {
            $message = sprintf('%s没有关注者， 赶紧[注册一个帐号](%%%%action.%s%%%%)成为第一位关注者吧！',
                               $this->owner->uname, 'register');
        }

        $this->tu->showEmptyListBlock($message);
    }
}

?>