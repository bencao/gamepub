<?php
/**
 * Table Definition for user_tag
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class User_tag extends Memcached_DataObject
{
	###START_AUTOCODE
	/* the code below is auto generated do not remove the above tag */

	public $__table = 'user_tag';                        // table name
	public $id;                              // int(11)  not_null primary_key auto_increment
	public $tagger;                          // int(11)  not_null multiple_key
	public $tag;                             // string(64)  not_null binary

	/* Static get */
	function staticGet($k,$v=NULL)
	{ return Memcached_DataObject::staticGet('User_tag',$k,$v); }

	/* the code above is auto generated do not remove the tag below */
	###END_AUTOCODE

	// Add tags in tag management
	// tagger - current user id
	// tags - the array of new tags to be added(we need to make sure the array is all new tags)
	//    static function addTags($tagger, $tags) {
	//    	$user_tag = new User_tag();
	//        $user_tag->tagger = $tagger;
	//        $user_tag->query('BEGIN');
	//        foreach ($tags as $newtag){
	//        	$user_tag->tag = $newtag;
	//	        $result = $user_tag->insert();
	//	    	if (!$result) {
	//	            common_log_db_error($user_tag, 'INSERT', __FILE__);
	//	            return false;
	//	    	}
	//    	}
	//    	$user_tag->query('COMMIT');
	//    	return true;
	//    }

	static function addATag($tagger, $tag)
	{
		$ut = new User_tag();
		$ut->whereAdd("tagger = " . $tagger);
		$ut->whereAdd("tag = '" . $tag  . "'");
		$cnt = $ut->count();
		if ($cnt > 0) {
			return false;
		}
		
		$user_tag = new User_tag();
		$user_tag->tagger = $tagger;
		$user_tag->tag = $tag;
		$result = $user_tag->insert();
		if (!$result) {
			common_log_db_error($user_tag, 'INSERT', __FILE__);
			return false;
		}
		return $result;
	}

	static function updateATag($tagger, $tagid, $newtag)
	{
		$ut = new User_tag();
		$ut->whereAdd("tagger = " . $tagger);
		$ut->whereAdd("tag = '" . $newtag . "'");
		$cnt = $ut->count();
		if ($cnt > 0) {
			return false;
		}
		
		$user_tag = new User_tag();
		$user_tag->tagger = $tagger;
		$user_tag->id = $tagid;
		if ($user_tag->find()) {
			$orig = clone($user_tag);
			$user_tag->tag = $newtag;
			return $user_tag->update($orig);
		} else {
			return false;
		}
	}

	// Remove tags in tag management
	// tagger - current user id
	// tags - the array of new tag ids to be removed
	static function deleteTags($tagger, $tagids) {
		$user_tag = new User_tag();
		$user_tag->query('BEGIN');
		foreach ($tagids as $deltag){
			$user_tag->id = $deltag;
			$result = $user_tag->delete();
			if (!$result) {
				common_log_db_error($user_tag, 'DELETE', __FILE__);
				return false;
			}
		}
		 
		$tagtions = new Tagtions();
		foreach ($tagids as $tagid) {
			$tagtions->query('DELETE FROM tagtions ' .
                    'WHERE tagtions.tagger = ' . $tagger . ' ' .
                    'AND tagtions.tagid = ' . $tagid . ' ');
		}
		$user_tag->query('COMMIT');
		$user_tag->free();
		$tagtions->free();
		return true;
	}
	
	static function getTagid($tagger, $tag){
		$user_tag = new User_tag();
		$user_tag->query('SELECT id FROM user_tag WHERE tagger = '. $tagger.
    	' AND tag = "'. $tag. '"');
		if ($user_tag->fetch()) {
			return $user_tag->id;
		}else {
			return false;
		}
	}

	function getTagsByTagger($tagger)
	{
		$user_tag = new User_tag();
		$user_tag->whereAdd("tagger='" . $tagger . "'");
		$user_tag->find();
		 
		$tags = array();
		while ($user_tag->fetch())
		{
			$tags[] = array('id' => $user_tag->id,
    		'tag' => $user_tag->tag);
		}
		 
		return $tags;
	}

    function deleteByTaggerAndTag()
    {
    	$this->query("delete from user_tag where tagger='"
    		. $this->tagger . "' and tag='" . $this->tag . "'");
    }

}