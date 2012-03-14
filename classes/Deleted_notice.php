<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Table Definition for notice
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Deleted_notice extends Memcached_DataObject
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'deleted_notice';                  // table name
    public $id;                              // int(4)  primary_key not_null
    public $user_id;                      // int(4)   not_null
    public $uri;                             // varchar(255)  unique_key
    public $content;                         // text
    public $rendered;                        // text
    public $created;                         // datetime()   not_null
    public $deleted;                         // datetime()   not_null
    public $retweet_num;
    public $discussion_num;

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Deleted_notice',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
