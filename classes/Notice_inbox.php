<?php
/*
 * LShai - a distributed microblogging tool
 */

if (!defined('SHAISHAI')) { exit(1); }

require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

// We keep 5 pages of inbox notices in memcache, +1 for pagination check

define('INBOX_CACHE_WINDOW', 101);
define('NOTICE_INBOX_GC_BOXCAR', 128);
define('NOTICE_INBOX_GC_MAX', 12800);
define('NOTICE_INBOX_LIMIT', 1000);
define('NOTICE_INBOX_SOFT_LIMIT', 1000);

define('NOTICE_INBOX_SOURCE_SUB', 1);
define('NOTICE_INBOX_SOURCE_GROUP', 2);
define('NOTICE_INBOX_SOURCE_REPLY', 3);
define('NOTICE_INBOX_SOURCE_FORWARD', 4);
define('NOTICE_INBOX_SOURCE_GATEWAY', -1);

class Notice_inbox extends Memcached_DataObject
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'notice_inbox';                    // table name
    public $user_id;                         // int(4)  primary_key not_null
    public $notice_id;                       // int(4)  primary_key not_null
    public $created;                         // datetime()   not_null
    public $source;                          // tinyint(1)   default_1

    /* Static get */
    function staticGet($k,$v=null)
    { return Memcached_DataObject::staticGet('Notice_inbox',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    //对于分类的消息, 需要单独缓存下来 
    function stream($user_id, $offset, $limit, $since_id, $max_id,
    	$own=false, $content_type=0, $first_tag=0, $gtag=null, $other=false)
    {    	
    	$own_key = $own ? '' : '_own';
    	
    	if($content_type && ! $first_tag && ! $gtag) {	
    		//缓存内容分类的消息
    		return Notice::streamForNoticeInbox(array('Notice_inbox', '_streamDirect'),
                              array($user_id, $own, $first_tag, $gtag, $other),
                              sprintf('notice_inbox:by_user%s:%d:%d', $own_key, $user_id, $content_type),
                              $offset, $limit, $since_id, $max_id, $content_type);
    		
    	} else if (! $content_type && $first_tag && ! $gtag) {    
    		//缓存话题分类的消息
    		return Notice::streamForNoticeInbox(array('Notice_inbox', '_streamDirect'),
                              array($user_id, $own, $first_tag, $gtag, $other),
                              sprintf('notice_inbox:by_user%s:%d:%d', $own_key, $user_id, $first_tag),
                              $offset, $limit, $since_id, $max_id, $content_type);
    	} else if ($other) {
    		//缓存其他分类的消息  		
    		return Notice::streamForNoticeInbox(array('Notice_inbox', '_streamDirect'),
                              array($user_id, $own, $first_tag, $gtag, $other),
                              sprintf('notice_inbox:by_other%s:%d', $own_key, $user_id),
                              $offset, $limit, $since_id, $max_id, $content_type);
    	} else if(! $content_type && ! $first_tag && ! $gtag) {
    		//缓存无分类的全部消息
        	return Notice::streamForNoticeInbox(array('Notice_inbox', '_streamDirect'),
                              array($user_id, $own, $first_tag, $gtag, $other),
                              sprintf('notice_inbox:by_user%s:%d', $own_key, $user_id),
                              $offset, $limit, $since_id, $max_id, $content_type);
    	} else {
    		//直接取消息，不缓存（只使用gtag或者结合使用gtag,tag,content_type）
    		return Notice_inbox::_streamDirect($user_id, $own, $first_tag, $gtag, $other, $offset, 
    				$limit, $since_id, $max_id,  $content_type, 0, 0) ;
    	}
    }

    //转载一次, 更新对于此用户此消息的时间, 转载的时间
    //现在是以notice_id来排序, 可以改为created来排序, 有必要的话
    function _streamDirect($user_id, $own, $first_tag, $gtag, $other, $offset, $limit, $since_id, $max_id, 
    	$content_type=0, $area_type=0, $topic_type=0)
    {
        $inbox = new Notice_inbox();

        $inbox->user_id = $user_id;

        if (!$own) {
            $inbox->whereAdd('source != ' . NOTICE_INBOX_SOURCE_GATEWAY);
        }

        if ($since_id != 0) {
            $inbox->whereAdd('notice_id > ' . $since_id);
        }

        if ($max_id != 0) {
            $inbox->whereAdd('notice_id <= ' . $max_id);
        }

        //添加消息的类型, 比如音乐视频等
        if ($content_type == 0) {
        	if($other)
        		$inbox->whereAdd('EXISTS (SELECT * from notice where id = notice_id and is_banned = 0 '.
        			'and notice.user_id != ' . $user_id. ')'); 
        	else 
        		$inbox->whereAdd('EXISTS (SELECT * from notice where id = notice_id and is_banned = 0)'); 
        } else {
        	$inbox->whereAdd('EXISTS (SELECT * from notice where id = notice_id and is_banned = 0 ' .
        			'and content_type = '. $content_type .')');
        }
        
        //一级话题
        if ($first_tag > 0) {
        	//$inbox->whereAdd('EXISTS (SELECT * from notice_tag where notice_inbox.notice_id=notice_tag.notice_id ' .
        	//	'and second_tag_id in (select id from second_tag where first_tag_id%10=' . ($first_tag%10) . '))');

        	//现在一级话题都采用999x, 二级话题都是999xzz
        	$inbox->whereAdd('notice_id IN (SELECT notice_tag.notice_id from notice_tag WHERE ' .
        		'notice_tag.second_tag_id DIV 100=' . $first_tag . ')');
        }
        	        		
        if($gtag) {
        	// 未分组的特殊处理
        	if ($gtag == '未分组') {
        		$taggedIds = Tagtions::getMyTaggedIds($user_id);
				$unTaggedIds = array();
				
				// 获取所有关注的人
				$subs = new Subscription();
				$subs->whereAdd('subscriber = ' . $user_id);
				$subs->find();
				
				while ($subs->fetch()) {
					// 排除掉已经被tag的人和本人
					if ($subs->subscribed != $user_id && ! in_array($subs->subscribed, $taggedIds)) {
						$unTaggedIds[] = $subs->subscribed;
					}
				}
				
        		$inbox->whereAdd('EXISTS (SELECT * from notice where notice.id = notice_id and notice.user_id in ' .  
        				'(' . implode(',', $unTaggedIds) . '))');
        	} else {
        		$tagid = User_tag::getTagid($user_id, $gtag);
        		$inbox->whereAdd('EXISTS (SELECT * from notice where notice.id = notice_id and notice.user_id in ' .
        				'(SELECT tagged from tagtions WHERE tagger = ' . $user_id . ' AND tagid = ' . $tagid . '))');
        	}
        }
        
        $inbox->orderBy('notice_id DESC');

        if (!is_null($offset)) {
            $inbox->limit($offset, $limit);
        }

        $ids = array();

        if ($inbox->find()) {
            while ($inbox->fetch()) {
                $ids[] = $inbox->notice_id;
            }
        }

        return $ids;
    }

    function &pkeyGet($kv)
    {
        return Memcached_DataObject::pkeyGet('Notice_inbox', $kv);
    }
    
    static function userExist($qryin, $notice_id) {
    	$inbox = new Notice_inbox();
    	$inbox->selectAdd(); // clears it
		$inbox->selectAdd('user_id');    	
    	$inbox->whereAdd('user_id in (' . $qryin . ')');
    	$inbox->whereAdd('notice_id =' . $notice_id);
    	$inbox->orderBy('user_id desc');
    	
    	$inbox->find();
    	return $inbox;
    }
    
    /**
     * Trim inbox for a given user to latest NOTICE_INBOX_LIMIT items
     * (up to NOTICE_INBOX_GC_MAX will be deleted).
     *
     * @param int $user_id
     * @return int count of notices dropped from the inbox, if any
     */
    static function gc($user_id)
    {
        $entry = new Notice_inbox();
        $entry->user_id = $user_id;
        $entry->orderBy('created DESC');
        $entry->limit(NOTICE_INBOX_LIMIT - 1, NOTICE_INBOX_GC_MAX);

        $total = $entry->find();

        if ($total > 0) {
            $notices = array();
            $cnt = 0;
            while ($entry->fetch()) {
                $notices[] = $entry->notice_id;
                $cnt++;
                //凑齐128个, 完全删掉
                if ($cnt >= NOTICE_INBOX_GC_BOXCAR) {
                    self::deleteMatching($user_id, $notices);
                    $notices = array();
                    $cnt = 0;
                }
            }

            if ($cnt > 0) {
                self::deleteMatching($user_id, $notices);
                $notices = array();
            }
        }

        return $total;
    }

    //删除, 我们可以创建一个新的表, 保存这些记录.
    static function deleteMatching($user_id, $notices)
    {
        $entry = new Notice_inbox();
        return $entry->query('DELETE FROM notice_inbox '.
                             'WHERE user_id = ' . $user_id . ' ' .
                             'AND notice_id in ('.implode(',', $notices).')');
    }
    
    static function bulkInsert($notice_id, $created, $ni)
    {
        $cnt = 0;

        $qryhdr = 'INSERT INTO notice_inbox (user_id, notice_id, source, created) VALUES ';
        $qry = $qryhdr;

        foreach ($ni as $id => $source) {
            if ($cnt > 0) {
                $qry .= ', ';
            }
            $qry .= '('.$id.', '.$notice_id.', '.$source.", '".$created. "') ";
            $cnt++;
            //?
            if (rand() % NOTICE_INBOX_SOFT_LIMIT == 0) {
                // FIXME: Causes lag in replicated servers
                // Notice_inbox::gc($id);
            }
            //凑齐128个id一起插入
            if ($cnt >= MAX_BOXCARS) {
                $inbox = new Notice_inbox();
                $result = $inbox->query($qry);
                if (PEAR::isError($result)) {
                    common_log_db_error($inbox, $qry);
                }
                $qry = $qryhdr;
                $cnt = 0;
            }
        }

        //没有凑齐的, 最后插入
        if ($cnt > 0) {
            $inbox = new Notice_inbox();
            $result = $inbox->query($qry);
            if (PEAR::isError($result)) {
                common_log_db_error($inbox, $qry);
            }
        }

        return;
    }
    
    static function insertNotice($user_id, $created, $notice_id, $source)
    {
    	$qry = 'INSERT INTO notice_inbox (user_id, notice_id, source, created) VALUES ';
    	$qry .= '('.$user_id.', '.$notice_id.', '.$source.", '".$created. "') ";
    	$inbox = new Notice_inbox();
        $result = $inbox->query($qry);
        if (PEAR::isError($result)) {
        	common_log_db_error($inbox, $qry);
        }
    }
    
    static function getInboxNoticeIdsForUser($user) 
    {
    	$inbox = new Notice_inbox();
    	$inbox->user_id = $user->id;
		$inbox->find();

        $ids = array();
        
        while ($inbox->fetch()) {
            $ids[] = $inbox->notice_id;
        }
        $inbox->free();

        return $ids;
    }
    
    static function getLatestNoticeId($user_id)
    {
    	$inbox = new Notice_inbox();
    	$qry = "select notice_id from notice_inbox where user_id = " . $user_id . " order by notice_id desc limit 0, 1";
    	$inbox->query($qry);
    	$id = 100000;
    	if ($inbox->fetch()) {
            $id = $inbox->notice_id;
        }
        return $id;
    }
}
