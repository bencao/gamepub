<?php
/**
 * Table Definition for question
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Question extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'question';                        // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $author_id;                       // int(11)  not_null multiple_key
    public $title;                           // string(32)  not_null
    public $description;                     // string(280)  not_null
    public $appendix;                        // string(280)  not_null
    public $best_answer_id;                  // int(11)  
    public $is_anonymous;                    // int(1)  
    public $is_alive;                        // int(1)  multiple_key
    public $created;                         // datetime(19)  not_null binary
    public $modified;                        // timestamp(19)  not_null unsigned zerofill binary timestamp
    public $end_time;                        // datetime(19)  not_null multiple_key binary
    public $award_amount;                    // int(11)  multiple_key
    public $answer_count;

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Question',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    static function saveNew($author_id, $title, $description, $award_amount, $is_anonymous = false, $image_url = null) {
    	$q = new Question();
    	$q->author_id = $author_id;
    	$q->title = $title;
    	$q->description = $description;
    	$q->is_alive = true;
    	$q->created = common_sql_now();
    	$q->modified = $q->created();
    	$q->end_time = common_sql_date(time() + 3600 * 24 * 30);
    	$q->award_amount = $award_amount;
    	$q->is_anonymous = $is_anonymous;
    	$qid = $q->insert();
    	
    	if ($image_url) {
    		Question_image::saveNew($qid, $image_url);
    	}
    	
    	if ($award_amount > 0) {
    		User_grade::deductScore($author_id, 10 * $award_amount);
    	}
    	
    	if ($is_anonymous) {
    		User_grade::deductScore($author_id, 10);
    	}
    	return $q;
    }
    
    static function getQuestionsCountByUserId($user_id) {
    	$q = new Question();
    	$q->whereAdd('author_id = ' . $user_id);
    	return $q->count();
    }
    
	static function getQuestionsByUserId($user_id, $offset = 0, $limit = false) {
    	$q = new Question();
    	$q->whereAdd('author_id = ' . $user_id);
    	if ($limit) {
    		$q->limit($offset, $limit);
    	}
    	$q->orderBy('id desc');
    	$q->find();
    	
    	return $q;
    }
    
	static function getMyclosedQuestionsByUserId($user_id, $offset = 0, $limit = false) {
    	$q = new Question();
    	$q->whereAdd('author_id = ' . $user_id);
    	$q->whereAdd('is_alive = 0');
    	if ($limit) {
    		$q->limit($offset, $limit);
    	}
    	$q->orderBy('id desc');
    	$q->find();
    	
    	return $q;
    }
    
    static function _getQuestionByGame($game, $offset = 0, $limit = false) {
    	$q = new Question();
    	
    	$features = array_merge(array($game->name), $game->getJobs());
    	foreach ($features as $f) {
    		$q->whereAdd("title like '%" . $f . "%'", 'OR');
    	}
    	$q->whereAdd('author_id IN (SELECT id FROM user WHERE game_id = ' . $game->id . ')', 'OR');
    	
    	if ($limit) {
    		$q->limit($offset, $limit);
    	}
    	$q->orderBy('id desc');
    	$q->selectAdd();
    	$q->selectAdd('id');
    	$q->find();
    	
    	$qs = array();
    	while ($q->fetch()) {
    		$qs[] = $q->id;
    	}
    	
    	$q->free();
		return $qs;
    }
    
    static function getQuestionByGame($game, $offset = 0, $limit = false) {
    	$ids = common_stream('question:getquestionbygame:' . $game->id, array("Question", "_getQuestionByGame"), array($game, $offset, $limit), 3600);
    	$q = new Question();
		$q->whereAdd('id in (' . implode(',',$ids). ')');
		$q->orderBy('id desc');
		$q->find();
		return $q;
    }
    
	static function getQuestionsByTitleLike($features, $offset = 0, $limit = false, $extra = false) {
    	$q = new Question();
    	foreach ($features as $f) {
    		$q->whereAdd("title like '%" . $f . "%'", 'OR');
    	}
    	if ($extra) {
    		$q->whereAdd($extra);
    	}
    	
    	if ($limit) {
    		$q->limit($offset, $limit);
    	}
    	$q->orderBy('id desc');
    	$q->find();
    	
    	return $q;
    }
    
	static function getQuestionsCountByTitleLike($features, $extra = false) {
    	$q = new Question();
    	foreach ($features as $f) {
    		$q->whereAdd("title like '%" . $f . "%'", 'OR');
    	}
    	if ($extra) {
    		$q->whereAdd($extra);
    	}
    	
    	return $q->count();
    }
    
	static function getMyansweredQuestionsByUserId($user_id, $offset = 0, $limit = false) {
		$q = new Question();
    	$a = new Answer();
    	$q->joinAdd($a);
    	$q->selectAdd();
    	$q->selectAdd('distinct question.id as id');
    	$q->whereAdd('answer.author_id = ' . $user_id);
    	$q->find();
    	
    	$ids = array();
    	while ($q->fetch()) {
    		$ids[] = $q->id;
    	}
    	
    	$qn = new Question();
    	$qn->whereAdd('id in (' . implode(',', $ids) . ')');
		if ($limit) {
    		$qn->limit($offset, $limit);
    	}
    	$qn->orderBy('id desc');
    	$qn->find();
    	
    	return $qn;
    }
    
	static function getMybestansweredQuestionsByUserId($user_id, $offset = 0, $limit = false) {
		$q = new Question();
    	$a = new Answer();
    	$q->joinAdd($a);
    	$q->selectAdd();
    	$q->selectAdd('distinct question.id as id');
    	$q->whereAdd('answer.author_id = ' . $user_id);
    	$q->whereAdd('answer.id = question.best_answer_id');
    	$q->find();
    	
    	$ids = array();
    	while ($q->fetch()) {
    		$ids[] = $q->id;
    	}
    	
    	$qn = new Question();
    	$qn->whereAdd('id in (' . implode(',', $ids) . ')');
		if ($limit) {
    		$qn->limit($offset, $limit);
    	}
    	$qn->orderBy('id desc');
    	$qn->find();
    	
    	return $qn;
    	
    	return $qn;
    }
    
	static function getAwardQuestionsCount() {
    	$q = new Question();
    	$q->whereAdd('is_alive = 1');
    	$q->whereAdd('award_amount > 0');
    	return $q->count();
    }
    
	static function getAwardQuestions($offset = 0, $limit = false) {
		$q = new Question();
    	$q->whereAdd('award_amount > 0');
    	$q->whereAdd('is_alive = 1');
    	$q->orderBy('award_amount desc, id desc');
    	if ($limit) {
    		$q->limit($offset, $limit);
    	}
    	$q->find();
    	
    	return $q;
    }
    
	static function getLatestQuestions($offset = 0, $limit = false) {
		$q = new Question();
    	$q->orderBy('id desc');
    	if ($limit) {
    		$q->limit($offset, $limit);
    	}
    	$q->find();
    	
    	return $q;
    }
    
	static function getClosingQuestions($offset = 0, $limit = false) {
		$q = new Question();
		$q->whereAdd('is_alive = 1');
    	$q->orderBy('created asc');
    	if ($limit) {
    		$q->limit($offset, $limit);
    	}
    	$q->find();
    	
    	return $q;
    }
    
    function getAnswers($offset = 0, $limit = false) {
    	$a = new Answer();
    	$a->whereAdd('question_id = ' . $this->id);
    	if ($offset && $limit) {
    		$a->limit($offset, $limit);
    	}
    	$a->find();
    	
    	return $a;
    }
    
    function close() {
    	$orig = clone($this);
    	$this->is_alive = false;
    	$this->update($orig);
    	return true;
    }
    
    function getAuthor() {
    	return Profile::staticGet('id', $this->author_id);
    }
    
    function getBestAnswer() {
    	if ($this->best_answer_id > 0) {
    		return Answer::staticGet('id', $this->best_answer_id);
    	} else {
    		return false;
    	}
    }
    
    function setBestAnswer($answer) {
    	$orig = clone($this);
    	$this->best_answer_id = $answer->id;
    	$this->is_alive = 0;
    	$this->update($orig);
    	
    	// award
    	if ($this->award_amount > 0) {
    		User_grade::addScore($answer->author_id, 10 * $this->award_amount);
    	}
    }
    
    function getQuestionImage() {
    	return Question_image::getImageUrlByQuestionId($this->id);
    }
    
    function setAwardAmount($amount) {
    	$currentlyHasAmount = ($this->award_amount > 0);
    	if ($amout == 0 || $currentlyHasAmount) {
    		return false;
    	}
    	$orig = clone($this);
    	$this->award_amount = $amount;
    	$this->update($orig);
    	return true;
    }
    
    function modifyAppendix($newContent) {
    	$orig = clone($this);
    	$this->appendix = $newContent;
    	$this->update($orig);
    }
    
    function increaseAnswerCount() {
    	$orig = clone($this);
    	$this->answer_count++;
    	$this->update($orig);
    }
    
    static function searchFor($q) {
    	$keywords = common_tokenize($q);
    	
    	$q = new Question();
    	foreach ($keywords as $k) {
    		$q->whereAdd("title like '%" . $k . "%'");
    	}
    	$q->find();
    	
    	return $q;
    }
    
    static function closeExpireQuestions() {
    	$q = new Questions();
    	$q->whereAdd("is_alive = 1");
    	$q->whereAdd("end_time < '" . common_sql_date(time()) . "'");
    	$q->find();
    	
    	while ($q->fetch()) {
    		$q->close();
    	}
    }
}