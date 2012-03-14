<?php
/**
 * Table Definition for official_design
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Official_design extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'official_design';                 // table name
    public $design_id;                       // int(11)  
    public $name;                            // string(16)  not_null primary_key

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Official_design',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    static function getOfficialDesigns() {
    	$d = new Design();
    	$d->whereAdd('id in (select design_id from official_design)');
    	
    	$d->find();
    	
    	return $d;
    }
    
    static function saveNew($name) {
//    	extract($colors);
    	
    	$d = new Design();
    	$d->backgroundcolor = 3368448;
    	$d->contentcolor = 4473924;
    	$d->sidebarcolor = 3355443;
    	$d->textcolor = 16777215;
    	$d->linkcolor = 12079372;
    	$d->backgroundimage = common_path('theme/' . $name . '/bg.jpg');;
//    	$d->disposition = $disposition;
    	$d->cssurl = common_path('theme/' . $name . '/t.css');
    	$d->name = $name;
    	$d->navcolor = 'e5630e';

    	$d->insert();
    	
    	$od = new Official_design();
    	$od->design_id = $d->id;
    	$od->name = $name;
    	$od->insert();
    	
    	return true;
    }
}
