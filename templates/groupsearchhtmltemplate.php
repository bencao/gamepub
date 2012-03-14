<?php
/**
 * Group search template class.
 */

if (!defined('SHAISHAI')) {
    exit(1);
}


class GroupsearchHTMLTemplate extends SearchHTMLTemplate
{
	
	var $isAdvance;
	
 	function title()
    {
    	return $this->q == '' ? ('搜索' . GROUP_NAME()) : ('含有“' . $this->q . '”的' . GROUP_NAME());
    }
    
    function show($args) {
    	$this->isAdvance = $args['is_advance'];
    	parent::show($args);
    }
    
    function showSearchResults() {
    	if ($this->resultset && $this->resultset->N > 0) {
    		$terms = preg_split('/[\s,]+/', $this->q);
	        
	        $results = new SearchGroupsList($this->resultset, $this, $terms);
            $this->cnt = $results->show();
            
	        $this->resultset->free();
    	} else {
	        $this->showEmptyList();
    	}
    }
    
    function showPagination() {
    	//        $this->numpagination($page > 1, $cnt > NOTICES_PER_PAGE,
//                          $page, 'groupsearch', array('q' => $q), $total, '');
//        return $cnt;
		$this->numpagination($this->total_count, 'groupsearch', array(), 
				array('q' => $this->q), NOTICES_PER_PAGE);
    }
    
	function searchAction() {
		return common_local_url('groupsearch');
	}
    
	function showMoreSearchItems() {
//    	$this->elementStart('li');
//    	$this->element('a', array('class' => 'search', 'href' => common_local_url('subjectsearch')), '跟“' . $this->q . '”有关的话题');
//    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->element('a', array('class' => 'search', 'href' => common_local_url('peoplesearch', null, array('q' => $this->q))), '跟“' . $this->q . '”有关的玩家');
    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->element('a', array('class' => 'search', 'href' => common_local_url('noticesearch', null, array('q' => $this->q))), '跟“' . $this->q . '”有关的消息');
    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->element('a', array('class' => 'search', 'href' => common_local_url('wendasearch', null, array('q' => $this->q))), '跟“' . $this->q . '”有关的问答');
    	$this->elementEnd('li');
    }
    
	function showSearchForm() {
    	$this->elementStart('form', (array('class' => 'search', 'action' => $this->searchAction(), 'method' => 'get')));
        $this->elementStart('fieldset');
        $this->element('legend', null, '搜索');
    	
    	$this->elementStart('p');
    	$this->text('继续搜索');
    	$this->element('input', array('class' => 'text200', 'type' => 'text', 'value' => $this->q, 'name' => 'q'));
    	$this->element('input', array('class' => 'submit button76 green76', 'type' => 'submit', 'value' => '搜索', 'name' => ($this->isAdvance ? 'advance' : 'normal')));
    	$this->elementEnd('p');
    	
    	$this->elementStart('p', array('class' => 'options', 'style' => 'display:' . ($this->isAdvance ? 'none' : 'block') . ';'));
    	$this->elementStart('a', array('class' => 'toggle_advance', 'href' => '#'));
    	$this->text('更多搜索条件');
    	$this->element('small', null, '▼');
    	$this->elementEnd('a');
    	$this->text('快速搜索');
    	$this->element('a', array('href' => '#'), '魔兽世界');
    	$this->element('a', array('href' => '#'), '海淀区');
    	$this->elementEnd('p');
    	
    	$this->elementStart('div', array('class' => 'more', 'style' => 'display:' . ($this->isAdvance ? 'block' : 'none') . ';'));
    	$this->elementStart('p');
    	$this->element('label', 'title', '范围：');
    	if ($this->trimmed('bynickname')) {
    		$this->element('input', array('class' => 'checkbox', 'type' => 'checkbox', 'name' => 'bynickname', 'value' => '1', 'id' => 'bynickname', 'checked' => 'checked'));
    	} else {
    		$this->element('input', array('class' => 'checkbox', 'type' => 'checkbox', 'name' => 'bynickname', 'value' => '1', 'id' => 'bynickname'));
    	}
    	$this->element('label', array('for' => 'bynickname'), '全名');
    	if ($this->trimmed('byowner')) {
    		$this->element('input', array('class' => 'checkbox', 'type' => 'checkbox', 'name' => 'byowner', 'value' => '1', 'id' => 'byowner', 'checked' => 'checked'));
    	} else {
    		$this->element('input', array('class' => 'checkbox', 'type' => 'checkbox', 'name' => 'byowner', 'value' => '1', 'id' => 'byowner'));
    	}
    	$this->element('label', array('for' => 'byowner'), '创建人');
		if ($this->trimmed('bycategory')) {
			$this->element('input', array('class' => 'checkbox', 'type' => 'checkbox', 'name' => 'bycategory', 'value' => '1', 'id' => 'bycategory', 'checked' => 'checked'));
		} else {
    		$this->element('input', array('class' => 'checkbox', 'type' => 'checkbox', 'name' => 'bycategory', 'value' => '1', 'id' => 'bycategory'));
		}
    	$this->element('label', array('for' => 'bycategory'), '分类');
    	if ($this->trimmed('bydescription')) {
    		$this->element('input', array('class' => 'checkbox', 'type' => 'checkbox', 'name' => 'bydescription', 'value' => '1', 'id' => 'bydescription', 'checked' => 'checked'));
    	} else {
    		$this->element('input', array('class' => 'checkbox', 'type' => 'checkbox', 'name' => 'bydescription', 'value' => '1', 'id' => 'bydescription'));
    	}
    	$this->element('label', array('for' => 'bydescription'), '描述');
    	$this->elementEnd('p');
    	$this->elementStart('p');
    	$this->element('label', array('class' => 'title', 'for' => 'game'), '游戏：');
    	$this->element('input', array('class' => 'text200', 'type' => 'text', 'name' => 'game', 'id' => 'game', 'value' => $this->trimmed('game')));
    	$this->elementEnd('p');
    	$this->elementStart('p');
    	$this->element('label', 'title', '类型：');
    	$this->elementStart('select', array('name' => 'type'));
    	$this->option('', '不限', $this->trimmed('type'));
    	$this->option('0', '公开', $this->trimmed('type'));
    	$this->option('1', '私有', $this->trimmed('type'));
    	$this->elementEnd('select');
    	$this->element('label', array('for' => 'loc'), '聚居地：');
    	$this->element('input', array('class' => 'text124', 'type' => 'text', 'name' => 'loc', 'id' => 'loc', 'value' => $this->trimmed('loc')));
    	$this->element('a', array('class' => 'toggle_back', 'href' => '#'), '恢复默认');
    	$this->elementEnd('p');
    	$this->elementEnd('div');
    	
    	$this->showSearchOptions();
    	
    	$this->elementEnd('fieldset');
        $this->elementEnd('form');
    }
    
