<?php
/**
 * People search template class.
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

class PeoplesearchHTMLTemplate extends SearchHTMLTemplate
{

	var $isAdvance;
	
 	function title()
    {
    	return $this->q == '' ? '搜索玩家' : ('含有“' . $this->q . '”的玩家');
    }
    
    function show($args) {
    	$this->isAdvance = $args['is_advance'];
    	parent::show($args);
    }
    
    function showSearchResults() {
    	if ($this->resultset && $this->resultset->N > 0) {
    		$terms = preg_split('/[\s,]+/', $this->q);
	        $pl = new PeopleSearchList($this, $this->resultset, null, $this->cur_user, $terms);
	        $cnt = $pl->show();
	        $this->resultset->free();
    	} else {
	        $this->showEmptyList();
    	}
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
    	$this->element('label', array('for' => 'bynickname'), '昵称');
//    	if ($this->trimmed('byno')) {
//    		$this->element('input', array('class' => 'checkbox', 'type' => 'checkbox', 'name' => 'byno', 'value' => '1', 'id' => 'byno', 'checked' => 'checked'));
//    	} else {
//    		$this->element('input', array('class' => 'checkbox', 'type' => 'checkbox', 'name' => 'byno', 'value' => '1', 'id' => 'byno'));
//    	}
//    	$this->element('label', array('for' => 'byno'), 'GamePub号');
		if ($this->trimmed('byinterest')) {
			$this->element('input', array('class' => 'checkbox', 'type' => 'checkbox', 'name' => 'byinterest', 'value' => '1', 'id' => 'byinterest', 'checked' => 'checked'));
		} else {
    		$this->element('input', array('class' => 'checkbox', 'type' => 'checkbox', 'name' => 'byinterest', 'value' => '1', 'id' => 'byinterest'));
		}
    	$this->element('label', array('for' => 'byinterest'), '兴趣');
    	if ($this->trimmed('bybio')) {
    		$this->element('input', array('class' => 'checkbox', 'type' => 'checkbox', 'name' => 'bybio', 'value' => '1', 'id' => 'bybio', 'checked' => 'checked'));
    	} else {
    		$this->element('input', array('class' => 'checkbox', 'type' => 'checkbox', 'name' => 'bybio', 'value' => '1', 'id' => 'bybio'));
    	}
    	$this->element('label', array('for' => 'bybio'), '简介');
    	$this->elementEnd('p');
    	$this->elementStart('p');
    	$this->element('label', array('class' => 'title', 'for' => 'game'), '游戏：');
    	$this->element('input', array('class' => 'text200', 'type' => 'text', 'name' => 'game', 'id' => 'game', 'value' => $this->trimmed('game')));
    	$this->elementEnd('p');
    	$this->elementStart('p');
    	$this->element('label', 'title', '性别：');
    	$this->elementStart('select', array('name' => 'sex'));
    	$this->option('', '不限', $this->trimmed('sex'));
    	$this->option('M', '男', $this->trimmed('sex'));
    	$this->option('F', '女', $this->trimmed('sex'));
    	$this->elementEnd('select');
    	$this->element('label', array('for' => 'loc'), '所在地：');
    	$this->element('input', array('class' => 'text124', 'type' => 'text', 'name' => 'loc', 'id' => 'loc', 'value' => $this->trimmed('loc')));
    	$this->element('a', array('class' => 'toggle_back', 'href' => '#'), '恢复默认');
    	$this->elementEnd('p');
    	$this->elementEnd('div');
    	
    	$this->showSearchOptions();
    	
    	$this->elementEnd('fieldset');
        $this->elementEnd('form');
    }
    
    function showPagination() {
    	//        $this->numpagination($page > 1, $cnt > NOTICES_PER_PAGE,
//                          $page, 'peoplesearch', array('q' => $q), $total, '');
//        return $cnt;
		$this->numpagination($this->total_count, 'peoplesearch', array(), 
				array('q' => $this->q), PROFILES_PER_PAGE);
    }
    
	function searchAction() {
		return common_local_url('peoplesearch');
	}
    
	function showMoreSearchItems() {
//    	$this->elementStart('li');
//    	$this->element('a', array('class' => 'search', 'href' => common_local_url('subjectsearch')), '跟“' . $this->q . '”有关的话题');
//    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->element('a', array('class' => 'search', 'href' => common_local_url('noticesearch', null, array('q' => $this->q))), '跟“' . $this->q . '”有关的消息');
    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->element('a', array('class' => 'search', 'href' => common_local_url('groupsearch', null, array('q' => $this->q))), '跟“' . $this->q . '”有关的' . GROUP_NAME());
    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->element('a', array('class' => 'search', 'href' => common_local_url('wendasearch', null, array('q' => $this->q))), '跟“' . $this->q . '”有关的问答');
    	$this->elementEnd('li');
    }
    
	function showRightsidebar()
    {
    	parent::showRightsidebar();
    	
    	if (common_current_user()) {
    		$recommends = Profile::getRecommendProfileToFollow(0, 9);
    		$this->showSearchRecommendsWidget($recommends);
    		$this->showSearchInviteWidget();
    	}
    	
//    	$this->tu->showTagcloudWidget("black");
    }
    
	function showSearchRecommendsWidget($recommends, $title='您可能感兴趣的人') {
    	if ($recommends->N > 0) {
	    	$this->elementStart('dl', 'widget recommend');
	    	$this->elementStart('dt');
	    	$this->text($title);
	    	$this->element('a', array('class' => 'toggle', 'href' => '#'), '更多');
	    	$this->elementEnd('dt');
	    	$this->elementStart('dd');
	    	$this->elementStart('ul', 'clearfix');
	    	while ($recommends->fetch()) {
		    	$this->elementStart('li');
		    	$this->elementStart('div', 'avatar');
		    	$this->elementStart('a', array('title' => ($recommends->sex == 'M' ? '他' : '她') . '与您有共同的兴趣', 'href' => $recommends->profileurl));
		    	$avatar = $recommends->getAvatar(AVATAR_STREAM_SIZE);
		    	$this->element('img', array('height' => '48', 'width' => '48', 
		    		'src' => ($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_STREAM_SIZE, $recommends->id, $recommends->sex), 
		    		'alt' => $recommends->nickname));
		    	$this->elementEnd('a');
		    	$this->elementEnd('div');
		    	$this->elementStart('p', 'nickname');
		    	$this->element('a', array('title' => ($recommends->sex == 'M' ? '他' : '她') . '与您有共同的兴趣', 'href' => $recommends->profileurl), 
		    			$recommends->nickname);
		    	$this->elementEnd('p');
		    	$this->elementEnd('li');
	    	}
	    	$this->elementEnd('ul');
	    	$this->elementEnd('dd');
	    	$this->elementEnd('dl');
    	}
    }
    
	function showSearchInviteWidget() {
    	$this->elementStart('dl', 'widget feedback');
    	$this->element('dt', null, '邀请好友');
    	$this->elementStart('dd');
    	$this->text('成功邀请朋友获得更多G币');
    	$this->element('a', array('class' => 'button76 green76', 'href' => common_path('main/invite'), 'target' => '_blank'), '邀请');
    	$this->elementEnd('dd');
    	$this->elementEnd('dl');
    }
    
    function showScripts()
    {
        parent::showScripts();  
        $this->script('js/lshai_relation.js');
    }
}

class PeopleSearchList extends ProfileList
{
    var $terms = null;
    var $pattern = null;
    
    function __construct($out, $profiles, $owner, $cur_user, $terms) {
    	parent::__construct($out, $profiles, $owner, $cur_user);
    	$this->terms = array_map('preg_quote',
                                 array_map('htmlspecialchars', $terms));

        $this->pattern = '/('.implode('|',$terms).')/i';
    }
    
	function highlight($text)
    {
        return preg_replace($this->pattern, '<strong style="font-weight:bold;color:#6E7F02;">\\1</strong>', htmlspecialchars($text));
    }  

	function showNickname($profile) {
    	$this->out->elementStart('p', 'nickname');
    	$this->out->elementStart('strong');
    	$this->out->elementStart('a', array('href' => $profile->profileurl, 'title' => '访问' . $profile->nickname . '的主页'));
    	$this->out->raw($this->highlight($profile->nickname));
    	$this->out->elementEnd('a');
    	$this->out->elementEnd('strong');
    	$this->out->element('span', null, $profile->sex == 'M' ? '男' : '女');
    	$this->out->element('span', null, $profile->followers . '人关注');
    	$this->out->elementEnd('p');
    }
    
	function showInfos($profile) {
    	$game = Game::staticGet('id', $profile->game_id);
    	$game_server = Game_server::staticGet('id', $profile->game_server_id);
    	$this->out->elementStart('p');
    	$this->out->raw('游戏：' . $this->highlight($game->name) . ' - ' . $this->highlight($game_server->name));
    	$this->out->elementEnd('p');
    	
    	$this->out->elementStart('p');
    	$this->out->raw('所在地: ' . $this->highlight($profile->location));
    	$this->out->elementEnd('p');
    }

}