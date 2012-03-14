<?php

define('SHAISHAI', true);
define('INSTALLDIR', realpath(dirname(__FILE__) . '/..'));

require INSTALLDIR . '/lib/environment.php';
require INSTALLDIR . '/lib/cache.php';
require INSTALLDIR . '/lib/common.php';
require INSTALLDIR . '/lib/router.php';

require_once INSTALLDIR . '/classes/Logout_log.php';
require_once INSTALLDIR . '/classes/User.php';
require_once INSTALLDIR . '/classes/Profile.php';

require_once INSTALLDIR . '/lib/util.php';

//清理用户的缓存
//首先获取一些用户的id, 通过logout_log来获取, 3天没有登录的话, 清除缓存
//一天回收一次, 清除已经有3天未登录的用户
//每天运行一次
$cur = time();
$dt = strftime('%Y-%m-%d', $cur);
//当天0:0:0的总秒数
$today = strtotime($dt);		
//前两天天
$two_day_before= $today - 3600*24*3;
$cachestr = date('Y:m:d', $two_day_before);

//$day =  strftime('%Y-%m-%d', time());

$ids = Logout_log::getUnloginUser($two_day_before);

$cache = common_memcache();

if(!$cache) {
	echo '缓存没有开启';
	return;
}

foreach ($ids as $id) {
	$user = User::staticGet('id', $id);

	if (!$user) {
		common_log(LOG_WARNING, 'No such user: ' . $id);
		continue;
	}

    $user->decache();

    //列出一些cache, 之后uncache
    //notice
	blowCaches($user, $cache);
	
	//profile
	$profile = $user->getProfile();
	$profile->blowSubscriberCount();
	$profile->blowSubscriptionCount();
	$profile->blowFriendCount();
	$profile->blowFaveCount();
	$profile->blowNoticeCount();
	$profile->blowNoticeCountByType();
	
	$user->blowReplyCount();
	$user->blowInboxCount();	
	$user->blowOutboxCount();
	$user->blowSysmesCount();
	
	$profile->decache();
	
	//与此user有关联的表, 比如头像, 收藏等
	//$avatar = Avatar::staticGet('user_id', $user->id);
	//$avatar->decache();
}

//清理一些通用的缓存
//热门排行
$cache->delete(common_cache_key('noticeheat:heatorder:'.$cachestr));
for($i=1; $i<5; $i++) {
	//content_type
	$cache->delete(common_cache_key('noticeheat:heatorder:'.$cachestr.':'.$i));
	$cache->delete(common_cache_key('noticeheat:heatorder:'.$cachestr.':'.$i.';last'));
}


function blowCaches($user, $cache) {
	blowNoticeInbox($user, $cache);
	blowNoticeWithFriends($user, $cache);
	blowNoticeCache($user, $cache);
    blowRepliesCache($user, $cache);
	blowFavesCache($user, $cache);
	blowGroupMember($user, $cache);
	blowMyNotice($user, $cache);
	//同城同校同行热门
	blowAreaType($user, $cache);
}

function blowNoticeInbox($user, $cache) {
	$cache->delete(common_cache_key('notice_inbox:by_user:'.$user->id));
	$cache->delete(common_cache_key('notice_inbox:by_user_own:'.$user->id));
	for($i=1; $i<5; $i++) {
		//content_type
		$cache->delete(common_cache_key('notice_inbox:by_user:'.$user->id.':'.$i));
		$cache->delete(common_cache_key('notice_inbox:by_user_own:'.$user->id.':'.$i));
	}
	$cache->delete(common_cache_key('notice_inbox:by_user:'.$user->id.';last'));
	$cache->delete(common_cache_key('notice_inbox:by_user_own:'.$user->id.';last'));
	for($i=1; $i<5; $i++) {
		//content_type
		$cache->delete(common_cache_key('notice_inbox:by_user:'.$user->id.':'.$i.';last'));
		$cache->delete(common_cache_key('notice_inbox:by_user_own:'.$user->id.':'.$i.';last'));
	}
}

