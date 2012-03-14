<?php
/**
 * Table Definition for invitation
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Invitation extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'invitation';                      // table name
    public $code;                            // varchar(32)  primary_key not_null
    public $rcode;
    public $user_id;                         // int(4)   not_null
    public $address;                         // varchar(255)  multiple_key not_null
    public $address_type;                    // varchar(8)  multiple_key not_null
    public $created;                         // datetime()   not_null

    /* Static get */
    function staticGet($k,$v=null)
    { return Memcached_DataObject::staticGet('Invitation',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    static function existsInvitation($email) {
    	$ivt = new Invitation();
    	$ivt->whereAdd("address_type = 'email'");
    	$ivt->whereAdd("address = '" . $email . "'");
    	$ivt->find();
    	
    	return $ivt && $ivt->N > 0;
    }
    
    function resendInvitedEmailsInFrequency($daysBefore) {
    	$currentUnixTimestamp = time();
    	$startDate = common_sql_date($currentUnixTimestamp - 24 * 3600 * ($daysBefore + 1));
    	$endDate = common_sql_date($currentUnixTimestamp - 24 * 3600 * $daysBefore);
    	
    	$ivt = new Invitation();
    	$ivt->whereAdd("address_type in ('email','qq')");
    	$ivt->whereAdd("created > '" . $startDate . "'");
    	$ivt->whereAdd("created < '" . $endDate . "'");
    	
    	$ivt->find();
    	
    	while ($ivt->fetch()) {
    		// try resend
    		mail_resend_invitation($ivt->address, $ivt->user_id, $ivt->code, $daysBefore);
    	}
    	$ivt->free();
    	
    	return true;
    }
    
    function deleteUselessInvitations($maxPreserveDays = 30) {
    	$date = common_sql_date(time() - 24 * 3600 * 1000 * $maxPreserveDays);
    	
    	$ivt = new Invitation();
    	$ivt->whereAdd("created < '" . $date . "'");
    	$ivt->delete(true);
    	$ivt->free();
    	
    	return true;
    }
}
