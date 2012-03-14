<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * A list of the user's subscriptions
 *
 * @category Social
 * @package  LShai
 */


class SubscriptionsAction extends GalleryAction
{
	var $tag;
	var $tag_id;
	
	function prepare($args)
	{
		if (! parent::prepare($args)) {return false;}
		$this->tag = $this->trimmed('gtag');
		$this->tag_id = User_tag::getTagId($this->owner->id, $this->tag);
		return true;
	}
	
	function extraHandle() {
		$this->addPassVariable('g_tag', $this->tag);
		$this->addPassVariable('g_tag_id', $this->tag_id);
	}
	
	function getSubs() {
		if ($this->tag) {
			if ($this->tag == '未分组') {
				return $this->owner->getUntaggedSubscriptions($this->offset, $this->limit);
			} else {
            	return $this->owner->getTaggedSubscriptions($this->tag, $this->offset, $this->limit);
			}
        } else {
            return $this->owner->getSubscriptions($this->offset, $this->limit);
        }
	}
	
	function getTotalPages() {
		if ($this->tag) {
			if ($this->tag == '未分组') {
				return $this->owner_profile->unTaggedSubscriptionsCount();
			} else {
				return $this->owner_profile->taggedSubscriptionsCount($this->tag);
			}
		} else {
			return $this->owner_profile->subscriptionCount();
		}
	}
	
	function getViewName() {
		return 'SubscriptionsHTMLTemplate';
	}
}
