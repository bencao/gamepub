<?php
/**
 * Base search template class.
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

abstract class SearchHTMLTemplate extends OtherHTMLTemplate
{
	var $q;
	var $resultset;
	var $total_count;
	
	function othertitle() {
		return $this->title();
	}
    
    function show($args) {
    	$this->q = $args['search_q'];
    	$this->resultset = $args['res'];
    	$this->total_count = $args['res_cnt_total'];
    	parent::show($args);
    }
    
    function showMoreSearchItems() {}
	
    function showRightsidebar()
    {
    	if ($this->q) {
	    	$this->elementStart('dl', 'widget moresearch');
	    	$this->element('dt', null, '更多相关的搜索');
	    	$this->elementStart('dd');
	    	$this->elementStart('ul');
	    	$this->showMoreSearchItems();
	    	$this->elementEnd('ul');
	    	$this->elementEnd('dd');
	    	$this->elementEnd('dl');
    	}
    	$this->tu->showTagcloudWidget("black");
    }

    function showContent() {
    	$this->showSearchForm();
        $this->showSearchResults();
        $this->showPagination();
    }
    
    function showPagination() {
    	
    }
    
	function showSearchResults() {}
	
	function searchAction() {
		return common_local_url('peoplesearch');
	}
    
    function showSearchForm() {
//    	$this->tu->startFormBlock(array('class' => 'search', 'action' => $this->searchAction(), 'method' => 'get'), '搜索');
    	
    	$this->elementStart('form', (array('class' => 'search', 'action' => $this->searchAction(), 'method' => 'get')));
        $this->elementStart('fieldset');
        $this->element('legend', null, '搜索');
    	
    	$this->elementStart('p');
    	$this->text('继续搜索');
    	$this->element('input', array('class' => 'text200', 'type' => 'text', 'value' => $this->q, 'name' => 'q'));
    	$this->element('input', array('class' => 'submit button76 green76', 'type' => 'submit', 'value' => '搜索'));
    	$this->elementEnd('p');
    	
    	$this->showSearchOptions();
    	
    	$this->elementEnd('fieldset');
        $this->elementEnd('form');
        
//    	$this->tu->endFormBlock();
    }
    
    function showSearchOptions() {}
    
	function showEmptyList()
    {
        $emptymsg = array();
        $emptymsg['dt'] = '没有搜到您需要的结果。';
        $this->tu->showEmptyListBlock($emptymsg);
    }
    
	function showScripts()
    {
        parent::showScripts();
        $this->script('js/lshai_search.js');
        $this->script('js/lshai_tagcloud_min.js');
    }
}