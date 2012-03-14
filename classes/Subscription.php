<?php
/*
 * LShai - a distributed microblogging tool
 */

if (!defined('SHAISHAI')) { exit(1); }

require_once('XMPPHP/XMPP.php');

/**
 * Table Definition for subscription
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Subscription extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'subscription';                    // table name
    public $subscriber;                      // int(4)  primary_key not_null
    public $subscribed;                      // int(4)  primary_key not_null
    public $jabber;                          // tinyint(1)   default_1
    public $sms;                             // tinyint(1)   default_1
    public $token;                           // varchar(255)  
    public $secret;                          // varchar(255)  
    public $created;                         // datetime()   not_null
    public $modified;                        // timestamp()   not_null default_CURRENT_TIMESTAMP
	public $is_unread;						//tinyint
	
    /* Static get */
    function staticGet($k,$v=null)
    { return Memcached_DataObject::staticGet('Subscription',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    function &pkeyGet($kv)
    {
        return Memcached_DataObject::pkeyGet('Subscription', $kv);
    }
    

       	//被关注的个数排行
//    SELECT subscribed, sum( 1 ) AS subnum
//	FROM `subscription`
//	GROUP BY subscribed
//	ORDER BY subnum DESC
//	LIMIT 0 , 30
    
    //七天前
//    $t = strtotime($dt);
//    $now = time();
//    $diff = $now - $t;

    //在profile里面有个字段followers, 可直接查询那个得到最多的关注数
    
    static function getSubscribedOrder($limit=20, $since=null,$area='all',$neededid=1) {
    	$sub = new Subscription();
		$sub->selectAdd(); // clears it
        $sub->selectAdd('subscribed');
        $sub->selectAdd('sum( 1 ) as subnum');

        $sub->limit(0, $limit);
        $sub->groupBy('subscribed');
        $sub->orderBy('subnum DESC');
        
        $sub->whereAdd('subscribed !='. common_config('newuser', 'default_id'));
        if($area == 'game')
        $sub->whereAdd('subscribed IN (select id from user where game_id = '.$neededid.')');
        else if($area == 'gameserver')
        $sub->whereAdd('subscribed IN (select id from user where game_server_id = '.$neededid.')');
    	if (!is_null($since)) {
            $sub->whereAdd('created > \'' . date('Y-m-d H:i:s', $since) . '\'');
        }
        
        $subs = array();
        if ($sub->find()) {
        	while ($sub->fetch()) {
        		// 减去自己
        		$subs[] = array('user_id'=>$sub->subscribed, 'num'=>$sub->subnum - 1);
        		//common_debug($sub->subscribed . ' '. $sub->subnum);
        	}
        }
        $sub->free();
   
        return $subs; //return user'array
    }
    
    
     static function getSubscribedNum($user_id,$while='week') {
     	switch($while)
     	{ //这个需要进一步确定。。
     		case 'week':
     			$period = 3600*24*7-3600*24;
     			break;
     		case 'day':
     			$period = 3600*24-3600*24;
     			break;
     		default :
     			$period = 3600*24*7-3600*24;
     			break;
     	}
     	$cur = time();
		$dt = strftime('%Y-%m-%d', $cur);
		//当天0:0:0的总秒数
		$today = strtotime($dt);		
		
		$someday = $today - $period;
		$time = date('Y-m-d H:i:s', $someday);
//		common_debug($time);
     	//common_debug($time);
    	$sub = new Subscription();
		$sub->selectAdd(); // clears it
        $sub->selectAdd('sum( 1 ) as num');
        $sub->whereAdd('subscribed='.$user_id);
        $sub->whereAdd('subscriber <>'.$user_id);
        //common_debug($user_id);
        $sub->whereAdd('created >'.'\'' .$time. '\'');
//    	if (!is_null($since)) {
//            $sub->whereAdd('created > \'' . date('Y-m-d H:i:s', $since) . '\'');
//        }
//        
        if ($sub->find()) {
        	$sub->fetch();
        	$num = ($sub->num)?$sub->num:0;
        }
        else $num = 0;
        $sub->free();
        return $num; //return update num
    }
    
      /*
	//关注的个数排行
    SELECT subscriber, sum( 1 ) AS subnum
	FROM `subscription`
	GROUP BY subscriber
	ORDER BY subnum DESC
	LIMIT 0 , 30	
*/
    static function getSubscriberOrder($limit=20, $since=null) {
    	$sub = new Subscription();
		$sub->selectAdd(); // clears it
        $sub->selectAdd('subscriber');
        $sub->selectAdd('sum( 1 ) as subnum');

        $sub->limit(0, $limit);
        $sub->groupBy('subscriber');
        $sub->orderBy('subnum DESC');
        
        if (!is_null($since)) {
            $sub->whereAdd('created > \'' . date('Y-m-d H:i:s', $since) . '\'');
        }
        
        $subs = array();
        if ($sub->find()) {
        	while ($sub->fetch()) {
        		$subs[] = array('id'=>$sub->subscribed, 'num'=>$sub->subnum);
        	}
        }
        $sub->free();
        return $subs;
    }
    
    	//VIP排行, 被关注的排行
//	SELECT subscriber, sum( 1 ) AS subnum
//	FROM subscription JOIN user ON subscription.subscribed = user.id
//	WHERE profile.is_vip = 1
//	GROUP BY subscriber
//	ORDER BY subnum DESC
//	LIMIT 0 , 30 

    static function getVIPOrder($limit=20, $since=null, $vip_type=0) {
    	$sub = new Subscription();
    	
    	if($vip_type != 0) {
	    	if (!is_null($since)) {
	        	$sql = 	'SELECT subscriber, sum( 1 ) AS subnum ' . 
					'FROM subscription JOIN profile ON subscription.subscribed = profile.id ' .
					'WHERE profile.is_vip = 1 AND subscription.created > \'' . date('Y-m-d H:i:s', $since) . '\' ' .
	        		'AND user.vip_type = ' . $vip_type . ' ' .
					'GROUP BY subscriber ' .
					'ORDER BY subnum DESC ' .
					'LIMIT 0 , ' . $limit; 
	    	} else {
	    		$sql = 	'SELECT subscriber, sum( 1 ) AS subnum ' . 
					'FROM subscription JOIN profile ON subscription.subscribed = profile.id ' .
					'WHERE profile.is_vip = 1 AND user.vip_type = ' . $vip_type . ' ' .
					'GROUP BY subscriber ' .
					'ORDER BY subnum DESC ' .
					'LIMIT 0 , ' . $limit; 
	    	}
    	} else {
    		if (!is_null($since)) {
	        	$sql = 	'SELECT subscriber, sum( 1 ) AS subnum ' . 
					'FROM subscription JOIN profile ON subscription.subscribed = profile.id ' .
					'WHERE profile.is_vip = 1 AND subscription.created > \'' . date('Y-m-d H:i:s', $since) . '\' ' .
					'GROUP BY subscriber ' .
					'ORDER BY subnum DESC ' .
					'LIMIT 0 , ' . $limit; 
	    	} else {
	    		$sql = 	'SELECT subscriber, sum( 1 ) AS subnum ' . 
					'FROM subscription JOIN profile ON subscription.subscribed = profile.id ' .
					'WHERE profile.is_vip = 1 '.
					'GROUP BY subscriber ' .
					'ORDER BY subnum DESC ' .
					'LIMIT 0 , ' . $limit; 
	    	}
    	}
    	   
        $subs = array();
        
        if ($sub->query($sql)) {
	        while ($sub->fetch()) {
	           $subs[] = array('id'=>$sub->subscribed, 'num'=>$sub->subnum);
	        }
        }
        $sub->free();
        return $subs;
    }

 	static function UpdateRead($user, $subed_id) {
 		$sub = new Subscription();
 		$sql = 'update subscription set is_unread = 0 where subscriber = ' . $user->id .
 		' and subscribed = ' . $subed_id;
 		$sub->query($sql);
 		$sub->free();
 	}
 	
 	static function saveNew($from_user_id, $to_user_id) {
 		$subscription = new Subscription();
        $subscription->subscriber = $from_user_id;
        $subscription->subscribed = $to_user_id;
        $subscription->created = common_sql_now();

        $result = $subscription->insert();

        if (!$result) {
            common_log_db_error($subscription, 'INSERT', __FILE__);
            return false;
        }
        return true;
 	}
 	
 	static function getSubscribedids($user_id) {
 		
 		$sub = new Subscription();
		$sub->selectAdd(); // clears it
        $sub->selectAdd('subscribed');
        $sub->whereAdd('subscriber = '.$user_id);
        $sub->find();
        $ids = array();
        while($sub->fetch())
        {
        	$ids[] = $sub->subscribed;
        }
        $sub->free();
        return $ids;
 	}
 	
	/* Subscribe user $user to other user $other.
	 * Note: $other must be a local user, not a remote profile.
	 * Because the other way is quite a bit more complicated.
	 */
	
	static function subscribeTo($user, $other, $scorechange = true)
	{
	    if ($user->isSubscribed($other)) {
	        return '已经关注此用户';
	    }
	
	    if ($other->hasBlocked($user)) {
	        return '您已被此用户屏蔽';
	    }
	
	    if (!$user->subscribeTo($other, $scorechange)) {
	        return '关注用户失败';
	        return;
	    }    
	
	    $cache = common_memcache();
	
	    if ($cache) {
	        $cache->delete(common_cache_key('user:notices_with_friends:' . $user->id));
	        for($i=1; $i<5; $i++) {
	            //content_type	
	            $ck_ct = 'user:notices_with_friends:' . $user->id . ':' . $i;
	            $cache->delete($ck_ct);
	        }
		}
	
	    $profile = $user->getProfile();
	    $otherProfile = $other->getProfile();
	
	    require_once INSTALLDIR.'/lib/mail.php';
	    mail_subscribe_notify($otherProfile, $profile);
	    
	    $profile->blowSubscriptionCount();
	    $otherProfile->blowSubscriberCount();
	
	    if ($otherProfile->autosubscribe && !$other->isSubscribed($user) && !$user->hasBlocked($other)) {
	        if (!$other->subscribeTo($user, $scorechange)) {
	            return '对方关注您失败。';
	        }
	        $cache = common_memcache();
	
	        if ($cache) {
	            $cache->delete(common_cache_key('user:notices_with_friends:' . $other->id));
	            for($i=1; $i<5; $i++) {
		            //content_type	
		            $ck_ct = 'user:notices_with_friends:' . $other->id . ':' . $i;
		            $cache->delete($ck_ct);
		        }
			}
	
	        mail_subscribe_notify($profile, $otherProfile);
	    }
	    
	    // 添加一些消息
		$notice = $otherProfile->getCurrentNotices(null, 4, 3);
		if ($notice && $notice->N > 0) {
			
			$userNoticeIds = Notice_inbox::getInboxNoticeIdsForUser($user);
			
		    $qry = 'INSERT INTO notice_inbox (user_id, notice_id, source, created) VALUES ';
		    $values = array();
			while ($notice->fetch()) {
				if (! $notice || in_array($notice->id, $userNoticeIds)) {
					// 已存在的记录不再插入
					continue;
				}
			    $values[] = '(' . $user->id . ', ' . $notice->id . ', ' . NOTICE_INBOX_SOURCE_SUB . ", '" . $notice->created . "') ";
			}
			$qry .= implode(',', $values);
			
		    $inbox = new Notice_inbox();
		    $result = $inbox->query($qry);
		    if (PEAR::isError($result)) {
		        common_log_db_error($inbox, $qry);
		    }
		}
	
	    return true;
	}
	
	/* Unsubscribe user $user from profile $other
	 * NB: other can be a remote user. */
	function unsubscribeTo($user, $other)
	{
	    if (!$user->isSubscribed($other))
	        return '您没有关注这个用户。';
	
	    $sub = new Subscription();
	
	    $sub->subscriber = $user->id;
	    $sub->subscribed = $other->id;
	
	    $sub->find(true);
	
	    // note we checked for existence above
	
	    if (!$sub->delete())
	        return '取消关注失败。';
	    
	    // deduct 3 scores for user who is unsubscribed
	    User_grade::deductScore($other->id, 2);
	    
	    Profile::subFollowersCount($other->id);
	
	    $cache = common_memcache();
	
	    if ($cache) {
	        $cache->delete(common_cache_key('user:notices_with_friends:' . $user->id));
	        for($i=1; $i<5; $i++) {
	            //content_type	
	            $ck_ct = 'user:notices_with_friends:' . $user->id . ':' . $i;
	            $cache->delete($ck_ct);
	        }
		}
	
	    $profile = $user->getProfile();
		
	    $profile->blowSubscriptionCount();
	    $other->blowSubscriberCount();
	    
	    // If the user doesn't exist in friends list, remove all it's tags
	//    if (!$user->isFriend($other)) {
	    $protag = new Tagtions();
	        
	    $result = $protag->query('DELETE FROM tagtions WHERE tagger = ' . $user->id . ' AND tagged = ' . $other->id);
	        
	//    if (! $result) {
	//        return '移除此用户分组失败。';
	//    }
	//    }
	
	    return true;
	}
	
}
