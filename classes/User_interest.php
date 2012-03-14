<?php
/**
 * Table Definition for user_interest
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class User_interest extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'user_interest';                   // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $user_id;                         // int(11)  multiple_key
    public $interest;                        // string(64)  
    public $category_id;                     // int(11)  

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('User_interest',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    function saveNew($user_id, $interest)
    {
    	$user_interest = new User_interest();
    	$user_interest->user_id = $user_id;
    	$user_interest->interest = $interest;
    	$user_interest->category_id = Interest_category::getIdByInterest($interest);
//    	if ($cid == null) {
//    		$user_interest->interest = $interest;
//    	} else {
//    		$user_interest->interest = Interest_category::getInterestByCategory($cid);
//    	}
    	$user_interest->insert();
    }
    
	function deleteInterest($userid, $interest)
    {
    	$user_interest = new User_interest();
    	$user_interest->query("delete from user_interest where user_id='" 
    		. $userid . "' and interest='" . $interest . "'");
    	$user_interest->free();
    }
    
	function saveInterests($userid, $ointerests)
    {
    	$interests = array();
    	foreach ($ointerests as $oi) {
    		$ooi = trim($oi);
    		if (empty($ooi) 
    			|| in_array($ooi, $interests)) {
    			continue;
    		}
    		$interests[] = $ooi;
    	}
    	
    	$oldinterests = User_interest::getInterestByUser($userid);
    	
    	$todelete = array_diff($oldinterests, $interests);
    	
    	foreach ($todelete as $td) {
    		User_interest::deleteInterest($userid, $td);
    	}
    	
    	$toadd = array_diff($interests, $oldinterests);
    	
    	foreach ($toadd as $ta)
    	{
    		User_interest::saveNew($userid, $ta);
    	}
    }
    
	function getInterestByUser($user_id)
    {
    	$user_interest = new User_interest();
    	$user_interest->whereAdd("user_id='" . $user_id 
    			. "'");

    	$user_interest->find();
    	
    	$userinterests = array();
    	
    	while ($user_interest->fetch())
    	{
    		$userinterests[] = $user_interest->interest;
    	}
    	
    	$user_interest->free();
    	
    	return $userinterests;
    }
    
    function getClassifiedInterestByUser($user_id)
    {
    	$user_interest = new User_interest();
    	$user_interest->whereAdd("user_id='" . $user_id 
    			. "' and category_id is not NULL");

    	$user_interest->find();
    	
    	$userinterests = array();
    	
    	while ($user_interest->fetch())
    	{
    		$userinterests[] = $user_interest->interest;
    	}
    	
    	$user_interest->free();
    	
    	return $userinterests;
    }
    
	function getClassifiedCategoriesByUser($user_id)
    {
    	$user_interest = new User_interest();
    	$user_interest->whereAdd("user_id='" . $user_id 
    			. "' and category_id is not NULL");

    	$user_interest->find();
    	
    	$categorys = array();
    	
    	while ($user_interest->fetch())
    	{
    		$categorys[] = $user_interest->category_id;
    	}
    	
    	$user_interest->free();
    	
    	return $categorys;
    }
    
	function getSelfDefinedInterestByUser($user_id)
    {
    	$user_interest = new User_interest();
    	$user_interest->whereAdd("user_id = '" . $user_id 
    			. "' and category_id is NULL");

    	$user_interest->find();
    	
    	$userinterests = array();
    	
    	while ($user_interest->fetch())
    	{
    		$userinterests[] = $user_interest->interest;
    	}
    	
    	$user_interest->free();
    	
    	return $userinterests;
    }
    
	function getSelfDefinedInterestStringByUser($user_id)
    {
    	$user_interest = new User_interest();
    	$user_interest->whereAdd("user_id = '" . $user_id 
    			. "' and category_id is NULL");

    	$user_interest->find();
    	
    	$userinterests = array();
    	
    	while ($user_interest->fetch())
    	{
    		$userinterests[] = $user_interest->interest;
    	}
    	
    	$user_interest->free();
    	
    	return implode(",", $userinterests);
    }
    
}
