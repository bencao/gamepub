<?php
/**
 * Table Definition for feedback
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Feedback extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'feedback';                        // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $report_user_id;                  // int(11)  
    public $ptype;                           // string(32)  
    public $priority;                        // string(2)  
    public $category;                        // string(64)    
    public $description;                     // string(1000)  
    public $sended;

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Feedback',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
}
