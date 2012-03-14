<?php
/**
 * Notice search template class.
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

class WendasearchHTMLTemplate extends SearchHTMLTemplate
{
	var $wt;
	
	function show($args)
    {
    	$this->wt = $args['wt'];
    	parent::show($args);
    }
    
	function title()
    {
    	return $this->q == '' ? '搜索问答' : ('含有“' . $this->q . '”的问答');
    }
    
	function searchAction() {
		return common_local_url('wendasearch');
	}
	
	function showSearchOptions() {
		$this->element('input', array('type' => 'hidden', 'name' => 'wt', 'value' => $this->wt));
	}
	
	function showMoreSearchItems() {
    	$this->elementStart('li');
    	$this->element('a', array('class' => 'search', 'href' => common_local_url('noticesearch', null, array('q' => $this->q))), '跟“' . $this->q . '”有关的消息');
    	$this->elementEnd('li');
		$this->elementStart('li');
    	$this->element('a', array('class' => 'search', 'href' => common_local_url('groupsearch')), '跟“' . $this->q . '”有关的' . GROUP_NAME());
    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->element('a', array('class' => 'search', 'href' => common_local_url('peoplesearch', null, array('q' => $this->q))), '跟“' . $this->q . '”有关的玩家');
    	$this->elementEnd('li');
    }
    
    function showSearchResults() {
    	if ($this->resultset && $this->resultset->N > 0) {
    		$terms = preg_split('/[\s,]+/', $this->q);
	        $wl = new WendaList($this, $this->resultset, $terms);
	        $cnt = $wl->show();
	        $this->resultset->free();
    	} else {
	        $this->showEmptyList();
    	}
    }
    
    function showPagination() {
    	$this->numpagination($this->total_count, 'wendasearch', array(), 
				array('q' => $this->q, 'wt' => $this->wt), NOTICES_PER_PAGE);
    }
}

class WendaList {

	var $out;
	var $wendas;
	var $pattern;
	
	function __construct($out, $wendas, $terms) {
		$this->out = $out;
		$this->wendas = $wendas;
		$this->pattern = '/('.implode('|',$terms).')/i';
	}
	
	function highlight($text)
    {
        return preg_replace($this->pattern, '<strong style="font-weight:bold;color:#6E7F02;">\\1</strong>', htmlspecialchars($text));
    }
	
	function show() {
		$this->out->elementStart('ol', array('id' => 'wendas'));
		
		while ($this->wendas && $this->wendas->fetch()) {
			$hasAnswer = ($this->wendas->best_answer_id > 0);
			
			$this->out->elementStart('li', 'wenda');
			$this->out->elementStart('p', 'title');
			$this->out->elementStart('a', array('href' => common_local_url('showwenda', array('qid' => $this->wendas->id)), 'target' => '_blank'));
			$this->out->raw($this->highlight($this->wendas->title));
			$this->out->elementEnd('a');
			$this->out->elementEnd('p');
			$this->out->elementStart('p', 'cont');
			if ($hasAnswer) {
				$answer = Answer::staticGet('id', $this->wendas->best_answer_id);
				$this->out->raw($this->highlight($answer->content));
			} else {
				$this->out->raw($this->highlight($this->wendas->description));
			}
			$this->out->elementEnd('p');
			if (! $hasAnswer) {
				$this->out->elementStart('div', 'info');
				$this->out->element('span', 'date', common_date_string($this->wendas->created));
				$this->out->element('span', 'award', $this->wendas->award_amount . '铜G币');
				$this->out->elementEnd('div');
			}
			$this->out->elementEnd('li');
		}
		
		$this->out->elementEnd('ol');
	}
}