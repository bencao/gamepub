<?php
// 此表的设计用途是用来存放系统人工或通过数据挖掘为用户定义的一些tag，再向其他用户推荐游友时，这些tag会很有价值。

/**
 * Table Definition for user_def_tag
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class User_def_tag extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'user_def_tag';                    // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $user_id;                         // int(11)  multiple_key
    public $tag;                             // string(64)  

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('User_def_tag',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    function saveTags($userid, $tags)
    {
    	$oldtags = User_def_tag::getTagsForUser($userid);
    	
    	$todelete = array_diff($oldtags, $tags);
    	
    	foreach ($todelete as $td) {
    		User_def_tag::deleteTag($userid, $td);
    	}
    	
    	$toadd = array_diff($tags, $oldtags);
    	
    	foreach ($toadd as $ta)
    	{
    		User_def_tag::saveNew($userid, $ta);
    	}
    }
    
    function saveNew($userid, $tag)
    {
    	$deftag = new User_def_tag();
    	$deftag->user_id = $userid;
    	$deftag->tag = $tag;
    	$deftag->insert();
    }
    
    function getTagsForUser($userid)
    {
    	$deftag = new User_def_tag();
    	$deftag->whereAdd("user_id = '" . $userid . "'");
    	
    	$deftag->find();
    	
    	$tags = array();
    	
    	while ($deftag->fetch()) {
    		$tags[] = $deftag->tag;
    	}
    	
    	$deftag->free();
    	
    	return $tags;
    }
    
    function deleteTag($userid, $tag)
    {
    	$deftag = new User_def_tag();
    	$deftag->query("delete from user_def_tag where user_id='" 
    		. $userid . "' and tag='" . $tag . "'");
    	$deftag->free();
    }
    
    function getUsersWithTags($tags)
    {
    	$qryin = "'" . implode("', '", $tags) . "'";
    	
    	$deftag = new User_def_tag();
    	$deftag->whereAdd("tag in (" . $qryin . ")");
    	
    	$deftag->find();
    	
    	$users = array();
    	
    	while ($deftag->fetch()) {
    		$users[] = $deftag->user_id;
    	}
    	
    	$deftag->free();
    	
    	return $users;
    }
}
