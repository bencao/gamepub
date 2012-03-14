<?php
/**
 * Table Definition for tagtions
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Tagtions extends Memcached_DataObject
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'tagtions';                     // table name
    public $tagger;                          // int(4)  primary_key not_null
    public $tagged;                          // int(4)  primary_key not_null
    public $tagid;                           // int(4)  primary_key not_null
    public $modified;                        // timestamp()   not_null default_CURRENT_TIMESTAMP

    /* Static get */
    function staticGet($k,$v=null)
    { return Memcached_DataObject::staticGet('Tagtions',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    // get all tags set for one user
    static function getTags($tagger, $tagged) {

        $tags = array();

        # XXX: store this in memcached

        $user_tag = new User_tag();
        $user_tag->query('SELECT user_tag.* ' .
                        'FROM user_tag JOIN tagtions ' .
                        'ON user_tag.id = tagtions.tagid ' .
                        'WHERE tagtions.tagger = ' . $tagger . ' ' .
                        'AND tagtions.tagged = ' . $tagged . ' ');
        while ($user_tag->fetch()) {
            $tags[] = $user_tag->tag;
        }
        $user_tag->free();
        
        return $tags;
    }
    
    // get all tag ids set for one user
    static function getTagIds($tagger, $tagged) {

        $tagIds = array();

        # XXX: store this in memcached

        $tagtions = new Tagtions();
        $tagtions->query('SELECT tagtions.* ' .
                        'FROM tagtions ' .
                        'WHERE tagtions.tagger = ' . $tagger . ' ' .
                        'AND tagtions.tagged = ' . $tagged . ' ');
        while ($tagtions->fetch()) {
            $tagIds[] = $tagtions->tagid;
        }
        $tagtions->free();
        
        return $tagIds;
    }

    // XXX:待优化
    // modify tags for one user(no create/remove user_tag)
    static function setTagsById($tagger, $tagged, $tagids) {

        $oldtagids = Tagtions::getTagIds($tagger, $tagged);

        # Delete stuff that's old that not in new

        $to_delete = array_diff($oldtagids, $tagids);

        # Insert stuff that's in new and not in old

        $to_insert = array_diff($tagids, $oldtagids);

        $tagtions = new Tagtions();

        $tagtions->tagger = $tagger;
        $tagtions->tagged = $tagged;
        	
        $tagtions->query('BEGIN');

        foreach ($to_delete as $deltag) {
            $tagtions->tagid = $deltag;
            $result = $tagtions->delete();
            if (!$result) {
                common_log_db_error($tagtions, 'DELETE', __FILE__);
                return false;
            }
        }

        foreach ($to_insert as $instag) {         
        	$tagtions->tagid = $instag;
            
        	$result = $tagtions->insert();
            if (!$result) {
                common_log_db_error($tagtions, 'INSERT', __FILE__);
                return false;
            }
        }

        $tagtions->query('COMMIT');
        $tagtions->free();

        return true;
    }
    
    // set tag(create new) when subscribing a user
    static function setFreshTags($tagger, $tagged, $tagids=array(), $newtag=null) {
    	$alltagids = array();
    	if ($newtag){
    		$user_tag = new User_tag();
    		$user_tag->tagger = $tagger;
    		$user_tag->tag = $newtag;
    		$user_tag->query('BEGIN');
    		$result = $user_tag->insert();
    	    if (!$result) {
                common_log_db_error($user_tag, 'INSERT', __FILE__);
                return false;
            }
            $user_tag->query('COMMIT');
	        $user_tag->query('SELECT user_tag.* ' .
	                        'FROM user_tag ' .
	                        'WHERE user_tag.tagger = ' . $tagger . ' ' .
	                        'AND user_tag.tag = "' . $newtag . '" ');
	        while ($user_tag->fetch()) {
	            $alltagids[] = $user_tag->id;
	        }
	        $user_tag->free();
    	}
    	
    	$alltagids = array_merge($alltagids, $tagids);
    	
        $tagtions = new Tagtions();
        $tagtions->tagger = $tagger;
        $tagtions->tagged = $tagged;

        $tagtions->query('BEGIN');

        foreach ($alltagids as $atag) {
            $tagtions->tagid = $atag;
            $result = $tagtions->insert();
            if (!$result) {
                common_log_db_error($tagtions, 'INSERT', __FILE__);
                return false;
            }
        }

        $tagtions->query('COMMIT');
        $tagtions->free();
        return true;
    }

    # Return profiles with a given tag
    static function getTagged($tagger, $tagid) {
        $profile = new Profile();
        $profile->query('SELECT profile.* ' .
                        'FROM profile JOIN tagtions ' .
                        'ON profile.id = tagtions.tagged ' .
                        'WHERE tagtions.tagger = ' . $tagger . ' ' .
                        'AND tagtions.tagid = ' . $tagid . ' ');
        $tagged = array();
        while ($profile->fetch()) {
            $tagged[] = clone($profile);
        }
        $profile->free();
        return $tagged;
    }
    
    static function addTag($tagger, $tagged, $tag) {
    	$tagtions = new Tagtions();

        $tagtions->tagger = $tagger;
        $tagtions->tagged = $tagged;
        $tagtions->tagid = $tag;
            
        return $tagtions->insert();
    }
    
	static function delTag($tagger, $tagged, $tag) {
    	$tagtions = new Tagtions();

        $tagtions->tagger = $tagger;
        $tagtions->tagged = $tagged;
        $tagtions->tagid = $tag;
            
        return $tagtions->delete();
    }
    
    static function getMyTaggedIds($tagger) {
    	$tagtions = new Tagtions();
    	$tagtions->selectAdd();
    	$tagtions->selectAdd('distinct tagged');
    	$tagtions->whereAdd('tagger = ' . $tagger);
    	$tagtions->find();

    	$res = array();
    	while ($tagtions->fetch()) {
    		$res[] = $tagtions->tagged;
    	}
    	$tagtions->free();
    	
    	return $res;
    }
    
}
