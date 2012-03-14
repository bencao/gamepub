<?php
/**
 * Table Definition for sysmessage
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class System_message extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'system_message';                      // table name
    public $id;                              //   not_null primary_key
    public $content;                         // string(140)  
    public $rendered;                        // blob(65535)  blob
    public $message_type;                    // int(4)  not_null
    public $created;                         // datetime(19)  not_null binary

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('System_message',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    /* Message_type: 0 - Broadcast message; 1 - Groupsystem message; 2 - Marketing message; 4 - Group invitation */
    
    static function saveNew($to, $content, $rendered=null, $type = 0, $guid = false) {
        $msg = new System_message();
        if (!$guid) {
        	$guid = System_message::create_guid();
        }
        $msg->id = $guid;
        $msg->content = common_shorten_links($content);
        if ($rendered) {
        	$msg->rendered = $rendered;
        }else {
        	$msg->rendered = $msg->content;
        }
        $msg->created = common_sql_now();
        $msg->message_type = $type;
        
        $result = $msg->insert();
        
        if (!$result) {
            common_log_db_error($msg, 'INSERT', __FILE__);
            return false;
        }

        if (!is_array($to)) {
        	$to = array($to);
        }
        Receive_sysmes::bulkInsert($msg->id, $msg->created, $to);
        
        return true;
    }
    
    // delete all the out of date messages( which were sent 1 month before)
    static function clearOutOfDates() {
    	$meg = new System_message();
    	$qry = "DELETE FROM system_message WHERE created < '%s'";
    	$date = date("Y-m-d", strtotime("-1 Month")) ." 00:00:00";
    	$meg->query(sprintf($qry, $date));
    }
    
    // generate a random number to be record id
    static function create_guid()
	{
	    $microTime = microtime();
		list($a_dec, $a_sec) = explode(" ", $microTime);
	
		$dec_hex = sprintf("%x", $a_dec* 1000000);
		$sec_hex = sprintf("%x", $a_sec);
	
		System_message::ensure_length($dec_hex, 5);
		System_message::ensure_length($sec_hex, 6);
	
		$guid = "";
		$guid .= $dec_hex;
		$guid .= System_message::create_guid_section(3);
		$guid .= '-';
		$guid .= System_message::create_guid_section(4);
		$guid .= '-';
		$guid .= System_message::create_guid_section(4);
		$guid .= '-';
		$guid .= System_message::create_guid_section(4);
		$guid .= '-';
		$guid .= $sec_hex;
		$guid .= System_message::create_guid_section(6);
		return $guid;
	
	}
	
	static function create_guid_section($characters)
	{
		$return = "";
		for($i=0; $i<$characters; $i++)
		{
			$return .= sprintf("%x", mt_rand(0,15));
		}
		return $return;
	}
	
	static function ensure_length(&$string, $length)
	{
		$strlen = strlen($string);
		if($strlen < $length)
		{
			$string = str_pad($string,$length,"0");
		}
		else if($strlen > $length)
		{
			$string = substr($string, 0, $length);
		}
	}
    
	function getStreamByIds($ids)
	{
		$cache = common_memcache();

		if (!empty($cache)) {
			$sysmess = array();
			foreach ($ids as $id) {
				$n = System_message::staticGet('id', $id);
				if (!empty($n)) {
					$sysmess[] = $n;
				}
			}
			return new ArrayWrapper($sysmess);
		} else {
			$sysmes = new System_message();
			if (empty($ids)) {
                return $sysmes;
            }
            $qry = '';
            foreach($ids as $k => $id) {
            	$qry .= '\'' . $id . '\','; 
            }
            $qry = substr($qry, 0, strlen($qry) -1);
            $sysmes->whereAdd('id in (' . $qry . ')');
            $sysmes->find();
            $temp = array();
            while ($sysmes->fetch()) {
                $temp[$sysmes->id] = clone($sysmes);
            }

            $wrapped = array();
            foreach ($ids as $id) {
                if (array_key_exists($id, $temp)) {
                    $wrapped[] = $temp[$id];
                }
            }

            return new ArrayWrapper($wrapped);
		}
	}
}