function blowNoticeWithFriends($user, $cache) {
    $cache->delete(common_cache_key('user:notices_with_friends:'. $user->id));
    for($i=1; $i<5; $i++) {
        //content_type	
        $ck_ct = 'user:notices_with_friends:' . $user->id . ':' . $i;
        $cache->delete($ck_ct);
    }
    $cache->delete(common_cache_key('user:notices_with_friends:'. $user->id . ';last'));
    for($i=1; $i<5; $i++) {
        //content_type	
        $ck_ct = 'user:notices_with_friends:' . $user->id . ':' . $i . ';last';
        $cache->delete($ck_ct);
    }
}

function blowNoticeCache($user, $cache)
{
	$cache->delete(common_cache_key('profile:notice_ids:'.$user->id));

	for($i=1; $i<5; $i++) {
		//content_type
		$ck_ct = 'profile:notice_ids:' . $user->id . ':' . $i;
		$cache->delete($ck_ct);
	}

	$cache->delete(common_cache_key('profile:notice_ids:'.$user->id.';last'));
	for($i=1; $i<5; $i++) {
		//content_type
		$ck_ct = 'profile:notice_ids:' . $user->id . ':' . $i . ';last';
		$cache->delete($ck_ct);
	}
}

function blowRepliesCache($user, $cache)
{
	$cache->delete(common_cache_key('reply:stream:'.$user->id));
	$cache->delete(common_cache_key('reply:stream:'.$user->id.';last'));
}

function blowFavesCache($user, $cache)
{
	$fave = new Fave();
	$cache->delete(common_cache_key('fave:ids_by_user:'.$user->id));
	$cache->delete(common_cache_key('fave:by_user_own:'.$user->id));
	//fave_group
	$favegroup = new Fave_group();
	$favegroup->user_id = $user->id;
	if ($favegroup->find()) {
		while ($favegroup->fetch()) {
			$cache->delete(common_cache_key('fave:ids_by_fave_group:'.$favegroup->id));	
			$cache->delete(common_cache_key('fave:ids_by_fave_group:'.$favegroup->id.';last'));		
		}
	}
	$cache->delete(common_cache_key('fave:ids_by_user:'.$user->id.';last'));
	$cache->delete(common_cache_key('fave:by_user_own:'.$user->id.';last'));	
}

function blowTagCache($user, $cache)
{
	$tag = new Notice_tag();
	$tag->notice_id = $this->id;
	if ($tag->find()) {
		while ($tag->fetch()) {
			$tag->blowCache($blowLast);
			$ck = 'profile:notice_ids_tagged:' . $this->user_id . ':' . $tag->tag;
				
			$cache->delete($ck);

			for($i=1; $i<5; $i++) {
				//content_type
				$ck_ct = 'profile:notice_ids_tagged:' . $this->user_id . ':' . $tag->tag . ':' . $i;
				$cache->delete($ck_ct);
			}

			if ($blowLast) {
				$cache->delete($ck . ';last');

				for($i=1; $i<5; $i++) {
					//content_type
					$ck_ct = 'profile:notice_ids_tagged:' . $this->user_id . ':' . $tag->tag . ':' . $i . ';last';
					$cache->delete($ck_ct);
				}
				 
			}
		}
	}
}

function blowGroupMember($user, $cache) {
	$cache->delete(common_cache_key('notice_inbox:by_user:' . $user->id));
	for($i=1; $i<5; $i++) {
		//content_type
		$cache->delete(common_cache_key('notice_inbox:by_user:' . $user->id . ':' . $i));
	}
	
	$cache->delete(common_cache_key('notice_inbox:by_user:' . $user->id . ';last'));
	for($i=1; $i<5; $i++) {
		//content_type
		$cache->delete(common_cache_key('notice_inbox:by_user:' . $user->id . ':' . $i. ';last'));
	}
}

function blowMyNotice($user, $cache) {
	$cache->delete(common_cache_key('user:mynotices:' . $user->id));
}

function blowAreaType($user, $cache) {
	for($j=1; $j<4; $j++) {
		$cache->delete(common_cache_key('notice:areatype:'.$user->id.':'.$j));
		$cache->delete(common_cache_key('notice:areatype:'.$user->id.':'.$j.';last'));
		for($i=1; $i<4; $i++) {
			$cache->delete(common_cache_key('notice:areatype:'.$user->id.':'.$j.':'.$i));
			$cache->delete(common_cache_key('notice:areatype:'.$user->id.':'.$j.':'.$i.';last'));
		}
	}
}