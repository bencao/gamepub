<?php
/**
 * Table Definition for famous_people
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Apply_vip extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'apply_vip';                   // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $uid;                             // int(11)  not_null
    public $phone_number;                    // int(11)  
    public $email;                           // string(100)  binary
    public $description;                     // string(1000)  binary
    public $url;                             // string(200)  binary
    public $created;                         // datetime(19)  binary

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Apply_vip',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}

?>