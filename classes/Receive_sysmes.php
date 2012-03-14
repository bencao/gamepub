<?php
/**
 * Table Definition for receive_sysmes
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

define('RECEIVE_SYSMES_SOFT_LIMIT', 1000);
if (! defined('MAX_BOXCARS')) {
	define('MAX_BOXCARS', 128);	
}

class Receive_sysmes extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'receive_sysmes';                      // table name
    public $user_id;                              // int(4)  primary_key not_null
    public $sysmes_id;                         // int(4)  primary_key not_null
    public $created;                        	 // datetime(19)  not_null binary
	public $is_read;								//tinyint
	
    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Receive_sysmes',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    /* Message_type: 0 - Broadcast message; 1 - Groupsystem message; 2 - Marketing message; 4 - Group invitation */
    
	static function bulkInsert($sysmes_id, $created, $uids)
    {
        $cnt = 0;

        $qryhdr = 'INSERT INTO receive_sysmes (user_id, sysmes_id, created) VALUES ';
        $qry = $qryhdr;

        foreach ($uids as $user_id) {
            if ($cnt > 0) {
                $qry .= ', ';
            }
            $qry .= '('.$user_id.", '".$sysmes_id."', '".$created. "') ";
            $cnt++;
            
            if (rand() % RECEIVE_SYSMES_SOFT_LIMIT == 0) {
                // FIXME: Causes lag in replicated servers
                // Notice_inbox::gc($id);
            }
            //凑齐128个id一起插入
            if ($cnt >= MAX_BOXCARS) {
                $rsm = new Receive_sysmes();
                $result = $rsm->query($qry);
                if (PEAR::isError($result)) {
                    common_log_db_error($rsm, $qry);
                }
                $qry = $qryhdr;
                $cnt = 0;
            }
        }

        //没有凑齐的, 最后插入
        if ($cnt > 0) {
            $rsm = new Receive_sysmes();
            $result = $rsm->query($qry);
            if (PEAR::isError($result)) {
                common_log_db_error($rsm, $qry);
            }
        }

        return;
    }
    
    // delete all the out of date receive_sysmes( which were sent 1 month before)
    static function clearOutOfDates() {
    	$meg = new System_message();
    	$qry = "DELETE FROM receive_sysmes WHERE created < '%s'";
    	$date = date("Y-m-d", strtotime("-1 Month")) ." 00:00:00";
    	$meg->query(sprintf($qry, $date));
    }
    
	//如果不算个数, 可以不用这样来求
    function unReadCount($user_id, $since_id=0)
    {
        $c = common_memcache();

        if (!empty($c)) {
            $cnt = $c->get(common_cache_key('receivesysmes:unreadcount:'.$user_id));
            if (is_integer($cnt)) {
                return (int) $cnt;
            }
        }
                
        $rsm = new Receive_sysmes();
        $rsm->selectAdd('count(1) as num');
        //$rsm->selectAdd('max(sysmes_id) as maxid');

        $rsm->whereAdd('is_read = 0');
        $rsm->whereAdd('user_id =' . $user_id);
        
//        if ($since_id != 0) {
//            $rsm->whereAdd('sysmes_id > ' . $since_id);
//        }

        $rsm->find();
        $rsm->fetch();
        
        $result = array();
        $result['num'] = 0;
//        $result['maxid'] = 100000;
        $result['num'] = $rsm->num;
        //$result['maxid'] = $rsm->maxid;
        
        $rsm->free();

        if (!empty($c)) {
            $c->set(common_cache_key('receivesysmes:unreadcount:'.$user_id), $cnt);
        }

        return $result;
    }
    
    function setRead($user_id, $since_id=0)
    {
    	$rsm = new Receive_sysmes();
    	$sql = 'update receive_sysmes set is_read = 1 where user_id = ' . $user_id . ' and is_read = 0';
        
        if ($since_id != 0) {
            $sql .= 'sysmes_id > ' . $since_id;
        }
        
        $rsm->query($sql);
        
        $rsm->blowSysMessageCount();
        
        $rsm->free();
    }
    
    function blowSysMessageCount()
    {
        $c = common_memcache();
        if (!empty($c)) {
            $c->delete(common_cache_key('receivesysmes:unreadcount:'.$this->user_id));
        }
    }
    
	function stream($user_id, $offset=0, $limit=NOTICES_PER_PAGE, $since_id=0, 
				$max_id=0, $since=null, $un_read=false)
	{
		$ids = Receive_sysmes::_streamDirect($user_id, $offset, $limit, $since_id, $max_id, $since, $un_read);
		return System_message::getStreamByIds($ids);
	}
	
    function _streamDirect($user_id, $offset=0, $limit=NOTICES_PER_PAGE, $since_id=0, 
    				$max_id=0, $since=null, $un_read=false)
    {
        $rsm = new Receive_sysmes();
        $rsm->user_id = $user_id;

        if (!is_null($since)) {
            $rsm->whereAdd('created > \'' . date('Y-m-d H:i:s', $since) . '\'');
        }
        
        if($un_read) {
        	$rsm->whereAdd('is_read = 0');
        }

        $rsm->orderBy('created DESC');

        if (!is_null($offset)) {
            $rsm->limit($offset, $limit);
        }

        $ids = array();
        if ($rsm->find()) {
            while ($rsm->fetch()) {
                $ids[] = $rsm->sysmes_id;
            }
        }
        return $ids;
    }
}
