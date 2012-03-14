<?php
/**
 * Table Definition for read_notification
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Read_notification extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'read_notification';               // table name
    public $user_id;                      // int(11)  not_null primary_key multiple_key
    public $notification_id;                 // int(11)  not_null primary_key multiple_key
    public $created;                         // datetime(19)  not_null binary

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Read_notification',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
