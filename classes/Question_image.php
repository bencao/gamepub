<?php
/**
 * Table Definition for question_image
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Question_image extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'question_image';                  // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $question_id;                     // int(11)  not_null multiple_key
    public $image_url;                       // string(255)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Question_image',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    static function saveNew($question_id, $image_url) {
    	$qi = new Question_image();
    	$qi->question_id = $question_id;
    	$qi->image_url = $image_url;
    	return $qi->insert();
    }
    
    static function getImageUrlByQuestionId($question_id) {
    	$qi = new Question_image();
    	$qi->whereAdd('question_id = ' . $question_id);
    	$qi->find();
    	
    	if ($qi && $qi->fetch()) {
    		return $qi->image_url;	
    	} else {
    		return false;
    	}
    }
}
