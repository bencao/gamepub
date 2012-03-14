<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class ShowwendaHTMLTemplate extends RightsidebarHTMLTemplate
{
	function showShaishaiScripts() {
		parent::showShaishaiScripts();
		$this->script('js/lshai_relation.js');
		$this->script('js/lshai_showwenda.js');
	}
	
	function show($args) {
		$this->question = $args['question'];
		$this->answers = $args['answers'];
		$this->questionAuthor = $this->question->getAuthor();
		$this->cur_user = $args['cur_user'];
		$this->is_own = $this->cur_user && $this->cur_user->id == $this->questionAuthor->id;
		
		parent::show($args);
	}
	
	function showRightsidebar() {
		$this->tu->showProfileDetailWidget($this->questionAuthor);
        
    	$this->tu->showSubInfoWidget($this->questionAuthor, $this->is_own, ! $this->cur_user);

		if( $this->is_own) {
        	$navs = new NavList_MyNotices($this->questionAuthor);
    	    $this->tu->showNavigationWidget($navs->lists(), $this->trimmed('action'));
        } else {
            $navs = new NavList_Visitor($this->questionAuthor, $this->is_own);
    	    $this->tu->showNavigationWidget($navs->lists(), $this->trimmed('action'), ! $this->cur_user);
        }
        
    	$this->tu->showGroupsWidget($this->questionAuthor, 6, $this->is_own, ! $this->cur_user);
    	$subscriptions = $this->questionAuthor->getSubscriptions(0, 18);
    	$this->tu->showUserListWidget($subscriptions, Profile::displayName($this->questionAuthor->sex, $this->is_own) . '关注的人');
    	if ($this->cur_user) {
    		$this->tu->showInviteWidget();
    	}
	}
	
	function showContent() {
		
		$this->element('h2', 'question', '酒馆问答 - 问题详情');
		
		$this->_showQuestion();
		
		$this->_showMyAnswer();
		
		$this->_showAnswers();
	}
	
	function _showMyAnswer() {
		if ($this->question->is_alive && ! $this->is_own) {
			$this->elementStart('dl', array('class' => 'myanswer', 'style' => 'display:none;'));
			$this->element('dt', null, '我来回答');
			$this->elementStart('dd');
			$this->tu->startFormBlock(array('action' => common_local_url('answernew'), 'method' => 'post'), '新建回答');
			$this->element('input', array('type' => 'hidden', 'name' => 'qid', 'value' => $this->question->id));
			$this->element('textarea', array('name' => 'content'));
			$this->elementStart('div', 'op clearfix');
			$this->element('input', array('type' => 'checkbox', 'class' => 'checkbox', 'name' => 'asnotice', 'value' => '1', 'id' => 'asnotice'));
			$this->element('label', array('for' => 'asnotice'), '同时发一条新消息');
		    if ($this->cur_user) {
				$this->element('input', array('type' => 'submit', 'class' => 'submit button76 green76', 'value' => '发表'));
			} else {
				$this->element('a', array('href' => common_local_url('register'), 'class' => 'trylogin submit button76 green76', 'rel' => 'nofollow'), '发表');
			}
			
			$this->elementEnd('div');
			$this->tu->endFormBlock();
			$this->elementEnd('dd');
			$this->elementEnd('dl');
		}
	}
	
	function _showAnswers() {
		$this->elementStart('dl', 'answers');
		$this->elementStart('dt');
		$this->raw('回答共<span>' . $this->answers->N . '</span>条');
		if ($this->cur_user) {
        	if ($this->cur_user->id != $this->question->author_id) {
        		$this->element('a', array('class' => 'report', 'href' => '#', 'url' => common_local_url('illegalreport'), 'to' => $this->question->author_id, 'title' => '举报这个问题'), '举报');
        	}
        } else {
        	$this->element('a', array('class' => 'trylogin', 'title' => '举报这个游戏', 'href' => common_local_url('register'), 'rel' => 'nofollow'), '举报');
        }
		$this->elementEnd('dt');
		$this->elementStart('dd');
		$this->elementStart('ul');
		while ($this->answers->fetch()) {
			$qAuthor = $this->answers->getAuthor();
			$this->elementStart('li', array('class' => 'notice', 'aid' => $this->answers->id));
			$this->elementStart('div', 'avatar');
			$this->elementStart('a', array('href' => $qAuthor->profileurl));
			$this->element('img', array('src' => $qAuthor->avatarUrl(AVATAR_STREAM_SIZE), 'alt' => $qAuthor->nickname));
			$this->elementEnd('a');
			$this->elementEnd('div');
			$this->elementStart('h3');
			$this->element('a', array('href' => $qAuthor->profileurl, 'title' => '访问' . $qAuthor->nickname . '的主页'), $qAuthor->nickname);
			if ($this->answers->id == $this->question->best_answer_id) {
				$this->text('最佳答案！');
			}
			$this->elementEnd('h3');
			$this->elementStart('div', 'content');
			$this->text($this->answers->content);
			$this->elementEnd('div');
			$this->elementStart('div', 'bar');
			$this->elementStart('div', 'info');
			$dt = common_date_iso8601($this->answers->created);
			$this->element('span', array('class' => 'time', 'title' => $dt),
                            common_date_string($this->answers->created));
			$this->elementEnd('div');
			$this->elementStart('ul', 'op');
			
			if ($this->is_own
				&& $this->question->best_answer_id == 0) {
				// is question author
				$this->elementStart('li', 'bestanswer');
				$this->element('a', array('href' => common_local_url('answerbest'), 'aid' => $this->answers->id), '采纳');
				$this->elementEnd('li');
			} else if ($this->cur_user && $this->answers->author_id == $this->cur_user->id) {
				// is answer author
				$this->elementStart('li', 'modifyanswer');
				$this->element('a', array('href' => common_local_url('answermodify'), 'aid' => $this->answers->id), '修改');
				$this->elementEnd('li');
			} else if ($this->cur_user) {
//				$this->elementStart('li', 'usefulanswer');
//				$this->element('a', array('href' => common_local_url('answeruseful'), 'aid' => $this->answers->id), '有用');
//				$this->elementEnd('li');
			}
			
			$this->elementEnd('ul');
			$this->elementEnd('div');
			$this->elementEnd('li');
		}
		$this->elementEnd('ul');
		$this->elementEnd('dd');
		$this->elementEnd('dl');
	}
	
	function _showQuestion() {
		$author = User::staticGet('id', $this->question->author_id);
		
		$this->elementStart('dl', 'question');
		
		if ($this->question->is_alive) {
			$this->elementStart('dt');
			$this->text('待解决');
		} else if ($this->question->best_answer_id > 0) {
			$this->elementStart('dt', 'solved');
			$this->text('已解决');
		} else {
			$this->elementStart('dt');
			$this->text('已关闭');
		}
		$this->elementStart('span');
		if ($this->question->is_anonymous) {
			$this->text('匿名提问');
		} else {
			$this->text('提问人：');
			$this->element('a', array('href' => $author->profileurl), $author->nickname);
		}
		$this->elementEnd('span');
		$this->elementEnd('dt');
		$this->elementStart('dd');
		$this->element('h4', null, $this->question->title);
		$this->elementStart('p', 'info');
		if ($this->question->award_amount > 0) {
			$this->element('span', 'award', '悬赏：' . $this->question->award_amount . '铜G币');
			$this->text(' - ');
		}
		$millseconds = strtotime($this->question->end_time) - time();
		$days = intval($millseconds / (24 * 3600));
		$hours = intval(($millseconds - 24 * 3600 * $days) / 3600);
		$this->text('离问题结束还有' . $days . '天' . $hours . '小时');
		$this->elementEnd('p');
		$this->elementStart('div', 'content');
		$this->element('span', null, $this->question->description);
		
		$imageurl = $this->question->getQuestionImage();
		if ($imageurl) {
			$this->elementStart('div', 'image_message');
			$this->elementStart('div', 'smallpicture');
			$this->elementStart('a', array('href' => '#', 'class' => 'smallimagebtn'));
			$this->element('img', array('class' => 'smallimage', 'src' => $imageurl));
			$this->elementEnd('a');
			$this->elementEnd('div');
			$this->elementStart('div', array('class' => 'bigpicture rounded5', 'style' => 'display:none;'));
			$this->elementStart('div', 'btnbanel');
			$this->element('cite', 'cite');
			$this->elementStart('cite');
			$this->element('a', array('class' => 'pickpicture', 'href' => '#'), '收起');
			$this->elementEnd('cite');
			$this->element('cite', 'cite', '|');
			$this->elementStart('cite');
			$this->element('a', array('class' => 'primitivepicture', 'href' => $imageurl), '原始图片');
			$this->elementEnd('cite');
			$this->element('cite', 'cite', '|');
			$this->elementStart('cite');
			$this->element('a', array('class' => 'rightrotate', 'href' => '#'), '向右转');
			$this->elementEnd('cite');
			$this->element('cite', 'cite', '|');
			$this->elementStart('cite');
			$this->element('a', array('class' => 'leftrotate', 'href' => '#'), '向左转');
			$this->elementEnd('cite');
			$this->elementEnd('div');
			$this->elementStart('div', 'wrappicture');
			$this->elementStart('a', array('class' => 'bigimagebtn', 'href' => '#'));
			$this->element('img', array('class' => 'bigimage', 'src' => $imageurl));
			$this->elementEnd('a');
			$this->elementEnd('div');
			$this->elementEnd('div');
			$this->elementEnd('div');
			$this->elementEnd('div');
		}
		$this->elementEnd('div');
		if ($this->question->is_alive && ! $this->is_own) {
			$this->element('a', array('href' => '#', 'class' => 'toanswer button94 orange94'), '我来回答');
		}
		$this->elementEnd('dd');
		$this->elementEnd('dl');
	}

}