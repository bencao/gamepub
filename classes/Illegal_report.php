<?php
/**
 * Table Definition for illegal_report
 */

class Illegal_report extends Memcached_DataObject
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'illegal_report';                  // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $reporter;                        // int(11)  not_null
    public $illtype;                         // boolean  not null
    public $targetid;                       // int(11)  not_null
    public $reason;                          // tinyint(4)  not_null multiple_key
    public $status;                          // tinyint(4)  multiple_key
    public $description;                     // string(255)  
    public $from_url;

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Illegal_report',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    // three kind of status in backgroud: 未处理，已处理，拒绝
    // report reason 0 - 请选择, 1 - 内容反动, 2 - 内容色情, 3 - 骚扰诈骗, 4 - 张贴广告', 5 - 滥发垃圾信息
}
