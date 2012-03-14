<?php
/**
 * Table Definition for deal_images
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Deal_images extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'deal_images';                     // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $deal_id;                         // int(11)  unique_key
    public $image_url;                       // string(255)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Deal_images',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    function saveNew($deal_id,$image_url) {
    	
    	$di = new Deal_images();
    	$di->deal_id = $deal_id;
    	$di->image_url = $image_url;
    	$di->insert(); 
    	return $di->id;
    } 
    
 	function getImagesByDealId($dl_id)
    {
		$deal_images = new Deal_images();
		$deal_images->selectAdd();
		$deal_images->selectAdd('image_url');
		$deal_images->whereAdd('deal_id = '.$dl_id);
		$deal_images->find();
		$deal_images->fetch();
		return $deal_images->image_url; 	
    }
    
    
}