    function showScripts() {
		parent::showScripts();
		$this->script('js/lshai_privategroupjoin.js');
	}
}

class SearchGroupsList extends GroupsList
{
    var $terms = null;
    var $pattern = null;

    function __construct($group, $out, $terms)
    {
        parent::__construct($group, $out);
        $this->terms = array_map('preg_quote',
                                 array_map('htmlspecialchars', $terms));
        $this->pattern = '/('.implode('|',$terms).')/i';
    }
    
    function highlight($text)
    {
        return preg_replace($this->pattern, '<strong style="font-weight:bold;color:#6E7F02;">\\1</strong>', htmlspecialchars($text));
    }

    function show()
    {
    	$this->out->elementStart('ol', array('id' => 'groups'));
        $cnt = 0;

        while ($this->group->fetch()) {
            $cnt++;
            if($cnt > NOTICES_PER_PAGE) {
                break;
            }
            $this->showGroup();
        }
        $this->out->elementEnd('ol');

        return $cnt;
    }

    function showGroup()
    {
		$this->out->elementStart('li', array('class' => 'group', 'id' => 'group-' . $this->group->id));
		
		$logo = ($this->group->stream_logo) ? $this->group->stream_logo : User_group::defaultLogo(GROUP_LOGO_STREAM_SIZE);
        $this->showImg($logo);
        $this->showInfo();
        $this->showAction();
        
        $this->out->elementEnd('li');
    }
    
    function showInfo()
    {
    	$this->out->elementStart('p', array('class' => 'nickname'));
    	$this->out->elementStart('strong');
		$this->out->elementStart('a', array('title' => '访问'.$this->group->nickname.GROUP_NAME().'主页', 
			'href' => common_local_url('showgroup', array('id' => $this->group->id))));
    	$this->out->raw($this->highlight($this->group->nickname));
		$this->out->elementEnd('a');
		$this->out->elementEnd('strong');
		
		$this->out->elementStart('span');
     	if ($this->group->grouptype == 0) {
            $groupType = '公开' . GROUP_NAME() . '';
        }else{
        	$groupType = '私有' . GROUP_NAME() . '';
        }
		$this->out->text($groupType);
		$this->out->elementEnd('span');
		
		$this->out->elementStart('span');
		$this->out->text($this->group->memberCount() . '成员');
		$this->out->elementEnd('span');
		$this->out->elementEnd('p');
		
		$this->out->elementStart('p');
		$this->out->text('' . GROUP_NAME() . '分类：');
		$this->out->raw($this->highlight($this->group->category. ' - '. $this->group->catalog));
		$this->out->elementEnd('p');
		
		if (mb_strlen($this->group->description)>20) {
			$this->out->elementStart('p');
		    $this->out->raw($this->highlight(common_cut_string($this->group->description, 20).'...'));
		    $this->out->elementEnd('p');
		}else if($this->group->description) {
			$this->out->elementStart('p');
			$this->out->raw($this->highlight($this->group->description));
			$this->out->elementEnd('p');
		}
    }
    
    function showAction()
    {
    	$this->out->elementStart('div', array('class' => 'op'));
        
     	if ($this->out->cur_user) {
            
            $inids = $this->out->cur_user->getGroupIds();
            if (in_array($this->group->id, $inids)) {
            	// nothing
            } else if (!$this->group->hasBlocked($this->out->cur_user) && !$this->group->closed) {
            	if ($this->group->grouptype) {
            		if ($this->group->hasApplicationFor($this->out->cur_user)) {
            			$this->out->element('div', 'done', '已申请');
            		}else {
	            		$this->out->element('a', array(
			    				'href' => common_path('group/' . $this->group->id . '/applyjoin'), 
			    				'class' => 'group_apply button76 orange76'), 
		    					'申请加入');
            		}
            	} else {
            		$this->out->element('a', array('href' => common_path('group/' . $this->group->id . '/join'), 
                       'target' => '_blank', 'class' => 'button76 orange76'), '加入' . GROUP_NAME());
            	}
            }
        }
        
        $this->out->elementEnd('div');
    }
}