<?php
/**
 * Table Definition for interest_catagory
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Interest_category extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'interest_category';               // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $name;                            // string(64)  

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Interest_category',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    function saveNew($name) {
    	$category = new Interest_category();
    	$category->name = $name;
    	$category->insert();
    }
    
    function getInterestByCategory($category)
    {
    	$category = Interest_category::staticGet('id', $category);
    	if ($category) {
    		return $category->name;
    	} else {
    		return null;
    	}
    }
    
    function getIdByInterest($interest)
    {
    	$category = new Interest_category();
    	$category->whereAdd("name='" . $interest . "'");
    	$category->find();
    	
    	while ($category->fetch()) {
    		return $category->id;
    	}
		
    	return null;
    }
    
    function getCategories()
    {
    	$category = new Interest_category();
    	$category->find();
    	
    	$cates = array();
    	while ($category->fetch()) {
    		$cates[] = $category->name;
    	}
    	$category->free();
		
    	return $cates;
    }
}
