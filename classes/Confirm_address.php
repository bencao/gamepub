<?php
/**
 * Table Definition for confirm_address
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Confirm_address extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'confirm_address';                 // table name
    public $code;                            // varchar(32)  primary_key not_null
    public $user_id;                         // int(4)   not_null
    public $address;                         // varchar(255)   not_null
    public $address_extra;                   // varchar(255)   not_null
    public $address_type;                    // varchar(8)   not_null
    public $claimed;                         // datetime()  
    public $sent;                            // datetime()  
    public $modified;                        // timestamp()   not_null default_CURRENT_TIMESTAMP

    /* Static get */
    function staticGet($k,$v=null)
    { return Memcached_DataObject::staticGet('Confirm_address',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function sequenceKey()
    { return array(false, false); }
    
    function getConfirmEmailByUserId($userid) {
    	$confirm = new Confirm_address();
    	$confirm->whereAdd("user_id='" . $userid . "'");
    	$confirm->whereAdd("address_type='email'");
    	$confirm->orderBy('modified desc');
//		if (!is_null($offset)) {
		$confirm->limit(0, 1);
//		}
		
		$confirm->find();

		if ($confirm->N == 0) {
			return null;
		}
		
		while ($confirm->fetch()) {
			$mail = $confirm->address;
//        	$confirms[] = clone($confirm);
        }
        	
        $confirm->free();
        
        return $mail;
    }
}
