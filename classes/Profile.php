<?php

if (!defined('SHAISHAI')) { exit(1); }

/**
 * Table Definition for profile
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Profile extends Memcached_DataObject
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'profile';                         // table name
    public $id;                              // int(4)  primary_key not_null
    public $uname;                        // varchar(64)  multiple_key not_null
    public $nickname;                        // varchar(255)  multiple_key
    public $profileurl;                      // varchar(255)
    public $qq;                              // varchar(255)  unique_key
    public $homepage;                        // varchar(255)  multiple_key
    public $bio;                             // varchar(140)  multiple_key
    public $location;                        // varchar(255)  multiple_key
    public $created;                         // datetime()   not_null
    public $modified;                        // timestamp()   not_null default_CURRENT_TIMESTAMP

    public $followers;
    public $province;
    public $city;
    public $district;
    public $sex;
    public $birthday;
    public $school;
    public $occupation;
    
    public $password;                        // varchar(255)
    public $email;                           // varchar(255)  unique_key
    public $emailnotifysub;                  // tinyint(1)   default_1
    public $emailnotifyfav;                  // tinyint(1)   default_1
    public $emailnotifynudge;                // tinyint(1)   default_1
    public $emailnotifymsg;                  // tinyint(1)   default_1
    public $emailnotifyattn;                 // tinyint(0)   default_0
    public $autosubscribe;                   // tinyint(1)
    public $sharefavorites;                 // tinyint(1) indicates whether user share his favorites
    public $passworderrorcount;				// tinyint(1) default 0
    public $is_banned;
    public $is_vip;                         // indicates whether user is VIP
    public $is_originuser;
    public $completeness;    
    
    public $game_server_id;
    public $game_id;
    public $game_job;
    public $game_org;
    public $design_id;
    
    public $visited_num;
    public $token;
    
    public $recommend_words;
    
    /* Static get */
    function staticGet($k,$v=null)
    { return Memcached_DataObject::staticGet('Profile',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    

    function updateKeys(&$orig)
    {
        $parts = array();
        foreach (array('qq', 'email') as $k) {
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

    function getAvatar($width, $height=null)
    {
        if (is_null($height)) {
            $height = $width;
        }
        return Avatar::pkeyGet(array('user_id' => $this->id,
                                     'width' => $width,
                                     'height' => $height));
    }

    function getOriginalAvatar()
    {
        $avatar = DB_DataObject::factory('avatar');
        $avatar->user_id = $this->id;
        $avatar->original = true;
        if ($avatar->find(true)) {
            return $avatar;
        } else {
            return null;
        }
    }

    function setOriginal($filename)
    {
    	$subpath = Avatar::subpath($this->id);
    	
        $imagefile = new ImageFile($this->id, Avatar::path($filename, $subpath));
        
        $avatar = new Avatar();
        $avatar->user_id = $this->id;
        $avatar->width = $imagefile->width;
        $avatar->height = $imagefile->height;
        $avatar->mediatype = image_type_to_mime_type($imagefile->type);
        $avatar->filename = $filename;
        $avatar->original = true;
        $avatar->url = Avatar::url($filename, $subpath);
        $avatar->created = DB_DataObject_Cast::dateTime(); # current time

        # XXX: start a transaction here
        
        if (!$this->delete_avatars() || !$avatar->insert()) {
            @unlink(Avatar::path($filename, $subpath));
            return null;
        }

        foreach (array(AVATAR_PROFILE_SIZE, AVATAR_STREAM_SIZE, AVATAR_MINI_SIZE) as $size) {
            # We don't do a scaled one if original is our scaled size
            if (!($avatar->width == $size && $avatar->height == $size)) {
                $scaled_filename = $imagefile->resize($size);

                //$scaled = DB_DataObject::factory('avatar');
                $scaled = new Avatar();
                $scaled->user_id = $this->id;
                $scaled->width = $size;
                $scaled->height = $size;
                $scaled->original = false;
                $scaled->mediatype = image_type_to_mime_type($imagefile->type);
                $scaled->filename = $scaled_filename;
                $scaled->url = Avatar::url($scaled_filename, $subpath);
                $scaled->created = DB_DataObject_Cast::dateTime(); # current time

                if (!$scaled->insert()) {
                    return null;
                }
            }
        }

        return $avatar;
    }

    function delete_avatars($original=true)
    {
        $avatar = new Avatar();
        $avatar->user_id = $this->id;
        $avatar->find();
        while ($avatar->fetch()) {
            if ($avatar->original) {
                if ($original == false) {
                    continue;
                }
            }
            $avatar->delete();
        }
        return true;
    }
    
    function avatarUrl($size=AVATAR_PROFILE_SIZE)
    {
        $avatar = $this->getAvatar($size);
        if ($avatar) {
            return $avatar->displayUrl();
        } else {
            return Avatar::defaultImage($size, $this->id, $this->sex);
        }
    }

    function getBestName()
    {
        return ($this->nickname) ? $this->nickname : $this->uname;
    }
    
    // we can get user from profile since we don't plan share with other websites currently
    // so one user must corresponding to one profile
    function getUser()
    {
        return User::staticGet('id', $this->id);
    }

    # Get latest notice on or before date; default now, 是否屏蔽部落消息
    function getCurrentNotice($dt=null, $group=4)
    {
        $notice = new Notice();
        $notice->user_id = $this->id;
        if ($dt) {
            $notice->whereAdd('created < "' . $dt . '"');
        }
        
        //4, 屏蔽掉部落消息
    	if ($group) {
            $notice->whereAdd('topic_type != ' . $group);
        }
//        $notice->whereAdd('is_delete = 0');
        $notice->whereAdd('is_banned = 0');
        $notice->orderBy('created DESC, notice.id DESC');
        $notice->limit(1);
        if ($notice->find(true)) {
            return $notice;
        }
        return null;
    }
    
    /**
     * 取多条
     * @param $dt
     * @param $group
     */
	function getCurrentNotices($dt=null, $group=4, $limit = 4)
    {
        $notice = new Notice();
        $notice->user_id = $this->id;
        if ($dt) {
            $notice->whereAdd('created < "' . $dt . '"');
        }
        
        //4, 屏蔽掉部落消息
    	if ($group) {
            $notice->whereAdd('topic_type != ' . $group);
        }
//        $notice->whereAdd('is_delete = 0');
        $notice->whereAdd('is_banned = 0');
        $notice->orderBy('created DESC, notice.id DESC');
        $notice->limit($limit);
        if ($notice->find()) {
            return $notice;
        }
        return null;
    }
    
    // Get the user scroe
    //缓存, 可以加, 与subscriptionCount函数类似, 当然, 每个操作, 都要清楚此用户的该缓存
    function getUserScrore()
    {
    	return User_grade::getScore($this->id);
    }
    
	function getUserScoreDetail()
    {
    	$score = User_grade::getScore($this->id);
    	$gold = (int) ($score/1000);
    	$silver = (int) (($score - $gold * 1000) / 100);
    	$bronze = (int) (($score - $gold * 1000 - $silver * 100) / 10);
    	return array('score' => $score, 'gold' => $gold, 'silver' => $silver, 'bronze' => $bronze);
    }
    
    // Get the user grade
    function getUserGrade()
    {
    	return User_grade::getGrade($this->id);
    }
    
    // Get the user upgrade percentage
    function getUserUpgradePercent()
    {
    	return User_grade::getUpgradeInfo($this->id);
    }
    
    // Get joined groups
    /*Not use*/
    function getGroups($offset=0, $limit=null)
    {
        $qry =
          'SELECT user_group.* ' .
          'FROM user_group JOIN group_member '.
          'ON user_group.id = group_member.group_id ' .
          'WHERE group_member.user_id = %d ' .
          'AND user_group.validity = 1 ' . 
          'ORDER BY group_member.created DESC ';

        if ($offset || $limit) {
            if (common_config('db','type') == 'pgsql') {
                $qry .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
            } else {
                $qry .= ' LIMIT ' . $offset . ', ' . $limit;
            }
        }

        $groups = new User_group();

        $cnt = $groups->query(sprintf($qry, $this->id));

        return $groups;
    }

    function getSubscriptions($offset=0, $limit=null)
    {
        $qry =
          'SELECT profile.* ' .
          'FROM profile JOIN subscription ' .
          'ON profile.id = subscription.subscribed ' .
          'WHERE subscription.subscriber = %d ' .
          'AND subscription.subscribed != subscription.subscriber ' .
          'ORDER BY subscription.created DESC ';

        if ($limit) {
        	$qry .= ' LIMIT ' . $offset . ', ' . $limit;
        }

        $profile = new Profile();

        $profile->query(sprintf($qry, $this->id));

        return $profile;
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

    function getSubscribers($offset=0, $limit=null, $search='')
    {
        $qry =
          'SELECT profile.* ' .
          'FROM profile JOIN subscription ' .
          'ON profile.id = subscription.subscriber ' .
          'WHERE subscription.subscribed = ' . $this->id . ' ';    
        if($search != ''){
        	$qry .= 'AND (profile.uname like "%'. $search .'%" ';
        	$qry .= 'OR profile.nickname like "%' . $search . '%") ';
        }
        
        $qry .= 'AND subscription.subscribed != subscription.subscriber ' .
          'ORDER BY subscription.created DESC ';
        
        if ($offset || $limit) {
            if (common_config('db','type') == 'pgsql') {
                $qry .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
            } else {
                $qry .= ' LIMIT ' . $offset . ', ' . $limit;
            }
        }
        
        $profile = new Profile();

        $cnt = $profile->query($qry);

        return $profile;
    }    

    function subscriptionCount()
    {
        $c = common_memcache();

        if (!empty($c)) {
            $cnt = $c->get(common_cache_key('profile:subscription_count:'.$this->id));
            if (is_integer($cnt)) {
                return (int) $cnt;
            }
        }

        $sub = new Subscription();
        $sub->subscriber = $this->id;

        $cnt = (int) $sub->count('distinct subscribed');

        $cnt = ($cnt > 0) ? $cnt - 1 : $cnt;

        if (!empty($c)) {
            $c->set(common_cache_key('profile:subscription_count:'.$this->id), $cnt);
        }

//        common_debug("subscriptionCount == $cnt");
        return $cnt;
    }
    
    
    function blowSubscriptionCount()
    {
        $c = common_memcache();
        if (!empty($c)) {
            $c->delete(common_cache_key('profile:subscription_count:'.$this->id));
        }
    }
    
    function taggedSubscribersCount($tag)
    {
    	$tagId = User_tag::getTagid($this->id, $tag);
        $qry =
          'SELECT profile.id ' .
          'FROM profile JOIN subscription ' .
          'ON profile.id = subscription.subscriber ' .
          'JOIN tagtions ON (tagtions.tagged = subscription.subscriber ' .
          'AND tagtions.tagger = subscription.subscribed) ' .
          'WHERE subscription.subscribed = %d ' .
          'AND tagtions.tagid = %d ' .
          'AND subscription.subscribed != subscription.subscriber ' .
          'ORDER BY subscription.created DESC ';

        $profile = new Profile();

        $cnt = $profile->query(sprintf($qry, $this->id, $tagId));

        return $profile->N;
    }
    
	function unTaggedSubscriptionsCount()
    {
    	$taggedIds = Tagtions::getMyTaggedIds($this->id);
		
		$unTaggedIds = array();
		
		$subs = new Subscription();
		$subs->whereAdd('subscriber = ' . $this->id);
		$subs->find();
		
		$cnt = 0;
		
		while ($subs->fetch()) {
			// 排除掉已经被tag的人
			if (! in_array($subs->subscribed, $taggedIds)
				&& $subs->subscribed != $this->id) {
				$cnt ++;
			}
		}
		
		return $cnt;
    }

    function subscriberCount()
    {
        $c = common_memcache();
        if (!empty($c)) {
            $cnt = $c->get(common_cache_key('profile:subscriber_count:'.$this->id));
            if (is_integer($cnt)) {
                return (int) $cnt;
            }
        }

        $sub = new Subscription();
        $sub->subscribed = $this->id;

        $cnt = (int) $sub->count('distinct subscriber');

        $cnt = ($cnt > 0) ? $cnt - 1 : $cnt;

        if (!empty($c)) {
            $c->set(common_cache_key('profile:subscriber_count:'.$this->id), $cnt);
        }

        return $cnt;
    }
    
    function blowSubscriberCount()
    {
        $c = common_memcache();
        if (!empty($c)) {
            $c->delete(common_cache_key('profile:subscriber_count:'.$this->id));
        }
    }
    
    function taggedSubscriptionsCount($tag)
    {
        $tagId = User_tag::getTagid($this->id, $tag);
    	$qry =
          'SELECT profile.id ' .
          'FROM profile JOIN subscription ' .
          'ON profile.id = subscription.subscribed ' .
          'JOIN tagtions on (tagtions.tagged = subscription.subscribed ' .
          'AND tagtions.tagger = subscription.subscriber) ' .
          'WHERE subscription.subscriber = %d ' .
          'AND tagtions.tagid = %d ' .
          'AND subscription.subscribed != subscription.subscriber ' .
          'ORDER BY subscription.created DESC ';

        $profile = new Profile();

        $profile->query(sprintf($qry, $this->id, $tagId));

        return $profile->N;
    }
    
    function faveCount()
    {
        $c = common_memcache();
        if (!empty($c)) {
            $cnt = $c->get(common_cache_key('profile:fave_count:'.$this->id));
            if (is_integer($cnt)) {
                return (int) $cnt;
            }
        }

        $faves = new Fave();
        $faves->user_id = $this->id;
        $cnt = (int) $faves->count('distinct notice_id');

        if (!empty($c)) {
            $c->set(common_cache_key('profile:fave_count:'.$this->id), $cnt);
        }

//        common_debug("faveCount == $cnt");
        return $cnt;
    }


    function blowFaveCount()
    {
        $c = common_memcache();
        if (!empty($c)) {
            $c->delete(common_cache_key('profile:fave_count:'.$this->id));
        }
    }

    function noticeCount()
    {
        $c = common_memcache();

        if (!empty($c)) {
            $cnt = $c->get(common_cache_key('profile:notice_count:'.$this->id));
            if (is_integer($cnt)) {
                return (int) $cnt;
            }
        }
               
        $notices = new Notice();
        $notices->whereAdd('user_id = ' . $this->id);
        $notices->whereAdd('is_banned = 0');
        $notices->whereAdd('reply_only = 0');
        $notices->whereAdd('topic_type <> 4');
        $cnt = (int) $notices->count('id');

        if (!empty($c)) {
            $c->set(common_cache_key('profile:notice_count:'.$this->id), $cnt);
        }

        return $cnt;
    }
    
    function blowNoticeCount()
    {
        $c = common_memcache();
        if (!empty($c)) {
            $c->delete(common_cache_key('profile:notice_count:'.$this->id));
        }
    }
    
    function increaseVisitedNum() {
    	$orig = clone($this);
    	$this->visited_num ++;
    	return $this->update($orig);
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
    
    //很多用户的属性
    function getRecommendProfileToFollow($offset=0, $limit=10)
    {
    	// return popular people with interest tag
    	$user = common_current_user();
    	
    	// 系统已经分好类的兴趣不需做分词处理
    	$classifiedCategories = User_interest::getClassifiedCategoriesByUser($user->id);
    	
    	$userdefineInterests = User_interest::getSelfDefinedInterestByUser($user->id);
    	
    	// 目前的推荐执行全表查询，但这样肯定性能上有很大问题；
    	// 以后用户量提高了将改进算法采用只联合Top 1000或数千用户表进行推荐的办法。
    	// 如果用该方法无法找到至少一个推荐用户，采用当前算法进行查询
    	$user_interest = new User_interest();
    	$user_interest->selectAdd();
    	$user_interest->selectAdd('user_id');
    	if (count($classifiedCategories) > 0) {
    		$user_interest->whereAdd('category_id in (' . implode(",", $classifiedCategories) . ')');
    	}
        
        // Ask for an extra to see if there's more.
        if (count($userdefineInterests) > 0) {
			$search_engine = $user_interest->getSearchEngine('leshai_userinterest');
//        	$search_engine->set_sort_mode('chron');        	
        	$search_engine->query(implode(' ', $userdefineInterests));
        }

//    	} else {
//	    	for ($i = 0, $len = count($userdefineInterests); $i < $len; $i ++) {
//	    		$user_interest->whereAdd("interest like '%" . $userdefineInterests[$i] . "%'", "OR");
//	    	}
//    	}
    	$user_interest->find();
    	
    	// 先取出要排除的ids
    	$exceptions = $user->getProfile()->getSubscriptionids();
    	$exceptions[] = $user->id;
    	$exceptions[] = common_config('newuser', 'default_id');
    	
    	$userswiththesameinterest = array();
    	while ($user_interest->fetch()) {
    		if (in_array($user_interest->user_id, $exceptions)) {
    			continue;
    		}
    		$userswiththesameinterest[] = $user_interest->user_id;
    	}
    	
//    	$userswithtag = User_def_tag::getUsersWithTags($tags);
//    	common_debug($userswiththesameinterest);
    	
    	$profile = new Profile();	
    	$profile->whereAdd('id in (' . implode(", ", $userswiththesameinterest) . ')');
    	$profile->orderBy('followers desc');
    	
    	if (!is_null($offset)) {
			$profile->limit($offset, $limit);
		}
    	
    	$profile->find();
    	
    	return $profile;
    }
    
    function getPopularProfileToFollow($offset=0, $limit=10)
    {
    	// return people profile who has the most subscribers.
    	$profile = new Profile();
    	
    	// 注意考虑未登录用户访问的情况
    	$user = common_current_user();
    	if ($user) {
	    	$subs = $user->getProfile()->getSubscriptionids();
	    	$subs[] = $user->id;
	    	if (common_config('newuser', 'default_id')) {
	    		// 排除官方用户
	    		$subs[] = common_config('newuser', 'default_id');
	    	}
	    	$qryin = implode(", ", $subs);
	    	$profile->whereAdd('id not in (' . $qryin . ')');
    	}
    	$profile->orderBy('followers desc');
    	
    	if (!is_null($offset)) {
			$profile->limit($offset, $limit);
		}
    	
    	$profile->find();
    	
    	return $profile;
    	
    }
    
    function getCityProfileToFollow($province, $city, $offset=0, $limit=10)
    {
    	$profile = new Profile();
    	
    	// 注意考虑未登录用户访问的情况
    	$user = common_current_user();
    	if ($user) {
	    	$subs = $user->getProfile()->getSubscriptionids();
	    	$subs[] = $user->id;
	    	if (common_config('newuser', 'default_id')) {
	    		$subs[] = common_config('newuser', 'default_id');
	    	}
	    	$qryin = implode(", ", $subs);
	    	$profile->whereAdd("province = '" . $province. "'");
    	    $profile->whereAdd("city = '". $city. "'");
	    	$profile->whereAdd('id not in (' . $qryin . ')');
    	}
    	$profile->orderBy('followers desc');
    	
    	if (!is_null($offset)) {
			$profile->limit($offset, $limit);
		}
    	
    	$profile->find();
    	
    	return $profile;
    	
    }
    
    function getActiveProfileToFollow($offset=0, $limit=10)
    {
    	$gradeRecord = new Grade_record();
    	$qry = 'select user_id, changed from grade_record ORDER BY changed DESC LIMIT 0,'. $limit;
    	$gradeRecord->query($qry);
		$activeusers = array();
		while ($gradeRecord->fetch()) {
			$activeusers[] = $gradeRecord->user_id;
		}
		$gradeRecord->free();
		
		$qryin = implode(", ", $activeusers);
		
		$profile = new Profile();
		$profile->whereAdd('id in (' . $qryin . ')');
		$profile->orderBy('followers desc');
		
		$profile->find();
		return $profile;
    }
    
    function getLatestProfileToFollow($offset=0, $limit=5)
    {
    	$profile = new Profile();
    	$profile->orderBy('id desc');
    	$profile->limit($offset, $limit);
    	$profile->find();
    	return $profile;
    }
    
    function addFollowersCount($id)
    {
    	$profile = Profile::staticGet('id', $id);
    	$orig = clone($profile);
    	$profile->followers ++;
    	$profile->update($orig);
    }
    
    function subFollowersCount($id)
    {
    	$profile = Profile::staticGet('id', $id);
    	$orig = clone($profile);
    	$profile->followers --;
    	$profile->update($orig);
    }
    
    function getProfileByIds($ids, $offset = 0, $limit = 9999, $order = false) {
    	$profile = new Profile();
    	$profile->whereAdd('id in (' . implode(',', $ids) . ')');
    	if ($order) {
    		$profile->orderBy($order);
    	}
    	$profile->find();
    	$profile->limit($offset, $limit);
    	return $profile;
    }

	function getGameRecentRegisteredPeople($game_id, $offset = 0, $limit = 10) {
		$profile = new Profile();
		// 暂时不限制在游戏内
//		$profile->whereAdd('game_id = ' . $game_id);
		$profile->whereAdd('is_banned = 0');
		$profile->orderBy('created desc');
		$profile->limit($offset, $limit);
		
		$profile->find();
		
		return $profile;
	}
	
	function getRandom100($sex = '', $province = null, $city = null) {
    	
    	
    	$memcachedKey = 'profile:random100';
    	if ($sex != '') {
    		$memcachedKey .= ':' . $sex;
    	}
    	if ($province != null
    		&& $city != null) {
    		$memcachedKey .= ':' . hash(HASH_ALGO, $province . '-' . $city);
    	}
    	
    	return common_stream($memcachedKey,
    		array("Profile", "_getRandom100"), array($sex, $province, $city), 24 * 3600);
    }
    
	function _getRandom100($sex = '', $province = null, $city = null)
	{
		$profile = new Profile();
    	$profile->selectAdd();
    	$profile->selectAdd('id');
    	
    	if($sex == 'F' || $sex == 'M')
		$profile->whereAdd("sex = '". $sex ."'");
		if($province && $city )
		{	
			if($province == '北京' || $province == '天津' || $province == '上海' || $province == '重庆')
			{
				$profile->whereAdd("province = '". $province ."'");
			}
			else {
					$profile->whereAdd("province = '". $province ."'");
					$profile->whereAdd("city = '". $city ."'");
			}		
		}
		$profile->orderBy('followers DESC');
		$profile->LIMIT(0,100);
		$profile->find();
    	$pops = array();
    	while ($profile->fetch()) {
    		$pops[] = $profile->id;
    	}
    	$profile->free();
//    	$pops = common_random_fetch($pops,100);
    	
    	return $pops;
	}
	
	function getRecentRegisteredPeople($offset = 0, $limit = 10) {
		$profile = new Profile();
//		$avatar = new Avatar();
//		$profile->joinAdd($avatar);
//		$profile->selectAdd();
//		$profile->selectAdd('profile.nickname as nickname');
//		$profile->selectAdd('profile.profileurl as profileurl');
//		$profile->selectAdd('profile.game_id as game_id');
//		$profile->selectAdd('profile.game_server_id as game_server_id');
//		$profile->selectAdd('profile.created as created');
//		$profile->selectAdd('profile.sex as sex');
//		$profile->selectAdd('avatar.url as avatarurl');
//		$profile->whereAdd('avatar.width=48 and avatar.height=48');
		$profile->whereAdd('is_banned = 0');
		$profile->orderBy('created desc');
		$profile->limit($offset, $limit);
		
		$profile->find();
		
		return $profile;
	}
	
	function getMostvisitUsers($limit=10, $area='all',$neededid=null)
	{
		
		$cur = time();
		$dt = strftime('%Y-%m-%d', $cur);
		//当天0:0:0的总秒数
		$today = strtotime($dt);

		$someday = $today - 3600*48;
		$time = date('Y-m-d H:i:s', $someday);
		
		$profile = new Profile();
    	$qry = 'select id, visited_num from profile where completeness > 80 and is_vip = 0 and email not like "%@lshai.com" and id != '.
    	    common_config('newuser', 'default_id');
    	$qry .=' and id IN (select distinct user_id from notice where created > "'.$time.'" )';
    	if($neededid)
    	{
    		if($area == 'game')
       			$qry .= ' and game_id = '.$neededid;
        	else if($area == 'gameserver')
        		$qry .= ' and game_server_id = '.$neededid;
       	}
        
    	$qry .=' ORDER BY visited_num DESC LIMIT 0,'. $limit;
    	$profile->query($qry);
		$mostvisitusers = array();
		while ($profile->fetch()) {
			$mostvisitusers[] = array('user_id'=>$profile->id,'visited_num'=>$profile->visited_num);
		}
		$profile->free();
		return $mostvisitusers;	
	}
	
	function getPeopleWantednum($sex = false, $agebegin = false, $ageend = false, $location = false, $game_id = false, $game_big_zone_id = false, $game_server_id = false)
	{
		$profile = new Profile();
		$profile->selectAdd('count(id) as num');
		if ($location) {
			$profile->whereAdd("location like '%" . $location . "%'");
		}
		if ($sex) {
			$profile->whereAdd("sex = '" . $sex . "'");
		}
		
		if ($agebegin && $ageend && ($agebegin < $ageend)) {
			$yearsec = 365*24*3600;
			$cur = time();
			$profile->whereAdd("birthday < '"  . strftime('%Y-%m-%d', $cur - $agebegin * $yearsec) . "'");
			$profile->whereAdd("birthday > '"  . strftime('%Y-%m-%d', $cur - $ageend * $yearsec) . "'");
		}
		
		//注：服务器id格式为 xxxyyzzz, 从左至右分别为三位游戏码, 2位大区码, 3位服务器码
		if ($game_id) {
			$profile->whereAdd("game_id = " . $game_id);
		}
		if ($game_big_zone_id) {
			$profile->whereAdd("game_server_id DIV 1000 = " . $game_big_zone_id);
		}			
		if ($game_server_id) {
			$profile->whereAdd("game_server_id = " . $game_server_id);
		}
		
		$user = common_current_user();
		if($user) {
			$profile->whereAdd("id != " . $user->id);
		}
		
		return $profile->count();
	}
	
	function getPeopleWanted($sex = false, $agebegin = false, $ageend = false, $location = false, $game_id = false, $game_big_zone_id = false, $game_server_id = false,
							$offset = 0, $limit = false)
	{
		$profile = new Profile();

		if ($location) {
			$profile->whereAdd("location like '%" . $location . "%'");
		}
		if ($sex) {
			$profile->whereAdd("sex = '" . $sex . "'");
		}
		if ($agebegin && $ageend && ($agebegin < $ageend)) {
			$yearsec = 365*24*3600;
			$cur = time();
			$profile->whereAdd("birthday < '"  . strftime('%Y-%m-%d', $cur - $agebegin * $yearsec) . "'");
			$profile->whereAdd("birthday > '"  . strftime('%Y-%m-%d', $cur - $ageend * $yearsec) . "'");
		}
		
		//注：服务器id格式为 xxxyyzzz, 从左至右分别为三位游戏码, 2位大区码, 3位服务器码
		if ($game_id) {
			$profile->whereAdd("game_id = " . $game_id);
		}		
		if ($game_big_zone_id) {
			$profile->whereAdd("game_server_id DIV 1000 = " . $game_big_zone_id);
		}	
		if ($game_server_id) {
			$profile->whereAdd("game_server_id = " . $game_server_id);
		}
		if ($limit) {
			$profile->limit($offset, $limit);
		}
		
		$user = common_current_user();
		if($user) {
			$profile->whereAdd("id != " . $user->id);
		}
		
		$profile->find();
		$ids = array();
		while ($profile->fetch()) {
	        $ids[] = $profile->id;
		}
		return $ids;
	}
	
	function getGameString() {
		$game = Game::staticGet('id', $this->game_id);
		$game_server = Game_server::staticGet('id', $this->game_server_id);
		
		return $game->name . ' - ' . $game_server->name;
	}
	
	static function getVipIds($limit = false, $game_id = null) {
		return common_stream('profile:vipids:' . ($limit ? $limit : 0) . ':' . ($game_id ? $game_id : 0), array("Profile", "_getVipIds"), array($limit, $game_id), 3600);
	}
	
	static function _getVipIds($limit, $game_id) {
		$profile = new Profile();
		$profile->whereAdd('is_vip = 1');
		if ($game_id) {
			$profile->whereAdd('game_id = ' . $game_id);
		}
		$profile->orderBy('created desc');
		if ($limit) {
			$profile->limit(0, $limit);
		}
		
		$profile->find();
		
		$ids = array();
		while ($profile->fetch()) {
			$ids[] = $profile->id;
		}
		return $ids;
	}
	
	static function getLatestNoticeAuthorIds($limit) {
		return common_stream('profile:latestnoticeauthorids:' . $limit, array("Profile", "_getLatestNoticeAuthorIds"), array($limit), 120);
	}
	
	static function _getLatestNoticeAuthorIds($limit) {
		$notice = new Notice();
		$notice->whereAdd('distinct user_id');
		$notice->whereAdd('is_banned = 0');
		$notice->orderBy('id desc');
		$notice->limit(0, $limit);
		$notice->find();
		
		$authors = array();
		
		while ($notice->fetch()) {
			$authors[] = $notice->user_id;
		}
		
		return array_unique($authors);
	}
	
	static function getByUNameAndPassword($uname, $password) {
		if (mb_strlen($password) == 0) {
	        return false;
	    }
	    
	    if (preg_match('/^[0-9]+$/', $uname)) {
	    	// use id for login
	    	$profile = Profile::staticGet('qq', $uname);
	    } else if (preg_match('/^.*@.*$/', $uname)) {
	    	// use email for login
	    	$profile = Profile::staticGet('email', $uname);
	    } else {
	    	$profile = Profile::staticGet('uname', $uname);
	    }
	    
	    if (is_null($profile) || $profile === false) {
	        return false;
	    } else {
	    	return $profile;
	    }
	}
	
	static function existEmail($email) {
		$email = strtolower($email);
	    if (!$email || strlen($email) == 0) {
	        return false;
	    }
	    $profile = Profile::staticGet('email', $email);
	    return ($profile !== false);
	}
	
	static function location($province, $city, $district) {
	    $location = null;
		if ($province) {
	       $location = $province;
		   if ($city != $province) {
		       $location = $location . $city;
		   }
		   if ($district != $city) {
		       $location = $location . $district;
		   }
	    }
	    return $location;
	}
	
	static function displayName($sex, $is_own) {
		if ($is_own) {
			return '我';
		} else {
			return $sex == 'M' ? '他' : '她';
		}
	}

}
