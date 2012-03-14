<?php
/**
 * Table Definition for notification
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Notification extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'notification';                    // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $content;                         // string(1000)  
    public $created;                         // datetime(19)  not_null binary

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Notification',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
