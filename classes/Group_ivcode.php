<?php
/**
 * Table Definition for group_ivcode
 */

class Group_ivcode extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'group_ivcode';                    // table name
    public $id;                              // int(11) primary_key
    public $code;                            // string(32)  not_null
    public $groupid;                         // int(11)  not_null unique_key
    public $modified;                        // timestamp(19)  not_null unsigned zerofill binary timestamp

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Group_ivcode',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    static function saveNew($groupid)
    {
    	$givid = new Group_ivcode();

        $givid->code   = common_confirmation_code(64);
        $givid->groupid = $groupid;
        $givid->modified = common_sql_now();

        $result = $givid->insert();

        if (!$result) {
            common_log_db_error($givid, 'INSERT', __FILE__);
            return false;
        }
        
        return true;
    } 
}
