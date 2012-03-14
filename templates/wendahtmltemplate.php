<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class WendaHTMLTemplate extends PublictwocolumnHTMLTemplate
{
	function title()
    {
    	return 'GamePub酒馆问答';
    }
	
	function show($args) {
		$cur_user = $args['cur_user'];
		if ($cur_user) {
			$this->cur_user_profile = $cur_user->getProfile();
		}
		$this->questionCount = $args['questionCount'];
		$this->answerCount = $args['answerCount'];
		$this->goodAnswerCount = $args['goodAnswerCount'];
		$this->questions = $args['questions'];
		$this->awardQuestions = $args['awardQuestions'];
		parent::show($args);
	}
	
	function showShaishaiScripts() {
		parent::showShaishaiScripts();
		$this->script('js/lshai_wenda.js');
	}
	
	function showContent()
    {
    	$this->elementStart('div', 'clearfix');
    	
    	$this->tu->startFormBlock(array('class' => 'qsearch',
    		'action' => common_local_url('wendasearch'), 'method' => 'post'), '搜索问题');
    	$this->element('input', array('name' => 'q', 'class' => 'text', 'type' => 'text'));
    	$this->element('input', array('type' => 'submit', 'class' => 'submit', 'name' => 'submit', 'value' => '搜索答案'));
    	$this->element('a', array('href' => common_local_url('newwenda'), 'class' => 'ask'), '我要提问');
    	$this->element('input', array('type' => 'submit', 'class' => 'submit', 'name' => 'submit', 'value' => '我要回答'));
    	$this->tu->endFormBlock();
    	
    	$this->elementStart('h4', 'qtip');
    	$this->text('您在游戏中遇到什么问题了？ 在这里提问吧，游友会给你耐心的解答。同时你也可以回答别人的问题哦，如果你的答案被采纳，提问者悬赏的G币就是你的啦，同时你在酒馆中的经验等级也会随之上升。');
    	$this->elementEnd('h4');
    	
    	$this->elementStart('div', 'qinfo');
    	if ($this->cur_user) {
	    	$this->elementStart('div', 'avatar');
	    	$this->elementStart('a', array('href' => $this->cur_user->profileurl));
	    	$this->element('img', array('src' => $this->cur_user_profile->avatarUrl(AVATAR_PROFILE_SIZE), 'alt' => $this->cur_user->nickname));
	    	$this->elementEnd('a');
	    	$this->elementEnd('div');
	    	
	    	$this->elementStart('div', 'profile');
	    	
	    	$this->elementStart('p', 'nickname');
	    	$this->element('a', array('href' => $this->cur_user->profileurl), $this->cur_user->nickname);
	    	$this->elementEnd('p');
//	    	$this->element('p', null, JOB_NAME() . '：' . $this->cur_user->game_job);
	    	
	    	$gradeinfo = $this->cur_user_profile->getUserUpgradePercent();    	
	    	$this->elementStart('p', 'level');
	    	$this->element('strong', null, '等级：');
	    	$this->raw('<span>' . $gradeinfo['grade'] . '</span>级');
	    	$scoreneed = ($gradeinfo['nextScore'] - $gradeinfo['score']) > 0 ? ($gradeinfo['nextScore'] - $gradeinfo['score']):0;
	    	$follneed = ($gradeinfo['nextfollowers'] - $this->cur_user_profile->followers) > 0 ? ($gradeinfo['nextfollowers'] - $this->cur_user_profile->followers):0;
	    	$this->elementStart('a', array('href'=>'#', 'title'=>'要升到'. ($gradeinfo['grade']+1) . 
	    	                         '级，您还需要' . ((int) ($scoreneed/10)) . '个铜G币和' . $follneed . '个关注者。', 'class' => 'progress'));
	    	$this->element('em', array('style' => 'width: ' . $gradeinfo['percent'] . '%;'));
	    	$this->elementEnd('a');
	    	$this->elementEnd('p');
	    	
	    	$this->elementStart('p', array('class' => 'point'));
	    	$this->element('strong', null, '财富：');
	    	$wealth = $this->cur_user_profile->getUserScoreDetail();
	    	$this->elementStart('span');
	    	$this->element('em', array('class' => 'gold', 'title' => '金G币'), $wealth['gold']);
	    	$this->element('em', array('class' => 'silver', 'title' => '银G币'), $wealth['silver']);
	    	$this->element('em', array('class' => 'bronze', 'title' => '铜G币'), $wealth['bronze']);
	    	$this->elementEnd('span');
	    	$this->elementEnd('p');
	    	
	    	$this->elementEnd('div');
    	} else {
    		$this->elementStart('div', 'avatar');
	    	$this->elementStart('a', array('href' => '#'));
	    	$this->element('img', array('src' => Avatar::defaultImage(AVATAR_PROFILE_SIZE, 100011), 'alt' => '游客'));
	    	$this->elementEnd('a');
	    	$this->elementEnd('div');
	    	
    		$this->elementStart('div', 'profile');
	    	
	    	$this->elementStart('p', 'nickname');
	    	$this->element('a', array('href' => '#'), '游客');
	    	$this->elementEnd('p');
	    	$this->element('p', null, JOB_NAME() . '：江湖小虾米');
	    	$this->elementEnd('div');
    	}
    	
    	$this->elementStart('ul');
    	$this->elementStart('li');
    	$this->element('span', null, '提问');
    	$this->text($this->questionCount);
    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->element('span', null, '回答');
    	$this->text($this->answerCount);
    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$percent = $this->answerCount ? (int) ($this->goodAnswerCount * 100 / $this->answerCount) : 0;
    	$this->element('span', null, '被采纳');
    	$this->text($this->goodAnswerCount . '(' . $percent . '%)');
    	$this->elementEnd('li');
    	$this->elementEnd('ul');
    	
    	$this->elementEnd('div');
    	
    	$this->elementEnd('div');
    	
    	if ($this->cur_user) {
	    	$this->elementStart('div', array('class' => 'qlist', 'id' => 'myqlist'));
	    	$this->elementStart('ul');
	    	$this->elementStart('li', 'active');
	    	$this->element('a', array('href' => '#', 'type' => 'mylatest'), '我的新提问');
	    	$this->elementEnd('li');
	    	$this->elementStart('li');
	    	$this->element('a', array('href' => '#', 'type' => 'myclosed'), '已关闭的提问');
	    	$this->elementEnd('li');
	    	$this->elementStart('li');
	    	$this->element('a', array('href' => '#', 'type' => 'mybestanswered'), '被采纳的提问');
	    	$this->elementEnd('li');
	    	$this->elementStart('li');
	    	$this->element('a', array('href' => '#', 'type' => 'myanswered'), '参与回答的提问');
	    	$this->elementEnd('li');
	    	$this->elementEnd('ul');
	    	$this->elementStart('table');
	    	$this->elementStart('thead');
	    	$this->elementStart('tr');
	    	$this->element('td', 'tt', '标题');
	    	$this->element('td', null, '悬赏');
	    	$this->element('td', null, '回答数');
	    	$this->element('td', null, '状态');
	    	$this->element('td', null, '提问时间');
	    	$this->elementEnd('tr');
	    	$this->elementEnd('thead');
	    	$this->elementStart('tbody');
	    	
	    	$cnt = 0;
	    	while ($cnt < QUESTIONS_PER_PAGE
	    		&& $this->questions 
	    		&& $this->questions->fetch()) {
		    	$this->elementStart('tr');
		    	$this->elementStart('td', 'tt');
		    	$this->element('a', array('href' => common_local_url('showwenda', array('qid' => $this->questions->id))), $this->questions->title);
		    	$this->elementEnd('td');
		    	$this->element('td', null, $this->questions->award_amount . '铜');
		    	$this->element('td', null, $this->questions->answer_count);
		    	$this->element('td', null, $this->questions->is_alive ? '待解决' : '已关闭');
		    	$this->element('td', null, common_date_string($this->questions->created));
		    	$this->elementEnd('tr');
		    	$cnt ++;
	    	}
	    	
	    	if ($cnt == 0) {
	    		$this->element('td', array('colspan' => '5'), '没有记录');
	    	}
	    	
	    	$this->elementEnd('tbody');
	    	
	    	if ($this->questions->N > QUESTIONS_PER_PAGE) {
		    	$this->elementStart('tfoot');
		    	$this->elementStart('tr');
		    	$this->element('td', array('colspan' => '4'));
		    	$this->elementStart('td');
		    	$this->element('a', array('href' => '#', 'page' => ($this->cur_page + 1), 'type' => 'mylatest'), '下一页>>');
		    	$this->elementEnd('td');
		    	$this->elementEnd('tr');
		    	$this->elementEnd('tfoot');
	    	}
	    	$this->elementEnd('table');
	    	$this->elementEnd('div');
    	}
    	
    	$this->elementStart('div', array('class' => 'qlist', 'id' => 'otherqlist'));
    	$this->elementStart('ul');
    	$this->elementStart('li', 'active');
    	$this->element('a', array('href' => '#', 'type' => 'award'), '悬赏的提问');
    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->element('a', array('href' => '#', 'type' => 'latest'), '最新提问');
    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->element('a', array('href' => '#', 'type' => 'closing'), '快到期的提问');
    	$this->elementEnd('li');
    	
    	$this->elementEnd('ul');
    	$this->elementStart('table');
    	$this->elementStart('thead');
    	$this->elementStart('tr');
    	$this->element('td', 'tt', '标题');
    	$this->element('td', null, '悬赏');
    	$this->element('td', null, '回答数');
    	$this->element('td', null, '状态');
    	$this->element('td', null, '提问时间');
    	$this->elementEnd('tr');
    	$this->elementEnd('thead');
    	$this->elementStart('tbody');
    	
    	$cnt = 0;
    	
    	while ($cnt < QUESTIONS_PER_PAGE
    		&& $this->awardQuestions 
    		&& $this->awardQuestions->fetch()) {
	    	$this->elementStart('tr');
	    	$this->elementStart('td', 'tt');
	    	$this->element('a', array('href' => common_local_url('showwenda', array('qid' => $this->awardQuestions->id))), $this->awardQuestions->title);
	    	$this->elementEnd('td');
	    	$this->element('td', null, $this->awardQuestions->award_amount . '铜');
	    	$this->element('td', null, $this->awardQuestions->answer_count);
	    	$this->element('td', null, $this->awardQuestions->is_alive ? '待解决' : '已关闭');
	    	$this->element('td', null, common_date_string($this->awardQuestions->created));
	    	$this->elementEnd('tr');
	    	$cnt ++;
    	}
    	
    	if ($cnt == 0) {
    		$this->element('td', array('colspan' => '5'), '没有记录');
    	}
    	
    	$this->elementEnd('tbody');
    	
    	if ($this->awardQuestions && $this->awardQuestions->N > QUESTIONS_PER_PAGE) {
	    	$this->elementStart('tfoot');
	    	$this->elementStart('tr');
	    	$this->element('td', array('colspan' => '4'));
	    	$this->elementStart('td');
	    	$this->element('a', array('href' => '#', 'page' => ($this->cur_page + 1), 'type' => 'award'), '下一页>>');
	    	$this->elementEnd('td');
	    	$this->elementEnd('tr');
	    	$this->elementEnd('tfoot');
    	}
    	
    	$this->elementEnd('table');
    	$this->elementEnd('div');
    }
}