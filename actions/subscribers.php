<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * List a user's subscribers
 *
 * @category Social
 * @package  LShai
 */
class SubscribersAction extends GalleryAction
{   
    function getSubs() {
    	return $this->owner->getSubscribers($this->offset, $this->limit);
    }
    
    function getTotalPages() {
    	return $this->owner_profile->subscriberCount();
    }
    
    function getViewName() {
    	return 'SubscribersHTMLTemplate';
    }
}




