<?php
/**
 * Table Definition for reply
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Reply extends Memcached_DataObject
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'reply';                           // table name
    public $notice_id;                       // int(4)  primary_key not_null
    public $user_id;                      // int(4)  primary_key not_null
    public $modified;                        // timestamp()   not_null default_CURRENT_TIMESTAMP
    public $replied_id;                      // int(4)
    
    public $is_read;							  //tinyint
    public $sender_id;                       // user who send reply

    /* Static get */
    function staticGet($k,$v=null)
    { return Memcached_DataObject::staticGet('Reply',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function &pkeyGet($kv)
    {
        return Memcached_DataObject::pkeyGet('Reply', $kv);
    }
    
    //回复给某用户的消息列表
    function stream($user_id, $offset=0, $limit=NOTICES_PER_PAGE, $since_id=0, $max_id=0, $since=null)
    {
        $ids = Notice::stream(array('Reply', '_streamDirect'),
                              array($user_id),
                              'reply:stream:' . $user_id,
                              $offset, $limit, $since_id, $max_id, $since);
        return $ids;
    }

    function _streamDirect($user_id, $offset=0, $limit=NOTICES_PER_PAGE, $since_id=0, 
    		$max_id=0, $since=null, $content_type=0, $area_type=0, $topic_type=0)
    {
        $reply = new Reply();
        $reply->user_id = $user_id;

        if ($since_id != 0) {
            $reply->whereAdd('notice_id > ' . $since_id);
        }

        if ($max_id != 0) {
            $reply->whereAdd('notice_id < ' . $max_id);
        }

        if (!is_null($since)) {
            $reply->whereAdd('modified > \'' . date('Y-m-d H:i:s', $since) . '\'');
        }
        
        $reply->whereAdd('exists (select * from notice where is_banned=0 and topic_type!=4 and id=notice_id)'); //is_delete=0 and 

        $reply->orderBy('notice_id DESC');

        if (!is_null($offset)) {
            $reply->limit($offset, $limit);
        }

        $ids = array();

        if ($reply->find()) {
            while ($reply->fetch()) {
                $ids[] = $reply->notice_id;
            }
        }

        return $ids;
    }
    
    //如果不算个数, 可以不用这样来求
    function unReadCount($user_id, $since_id=0)
    {
        $c = common_memcache();

        if (!empty($c)) {
            $cnt = $c->get(common_cache_key('reply:unreadcount:'.$user_id));
            if (is_integer($cnt)) {
                return (int) $cnt;
            }
        }
                
        $rep = new Reply();
        $rep->selectAdd('count(1) as num');
        $rep->selectAdd('max(notice_id) as maxid');

        $rep->whereAdd('is_read = 0');
        $rep->whereAdd('user_id =' . $user_id);
        
        if ($since_id != 0) {
            $rep->whereAdd('notice_id > ' . $since_id);
        }
        
        $rep->whereAdd('exists (select * from notice where is_banned=0 and id=notice_id and topic_type != 4)'); //is_delete=0 and 

        $rep->find();
        $rep->fetch();
        
        $result = array();
        $result['num'] = 0;
        $result['maxid'] = 100000;
        $result['num'] = $rep->num;
        $result['maxid'] = $rep->maxid;
//        $cnt = $rep->num;
        
        $rep->free();

        if (!empty($c)) {
            $c->set(common_cache_key('reply:unreadcount:'.$user_id), $cnt);
        }

        return $result;
//        return $cnt;
    }
    
    function setRead($user_id, $since_id=0)
    {
    	$rep = new Reply();
    	$sql = 'update reply set is_read = 1 where user_id = ' . $user_id . ' and is_read = 0';
        
        if ($since_id != 0) {
            $sql .= ' and notice_id > ' . $since_id;
        }
        
        $rep->query($sql);
        
        $rep->blowReplyCount();
        
        $rep->free();
    }
    
    static function getUnreadReplyByUserid($userid, $limit = 3, $since_time = null) {
    	$rep = new Reply();
    	$rep->whereAdd('user_id = ' . $userid);
    	$rep->whereAdd('is_read = 0');
    	if ($since_time) {
    		$rep->whereAdd("modified > '" . $since_time . "'");
    	}
    	$rep->limit(0, $limit);
    	$rep->orderBy('notice_id asc');
    	
    	$rep->find();
    	
    	return $rep;
    }
    
    static function setReadByNoticeidAndUserid($notice_id, $user_id) {
    	$r = new Reply();
    	$r->whereAdd('notice_id = ' . $notice_id);
    	$r->whereAdd('user_id = ' . $user_id);
    	$r->find();
    	
    	if ($r && $r->fetch()) {
    		if (! $r->is_read) {
    			$orig = clone($r);
    			$r->is_read = 1;
    			$r->update($orig);
    		}
    	}
    	return true;
    }
    
    //新建, 删除时调用    
    function blowReplyCount()
    {
        $c = common_memcache();
        if (!empty($c)) {
            $c->delete(common_cache_key('reply:unreadcount:'.$this->user_id));
        }
    }
}
