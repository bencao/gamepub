<?php
/**
 * Table Definition for user_group
 */

class User_group extends Memcached_DataObject
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'user_group';                      // table name
    public $id;                              // int(4)  primary_key not_null
    public $uname;                        // varchar(64)  unique_key
    public $nickname;                        // varchar(255)
    public $homepage;                        // varchar(255)
    public $description;                     // varchar(140)
    public $location;                        // varchar(255)
    public $category ;                       // varchar(8)
    public $catalog;                         // varchar(8)
    public $original_logo;                   // varchar(255)
    public $homepage_logo;                   // varchar(255)
    public $stream_logo;                     // varchar(255)
    public $mini_logo;                       // varchar(255)
    public $design_id;                       // int(4)
    public $created;                         // datetime()   not_null
    public $modified;                        // timestamp()   not_null default_CURRENT_TIMESTAMP
    public $grouptype;                       // bool
    public $ownerid;                         // group owner's user id
    public $isadvanced;                      // bool
    public $closed;                          // bool
    public $heat;                            // int(11)  not_null
    public $validity;                        // int(1)  not_null
    public $groupclass;                      // int(1)  not_null
    public $game_id;                         // int(11)  
    public $game_server_id;                  // int(11)  
    public $post;							 // text
    public $backmusic;						 // varchar(255)
    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('User_group',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function defaultLogo($size)
    {
        static $sizenames = array(GROUP_LOGO_PROFILE_SIZE => 'profile',
                                  GROUP_LOGO_STREAM_SIZE => 'stream',
                                  GROUP_LOGO_MINI_SIZE => 'mini');
        return theme_path('g-0-'.$sizenames[$size].'.jpg', 'default');
    }

    function homeUrl()
    {
        return common_path('group/' . $this->id);
    }

    function permalink()
    {
        return common_path('group/' . $this->id);
    }
    
	function getNotices($offset=0, $limit=NOTICES_PER_PAGE, $content_type=0, $first_tag=0)
    {
        if($content_type && ! $first_tag) {
        	//缓存内容分类的消息
        	$ids = Notice::stream(array($this, '_streamDirect'), 
        						array($first_tag),
                              	'user_group:notice_ids:' . $this->id . ':' . $content_type,
                              	$offset, $limit, 0, 0, null, $content_type);        	
        } else if(! $content_type && $first_tag) {
        	//缓存话题分类的消息
        	$ids = Notice::stream(array($this, '_streamDirect'), 
        						array($first_tag),
                              	'user_group:notice_ids:' . $this->id . ':' . $first_tag,
                              	$offset, $limit);
        } else if(! $content_type && ! $first_tag) {    
        	//缓存无分类的消息   
        	$ids = Notice::stream(array($this, '_streamDirect'), 
        						array($first_tag),
                              	'user_group:notice_ids:' . $this->id,
                              	$offset, $limit);
                      
        } else {
        	//直接读取
        	$ids = $this->_streamDirect($first_tag, $offset, $limit, 0, 0, null, $content_type);
        }

        return Notice::getStreamByIds($ids);
   }

    function _streamDirect($first_tag, $offset, $limit, $since_id,
						$max_id, $since, $content_type=0, $area_type=0, $topic_type=0)
    {
    	$qry = "SELECT group_inbox.notice_id ".
    	       "FROM notice JOIN group_inbox ON notice.id = group_inbox.notice_id ".
    	       "WHERE group_inbox.group_id = ". $this->id.
    	       " AND notice.is_banned = 0 ";
    	if ($content_type > 0)
        	$qry .= ' AND notice.content_type = '. $content_type;
        if ($first_tag > 0) {
        	//$qry .= ' AND EXISTS (SELECT * from notice_tag where notice.id=notice_tag.notice_id ' .
        	//	'and notice_tag.second_tag_id in (select id from second_tag where first_tag_id%10=' . ($first_tag%10) . '))';
        	
        	//现在一级话题都采用999x, 二级话题都是999xzz
        	$qry .= ' AND notice_id IN (SELECT notice_tag.notice_id from notice_tag WHERE ' .
        		'notice_tag.second_tag_id DIV 100=' . $first_tag . ')'; 
        } 
        
    	if ($since_id != 0) {
            $qry .= ' and notice_id > ' . $since_id;
        }

        if ($max_id != 0) {
            $qry .= ' and notice_id <= ' . $max_id;
        }
        	
        //$qry .= ' ORDER BY group_inbox.notice_id DESC';
        $qry .= ' ORDER BY notice.modified DESC';
        
        if ($offset) {
            if (common_config('db','type') == 'pgsql') {
                $qry .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
            } else {
                $qry .= ' LIMIT ' . $offset . ', ' . $limit;
            }
        }
        $inbox = new Group_inbox();
        $inbox->query($qry);

        $ids = array();
        while ($inbox->fetch()) {
            $ids[] = $inbox->notice_id;
        }

        return $ids;
    }

    function alloweduname($uname)
    {
        static $blacklist = array('new');
        return !in_array($uname, $blacklist);
    }

    function getMembers($offset=0, $limit=null)
    {
        $qry =
          'SELECT profile.* ' .
          'FROM profile JOIN group_member '.
          'ON profile.id = group_member.user_id ' .
          'WHERE group_member.group_id = %d ' .
          'ORDER BY group_member.created DESC ';

        if ($limit != null) {
            if (common_config('db','type') == 'pgsql') {
                $qry .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
            } else {
                $qry .= ' LIMIT ' . $offset . ', ' . $limit;
            }
        }

        $members = new Profile();

        $members->query(sprintf($qry, $this->id));
        return $members;
    }
    
    function getLatestMember()
    {
        $qry =
          'SELECT profile.* ' .
          'FROM profile JOIN group_member '.
          'ON profile.id = group_member.user_id ' .
          'WHERE group_member.group_id = %d ' .
          'ORDER BY group_member.created DESC ';

        if (common_config('db','type') == 'pgsql') {
            $qry .= ' LIMIT ' . 0 . ' OFFSET ' . 1;
        } else {
            $qry .= ' LIMIT ' . 0 . ', ' . 1;
        }

        $member = new Profile();

        $member->query(sprintf($qry, $this->id));
        return $member;
    }
    
    function memberCount() {

        $gm = new Group_member();
        $gm->group_id = $this->id;
        $cnt = (int) $gm->count('distinct user_id');

        return $cnt;
    }
    
    function blowMemeberCount() {
    	$c = common_memcache();
        if (!empty($c)) {
            $c->delete(common_cache_key('group:member_count:'.$this->id));
        }
    }

    function getAdmins($offset=0, $limit=null)
    {
        $qry =
          'SELECT profile.* ' .
          'FROM profile JOIN group_member '.
          'ON profile.id = group_member.user_id ' .
          'WHERE group_member.group_id = %d ' .
          'AND group_member.is_admin = 1 ' .
          'ORDER BY group_member.modified ASC ';

        if ($limit != null) {
            if (common_config('db','type') == 'pgsql') {
                $qry .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
            } else {
                $qry .= ' LIMIT ' . $offset . ', ' . $limit;
            }
        }

        $admins = new Profile();

        $admins->query(sprintf($qry, $this->id));
        return $admins;
    }

    function getBlocked($offset=0, $limit=null)
    {
        $qry =
          'SELECT profile.* ' .
          'FROM profile JOIN group_block '.
          'ON profile.id = group_block.blocked ' .
          'WHERE group_block.group_id = %d ' .
          'ORDER BY group_block.modified DESC ';

        if ($limit != null) {
            if (common_config('db','type') == 'pgsql') {
                $qry .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
            } else {
                $qry .= ' LIMIT ' . $offset . ', ' . $limit;
            }
        }

        $blocked = new Profile();

        $blocked->query(sprintf($qry, $this->id));
        return $blocked;
    }
    
    function getBlockedCount() {

        $gb = new Group_block();
        $gb->group_id = $this->id;
        $cnt = (int) $gb->count('distinct blocked');

        return $cnt;
    }
    
    function getApplicants($offset=0, $limit=null)
    {
    	$qry = 
    	    "select * from group_application where group_application.groupid = %d";
        if ($limit != null) {
            if (common_config('db','type') == 'pgsql') {
                $qry .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
            } else {
                $qry .= ' LIMIT ' . $offset . ', ' . $limit;
            }
        }
        
    	$groupapp = new Group_application();
    	$groupapp->query(sprintf($qry, $this->id));
    	return $groupapp;
    }
    
	function getApplicantsCount()
    {
    	return Group_application::getApplyNum($this->id);
    }

    function getInvites($offset=0, $limit=null)
    {
    	$qry = 
    	    "select * from user where id in (select inviteeid from group_invitation where group_invitation.groupid = %d)";
        if ($limit != null) {
            if (common_config('db','type') == 'pgsql') {
                $qry .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
            } else {
                $qry .= ' LIMIT ' . $offset . ', ' . $limit;
            }
        }
        
    	$groupinv = new User();
    	$groupinv->query(sprintf($qry, $this->id));
//    	$applicants = array();
//    	while($groupapp->fetch()) {
//    		$applicants[] = clone($groupapp);
//    	}
//    	$groupapp->free();
    	return $groupinv;
    }
    
    // This method is used when removing a group
    function removeBlocked()
    {


        $block = new Group_block();

        $block->group_id = $this->id;

        if ($block->find()) {
            while ($block->fetch()) {
                $block->delete();
            }
        }
        $block->free();
    }   

    // This method is used when removing a group
    function removeDesign()
    {


        $design = new Group_self_design();

        $design->group_id = $this->id;

        if ($design->find()) {
            while ($design->fetch()) {
            	$design->removeDesign();
                $design->delete();
            }
        }
        $design->free();
    }
    
    // This method is used when removing a group
    function removeIvcode()
    {

        $ivcode = new Group_ivcode();

        $ivcode->groupid = $this->id;

        if ($ivcode->find()) {
            while ($ivcode->fetch()) {
                $ivcode->delete();
            }
        }
        $ivcode->free();
    }
    
    // This method is used when removing a group
    function removeJoinApp()
    {

        $gapp = new Group_application();

        $gapp->groupid = $this->id;

        if ($gapp->find()) {
            while ($gapp->fetch()) {
                $gapp->delete();
            }
        }
        $gapp->free();
    }

    function setOriginal($filename)
    {
        $imagefile = new ImageFile($this->id, Avatar::path($filename, Avatar::groupsubpath($this->id)));
        $orig = clone($this);
        $this->original_logo = Avatar::url($filename, Avatar::groupsubpath($this->id));
        $this->homepage_logo = Avatar::url($imagefile->resize(GROUP_LOGO_PROFILE_SIZE, 0, 0, null, null, 1), Avatar::groupsubpath($this->id));
        $this->stream_logo = Avatar::url($imagefile->resize(GROUP_LOGO_STREAM_SIZE, 0, 0, null, null, 1), Avatar::groupsubpath($this->id));
        $this->mini_logo = Avatar::url($imagefile->resize(GROUP_LOGO_MINI_SIZE, 0, 0, null, null, 1), Avatar::groupsubpath($this->id));

        return $this->update($orig);
    }

    function getBestName()
    {
        return ($this->nickname) ? $this->nickname : $this->uname;
    }

    static function getForuname($uname)
    {
        $uname = strtolower($uname);
        $group = User_group::staticGet('uname', $uname);
        if (!empty($group)) {
            return $group;
        }
        // We don't use group alias to find a group currently
//        $alias = Group_alias::staticGet('alias', $uname);
//        if (!empty($alias)) {
//            return User_group::staticGet('id', $alias->group_id);
//        }
        return null;
    }

    function getDesign()
    {
        return Design::staticGet('id', $this->design_id);
    }

    function getUserMembers()
    {

        $user = new User();

        $qry =
          'SELECT id ' .
          'FROM user JOIN group_member '.
          'ON user.id = group_member.user_id ' .
          'WHERE group_member.group_id = %d ';

        $user->query(sprintf($qry, $this->id));

        $ids = array();

        while ($user->fetch()) {
            $ids[] = $user->id;
        }

        $user->free();

        return $ids;
    }
    
    function getGroupIvid() {
    	return Group_ivcode::staticGet('groupid', $this->id)->code;
    }
    
    function hasBlocked($user) {
    	return Group_block::isBlocked($this, $user);
    }
    
    function hasMember($user) {
    	$mem = new Group_member();

        $mem->group_id = $this->id;
        $mem->user_id = $user->id;

        if ($mem->find()) {
            return true;
        } else {
            return false;
        }
    }
    
    function hasApplicationFor($user) {
    	$qry = 
    	    "select count(*) as appnum from group_application where groupid = %d and inviteeid = %d";
    	
    	$groupApp = new Group_application();
    	$groupApp->query(sprintf($qry, $this->id, $user->id));
    	$groupApp->fetch();
    	$appnum = intval($groupApp->appnum);
    	$groupApp->free();
    	
    	if ($appnum>0) {
    		return true;
    	}else {
    		return false;
    	}
    }
    
    function saveNewApplication($user, $message) {
    	$groupApp = new Group_application();

        $groupApp->groupid   = $this->id;
        $groupApp->inviteeid = $user->id;
        $groupApp->message   = $message;

        $result = $groupApp->insert();
        
        if (! $result) {
            common_log_db_error($groupApp, 'INSERT', __FILE__);
            return false;
        }
        
        return true;
    }
    
    function hasAdmin($user) {
    	$mem = new Group_member();

        $mem->group_id = $this->id;
        $mem->user_id = $user->id;
        $mem->is_admin = 1;

        if ($mem->find()) {
            return true;
        } else {
            return false;
        }
    }
    
    function toggleAdmin($user, $isAdmin = true) {
    	$member = Group_member::pkeyGet(array('group_id' => $this->id,
                                              'user_id' => $user->id));

        $orig = clone($member);

        $member->is_admin = $isAdmin ? 1 : 0;
        
        $result = $member->update($orig);
        
        if (!$result) {
            common_log_db_error($member, 'UPDATE', __FILE__);
            return false;
        }
        
        return true;
    }
    
    function addMember($user, $isAdmin = false) {
    	$member = new Group_member();

        $member->group_id   = $this->id;
        $member->user_id = $user->id;
        if ($isAdmin) {
        	$member->is_admin = 1;
        }
        $member->created    = common_sql_now();

        $result = $member->insert();

        if (!$result) {
            common_log_db_error($member, 'INSERT', __FILE__);
        	return false;
        }
        
        $this->addGroupHeat(2);
        return true;
    }
    
    function saveNew($fields) {
    	extract($fields);
    	
    	$group = new User_group();

        $group->uname    		= $uname;
        $group->nickname    	= $nickname;
        $group->description 	= $description;
        $group->location    	= $location;
        $group->category    	= $category;
        $group->catalog     	= $catalog;
        $group->game_id			= $game_id;
        $group->game_server_id	= $game_server_id;
        $group->created     	= common_sql_now();
        $group->grouptype   	= $grouptype;
        $group->ownerid     	= $ownerid;
        $group->isadvanced  	= $isadvanced;
        $group->validity		= $validity;
        $group->groupclass		= $groupclass;

        $result = $group->insert();

        if (!$result) {
            common_log_db_error($group, 'INSERT', __FILE__);
            return false;
        }
        
        $result = Group_ivcode::saveNew($group->id);
        
        // create group folder for files, e.g. logo
        $subpath = Avatar::groupsubpath($group->id);
        $path = Avatar::filepath($subpath);
        Imagefile::mkdirs($path);
        
        return $group;
    }
    
    function isOwnedBy($user) {
    	return $this->ownerid == $user->id;
    }
    
    function getOwner() {
    	return User::staticGet($this->ownerid);
    }
    
    static function fromInvitation($group_id, $invitee) {
    	$group = self::staticGet($group_id);
    	Subscription::subscribeTo($invitee, $group->getOwner(), false);
    	User_grade::addScore($group->ownerid, 12);
    	Group_ad_member::saveNew($group_id, $invitee->id);
    	return $group->addMember($invitee);
    }
    
	function applyDesign($design_id) {
    	$orig = clone($this);
    	$this->design_id = $design_id;
    	$this->update($orig);
    	return true;
    }
    
    //add by xiangyun
    static function addHeat($gid, $heat)
    {
    	$group = self::staticGet($gid);
    	$orig = clone($group);
    	$group->heat += $heat;
        $result = $group->update($orig);
	    if (!$result) {
	        common_log_db_error($group, 'UPDATE', __FILE__);
	        return false;
	    }
	    
    	return true;
    }
    
	function addGroupHeat($heat)
    {
    	$orig = clone($this);
    	$this->heat += $heat;
        $result = $this->update($orig);
	    if (!$result) {
	        common_log_db_error($group, 'UPDATE', __FILE__);
	        return false;
	    }
	    
    	return true;
    }
    
    //add by xiangyun
    function setValidity($validity)
    {    	
    	$orig = clone($this);
    	$this->validity = $validity;
        $result = $this->update($orig);
	    if (!$result) {
	        common_log_db_error($this, 'UPDATE', __FILE__);
	        return false;
	    }	    
    	return true;
    }
    
    function updatePost($post='')
    { 	
    	$orig = clone($this);
    	$this->post = $post;
        $result = $this->update($orig);
	    if (!$result) {
	        common_log_db_error($this, 'UPDATE', __FILE__);
	        return false;
	    }	    
    	return true;
    }
	
	function getGame()
	{
		return Game::staticGet('id', $this->game_id);
	}
	
	function getGameServer()
	{
		return Game_server::staticGet('id', $this->game_server_id);
	}
    
	function getAuditNumber()
	{
		$group_invitaion = new Group_invitation();
		$group_invitaion->groupid = $this->id;
		return $group_invitaion->count('*');
	}
	
    function clearDesign() {
    	$orig = clone($this);
    	$this->design_id = 0;
    	$this->update($orig);
    	
    	return true;
    }
	
	function getRank()
	{
		//only top 30 for game groups
		$groups = self::_getGameGroups(0, 30, $this->game_id);
		$i = 0;
		if(in_array($this->id, $groups)){
			foreach( $groups as $gid){
   				$i++;
   				if($gid == $this->id){
   					return $i;
   				}
			}			
		}
		return 0;
	}
    
	function blowGameGroupsCache()
	{
		$cache = common_memcache();
		if ($cache) {
			//blow last page because it is ordered by heat
			$last_offset = self::getGameGroupsByGameCount($this->game_id) / GROUPS_PER_PAGE_GAME * GROUPS_PER_PAGE_GAME;
			$cache->delete(common_cache_key('group:gamegroupsbygame:'. $this->game_id . ':' . $last_offset));
			$cache->delete(common_cache_key('group:gamegroupsbygamecount:'. $this->game_id));
			$last_offset = self::getGameGroupsByServerCount($this->game_server_id) / GROUPS_PER_PAGE_GAME * GROUPS_PER_PAGE_GAME;
			$cache->delete(common_cache_key('group:gamegroupsbyserver:' . $this->game_server_id . $last_offset));
			$cache->delete(common_cache_key('group:gamegroupsbyservercount:' . $this->game_server_id));
		}
	}	
	
	function blowLifeGroupsCache()
	{
		$cache = common_memcache();
		if ($cache) {
			$last_offset = self::getLifeGroupsCount() / GROUPS_PER_PAGE * GROUPS_PER_PAGE;
			//blow last page because it is ordered by heat
			$cache->delete(common_cache_key('group:lifegroups:' . $last_offset));
			$cache->delete(common_cache_key('group:lifegroupscount:'));
		}
	}
	
	//added on 2010-10-1
	function destroy()
	{
		$this->query('BEGIN');
		
		$member = new Group_member();
        $member->group_id = $this->id;
		$member->delete();
		$this->removeBlocked();
        $this->removeIvcode();
        $this->removeJoinApp();
        $this->removeDesign();
        $result = $this->delete();
        
        if (!$result) {
        	$this->query('ROLLBACK');
        	return false;
        }
        
        $this->query('COMMIT');
        return true;
	}

    static function getAllAuditGroups()
    {
    	$qry =
          'SELECT user_group.* ' .
    	  'FROM user_group ' .
    	  'WHERE user_group.validity = 0';

        $groups = new User_group();

        $cnt = $groups->query($qry);

        return $groups;    	
    }
	
	static function _getGroupsByIds($ids)
	{
		$groups = new User_group();
		$groups->whereAdd('id in (' . implode(',',$ids). ')');
		$groups->orderBy('heat desc');
		$groups->find();
		return $groups;
	}
	
	static function _getLifeGroups($offset, $limit)
    {
    	$group = new User_group();
    	$group->selectAdd();
    	$group->selectAdd('id');
    	$group->whereAdd('groupclass = 0');
    	$group->whereAdd('validity = 1');
    	$group->orderBy('heat DESC');
    	$group->limit($offset, $limit);
    	$group->find();
    	
    	$groups = array();

    	while ($group->fetch()) {
    		$groups[] = $group->id;
    	}
    	
    	$group->free();
    	return $groups;
    }
	
	static function _getLifeGroupsCount()
	{
    	$group = new User_group();
    	$group->whereAdd('groupclass = 0');
    	$group->whereAdd('validity = 1');
    	return $group->count();
	}
    
	static function _getGameGroups($offset, $limit, $gameid=0, $serverid=0)
	{
		$group = new User_group();
		if ($gameid != 0) {
			$group->whereAdd('game_id = '. $gameid);
		}
		if ($serverid != 0) {
			$group->whereAdd('game_server_id = '. $serverid);
		}
		$group->selectAdd();
    	$group->selectAdd('id');
    	$group->whereAdd('groupclass = 1');
    	$group->whereAdd('validity = 1');
    	$group->orderBy('heat DESC');
    	$group->limit($offset, $limit);
    	$group->find();
    	
    	$groups = array();

    	while ($group->fetch()) {
    		$groups[] = $group->id;
    	}

    	$group->free();
        return $groups;
	}
	
	static function _getGameGroupsCount($gameid=0, $serverid=0)
	{
		$group = new User_group();
		if ($gameid != 0) {
			$group->whereAdd('game_id = '. $gameid);
		}
		if ($serverid != 0) {
			$group->whereAdd('game_server_id = '. $serverid);
		}
    	$group->whereAdd('groupclass = 1');
    	$group->whereAdd('validity = 1');
    	return $group->count();
	}

	static function getLifeGroups($offset=0, $limit=GROUPS_PER_PAGE)
	{
		$ids = common_stream('group:lifegroups:' . $offset, array("User_group", "_getLifeGroups"), array($offset, $limit), 3600);
		return self::_getGroupsByIds($ids);
	}
	
	static function getLifeGroupsCount()
	{
        return common_stream('group:lifegroupscount', array("User_group", "_getLifeGroupsCount"), null, 3600);
	}
	
	static function getGameGroupsHottest($offset=0, $limit=GROUPS_HOTTEST)
	{
		//从18个中随机取出9个热门游戏公会,缓存没有意义
		$top_ids = self::_getGameGroups($offset, GROUPS_PER_PAGE);
        return self::_getGroupsByIds(common_random_fetch($top_ids, $limit));
	}
	
	static function getGameGroupsByGameHottest($gameid, $offset=0, $limit=GROUPS_HOTTEST)
	{
		//某个游戏最热门的游戏公会
		$top_ids = self::_getGameGroups($offset, $limit, $gameid);
        return self::_getGroupsByIds($top_ids);
	}
	
	static function getGameGroupsByGame($gameid, $offset=0, $limit=GROUPS_PER_PAGE_GAME)
	{
		$ids = common_stream('group:gamegroupsbygame:' . $gameid . ':' . $offset, array("User_group", "_getGameGroups"), array($offset, $limit, $gameid, 0), 3600);
    	return self::_getGroupsByIds($ids);
	}
	
	static function getGameGroupsByGameCount($gameid)
	{
        return common_stream('group:gamegroupsbygamecount:'. $gameid, array("User_group", "_getGameGroupsCount"), array($gameid, 0), 3600);
	}
	
	static function getGameGroupsByServer($serverid, $offset=0, $limit=GROUPS_PER_PAGE_GAME)
	{
		$ids = common_stream('group:gamegroupsbyserver:' . $serverid . ':' . $offset, array("User_group", "_getGameGroups"), array($offset, $limit, 0, $serverid), 3600);
    	return self::_getGroupsByIds($ids);
	}

	static function getGameGroupsByServerCount($serverid)
	{
        return common_stream('group:gamegroupsbyservercount:' . $serverid, array("User_group", "_getGameGroupsCount"), array(0, $serverid), 3600);
	}
    
    static function getAuditGroups($userid, $offset=0, $limit=null)
    {
    	$qry =
          'SELECT user_group.* ' .
    	  'FROM user_group ' .
    	  'WHERE user_group.validity = 0 '.
    	  'AND (user_group.id in ( SELECT groupid FROM group_invitation WHERE inviteeid = %d) '.
    	  'OR user_group.id in (SELECT group_id FROM group_member WHERE user_id = %d))';

        if ($offset || $limit) {
            if (common_config('db','type') == 'pgsql') {
                $qry .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
            } else {
                $qry .= ' LIMIT ' . $offset . ', ' . $limit;
            }
        }

        $groups = new User_group();

        $cnt = $groups->query(sprintf($qry, $userid, $userid));

        return $groups;    	
    }
    
    static function getApplyGroups($user_id, $offset=0, $limit=null)
    {
    	$qry =
          'SELECT user_group.* ' .
          'FROM user_group JOIN group_application '.
          'ON user_group.id = group_application.groupid ' .
          'WHERE group_application.inviteeid = %d ' .
          'AND user_group.validity = 1 ' .
          'ORDER BY user_group.created DESC ';

        if ($offset || $limit) {
            if (common_config('db','type') == 'pgsql') {
                $qry .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
            } else {
                $qry .= ' LIMIT ' . $offset . ', ' . $limit;
            }
        }

        $groups = new User_group();

        $cnt = $groups->query(sprintf($qry, $user_id));

        return $groups;     	
    }
    
    //get Join Game groups
	static function getUserGameGroups($user_id, $offset=0, $limit=null)
    {
        $qry =
          'SELECT user_group.* ' .
          'FROM user_group JOIN group_member '.
          'ON user_group.id = group_member.group_id ' .
          'WHERE group_member.user_id = %d ' .
          'AND user_group.groupclass = 1 ' .
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

        $cnt = $groups->query(sprintf($qry, $user_id));

        return $groups;
    }
    
	static function getUserLifeGroups($user_id, $offset=0, $limit=null)
    {
        $qry =
          'SELECT user_group.* ' .
          'FROM user_group JOIN group_member '.
          'ON user_group.id = group_member.group_id ' .
          'WHERE group_member.user_id = %d ' .
          'AND user_group.groupclass = 0 ' .
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

        $cnt = $groups->query(sprintf($qry, $user_id));

        return $groups;
    }
    
    static function existUname($uname, $ename=null) {
		if($ename){
			if($uname == $ename) return false;
		}
	    $group = User_group::staticGet('uname', $uname);
	    return $group !== false;
	}
	
	static function validName($str)
	{
		return preg_match('/^[A-Za-z0-9\x{4e00}-\x{9fa5}]{1,64}$/u', $str);
	}
}
