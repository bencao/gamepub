<?php
/**
 * Table Definition for Retweet
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Retweet extends Memcached_DataObject
{
	    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'retweet';      	// table name
    public $notice_id;                       		// int(4)  primary_key not_null
    public $user_id;							// int(4) not null
    public $content; 							// varchar(140)
   	public $modified;                       // timestamp()   not_null default_CURRENT_TIMESTAMP
	
    /* Static get */
    function staticGet($k,$v=null)
    { return Memcached_DataObject::staticGet('Retweet',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    function &pkeyGet($kv)
    {
        return Memcached_DataObject::pkeyGet('Retweet', $kv);
    }
    
    //新建时要blow, 先把自己关注的都列出来, 以后好友多的时候再限制.
//   function stream($notice_id, $user_id, $offset=0, $limit=NOTICES_PER_PAGE, $since_id=0, $max_id=0, $since=null)
//    {
//        $ids = Notice::stream(array('Retweet', '_streamDirect'),
//                              array($notice_id, $user_id),
//                              'retweet:stream:' . $notice_id,
//                              $offset, $limit, $since_id, $max_id, $since);
//        return $ids;
//    }

    //以后建立缓存, 消息删除时要blow, 直接查询出消息
    function stream($notice_id, $user_id, $offset=0, $limit=NOTICES_PER_PAGE, $since_id=0, $max_id=0, $since=null)
    {
        $retweet = new Retweet();
        
         $qry =  'SELECT retweet.* ' .
          'FROM retweet  JOIN subscription '.
          'ON retweet.user_id = subscription.subscribed  ' .
          'WHERE subscription.subscriber = ' . $user_id . '  and retweet.notice_id = ' . $notice_id . ' ';
        

        if ($since_id != 0) {
            $qry .= ' notice_id > ' . $since_id;
        }

        if ($max_id != 0) {
            $qry .= ' notice_id < ' . $max_id;
        }

        if (!is_null($since)) {
            $qry .= ' modified > \'' . date('Y-m-d H:i:s', $since) . '\'';
        }
        
        $qry .= ' and exists (select * from notice where is_banned=0 and id=notice_id)'; //is_delete=0 and 
        
        if (!is_null($offset)) {
            $qry .= ' limit ' . $offset . ', ' . $limit;
        }
        
//        $retweets = array();

        $retweet->query($qry);
        return $retweet;        
        
//        while ($retweet->fetch()) {
//            $retweets[] = clone($retweet);
//        }
//
//        return new ArrayWrapper($retweets);
    }
    
    /*
    //转载排行, 先剔除删除及屏蔽的消息
	SELECT user_id, sum( 1 ) AS noticenum
	FROM retweet 
	GROUP BY user_id
	ORDER BY noticenum DESC
	LIMIT 0 , 30	
	*/
    
    static function getRetweetOrder($limit=20, $since=null) {
    	$retweet = new Retweet();
    	
    	$retweet->selectAdd(); // clears it
        $retweet->selectAdd('user_id');
        $retweet->selectAdd('sum( 1 ) as subnum');

        $retweet->limit(0, $limit);
        $retweet->groupBy('user_id');
        $retweet->orderBy('subnum DESC');
        
        if (!is_null($since)) {
            $retweet->whereAdd('modified > \'' . date('Y-m-d H:i:s', $since) . '\'');
        }
        
        $retweet->whereAdd('exists (select * from notice where is_banned=0 and id=notice_id)'); //is_delete=0 and 
        
        $retweets = array();
        if ($retweet->find()) {
        	while ($retweet->fetch()) {
        		$retweets[] = array('id'=>$retweet->user_id, 'num'=>$retweet->subnum);
        	}
        }
        $retweet->free();
        return $retweets;
    }
    
	function getRetweetNum($user_id,$while='week') {
		switch($while)
		{
			case 'week':
				$period = 3600*24*7;
				break;
			case 'day':
				$period = 3600*24;
				break;
			default :
				$period = 3600*24*7;
				break;
		}
		$cur = time();
		$dt = strftime('%Y-%m-%d', $cur);
		//当天0:0:0的总秒数
		$today = strtotime($dt);

		$someday = $today - $period;
		$time = date('Y-m-d H:i:s', $someday);

		$notice = new Notice();
		$query = "SELECT sum(1)AS num FROM retweet,notice WHERE notice.user_id = ".$user_id." and id=notice_id and retweet.modified >"."\"".$time."\"";

		$notice->query($query);
		$notice->fetch();
		$num = $notice->num?$notice->num:0;
		$notice->free();
		return $num; //return update num
	}
	
}