<?php
/**
 * Base search action class.
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Base search action class.
 * @category Action
 */
class SearchAction extends ShaiAction
{
	var $q;
	var $resultset;
	var $total;
	
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
	
	function prepare($args) {
		if (! parent::prepare($args)) {return false;}
		$this->q = $this->trimmed('q');
		$this->addPassVariable('search_q', $this->q);
		return true;
	}
	
	function handle($args) {
		parent::handle($args);
		
		if (! empty($this->q) && $this->q != '' && $this->doSearch($args)) {
			$this->addPassVariable('res', $this->resultset);
        	$this->addPassVariable('res_cnt_total', $this->total);	
		} else {
			$this->addPassVariable('res', null);
        	$this->addPassVariable('res_cnt_total', 0);
		}
		
        $this->displayWith($this->getViewName());
	}
	
	function getViewName() {
		return '';
	}
	
    /**
     * Return true if read only.
     *
     * @return boolean true
     */
    function isReadOnly($args)
    {
        return true;
    }

}

