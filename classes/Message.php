<?php
/**
 * Table Definition for message
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Message extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'message';                         // table name
    public $id;                              // int(4)  primary_key not_null
    public $uri;                             // varchar(255)  unique_key
    public $from_user;                    // int(4)   not_null
    public $to_user;                      // int(4)   not_null
    public $content;                         // varchar(140)  
    public $rendered;                        // text()  
    public $url;                             // varchar(255)  
    public $created;                         // datetime()   not_null
    public $modified;                        // timestamp()   not_null default_CURRENT_TIMESTAMP
    public $source;                          // varchar(32)  

    public $is_deleted_from;				 //tinyint
    public $is_deleted_to;					 //tinyint
    public $is_read;								//tinyint
    
    /* Static get */
    function staticGet($k,$v=null)
    { return Memcached_DataObject::staticGet('Message',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    function getFrom()
    {
        return Profile::staticGet('id', $this->from_user);
    }
    
    function getTo()
    {
        return Profile::staticGet('id', $this->to_user);
    }
    
    static function saveNew($from, $to, $content, $source) {
        
        $msg = new Message();
        
        $msg->from_user = $from;
        $msg->to_user = $to;
        $msg->content = common_shorten_links($content);
        $msg->rendered = self::renderMessageText($content);
        $msg->created = common_sql_now();
        $msg->source = $source;
        
        $result = $msg->insert();
        
        if (!$result) {
            common_log_db_error($msg, 'INSERT', __FILE__);
            return '不能插入站内信.';
        }
        
        $orig = clone($msg);
        $msg->uri = common_path('message/' . $msg->id);
        $result = $msg->update($orig);
        
        if (!$result) {
            common_log_db_error($msg, 'UPDATE', __FILE__);
            return '不能更新悄悄话的链接.';
        }
        
        Message::blowMessageCount($to);
        
        $to_user = User::staticGet('id', $to);
        $to_user->blowInboxCount();
        $from_user = User::staticGet('id', $from);
        $from_user->blowOutboxCount();
        
        return $msg;
    }
    
    //如果都为0, 则定期清理数据库
    function deleteInbox()
    {
    	Message::blowMessageCount($this->to_user);
    	
        $this->query('BEGIN');
        $this->query(sprintf("UPDATE message set is_deleted_to = 1 WHERE id = %d", $this->id));
        $this->query('COMMIT');
    }
    
    function deleteOutbox()
    {
        $this->query('BEGIN');
        $this->query(sprintf("UPDATE message set is_deleted_from = 1 WHERE id = %d", $this->id));
        $this->query('COMMIT');
    }
    
    static function deleteAllInbox($id, $limit=20) {
    	Message::blowMessageCount($id);
    	
    	$message = new Message();
    	$qry = 'update message set is_deleted_to = 1 where to_user = ' . $id .
    	' and is_deleted_to = 0 ';//. ' limit 0,' . $limit;
    	$message->query($qry);
    	$message->free();
    }

    static function deleteAllOutbox($id, $limit=20) {
    	$message = new Message();
    	$qry = 'update message set is_deleted_from = 1 where from_user = ' . $id . 
    	' and is_deleted_from = 0 ';//. ' limit 0,' . $limit;
    	$message->query($qry);
    	$message->free();
    }
    
    function unReadCount($to_user, $since_id=0)
    {
        $c = common_memcache();

        if (!empty($c)) {
            $cnt = $c->get(common_cache_key('message:unreadcount:'.$to_user));
            if (is_integer($cnt)) {
                return (int) $cnt;
            }
        }
                
        $mes = new Message();
        $mes->selectAdd('count(1) as num');
        $mes->selectAdd('max(id) as maxid');

        $mes->whereAdd('is_read = 0');
        $mes->whereAdd('to_user =' . $to_user);
        
        if ($since_id != 0) {
            $mes->whereAdd('id > ' . $since_id);
        }

        $mes->find();
        $mes->fetch();
        $result = array();
        $result['num'] = 0;
        $result['maxid'] = 0;
        $result['num'] = $mes->num;
        $result['maxid'] = $mes->maxid;
//        $cnt = $mes->num;
        
        $mes->free();

        if (!empty($c)) {
            $c->set(common_cache_key('message:unreadcount:'.$to_user), $cnt);
        }

        return $result;
//        return $cnt;
    }
    
    function setRead($user_id, $since_id=0)
    {
    	$mes = new Message();
    	$sql = 'update message set is_read = 1, modified = now() where to_user = ' . $user_id . ' ';
        
        if ($since_id != 0) {
            $sql .= 'id > ' . $since_id;
        }
        
        $mes->query($sql);
        
        Message::blowMessageCount($user_id);
        
        $mes->free();
    }
    
    static function blowMessageCount($id)
    {
        $c = common_memcache();
        if (!empty($c)) {
            $c->delete(common_cache_key('message:unreadcount:'.$id));
        }
    }
    
    static function maxContent()
    {
        $desclimit = common_config('message', 'contentlimit');
        // null => use global limit (distinct from 0!)
        if (is_null($desclimit)) {
            $desclimit = common_config('site', 'textlimit');
        }
        return $desclimit;
    }

    static function contentTooLong($content)
    {
        $contentlimit = self::maxContent();
        return ($contentlimit > 0 && !empty($content) && (mb_strlen($content) > $contentlimit));
    }
    
    static function getUnreadMessageByUserid($user_id, $limit = 3, $since_time = null) {
    	$msg = new Message();
    	$msg->whereAdd('to_user = ' . $user_id);
    	if ($since_time) {
    		$msg->whereAdd("modified > '" . $since_time . "'");
    	}
    	$msg->whereAdd('is_read = 0');
    	$msg->limit(0, $limit);
    	$msg->orderBy('id asc');
    	$msg->find();
    	
    	return $msg;
    }
    
	static function renderMessageText($text)
	{
		require_once INSTALLDIR . '/lib/renderhelper.php';
	    $r = htmlspecialchars($text);
	
	    //\x十六进制, 对字符为[\0-\8\11-\12\14-\25]取代为空
	    $r = preg_replace('/[\x{0}-\x{8}\x{b}-\x{c}\x{e}-\x{19}]/', '', $r);
	    $r = common_replace_urls_callback($r, 'common_linkify');
	    return $r;
	}
	
}
