<?php
/**
 * Table Definition for answer
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Answer extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'answer';                          // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $author_id;                       // int(11)  not_null multiple_key
    public $question_id;                     // int(11)  not_null multiple_key
    public $content;                         // string(511)  not_null
    public $created;

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Answer',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    function modifyContent($newContent) {
    	$orig = clone($this);
    	$this->content = $newContent;
    	
    	$this->update($orig);
    	
    	return true;
    }
    
    static function saveNew($author_id, $question_id, $content) {
    	$a = new Answer();
    	$a->author_id = $author_id;
    	$a->question_id = $question_id;
    	$a->content = $content;
    	$a->created = common_sql_now();
    	$a->insert();
    	return $a;
    }
    
    static function checkDuplicate($author_id, $question_id) {
    	$a = new Answer();
    	$a->whereAdd('author_id = ' . $author_id);
    	$a->whereAdd('question_id = ' . $question_id);
    	return $a->count();
    }
    
	static function getGoodAnswersCountByUserId($user_id) {
    	$a = new Answer();
    	$a->whereAdd('answer.author_id = ' . $user_id);
    	
    	$q = new Question();
    	$q->whereAdd('question.best_answer_id = answer.id');
    	$a->joinAdd($q);
    	
    	return $a->count();
    }
    
	static function getAnswersCountByUserId($user_id) {
    	$a = new Answer();
    	$a->whereAdd('author_id = ' . $user_id);
    	return $a->count();
    }
    
    static function getAnswersByUserId($user_id, $limit = false) {
    	$a = new Answer();
    	$a->whereAdd('author_id = ' . $user_id);
    	if ($limit) {
    		$a->limit(0, $limit);
    	}
    	$a->find();
    	
    	return $a;
    }
    
	function getAuthor() {
    	return Profile::staticGet('id', $this->author_id);
    }
    
	function getQuestion() {
    	return Question::staticGet('id', $this->question_id);
    }
}
