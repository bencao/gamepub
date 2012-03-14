<?php 
if (!defined('SHAISHAI')) {
    exit(1);
}



class BlacklistAction extends GalleryAction
{
	function __construct() {
		parent::__construct();
		$this->no_anonymous = true;
	}
	
	function getSubs() {
		return $this->owner->getBlacklist($this->offset, $this->limit);
	}
	
	function getTotalPages() {
		return $this->owner->blacklistCount();
	}
	
	function getViewName() {
		return 'BlacklistHTMLTemplate';
	}
}

?>