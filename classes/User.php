<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Table Definition for user
 */

require_once INSTALLDIR.'/classes/Memcached_DataObject.php';
require_once 'Validate.php';

class User extends Memcached_DataObject
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'user';                            // table name
    public $id;                              // int(4)  primary_key not_null
    public $uname;                        // varchar(64)  unique_key
    public $nickname;						 // varchar(255)
    public $created;                         // datetime() not_null
    public $modified;                        // timestamp() not_null default_CURRENT_TIMESTAMP
    public $game_id;
	public $game_server_id;
	public $game_job;
	public $game_org;
	public $design_id;
    
    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('User',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function getProfile()
    {
        return Profile::staticGet('id', $this->id);
    }

    function isSubscribed($other)
    {
        assert(!is_null($other));
        // XXX: cache results of this query
        $sub = Subscription::pkeyGet(array('subscriber' => $this->id,
                                           'subscribed' => $other->id));
        return (is_null($sub)) ? false : true;
    }

    // 'update' won't write key columns, so we have to do it ourselves.
    function updateKeys(&$orig)
    {
        $parts = array();
        foreach (array('uname') as $k) {
            if (strcmp($this->$k, $orig->$k) != 0) {
                $parts[] = $k . ' = ' . $this->_quote($this->$k);
            }
        }
        if (count($parts) == 0) {
            // No changes
            return true;
        }
        $toupdate = implode(', ', $parts);

        $table = $this->tableName();
        if(common_config('db','quote_identifiers')) {
            $table = '"' . $table . '"';
        }
        $qry = 'UPDATE ' . $table . ' SET ' . $toupdate .
          ' WHERE id = ' . $this->id;
        $orig->decache();
        $result = $this->query($qry);
        if ($result) {
            $this->encache();
        }
        return $result;
    }

    function allowed_uname($uname)
    {
        // XXX: should already be validated for size, content, etc.
        static $blacklist = array('rss', 'xrds', 'doc', 'main','admin', 'administrator',
                                  'settings', 'notice', 'user',
                                  'search', 'avatar', 'tag', 'tags',
                                  'api', 'message', 'group', 'groups',
                                  'local', 'gamepub', 'hotnotice', 'featured', 
                                  'featuredrss', 'favoritedrss', 'opensearch', 
                                  'facebook', 'twitter', 'attachment', 
                                  'conversation', 'replylist', 'hotpicture', 
                                  'hotmusic', 'hotvideo', 'cityhot', 'hottopics',
        						  'public', 'toplist', 'homepage', 
        						  'share', 'peopletag', 'favorited', 'register', 'clients',
        						  'crm',  'funnypeople', 'experiences', 'requestforhelp', 
								  'citypeople', 'rank', 'activities', 'mplayer', 'ajax', 'uploadvideo', 'getvideostatus',
								  'discuss', 'retweet', 'game', 'gameserver', 'home', 'discussionlist', 'showtime',
        						  // directory name
        						  'actions', 'ajax', 'api', 'clasess', 'db', 'doc-src', 'extlib', 'lib', 'scripts', 'services', 'templates', 'hooyou', 'downloads', 'plugins');
        $merged = array_merge($blacklist, common_config('uname', 'blacklist'));
        return !in_array($uname, $merged);
    }

    function getCurrentNotice($dt=null)
    {
        $profile = $this->getProfile();
        if (!$profile) {
            return null;
        }
        return $profile->getCurrentNotice($dt);
    }

    function subscribeTo($other, $scorechange = true)
    {
        $this->query('BEGIN');
        
        $sub = new Subscription();
        $sub->subscriber = $this->id;
        $sub->subscribed = $other->id;

        $sub->created = common_sql_now(); // current time

        if (!$sub->insert()) {
            return false;
        }
        
        Profile::addFollowersCount($other->id);
        
        // Send a system message to the user who was followed
        $content = '用户 ' . $this->nickname . ' 开始在' . common_config('site', 'name') . '上关注您了，您可以通过点击名字来查看这位用户的详细资料。';
    	// Render the message with link to user detail
    	$rendered = '用户 '. common_user_linker($this->id). ' 开始在' . common_config('site', 'name') . '上关注您了，您可以通过点击名字来查看这位用户的详细资料。';
    	// We put a notice into sysmessage
        System_message::saveNew(array($other->id), $content, $rendered, 0);
        
        if($scorechange){
	        // add 2 scores to the subscribed
	        // deduct 1 score to the subscriber
	        User_grade::addScore($other->id, 2);
	        User_grade::deductScore($this->id, 1);
        }
		
        $this->query('COMMIT');
        
        return true;
    }

    function hasBlocked($other)
    {
        $block = Profile_block::get($this->id, $other->id);

        if (is_null($block)) {
            $result = false;
        } else {
            $result = true;
            $block->free();
        }

        return $result;
    }
    
    static function _sendMailConfirmation($profile, $email) {
    	$confirm = new Confirm_address();
        $confirm->code = common_confirmation_code(128);
        $confirm->user_id = $profile->id;
        $confirm->address = $email;
        $confirm->address_type = 'email';

        $result = $confirm->insert();
        
        mail_confirm_address($profile, $confirm->code, $email);
    }

    static function register($fields) {

    	extract($fields);
        
        // saveNewUser
    	$user = new User();
    	
    	$user->query('BEGIN');
    	
        $user->uname = $uname;
        $user->nickname = $nickname;
        $user->profileurl = common_profile_url($uname);

        $user->game_id = $game_id;
        $user->game_server_id = $game_server_id;
        $user->game_job = '';
        $user->game_org = '';
        
        $user->created = common_sql_now();
        $result = $user->insert();

        if (! $result) {
            common_log_db_error($user, 'INSERT', __FILE__);
            return false;
        }

        $profile = new Profile();
        
        $profile->id = $user->id;
        $profile->uname = $user->uname;
        $profile->profileurl = $user->profileurl;
        $profile->sex = $sex;
        $profile->emailnotifyattn = 0;
        $profile->emailnotifyfav = 0;
        $profile->emailnotifysub = 0;
        $profile->emailnotifymsg = 0;
        $profile->emailnotifynudge = 1;
        $profile->password = common_munge_password($password, $user->id);
        
        $profile->game_id = $user->game_id;
        $profile->game_server_id = $user->game_server_id;
        $profile->game_job = $user->game_job;
        $profile->game_org = $user->game_org;
        
        if ($mail_confirmed) {
        	$profile->email = $email;
        }
        
        $profile->is_originuser = 0;
        
    	$inboxes = common_config('inboxes', 'enabled');

        if ($inboxes === true || $inboxes == 'transitional') {
            $profile->inboxed = 1;
        }
        $profile->nickname = $user->nickname;

        $profile->created = common_sql_now();

        $result = $profile->insert();
        
    	if (empty($result)) {
            common_log_db_error($profile, 'INSERT', __FILE__);
            return false;
        }
        
        //基类路径, 每个用户的路径, 在此新建一个新的目录, 比如id=100000, 则为file/10/00/00/200912/shaimm.png
        //头像放在默认的文件夹下面file/10/00/00/default/shaimm.png
        //六个字的id, 以后可以改
        $subpath = Avatar::subpath($user->id);
        $path = Avatar::filepath($subpath);
        Imagefile::mkdirs($path);
        
        if (! Subscription::saveNew($user->id, $user->id)) {
        	return false;
        }
        
        // Create a user score&grade record, default grade 1, scrore 10
       	User_grade::newScore($user->id);

        // Default system subscription


        //每个用户默认创建一个收藏夹
       	Fave_group::addNew($user, '我的收藏');
       	Fave_group::addNew($user, '图片收藏');
       	Fave_group::addNew($user, '音乐收藏');
       	Fave_group::addNew($user, '视频收藏');
       	
       	$usergame = $user->getGame();
       	User_tag::addATag($user->id, '朋友');
       	User_tag::addATag($user->id, $usergame->game_group_name);
       	User_tag::addATag($user->id, '高玩');
       	
       	$user->updateCompleteness();
       	
        $user->query('COMMIT');
        
        if (! $mail_confirmed) {
        	self::_sendMailConfirmation($profile, $email);
    	}
    	
        return $user;
    }

    
    // Things we do when the email changes

    function emailChanged()
    {
        $invites = new Invitation();
        $invites->address = $this->email;
        $invites->address_type = 'email';

        if ($invites->find()) {
            while ($invites->fetch()) {
                $other = User::staticGet($invites->user_id);
                Subscription::subscribeTo($other, $this);
                // add by zhcao
                Subscription::subscribeTo($this, $other);
            }
        }
    }

    function hasFave($notice)
    {
        $cache = common_memcache();
        if ($cache) {
            // This is the stream of favorite notices, in rev chron
            // order. This forces it into cache.
            $ids = Fave::stream($this->id, 0, NOTICE_CACHE_WINDOW);
            // If it's in the list, then it's a fave
            if (in_array($notice->id, $ids)) {
                return true;
            }
            // If we're not past the end of the cache window,
            // then the cache has all available faves, so this one
            // is not a fave.
            if (count($ids) < NOTICE_CACHE_WINDOW) {
                return false;
            }
            // Otherwise, cache doesn't have all faves;
            // fall through to the default
        }
        $fave = Fave::pkeyGet(array('user_id' => $this->id,
                                    'notice_id' => $notice->id));
        return ((is_null($fave)) ? false : true);
    }    
    
	function hasFaveGroup($favegroupName)
    {
        $favegroup = Fave_group::pkeyGet(array('user_id' => $this->id,
                                    'name' => $favegroupName));
        return ((is_null($favegroup)) ? false : true);
    }
    
    //得到此用户的所有收藏夹, 与下面的函数重复
    function getFaveGroup($favegroupName=null)
    {
        $faveGroups = Fave_group::getFaveGroup($this->id, $favegroupName);
        return $faveGroups;
    }

    function mutuallySubscribed($other)
    {
        return $this->isSubscribed($other) &&
          $other->isSubscribed($this);
    }
    
	function subscribedOrSubscriber($other)
    {
        return $this->isSubscribed($other) ||
          $other->isSubscribed($this);
    }

    function mutuallySubscribedUsers()
    {
        // 3-way join; probably should get cached
        $UT = common_config('db','type')=='pgsql'?'"user"':'user';
        $qry = "SELECT $UT.* " .
          "FROM subscription sub1 JOIN $UT ON sub1.subscribed = $UT.id " .
          "JOIN subscription sub2 ON $UT.id = sub2.subscriber " .
          'WHERE sub1.subscriber = %d and sub2.subscribed = %d ' .
          "ORDER BY $UT.uname";
        $user = new User();
        $user->query(sprintf($qry, $this->id, $this->id));

        return $user;
    }

    function getReplies($offset=0, $limit=NOTICES_PER_PAGE, $since_id=0, $before_id=0, $since=null)
    {
    	Reply::setRead($this->id, $since_id);
        $ids = Reply::stream($this->id, $offset, $limit, $since_id, $before_id, $since);
        return Notice::getStreamByIds($ids);
    }
    
	function getSysmes($offset=0, $limit=NOTICES_PER_PAGE, $since_id=0, $before_id=0, $since=null)
    {
    	Receive_sysmes::setRead($this->id, $since_id);
        return Receive_sysmes::stream($this->id, $offset, $limit, $since_id, $before_id, $since);
    }

    function getTaggedNotices($tag, $offset=0, $limit=NOTICES_PER_PAGE, $since_id=0, 
    			$before_id=0, $since=null, $content_type=0, $area_type=0, $topic_type=0) {
    	if($content_type > 0) {
    		//添加blow
    		$ids = Notice::stream(array($this, '_streamTaggedDirect'),
                              array($tag),
                              'profile:notice_ids_tagged:' . $this->id . ':' . $tag . ':' . $content_type,
                              $offset, $limit, $since_id, $max_id, $since, $content_type, $area_type, $topic_type);
    	} else {
        	$ids = Notice::stream(array($this, '_streamTaggedDirect'),
                              array($tag),
                              'profile:notice_ids_tagged:' . $this->id . ':' . $tag,
                              $offset, $limit, $since_id, $max_id, $since, $content_type, $area_type, $topic_type);
    	}
        return Notice::getStreamByIds($ids);
    }
    
	//一级目录的tag
	//User里面的tag不用这样%
    function _streamTaggedDirect($tag, $offset, $limit, $since_id, $max_id, 
    				$since, $content_type=0, $area_type=0, $topic_type=0)
    {
        $notice = new Notice();

        $query =
          "select id from notice join notice_tag on id=notice_id where second_tag_id in (select id from second_tag where first_tag_id = ".
          $tag . ") and user_id=" . $notice->escape($this->id);

        if ($since_id != 0) {
            $query .= " and id > $since_id";
        }

        if ($max_id != 0) {
            $query .= " and id < $max_id";
        }
        
        if ($content_type != 0) {
            $query .= " and content_type =  $content_type";
        }
        
        if ($topic_type != 0) {
        	$query .= " and topic_type != $topic_type";
        }

        if (!is_null($since)) {
            $query .= " and created > '" . date('Y-m-d H:i:s', $since) . "'";
        }
        
        $query .= ' and is_banned = 0'; //and is_delete = 0 

        $query .= ' order by id DESC';

        if (!is_null($offset)) {
            $query .= " limit $offset, $limit";
        }

        $notice->query($query);

        $ids = array();

        while ($notice->fetch()) {
            $ids[] = $notice->id;
        }

        return $ids;
    }

    function getNotices($offset=0, $limit=NOTICES_PER_PAGE, $since_id=0, 
    			$before_id=0, $since=null, $content_type=0, $area_type=0, $topic_type=0, 
    			$first_tag=0, $is_retweet=false)
    {
        if($content_type && ! $first_tag) {
        	//缓存内容分类的消息
        	$ids = Notice::stream(array($this, '_streamDirect'),
                              array($first_tag, $is_retweet),
                              'profile:notice_ids:' . $this->id . ':' . $content_type,
                              $offset, $limit, $since_id, $before_id, $since, $content_type, $area_type, $topic_type);        	
        } else if (! $content_type && $first_tag) { 
        	//缓存话题分类的消息
        	$ids = Notice::stream(array($this, '_streamDirect'),
                              array($first_tag, $is_retweet),
                              'profile:notice_ids:' . $this->id . ':' . $first_tag,
                              $offset, $limit, $since_id, $before_id, $since, $content_type, $area_type, $topic_type);
        } else if ($is_retweet) {
        	//缓存转载的消息
        	$ids = Notice::stream(array($this, '_streamDirect'),
                              array($first_tag, $is_retweet),
                              'profile:notice_ids:retweet' . $this->id,
                              $offset, $limit, $since_id, $before_id, $since, $content_type, $area_type, $topic_type);
        } else {
        	//缓存无分类的消息
        	$ids = Notice::stream(array($this, '_streamDirect'),
                              array($first_tag, $is_retweet),
                              'profile:notice_ids:' . $this->id,
                              $offset, $limit, $since_id, $before_id, $since, $content_type, $area_type, $topic_type);
        }

        return Notice::getStreamByIds($ids);
   }
   
    function _streamDirect($first_tag, $is_retweet, $offset, $limit, $since_id, $max_id, $since, 
    		$content_type, $area_type, $topic_type)
    {
        $notice = new Notice();
        $notice->user_id = $this->id;
        $notice->selectAdd();
        $notice->selectAdd('id');
        if ($since_id != 0) {
            $notice->whereAdd('id > ' . $since_id);
        }
        if ($max_id != 0) {
            $notice->whereAdd('id <= ' . $max_id);
        }
        if (!is_null($since)) {
            $notice->whereAdd('created > \'' . date('Y-m-d H:i:s', $since) . '\'');
        }
       	if ($content_type > 0) {
            $notice->whereAdd('content_type = ' . $content_type);
        }

        if ($first_tag > 0) {
        	//$notice->whereAdd('EXISTS (SELECT * from notice_tag where notice.id=notice_tag.notice_id ' .
        	//'and second_tag_id in (select id from second_tag where first_tag_id=' . $first_tag . '))');
        	
        	//现在一级话题都采用999x, 二级话题都是999xzz
        	$notice->whereAdd('id IN (SELECT notice_tag.notice_id from notice_tag WHERE ' .
        		'notice_tag.second_tag_id DIV 100=' . $first_tag . ')');
        } 

       	if ($is_retweet)
        	$notice->whereAdd('retweet_from != 0'); 	
        	
      	//不显示群组消息
        $notice->whereAdd('topic_type != 4');	
        
        $notice->whereAdd('is_banned = 0');
        $notice->whereAdd('reply_only = 0');
        $notice->orderBy('id DESC');
        
        if (!is_null($offset)) {
            $notice->limit($offset, $limit);
        }
        
        $ids = array();
        if ($notice->find()) {
            while ($notice->fetch()) {
                $ids[] = $notice->id;
            }
        }
        return $ids;
    }
    
    //这个是查询此用户所有的收藏, 但不是属于某收藏夹下的收藏, 可以不用
    function favoriteNotices($offset=0, $limit=NOTICES_PER_PAGE, $own=false)
    {
        $ids = Fave::stream($this->id, $offset, $limit, $own);
        return Notice::getStreamByIds($ids);
    }
    
    //如果不加content_type, first_tag过滤, 是非常好做的, 直接使用inbox里面的机制, 但是加了两个条件之后呢??
    //可以在插入数据的时候优化, 而不是查询优化? 使用队列出入消息到notice_inbox里面
    function noticesWithFriends($offset=0, $limit=NOTICES_PER_PAGE, $since_id=0, $before_id=0, 
    				$content_type=0, $first_tag=0, $other=false)
    {
    	$ids = Notice_inbox::stream($this->id, $offset, $limit, $since_id, $before_id, 
    		false, $content_type, $first_tag, $other);

        return Notice::getStreamByIds($ids);
    }

    function noticeInbox($offset=0, $limit=NOTICES_PER_PAGE, $since_id=0, $before_id=0, 
    		$content_type=0, $first_tag=0, $gtag=null, $other=false)
    {
    	$ids = Notice_inbox::stream($this->id, $offset, $limit, $since_id, $before_id, 
    		true, $content_type, $first_tag, $gtag, $other);

        return Notice::getStreamByIds($ids);
    }
    
    function latestNoticeId()
    {
    	return Notice_inbox::getLatestNoticeId($this->id);
    }
    
    function myNotices($offset=0, $limit=NOTICES_PER_PAGE, $since_id=0, $before_id=0, $since=null)
    {
		$qry = 'SELECT notice.* ' .
              'FROM notice WHERE notice.user_id = %d AND notice.is_banned = 0'; //AND notice.is_delete = 0 
        return Notice::getStream(sprintf($qry, $this->id),
                                     'user:mynotices:' . $this->id,
                                     $offset, $limit, $since_id, $before_id,
                                     $order, $since);
    }

    function blowFavesCache()
    {
        $cache = common_memcache();
        if ($cache) {
            // Faves don't happen chronologically, so we need to blow
            // ;last cache, too
            $cache->delete(common_cache_key('fave:ids_by_user:'.$this->id));
            $cache->delete(common_cache_key('fave:ids_by_user:'.$this->id.';last'));
            $cache->delete(common_cache_key('fave:ids_by_user_own:'.$this->id));
            $cache->delete(common_cache_key('fave:ids_by_user_own:'.$this->id.';last'));
        }
        $profile = $this->getProfile();
        $profile->blowFaveCount();
    }

    function getSelfTags()
    {
        return Tagtions::getTags($this->id, $this->id);
    }

    function setSelfTags($newtags)
    {
        return Tagtions::setTags($this->id, $this->id, $newtags);
    }

    function block($other)
    {
        // Add a new block record
        $block = new Profile_block();
        // Begin a transaction
        $block->query('BEGIN');
        $block->blocker = $this->id;
        $block->blocked = $other->id;
        $result = $block->insert();
        if (!$result) {
            common_log_db_error($block, 'INSERT', __FILE__);
            return false;
        }
        // Cancel their subscription, if it exists
        $sub = Subscription::pkeyGet(array('subscriber' => $other->id,
                                           'subscribed' => $this->id));
        if ($sub) {
            $result = $sub->delete();
            if (!$result) {
                common_log_db_error($sub, 'DELETE', __FILE__);
                return false;
            }
           	Profile::subFollowersCount($this->id);
        }
        $block->query('COMMIT');
        return true;
    }

    function unblock($other)
    {
        // Get the block record

        $block = Profile_block::get($this->id, $other->id);
        if (!$block) {
            return false;
        }
        $result = $block->delete();
        if (!$result) {
            common_log_db_error($block, 'DELETE', __FILE__);
            return false;
        }
        return true;
    }

    function getGroups($offset=0, $limit=null)
    {
        $profile = $this->getProfile();
        if (!$profile) {
            return null;
        }
        return $profile->getGroups($offset, $limit);
    }
    
    function getGroupIds()
    {
        $qry =
          'SELECT group_id ' .
          'FROM group_member WHERE user_id = %d';

        $group_member = new Group_member();
        $group_member->query(sprintf($qry, $this->id));
        $groupids = array();
        while($group_member->fetch()) {
        	$groupids[] = $group_member->group_id;
        }
        $group_member->free();
        return $groupids;
    }
    
    // Get the number of groups this user owns
    function getOwnedGroupsNum()
    {
    	$qry = 
    	    "select count(*) as ownedgroupsnum from user_group where user_group.ownerid = %d";
    	$group = new User_group();
    	$group->query(sprintf($qry, $this->id));
    	$group->fetch();
    	$ownedgroupsnum = intval($group->ownedgroupsnum);
    	$group->free();
    	return $ownedgroupsnum;
    }
    
 // Get the number of groups this user owns
    function getOwnedLifeGroupsNum()
    {
    	$qry = 
    	    "select count(*) as ownedgroupsnum from user_group where user_group.ownerid = %d and user_group.groupclass = 0";
    	$group = new User_group();
    	$group->query(sprintf($qry, $this->id));
    	$group->fetch();
    	$ownedgroupsnum = intval($group->ownedgroupsnum);
    	$group->free();
    	return $ownedgroupsnum;
    }
    
 // Get the number of groups this user owns
    function getOwnedGameGroupsNum()
    {
    	$qry = 
    	    "select count(*) as ownedgroupsnum from user_group where user_group.ownerid = %d and user_group.groupclass = 1";
    	$group = new User_group();
    	$group->query(sprintf($qry, $this->id));
    	$group->fetch();
    	$ownedgroupsnum = intval($group->ownedgroupsnum);
    	$group->free();
    	return $ownedgroupsnum;
    }
    
    // Return whether the group join application has been submited before
    function haveApplied($groupid)
    {
    	$qry = 
    	    "select count(*) as appnum from group_application where groupid = %d and inviteeid = %d";
    	$groupApp = new Group_application();
    	$groupApp->query(sprintf($qry, $groupid, $this->id));
    	$groupApp->fetch();
    	$appnum = intval($groupApp->appnum);
    	$groupApp->free();
    	if ($appnum>0) {
    		return true;
    	}else {
    		return false;
    	}
    }

    function getSubscriptions($offset=0, $limit=null)
    {
        $profile = $this->getProfile();
        assert(!empty($profile));
        return $profile->getSubscriptions($offset, $limit);
    }
    
    function getSubscriptionids() {
    	$qry =
          'SELECT subscribed FROM subscription ' .
          'WHERE subscriber = %d ' .
          'AND subscribed != subscriber ';

        $subs = new Subscription();
        $subs->query(sprintf($qry, $this->id));
        $list = array();
        while($subs->fetch()) {
	        	$list[] = $subs->subscribed;
	        }
	    return $list;
    }
    
    function getFriends($offset=0, $limit=null)
    {
        $profile = $this->getProfile();
        assert(!empty($profile));
        return $profile->getFriends($offset, $limit);
    }

    function getSubscribers($offset=0, $limit=null, $search='')
    {
        $profile = $this->getProfile();
        assert(!empty($profile));
        return $profile->getSubscribers($offset, $limit, $search);
    }
    
    function blacklistCount() {
    	$block = new Profile_block();
        $block->whereAdd('blocker = ' . $this->id);
        return $block->count();
    }
    
    function getBlacklist($offset = 0, $limit = null) {
    	$block = new Profile_block();
        $block->whereAdd('blocker = ' . $this->id);
        $block->find();
        
        $blocked = array();
        while ($block->fetch()) {
        	$blocked[] = $block->blocked;
        }
        $block->free();
        
        $profile = new Profile();
        $profile->whereAdd('id in (' . implode(',', $blocked) . ')');
        $profile->find();
        
        return $profile;
    }
    
    //可以缓存
    function getSubscriberNum()
    {
    	$qry = 
    	    "select count(*) as subscribernum from subscription where subscription.subscribed = %d";
    	$subscriber = new Subscription();
    	$subscriber->query(sprintf($qry, $this->id));
    	$subscriber->fetch();
    	$subscribernum = intval($subscriber->subscribernum);
    	$subscriber->free();
    	return $subscribernum;
    }

    // XXX 有待确认
    function getTaggedSubscribers($tag, $offset=0, $limit=null)
    {
    	$tagId = User_tag::getTagid($this->id, $tag);
        $qry =
          'SELECT profile.* ' .
          'FROM profile JOIN subscription ' .
          'ON profile.id = subscription.subscriber ' .
          'JOIN tagtions ON (tagtions.tagged = subscription.subscriber ' .
          'AND tagtions.tagger = subscription.subscribed) ' .
          'WHERE subscription.subscribed = %d ' .
          'AND tagtions.tagid = %d ' .
          'AND subscription.subscribed != subscription.subscriber ' .
          'ORDER BY subscription.created DESC ';

        if ($offset) {
            if (common_config('db','type') == 'pgsql') {
                $qry .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
            } else {
                $qry .= ' LIMIT ' . $offset . ', ' . $limit;
            }
        }
        $profile = new Profile();
        $cnt = $profile->query(sprintf($qry, $this->id, $tagId));
        return $profile;
    }

    function getTaggedSubscriptions($tag, $offset=0, $limit=null)
    {
        $tagId = User_tag::getTagid($this->id, $tag);
    	$qry =
          'SELECT profile.* ' .
          'FROM profile JOIN subscription ' .
          'ON profile.id = subscription.subscribed ' .
          'JOIN tagtions on (tagtions.tagged = subscription.subscribed ' .
          'AND tagtions.tagger = subscription.subscriber) ' .
          'WHERE subscription.subscriber = %d ' .
          'AND tagtions.tagid = %d ' .
          'AND subscription.subscribed != subscription.subscriber ' .
          'ORDER BY subscription.created DESC ';
        
    	if ($limit) {
        	$qry .= ' LIMIT ' . $offset . ', ' . $limit;
    	}
    	
        $profile = new Profile();

        $profile->query(sprintf($qry, $this->id, $tagId));

        return $profile;
    }
    
	function getUntaggedSubscriptions($offset=0, $limit=null)
    {
		$taggedIds = Tagtions::getMyTaggedIds($this->id);
		$unTaggedIds = array();
		
		// 获取所有我关注的人
		$subs = new Subscription();
		$subs->whereAdd('subscriber = ' . $this->id);
		$subs->find();
		
		while ($subs->fetch()) {
			// 排除掉已经被tag的人和本人
			if ($subs->subscribed != $this->id && ! in_array($subs->subscribed, $taggedIds)) {
				$unTaggedIds[] = $subs->subscribed;
			}
		}
		
    	return Profile::getProfileByIds($unTaggedIds);
    }
    
    // Get all tag names created by one user
    //可以缓存
    function getAllTagNames()
    {
    	$user_tag = new User_tag();
        $user_tag->query('SELECT * FROM user_tag ' .
                            'WHERE tagger = ' . $this->id);
        $tags = array();
        while ($user_tag->fetch()) {
            $tags[] = $user_tag->tag;
        }
        $user_tag->free();
        return $tags;
    }
    
    // Get all tags created by one user
    //缓存
    function getAllTags()
    {
    	$user_tag = new User_tag();
        $user_tag->query('SELECT * FROM user_tag ' .
                            'WHERE tagger = ' . $this->id);
        $tags = array();
        while ($user_tag->fetch()) {
            $tags[] = clone($user_tag);
        }
        $user_tag->free();
        return $tags;
    }
    
    // Get the user scroe
    //缓存, 可以加, 与subscriptionCount函数类似, 当然, 每个操作, 都要清楚此用户的该缓存
    function getUserScrore()
    {
    	return User_grade::getScore($this->id);
    }
    
    // Get the user grade
    function getUserGrade()
    {
    	return User_grade::getGrade($this->id);
    }
    
    // Get system recommended users, these users are provided to get followed
    function getRecommmended()
    {
		$this->query('BEGIN');
		//4个兴趣
//		$interests = User_interest::getInterestByUser($this->id);

//		$profileInter = null;
//		if (! empty($interests)) {
		$profileInter = Profile::getRecommendProfileToFollow(0, 4);
//		}

		$profile = $this->getProfile();
		//3个同城
		$city = Profile::getCityProfileToFollow($profile->province, $profile->city, 0, 3);
		
		$this->query('COMMIT');
		
	    $list = array();
    	if($profileInter){
		    while ($profileInter->fetch()) {
	        	$list[] = $profileInter->id;
	        }
    	}
    	if($city){
	   	 	while ($city->fetch()) {
	        	$list[] = $city->id;
	        }
    	}
    	
        $list = array_unique($list);
    	foreach($list as $k => $v) {
	    	if($v == $this->id)
	    		unset($list[$k]);
	    }
        
        //返回的是id列表
        return $list;
        
    }

    function getDesign()
    {
    	if ($this->design_id == 0) {
    		$design =  Design::defaultGameDesignByGameId($this->game_id);
    	} else {
    		$design = Design::staticGet('id', $this->design_id);
    	}
        
        return $design;
    }
    
    function areaTypeNotices($offset=0, $limit=NOTICES_PER_PAGE, $area_type=0, $content_type=0)
    {
        $ids = Notice::areaTypeStream($this, $offset, $limit, $area_type, $content_type);
        return Notice::getStreamByIds($ids);
    }
    
    // time param format is 'Y-m-d HH:MM:SS'
    static function getNoticesNum($userid, $fromdate, $todate) {
    	$notice = new Notice();
        $qry = "select count(*) as noticeNum from notice where user_id = %d and created between '%s' and '%s'";
    	$notice->query(sprintf($qry, $userid, $fromdate, $todate));
    	$notice->fetch();
    	$noticesNum = intval($notice->noticeNum);
    	$notice->free();
    	return $noticesNum;
    }
    
    //清理缓存
    function getSubscriberNotices($offset=0, $limit=NOTICES_PER_PAGE, $since_id=0, $before_id=0, 
    		$since=null, $content_type=0)
    {
    	$conQry = "";
    	$order = ' ORDER BY notice.created DESC';
    	
    	$qry =
              'SELECT notice.* ' .
              'FROM notice JOIN subscription ON notice.user_id = subscription.subscribed ' .
              'WHERE subscription.subscriber = ' . $this->id . ' AND notice.is_banned = 0 ';  //AND notice.is_delete = 0 
    	
    	
    	 if($content_type != 0) 
            	$conQry = ' and notice.content_type = ' . $content_type;
            	
         $qry .= $conQry;
         $qry .= $order;

         $notice = new Notice();
         $notice->query($qry);
         return $notice;
    }
    
    function updateCompleteness() {
    	$profile = $this->getProfile();
    	$p = 0;
    	if (! empty($profile->uname)) {
    		$p += 5;
    	}
    	if (! empty($profile->nickname)) {
    		$p += 5;
    	}
    	if (! empty($profile->birthday)) {
    		$p += 3;
    	}
    	if (! empty($profile->sex)) {
    		$p += 5;
    	}
    	if (! empty($profile->bio)) {
    		$p += 10;
    	}
    	if (! empty($profile->location)) {
    		$p += 5;
    	}
//    	if (! empty($profile->qq)) {
//    		$p += 5;
//    	}
    	if (! empty($profile->occupation)) {
    		$p += 3;
    	}
    	if (! empty($profile->school)) {
    		$p += 3;
    	}
    	if ($profile->getOriginalAvatar()) {
    		$p += 15;
    	}
    	if (! empty($profile->email)) {
    		$p += 10;
    	}
    	$sdi = User_interest::getSelfDefinedInterestByUser($this->id);
    	if (! empty($sdi)) {
    		$p += 5;
    	}
    	$cdi = User_interest::getClassifiedInterestByUser($this->id);
    	if (! empty($cdi)) {
    		$p += 5;
    	}
    	if (! empty($this->game_id)) {
    		$p += 5;
    	}
    	if (! empty($this->game_server_id)) {
    		$p += 7;
    	}
    	if (! empty($this->game_org)) {
    		$p += 7;
    	}
    	if (! empty($this->game_job)) {
    		$p += 7;
    	}
    	if ($p != $profile->completeness) {
    		$orig = clone($profile);
    		$profile->completeness = $p;
    		$profile->update($orig);
    	}
    	
    }
    
	function getUserByIds($ids) {
    	$user = new User();
    	$user->whereAdd('id in (' . implode(',', $ids) . ')');
    	$user->find();
    	
    	return $user;
    }
    
    function applyDesign($design_id) {
    	$orig = clone($this);
    	$this->design_id = $design_id;
    	$this->update($orig);
    	
    	$profile = $this->getProfile();
    	$origp = clone($profile);
    	$profile->design_id = $this->design_id;
    	$profile->update($origp);
    	return true;
    }
    
    function clearDesign() {
    	$orig = clone($this);
    	$this->design_id = 0;
    	$this->update($orig);
    	
    	$profile = $this->getProfile();
    	$origp = clone($profile);
    	$profile->design_id = $this->design_id;
    	$profile->update($origp);
    	return true;
    }

    static function getUserBynickname($nickname, $offset=0, $limit=2)
    {
    	$user = new User();
    	$user->selectAdd(); // clears it
		$user->selectAdd('uname');    	
    	$user->whereAdd("nickname = '" . $nickname . "'");
    	$user->orderBy('id asc');
    	
    	if (!is_null($offset)) {
			$user->limit($offset, $limit);
		}
    	$user->find();
    	return $user;
    	
    }
    
	function getSubscribersBynickname($nickname, $offset=0, $limit=2)
    {
        $qry =
          'SELECT user.* ' .
          'FROM user JOIN subscription ' .
          'ON user.id = subscription.subscriber ' .
          'WHERE subscription.subscribed =  ' . $this->id . ' ' .
          " AND subscription.subscribed != subscription.subscriber AND user.nickname = '" . $nickname . "'" .
          ' ORDER BY subscription.created DESC ';

        if ($offset) {
            if (common_config('db','type') == 'pgsql') {
                $qry .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
            } else {
                $qry .= ' LIMIT ' . $offset . ', ' . $limit;
            }
        }

        $user = new User();
        $cnt = $user->query($qry);
        return $user;
    }
    
    function getSubscriptionsBynickname($nickname, $offset=0, $limit=2)
    {
        $qry =
          'SELECT user.* ' .
          'FROM user JOIN subscription ' .
          'ON user.id = subscription.subscribed ' .
          'WHERE subscription.subscriber =  ' . $this->id . ' ' .
          " AND subscription.subscribed != subscription.subscriber AND user.nickname = '" . $nickname . "'" .
          ' ORDER BY subscription.created DESC ';

        if (common_config('db','type') == 'pgsql') {
            $qry .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
        } else {
            $qry .= ' LIMIT ' . $offset . ', ' . $limit;
        }

        $user = new User();
        $user->query($qry);
        return $user;
    }
    
	function noticeCountByType($content_type=0, $first_tag=0, $is_retweet=false)
    {
        $c = common_memcache();

        if (!empty($c)) {
        	if($content_type > 0)
            	$cnt = $c->get(common_cache_key('profile:notice_count:'.$this->id.':'.$content_type));
            else if($first_tag > 0)
            	$cnt = $c->get(common_cache_key('profile:notice_count:'.$this->id.':'.$first_tag));
            else if($is_retweet)
            	$cnt = $c->get(common_cache_key('profile:notice_count:retweet'.$this->id));
            else
            	$cnt = $c->get(common_cache_key('profile:notice_count:'.$this->id));
            	
            if (is_integer($cnt)) {
                return (int) $cnt;
            }
        }
        
        $notices = new Notice();
        $notices->whereAdd('user_id = ' . $this->id);
        
        if ($content_type > 0) {
        	$notices->whereAdd('content_type = ' . $content_type);
        }
        
        if($first_tag > 0) {
        	//$notices->whereAdd('EXISTS (SELECT * from notice_tag where notice.id=notice_tag.notice_id ' .
        	//'and second_tag_id in (select id from second_tag where first_tag_id=' . $first_tag. '))'); 
        	
        	//现在一级话题都采用999x, 二级话题都是999xzz
        	$notices->whereAdd('id IN (SELECT notice_tag.notice_id from notice_tag WHERE ' .
        		'notice_tag.second_tag_id DIV 100=' . $first_tag . ')');
        }
        
        if($is_retweet)
        	$notices->whereAdd('retweet_from != 0'); 	
        	
        //不计算群组消息数
        $notices->whereAdd('topic_type != 4');
        
        $notices->whereAdd('is_banned = 0');
        $notices->whereAdd('reply_only = 0');
        
        $cnt = $notices->count();

        if (!empty($c)) {
        	if($content_type > 0)
            	$c->set(common_cache_key('profile:notice_count:'.$this->id.':'.$content_type), $cnt);
             else if($first_tag != 100000)
             	$c->set(common_cache_key('profile:notice_count:'.$this->id.':'.$first_tag), $cnt);
            else if($is_retweet)
            	$c->set(common_cache_key('profile:notice_count:retweet:'.$this->id), $cnt);
            else
            	$c->set(common_cache_key('profile:notice_count:'.$this->id), $cnt);
        }

        return $cnt;
    }
    
	function blowNoticeCountByType()
    {
        $c = common_memcache();
        if (!empty($c)) {
        	//content_type range is 1-5
        	for($i=1; $i<=5; $i++)
            	$c->delete(common_cache_key('profile:notice_count:'.$this->id.':'.$i));
        	//use uniform first tags
        	$fts = First_tag::getUniformFirstTags();
			foreach ($fts as $id => $name) {
				$c->delete(common_cache_key('profile:notice_count:'.$this->id.':'.$id));
			}
            $c->delete(common_cache_key('profile:notice_count:retweet:'.$this->id));
            $c->delete(common_cache_key('profile:notice_count:'.$this->id));
        }
    }
    
	//回复时清理缓存, 判断是回复, 且不是部落的回复, 在notice::savenew里面加
    function replyCount() {
    	$c = common_memcache();

        if (!empty($c)) {
            $cnt = $c->get(common_cache_key('profile:reply_count:'.$this->id));
            if (is_integer($cnt)) {
                return (int) $cnt;
            }
        }

        $reply = new Reply();
        $reply->user_id = $this->id;
        $reply->whereAdd('exists (select * from notice where is_banned=0 and topic_type!=4 and id=notice_id)'); //is_delete=0 and 
        $cnt = (int) $reply->count();

        if (!empty($c)) {
            $c->set(common_cache_key('profile:reply_count:'.$this->id), $cnt);
        }

        return $cnt;
    }
    
    function blowReplyCount() {
    	$c = common_memcache();
        if (!empty($c)) {
            $c->delete(common_cache_key('profile:reply_count:'.$this->id));
        }
    }
    
    function inboxCount() {
    	$c = common_memcache();

        if (!empty($c)) {
            $cnt = $c->get(common_cache_key('profile:inbox_count:'.$this->id));
            if (is_integer($cnt)) {
                return (int) $cnt;
            }
        }

        $inbox = new Message();
        $inbox->to_user = $this->id;
        $inbox->is_deleted_to = 0;
        $cnt = (int) $inbox->count('distinct id');

        if (!empty($c)) {
            $c->set(common_cache_key('profile:inbox_count:'.$this->id), $cnt);
        }

        return $cnt;
    }
    
    function blowInboxCount() {
    	$c = common_memcache();
        if (!empty($c)) {
            $c->delete(common_cache_key('profile:inbox_count:'.$this->id));
        }
    }

    function outboxCount() {
    	$c = common_memcache();

        if (!empty($c)) {
            $cnt = $c->get(common_cache_key('profile:outbox_count:'.$this->id));
            if (is_integer($cnt)) {
                return (int) $cnt;
            }
        }

        $outbox = new Message();
        $outbox->from_user = $this->id;
        $outbox->is_deleted_from = 0;
        $cnt = (int) $outbox->count('distinct id');

        if (!empty($c)) {
            $c->set(common_cache_key('profile:outbox_count:'.$this->id), $cnt);
        }

        return $cnt;
    }
    
    function blowOutboxCount() {
    	$c = common_memcache();
        if (!empty($c)) {
            $c->delete(common_cache_key('profile:outbox_count:'.$this->id));
        }
    }
    
    //重新改写
    function sysmesCount() {
    	$c = common_memcache();

        if (!empty($c)) {
            $cnt = $c->get(common_cache_key('profile:sysmes_count:'.$this->id));
            if (is_integer($cnt)) {
                return (int) $cnt;
            }
        }

        $rsm = new Receive_sysmes();
        $rsm->user_id = $this->id;
        $cnt = (int) $rsm->count('distinct sysmes_id');

        if (!empty($c)) {
            $c->set(common_cache_key('profile:sysmes_count:'.$this->id), $cnt);
        }

        return $cnt;
    }
    
    function blowSysmesCount() {
    	$c = common_memcache();
        if (!empty($c)) {
            $c->delete(common_cache_key('profile:sysmes_count:'.$this->id));
        }
    }

	function getGame()
	{
		return Game::staticGet('id', $this->game_id);
	}
	
	function getGameServer()
	{
		return Game_server::staticGet('id', $this->game_server_id);
	}
	
	//通过game属性获得
	function getUsernumbyGame($game_id = '')
	{
		$user = new User();
		$user->selectAdd();
    	$user->selectAdd('count(*) as num');
    	
    	$user->whereAdd("game_id = ". $game_id);
    	
    	$user->find();
		$user->fetch();
		return $user->num;
	}
	
 	function getActiveTop50() {
    	$queryString = "SELECT user.id FROM user, grade_record WHERE user.id = grade_record.user_id";
    	$queryString .= " ORDER BY grade_record.changed DESC";
    	$queryString .= " LIMIT 0,50";
    	
    	$user = new User();
    	$user->query($queryString);
    	
    	$pops = array();
    	while ($user->fetch()) {
    		$pops[] = $user->id;
    	}
    	$user->free();
    	
    	return $pops;
    }

    function getLevel() {
    	return User_grade::getGrade($this->id);
    }
    
	static function getUnameByNickname($nickname) {
		$ccn = strtolower($nickname);
	    $user = User::staticGet('uname', $ccn);
	    if($user) {
	    	return $nickname;
	    	//return $user->nickname;
	    } else {
	    	$cur_user = common_current_user();
	    	if($cur_user) {
	    		//关注我的人
	    		$users = $cur_user->getSubscribersBynickname($nickname);
	    		if($users->N > 0) {
	    			if($users->N > 1)
			    	 	return null;
			    	else {	  
			    	 	$users->fetch();
			    	 	return $users->uname;
			    	 }
	    		} else {
	    			//我关注的人
	    			$users = $cur_user->getSubscriptionsBynickname($nickname);
	    			if($users->N > 0) {
		    			if($users->N > 1)
				    	 	return null;
				    	else {	    	 	
				    	 	$users->fetch();
				    	 	return $users->uname;
				    	 }
	    			} else {
	    				if($cur_user->nickname == $nickname) 
	    					return $cur_user->uname;
	    				else {
		    				//全部查询	    				
		    				$others = User::getUserBynickname($nickname);
						    if($others->N > 0) {
						    	if($others->N > 1)
						    	 	return null;
						    	else {	    	 	
						    	 	$others->fetch();
						    	 	return $others->uname;
						    	 }
						    } else 
								return null;
	    				}
	    			}
	    		}
	    	} else {
	    		$others = User::getUserBynickname($nickname);
			    if($others->N > 0) {
			    	if($others->N > 1)
			    	 	return null;
			    	else {	    	 	
			    	 	$others->fetch();
			    	 	return $others->uname;
			    	 }
			    } else 
					return null;
	    	}
	    }
	}
	
	//返回user对象
	static function relativeUser($sender, $uname, $dt=null)
	{
	    // Try to find profiles this profile is subscribed to that have this uname
	    $recipient = new User();
	    // XXX: use a join instead of a subquery
	    $recipient->whereAdd('EXISTS (SELECT subscribed from subscription where subscriber = '.$sender->id.' and subscribed = id)', 'AND');
	    $recipient->whereAdd("uname = '" . trim($uname) . "'", 'AND');
	    if ($recipient->find(true)) {
	        // XXX: should probably differentiate between profiles with
	        // the same name by date of most recent update
	        return $recipient;
	    }
	    // Try to find profiles that listen to this profile and that have this uname
	    $recipient = new User();
	    // XXX: use a join instead of a subquery
	    $recipient->whereAdd('EXISTS (SELECT subscriber from subscription where subscribed = '.$sender->id.' and subscriber = id)', 'AND');
	    $recipient->whereAdd("uname = '" . trim($uname) . "'", 'AND');
	    if ($recipient->find(true)) {
	        // XXX: should probably differentiate between profiles with
	        // the same name by date of most recent update
	        return $recipient;
	    }
	    // If this is a local user, try to find a local user with that uname.
	    $sender = User::staticGet($sender->id);
	    if ($sender) {
	        $recipient_user = User::staticGet('uname', $uname);
	        if ($recipient_user) {
	            return $recipient_user;
	        }
	    }
	    // Otherwise, no links. @messages from local users to remote users,
	    // or from remote users to other remote users, are just
	    // outside our ability to make intelligent guesses about
	    return null;
	}
}
