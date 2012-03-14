<?php

if (!defined('SHAISHAI')) { exit(1); }

/**
 * Table Definition for notice
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

/* We keep the first three 20-notice pages, plus one for pagination check,
 * in the memcached cache. */

define('NOTICE_CACHE_WINDOW', 61);

define('NOTICE_LOCAL_PUBLIC', 1);
//define('NOTICE_REMOTE_OMB', 0);
define('NOTICE_LOCAL_NONPUBLIC', -1);
define('NOTICE_GATEWAY', -2);

if (! defined('MAX_BOXCARS')) {
	define('MAX_BOXCARS', 128);
}

require_once INSTALLDIR.'/lib/mail.php';

class Notice extends Memcached_DataObject
{
	###START_AUTOCODE
	/* the code below is auto generated do not remove the above tag */

	public $__table = 'notice';                          // table name
	public $id;                              // int(4)  primary_key not_null
	public $user_id;                      // int(4)   not_null
	public $uri;                             // varchar(255)  unique_key
	public $content;                         // varchar(140)
	public $rendered;                        // text()
	public $url;                             // varchar(255)
	public $created;                         // datetime()   not_null
	public $modified;                        // timestamp()   not_null default_CURRENT_TIMESTAMP
	public $reply_to;                        // int(4)
	public $source;                          // varchar(32)
	public $conversation;                    // int(4)
	public $is_banned;					 	 // tinyint
	public $content_type; 					 // smallint
	public $topic_type;						 // smallint
	public $retweet_from;					 //smallint
	public $reply_num; 					 //int(4)
	public $retweet_num;				//int(4)
	public $heat;                          //int(4)
	public $discussion_num;
	public $server_id;
	public $reply_only;
	public $is_reply;

	/* Static get */
	function staticGet($k,$v=NULL) {
		return Memcached_DataObject::staticGet('Notice',$k,$v);
	}

	/* the code above is auto generated do not remove the tag below */
	###END_AUTOCODE

	function getProfile()
	{
		return Profile::staticGet('id', $this->user_id);
	}
	function getAward()
	{
		
		return Notice_award::staticGet('notice_id', $this->id);
	}
	
	function getUser()
	{
		return User::staticGet('id', $this->user_id);
	}

	//add by frederica for send notice to link_user
	//get article notice only
	function getNotice($user_id,$start_time,$end_time)
	{
		$notice = new Notice();
		$query = "SELECT id FROM notice WHERE user_id='$user_id' AND content_type=1 " .
		"AND is_banned=0 AND (modified BETWEEN '$start_time' AND DATE_ADD('$end_time',INTERVAL 1 DAY))"; //AND is_delete=0 
//		common_debug($query);
		$notice->query($query);
		$ids = array();
		while ($notice->fetch()) {
			$ids[] = $notice->id;
		}
		$notice->free();
		return $ids;
	}

	//这个可以这样写, $notice = Notice::staticGet('id', $id); $notice->content等等
	function getContent($id)
	{
		$notice = new Notice();
		$query = "SELECT content,modified FROM notice WHERE id='$id' AND is_banned=0"; //AND is_delete=0 
//		common_debug($query);
		$notice->query($query);
		//$notice_select= array();
		while ($notice->fetch()) {
			//$notice_select[]->id=$notice->id;
			$notice_select->content = $notice->content;
			$notice_select->modified=$notice->modified;
		}
		$notice->free();
		return $notice_select;
	}

	//
	//    SELECT user_id, sum( retweet_num ) AS num
	//FROM notice
	//GROUP BY user_id
	//ORDER BY num DESC
	//LIMIT 0 , 30
	function getInfluenceUser($limit=15 ,$area='all' ,$neededid=1) {
		$notice = new Notice();
		$query = "SELECT user_id,sum(retweet_num) AS num FROM notice WHERE user_id != ".
		    common_config('newuser', 'default_id');
	    if($area == 'game')
        $query .= " and user_id IN (select id from user where game_id = ".$neededid.")";
        else if($area == 'gameserver')
        $query .= " and user_id IN (select id from user where game_server_id = ".$neededid.")";
		$query .= " GROUP BY user_id ORDER BY num DESC LIMIT 0,".$limit;
		$notice->query($query);
		$infusers = array();
		while ($notice->fetch()) {
			$infusers[] = array('user_id'=>$notice->user_id,'num'=>$notice->num);
		}
		$notice->free();
		return $infusers;
	}
	
	function delete()
	{
		$this->blowCaches(true);
		//加个收藏夹的cache
		$this->blowFavesCache(true);
		$this->blowSubsCache(true);

		$deleted = new Deleted_notice();

        $deleted->id         = $this->id;
        $deleted->user_id = $this->user_id;
        $deleted->uri        = $this->uri;
        $deleted->content = $this->content;
        $deleted->rendered        = $this->rendered;
        $deleted->created    = $this->created;
        $deleted->deleted    = common_sql_now();
		
		$this->query('BEGIN');
		
		$deleted->insert();
		
		//Null any notices that are replies to this notice
		$this->query(sprintf("UPDATE notice set reply_to = null WHERE reply_to = %d", $this->id));
		//要不要删除这些呢? 现在是删除
		$related = array('Reply', 'Fave', 'Notice_tag', 'Group_inbox',
                         'Queue_item',  //'Retweet',
						 'Video',  'Notice_heat', //'Discussion', 不行?
						 'Music_history'
							);
		if (common_config('inboxes', 'enabled')) {
			$related[] = 'Notice_inbox';
		}
		foreach ($related as $cls) {
			$inst = new $cls();
			$inst->query('DELETE FROM ' . strtolower($cls) . ' WHERE notice_id = ' . $this->id);
//			$inst->notice_id = $this->id;
//			$inst->delete();
		}
//		$this->query(sprintf("UPDATE notice set is_delete = 1 WHERE id = %d", $this->id));
		//$result = parent::delete();
		//还有基于此消息的会话
		if(!empty($this->reply_to)) {
			$reply_notice = Notice::staticGet('id', $this->reply_to);
			if(!empty($reply_notice)) {
				$orig = clone($reply_notice);
				$reply_notice->reply_num -= 1;
				$reply_notice->update($orig);
			}
		}
		if($this->topic_type != 4) {
			$user = User::staticGet('id', $deleted->user_id);
			$game = Game::staticGet('id', $user->game_id);
			$tmp = clone($game);
			$game->notice_num--;
			$game->update($tmp);
			$game_server = Game_server::staticGet('id', $user->game_server_id);
			$tmp = clone($game_server);
			$game_server->notice_num--;
			$game_server->update($tmp);
		}
		//如果此条消息为图片消息, 则可以把图片从目录中删除
		$result = parent::delete();
		
		$this->query('COMMIT');
	}

	function saveTags()
	{
		$count = preg_match_all('/hottopics\?tag=([0-9]+)/i', $this->rendered, $match);
		if (!$count) {
			return false;
		}
		
		foreach(array_unique($match[1]) as $second_tag_id) {
			/* elide characters we don't want in the tag */
			$this->saveTag($second_tag_id);
		}
		return true;
	}

	function saveTag($second_tag_id)
	{	
		$tag = new Notice_tag();
		$tag->notice_id = $this->id;
		$tag->second_tag_id = $second_tag_id;
		$tag->created = $this->created;
		$id = $tag->insert();

		if (!$id) {
			throw new ServerException(sprintf('DB error inserting hashtag: %s',
			$last_error->message));
			return;
		}
		
		// if it's saved, blow its cache
        $tag->blowCache(false);
	}



	function addRetweetToInboxes($user_id, $retweet_from, $created)
	{
		$enabled = common_config('inboxes', 'enabled');

		if ($enabled === true || $enabled === 'transitional') {
			$inbox = new Notice_inbox();

			$inbox->query('BEGIN');
			
			//被关注的用户的id-已存在的id
			$user = User::staticGet('id', $user_id);
			$profile = $user->getSubscribers();
			$ids = array();
			while ($profile && $profile->fetch()) {
				$ids[] = $profile->id;				
			}
			$ids[] = $user_id;
			
			$qryin = implode(", ", $ids);
			$inbox = Notice_inbox::userExist($qryin, $retweet_from);
			$nis = array();
			while ($inbox && $inbox->fetch()) {
				$nis[] = $inbox->user_id;
			}
			$diff = array_diff($ids, $nis);
			$ni = array();
			foreach($diff as $k => $v)
				$ni[$v] = NOTICE_INBOX_SOURCE_SUB;

			//估计还需要减少其他的, 查出关注者在notice_inbox已有的.
			Notice_inbox::bulkInsert($retweet_from, $created, $ni);
			
			$inbox->query('COMMIT');
		}
		return;
	}

	static function saveNew($user_id, $content, $addContent=null, $source=null,
			$is_local=1, $options=array()) {
				
		$defaults = array('reply_to' => null, 'uri' => null, 'created' => null, 'addRendered' => null, 
						'is_banned' => 0, 'content_type' => 1, 'topic_type' => null, 
						'retweet_from' => null, 'reply_to_uname' => null, 'replyonly' => 0);

        if (!empty($options)) {
            $options = $options + $defaults;
            extract($options);
        } else {
        	extract($defaults);
        }
			
		$user = User::staticGet($user_id);
		$content = common_shorten_links($content);
		$final = $content . $addContent;

		if (Notice::contentTooLong($final)) {
			common_log(LOG_INFO, 'Rejecting notice that is too long.');
			return '消息超过了' . Notice::maxContent() . '个字符.';
		}

		if (!$user) {
			common_log(LOG_ERR, 'Problem saving notice. Unknown user.');
			return '未知用户.';
		}

		if (common_config('site', 'dupelimit') > 0 && !Notice::checkDupes($user_id, $final)) {
			common_log(LOG_WARNING, 'Dupe posting by user #' . $user_id . '; throttled.');
			return '您在短时间内发布了太多重复的消息, 请过几分钟再发消息. ';
		}

		$notice = new Notice();
		$notice->user_id = $user_id;
		$notice->server_id = $user->game_server_id;

		$notice->query('BEGIN');

//		$notice->reply_to = $reply_to;
		$notice->retweet_from = $retweet_from;

		if (!empty($created)) {
			$notice->created = $created;
		} else {
			$notice->created = common_sql_now();
		}

		$notice->content = $final;		

		$notice->source = $source;
		$notice->uri = $uri;		
		
		//reply_to不空, 则是回复某条消息的; 为空, 通过回复判断@nickname, 则只是回复某人
		//reply_to是直接对某个消息的回复, 而conversation是一系列的对话
		$notice->reply_to = self::getReplyTo($reply_to, $user_id, $source, $final);
		
		//得到回复的昵称列表, 后面再加上reply_to或reply_to_uname的
		$unames = $notice->getRepliesuname($reply_to_uname);
		
		if (!empty($notice->reply_to)) {
			if($notice->source == 'web') {
				$reply_notice = Notice::staticGet('id', $notice->reply_to);
				if (!empty($reply_notice)) {
					$reply_user = User::staticGet('id', $reply_notice->user_id);
					require_once INSTALLDIR . '/lib/renderhelper.php';
					$reply_content = '@' . common_at_link($notice->user_id, $reply_user->nickname, $reply_user->uname) . ' '; 
					$notice->is_reply = 1;
					$notice->content = '@' . $reply_user->nickname . ' ' . $notice->content;
					$notice->rendered = '<span>' . $reply_content . Notice::renderContent($content, $notice, $reply_user->uname). '</span> ' . $addRendered;
					$notice->conversation = $reply_notice->conversation;
					if(!in_array($reply_user->uname, $unames))
						$unames[] = $reply_user->uname;
					
					//回复加1
					$origReply = clone($reply_notice);
					$reply_notice->reply_num ++;
					$reply_notice->update($origReply);
					
					User_grade::addScore($reply_user->id, 2);
					 
					//原始消息的回复列表blow
					$reply_notice->blowReplyListCache();
				}
			} else {
				$reply_notice = Notice::staticGet('id', $notice->reply_to);
				if (!empty($reply_notice)) {
					$reply_user = User::staticGet('id', $reply_notice->user_id);
					$notice->is_reply = 1;
					$notice->rendered = '<span>' . Notice::renderContent($content, $notice, $reply_user->uname). '</span> ' . $addRendered;
					$notice->conversation = $reply_notice->conversation;
					if(!in_array($reply_user->uname, $unames))
						$unames[] = $reply_user->uname;

					$origReply = clone($reply_notice);
					$reply_notice->reply_num ++;
					$reply_notice->update($origReply);
					
					User_grade::addScore($reply_user->id, 2);
					//原始消息的回复列表blow
					$reply_notice->blowReplyListCache();				
				}
			}
		} else if ($reply_to_uname) {
			$reply_user = User::staticGet('uname', $reply_to_uname);
			require_once INSTALLDIR . '/lib/renderhelper.php';
			$reply_content = '@' . common_at_link($notice->user_id, $reply_user->nickname, $reply_to_uname) . ' '; 
			$notice->is_reply = 1;
			$notice->content = '@' . $reply_user->nickname . ' ' . $notice->content;
			$notice->rendered = '<span>' . $reply_content . Notice::renderContent($content, $notice). '</span> ' . $addRendered;
			if(!in_array($reply_user->uname, $unames))
					$unames[] = $reply_user->uname;
		} else {
			$notice->rendered = '<span>' . Notice::renderContent($content, $notice). '</span> ' . $addRendered;
		}
		
		$notice->is_banned = $is_banned;
		$notice->content_type = $content_type;
		$notice->topic_type = $topic_type;
		
		if (Event::handle('StartNoticeSave', array(&$notice))) {
			$id = $notice->insert();
			if (!$id) {
				common_log_db_error($notice, 'INSERT', __FILE__);
				return '保存消息产生问题.';
			}

		    // Update ID-dependent columns: URI, conversation
            $orig = clone($notice);
            $changed = false;
            if (empty($uri)) {
                $notice->uri = common_notice_uri($notice);
                $changed = true;
            }
            // If it's not part of a conversation, it's
            // the beginning of a new conversation.
            if (empty($notice->conversation)) {
                $notice->conversation = $notice->id;
                $changed = true;
            }
            if ($changed) {
                if (!$notice->update($orig)) {
                    common_log_db_error($notice, 'UPDATE', __FILE__);
                    throw new ServerException('Problem saving notice.');
                }
            }

			$notice->saveReplies($unames);
			
			// if tag exists in msg, give additional 2 scores
			//$haveTag = $notice->saveTag();
			$haveTag = $notice->saveTags();

			//先判断是否为部落消息, 是则保持在group_inbox里面, 否则保存在notice_inbox里面
			// if it's a reply notice and set to replyonly, we don't add it to followers' inboxes
			if ($replyonly == 0 || $topic_type == 4) {
					
			    $notice->addToInboxes($unames);
			    //$notice->distribute();
			} else {
				$orig = clone($notice);				
				$notice->reply_only = 1;
				$notice->update($orig);
			}
			
			// adjust the user score and grade according to message type
			// don't add score if it's reply or retweet
			// text - 1, pic - 3, music - 3, video - 3
			// any gorup msg only add 1 score
			// tag msg have additional 2 scores
			$scoreAdd = 1;
			if(empty($reply_to)&&empty($retweet_from)){
				if($notice->topic_type == 4){
					User_grade::addScore($user_id, $scoreAdd);
				}else {
					if ($haveTag){
						$scoreAdd = $scoreAdd+2;
					}
					if($notice->content_type == 1){
						User_grade::addScore($user_id, $scoreAdd);
					}else{
						User_grade::addScore($user_id, $scoreAdd+2);
					}
				}
			}
			
			if($notice->topic_type != 4) {
				$game = Game::staticGet('id', $user->game_id);
				$tmp = clone($game);
				$game->notice_num++;
				$game->update($tmp);
				$game_server = Game_server::staticGet('id', $user->game_server_id);
				$tmp = clone($game_server);
				$game_server->notice_num++;
				$game_server->update($tmp);
			}

			$notice->query('COMMIT');

			Event::handle('EndNoticeSave', array($notice));
		}

		# Clear the cache for subscribed users, so they'll update at next request
		//针对部落,/普通的消息, 这个处理方法是不一样的
		$notice->blowCaches();

		return $notice;
	}

	static function checkDupes($user_id, $content) {
		$user = User::staticGet($user_id);
		if (!$user) {
			return false;
		}
		$notice = $user->getNotices(0, NOTICE_CACHE_WINDOW);
		if ($notice) {
			$last = 0;
			while ($notice->fetch()) {
				if (time() - strtotime($notice->created) >= common_config('site', 'dupelimit')) {
					return true;
				} else if ($notice->content == $content) {
					return false;
				}
			}
		}
		# If we get here, oldest item in cache window is not
		# old enough for dupe limit; do direct check against DB
		$notice = new Notice();
		$notice->user_id = $user_id;
		$notice->content = $content;
		if (common_config('db','type') == 'pgsql')
		$notice->whereAdd('extract(epoch from now() - created) < ' . common_config('site', 'dupelimit'));
		else
		$notice->whereAdd('now() - created < ' . common_config('site', 'dupelimit'));

		$cnt = $notice->count();
		return ($cnt == 0);
	}

	//count前的消息小于timespan(以秒计时)
	static function checkEditThrottle($user_id) {
		$user = User::staticGet($user_id);
		if (!$user) {
			return false;
		}
		# Get the Nth notice
		$notice = $user->getNotices(common_config('throttle', 'count') - 1, 1);
		if ($notice && $notice->fetch()) {
			# If the Nth notice was posted less than timespan seconds ago
			if (time() - strtotime($notice->created) <= common_config('throttle', 'timespan')) {
				# Then we throttle
				return false;
			}
		}
		# Either not N notices in the stream, OR the Nth was not posted within timespan seconds
		return true;
	}

	function getUploadedAttachment() {
		$post = clone $this;
		$query = 'select file.url as up, file.id as i from file join file_to_post on file.id = file_id where post_id=' . $post->escape($post->id) . ' and url like "%/notice/%/file"';
		$post->query($query);
		$post->fetch();
		if (empty($post->up) || empty($post->i)) {
			$ret = false;
		} else {
			$ret = array($post->up, $post->i);
		}
		$post->free();
		return $ret;
	}

	function hasAttachments() {
		$post = clone $this;
		$query = "select count(file_id) as n_attachments from file join file_to_post on (file_id = file.id) join notice on (post_id = notice.id) where post_id = " . $post->escape($post->id);
		$post->query($query);
		$post->fetch();
		$n_attachments = intval($post->n_attachments);
		$post->free();
		return $n_attachments;
	}

	//这个要判断
	function attachments() {
		$att = array();
		$f2p = new File_to_post;
		$f2p->post_id = $this->id;
		if ($f2p->find()) {
			while ($f2p->fetch()) {
				$f = File::staticGet($f2p->file_id);
				$att[] = clone($f);
			}
		}
		return $att;
	}

	//看看还需要加什么, 新加的各种cache
	//first_tag都清理掉了, 看看是否只清理此条消息带有的.
	function blowCaches($blowLast=false)
	{
		if($this->topic_type != 4) {
			$this->blowSubsCache($blowLast);
			$this->blowNoticeCache($blowLast);
			$this->blowRepliesCache($blowLast);
			// XXX: public缓存要改
			$this->blowPublicCache($blowLast);
			
			$this->blowTaggedNoticeCache($blowLast);
			// XXX: profile=>user
			//这里加很多消息数目类的缓存
			$profile = Profile::staticGet($this->user_id);
			$profile->blowNoticeCount();
			$user = User::staticGet($this->user_id);
			$user->blowNoticeCountByType();
		} else {
			//只部落
			$this->blowGroupCache(true);
		}
		// XXX: tag这块缓存
		$this->blowTagCache($blowLast);
		$this->blowConversationCache($blowLast);
		$this->blowReplyListCache($blowLast);
	}
	
	function blowTaggedNoticeCache($blowLast=false)
	{
		$cache = common_memcache();
		if ($cache) {
			$ck = common_cache_key('taggednotices:server:'.$this->server_id);
			$user = User::staticGet('id', $this->user_id);
			$game_ck = common_cache_key('taggednotices:game:'.$user->game_id);			
			$cache->delete($ck);
			$cache->delete($game_ck);
			$cache->delete(common_cache_key('firsttaggednotices:game:'.$user->game_id));
			$cache->delete(common_cache_key('firsttaggednotices:server:'.$this->server_id));
			$cache->delete(common_cache_key('sectaggednotices:game:'.$user->game_id));
			$cache->delete(common_cache_key('sectaggednotices:server:'.$this->server_id));
			if ($blowLast) {
				$cache->delete($ck.';last');
				$cache->delete($game_ck.';last');
				$cache->delete(common_cache_key('firsttaggednotices:game:'.$user->game_id.';last'));
				$cache->delete(common_cache_key('firsttaggednotices:server:'.$this->server_id.';last'));
				$cache->delete(common_cache_key('sectaggednotices:game:'.$user->game_id.';last'));
				$cache->delete(common_cache_key('sectaggednotices:server:'.$this->server_id.';last'));				
			}
		}
	} 
	
	function blowConversationCache($blowLast=false)
	{
		$cache = common_memcache();
		if ($cache) {
			$ck = common_cache_key('notice:conversation_ids:'.$this->conversation);
			$cache->delete($ck);
			if ($blowLast) {
				$cache->delete($ck.';last');
			}
		}
	}

	function blowGroupCache($blowLast=false)
	{
		$cache = common_memcache();
		if ($cache) {
			$group_inbox = new Group_inbox();
			$group_inbox->notice_id = $this->id;
			if ($group_inbox->find()) {
				while ($group_inbox->fetch()) {
					$cache->delete(common_cache_key('user_group:notice_ids:' . $group_inbox->group_id));
					if($this->content_type > 0)
						$cache->delete(common_cache_key('user_group:notice_ids:'.$group_inbox->group_id.':'.$this->content_type));
					$group = User_group::staticGet('id', $group_inbox->group_id);
					$fts = First_tag::getFirstTags($group->game_id);
					foreach ($fts as $id => $name) {
						$cache->delete(common_cache_key('user_group:notice_ids:'.$group_inbox->group_id.':'.$id));
					}
					if ($blowLast) {
						$cache->delete(common_cache_key('user_group:notice_ids:' . $group_inbox->group_id.';last'));
						if($this->content_type > 0)
							$cache->delete(common_cache_key('user_group:notice_ids:'.$group_inbox->group_id.':'.$this->content_type.';last'));
						foreach ($fts as $id => $name) {
							$cache->delete(common_cache_key('user_group:notice_ids:'.$group_inbox->group_id.':'.$id.';last'));
						}
					}
				}
			}
			$group_inbox->free();
			unset($group_inbox);
		}
	}

	function blowTagCache($blowLast=false)
	{
		$cache = common_memcache();
		if ($cache) {
			$tag = new Notice_tag();
			$tag->notice_id = $this->id;
			if ($tag->find()) {
				while ($tag->fetch()) {
					$tag->blowCache($blowLast);
//					$ck = 'profile:notice_ids_tagged:' . $this->user_id . ':' . $tag->tag;
//					$cache->delete($ck);
//					if($this->content_type > 0) {
//						$ck_ct = 'profile:notice_ids_tagged:' . $this->user_id . ':' . $tag->tag . ':' . $this->content_type;
//						$cache->delete($ck_ct);
//					}

					if ($blowLast) {
//						$cache->delete($ck . ';last');
//						if($this->content_type > 0) {
//							$ck_ct = 'profile:notice_ids_tagged:' . $this->user_id . ':' . $tag->tag . ':' . $this->content_type. ';last';
//							$cache->delete($ck_ct);
//						}
					}
				}
			}
			$tag->free();
			unset($tag);
		}
	}

	//加上清理notice_inbox:by_user_own_tag
	function blowSubsCache($blowLast=false)
	{
		$cache = common_memcache();
		if ($cache) {
			$user = new User();

			$UT = common_config('db','type')=='pgsql'?'"user"':'user';
			$user->query('SELECT id ' .

                         "FROM $UT JOIN subscription ON $UT.id = subscription.subscriber " .
                         'WHERE subscription.subscribed = ' . $this->user_id);

			while ($user->fetch()) {
				$cache->delete(common_cache_key('notice_inbox:by_user:'.$user->id));
				$cache->delete(common_cache_key('notice_inbox:by_user_own:'.$user->id));
				if($this->content_type > 0) {
					$cache->delete(common_cache_key('notice_inbox:by_user:'.$user->id.':'.$this->content_type));
					$cache->delete(common_cache_key('notice_inbox:by_user_own:'.$user->id.':'.$this->content_type));
				}
				$cache->delete(common_cache_key('notice_inbox:by_other:'.$user->id));
				$cache->delete(common_cache_key('notice_inbox:by_other_own:'.$user->id));
				//某个用户对应的游戏first_tag
				$fts = First_tag::getFirstTags($user->game_id);
				foreach ($fts as $id => $name) {
					$cache->delete(common_cache_key('notice_inbox:by_user_tag:'.$user->id.':'.$id));
					$cache->delete(common_cache_key('notice_inbox:by_user_own_tag:'.$user->id.':'.$id));
				}
				if ($blowLast) {
					$cache->delete(common_cache_key('notice_inbox:by_user:'.$user->id.';last'));
					$cache->delete(common_cache_key('notice_inbox:by_user_own:'.$user->id.';last'));
					if($this->content_type > 0) {
						$cache->delete(common_cache_key('notice_inbox:by_user:'.$user->id.':'.$this->content_type.';last'));
						$cache->delete(common_cache_key('notice_inbox:by_user_own:'.$user->id.':'.$this->content_type.';last'));				
					}
					$cache->delete(common_cache_key('notice_inbox:by_other:'.$user->id.';last'));
					$cache->delete(common_cache_key('notice_inbox:by_other_own:'.$user->id.';last'));
					foreach ($fts as $id => $name) {
						$cache->delete(common_cache_key('notice_inbox:by_user_tag:'.$user->id.':'.$id.';last'));
						$cache->delete(common_cache_key('notice_inbox:by_user_own_tag:'.$user->id.':'.$id.';last'));
					}
				}
			}
			$user->free();
			unset($user);
		}
	}

	function blowNoticeCache($blowLast=false)
	{
		$cache = common_memcache();
		if (!empty($cache)) {
			$cache->delete(common_cache_key('profile:notice_ids:'.$this->user_id));
			$cache->delete(common_cache_key('profile:notice_ids:retweet:'.$this->user_id));
			if($this->content_type > 0) 
				$cache->delete(common_cache_key('profile:notice_ids:'.$this->user_id.':'.$this->content_type));
			$user= User::staticGet('id', $this->user_id);
			$fts = First_tag::getFirstTags($user->game_id);
			foreach ($fts as $id => $name) {
				$cache->delete(common_cache_key('profile:notice_ids:'.$this->user_id.':'.$id));
			}

			//普通增加消息, ;last是不会删除的, 所以缓存的id一直在增长, 并没有完全删除
			if ($blowLast) {
				$cache->delete(common_cache_key('profile:notice_ids:'.$this->user_id.';last'));
				$cache->delete(common_cache_key('profile:notice_ids:retweet:'.$this->user_id.';last'));
				if($this->content_type > 0) 
					$cache->delete(common_cache_key('profile:notice_ids:'.$this->user_id.':'.$this->content_type.';last'));
				foreach ($fts as $id => $name) {
					$cache->delete(common_cache_key('profile:notice_ids:'.$this->user_id.':'.$id.';last'));
				}
			}
		}
	}

	function blowRepliesCache($blowLast=false)
	{
		$cache = common_memcache();
		if ($cache) {
			$reply = new Reply();
			$reply->notice_id = $this->id;
			if ($reply->find()) {
				while ($reply->fetch()) {
					$cache->delete(common_cache_key('reply:stream:'.$reply->user_id));
					if ($blowLast) {
						$cache->delete(common_cache_key('reply:stream:'.$reply->user_id.';last'));
					}
				}
			}
			$reply->free();
			unset($reply);
		}
	}

	function blowReplyListCache($blowLast=false)
	{
		$cache = common_memcache();
		if ($cache) {
			$cache->delete(common_cache_key('notice:replylist_ids:'.$this->id));
			if(!is_null($this->reply_to)) {
				$cache->delete(common_cache_key('notice:replylist_ids:'.$this->reply_to));
			}
			if ($blowLast) {
				$cache->delete(common_cache_key('notice:replylist_ids:'.$this->id.';last'));
				if(!is_null($this->reply_to)) {
					$cache->delete(common_cache_key('notice:replylist_ids:'.$this->reply_to.';last'));
				}
			}
		}
	}

	function blowPublicCache($blowLast=false)
	{
		$cache = common_memcache();
		if ($cache) {
			$cache->delete(common_cache_key('public'));
			if($this->content_type > 0) {
				$cache->delete(common_cache_key('public:'.$this->content_type));
			}
			for($i=1; $i<5; $i++) {
				$cache->delete(common_cache_key('public:'.$i));
			}
			$user= User::staticGet('id', $this->user_id);
			$fts = First_tag::getFirstTags($user->game_id);
			foreach ($fts as $id => $name) {
				$cache->delete(common_cache_key('public:'.$id));
			}
			if ($blowLast) {
				$cache->delete(common_cache_key('public;last'));
				if($this->content_type > 0) {
					$cache->delete(common_cache_key('public:'.$this->content_type.';last'));
				}
				foreach ($fts as $id => $name) {
					$cache->delete(common_cache_key('public:'.$id.';last'));
				}
			}
		}
	}

	function blowFavesCache($blowLast=false)
	{
		$cache = common_memcache();
		if ($cache) {
			$fave = new Fave();
			$fave->notice_id = $this->id;
			if ($fave->find()) {
				while ($fave->fetch()) {
					$cache->delete(common_cache_key('fave:ids_by_user:'.$fave->user_id));
					$cache->delete(common_cache_key('fave:by_user_own:'.$fave->user_id));
					//fave_group
					$cache->delete(common_cache_key('fave:ids_by_fave_group:'.$fave->favegroup_id));
					if ($blowLast) {
						$cache->delete(common_cache_key('fave:ids_by_user:'.$fave->user_id.';last'));
						$cache->delete(common_cache_key('fave:by_user_own:'.$fave->user_id.';last'));
						//fave_group
						$cache->delete(common_cache_key('fave:ids_by_fave_group:'.$fave->favegroup_id.';last'));
					}
				}
			}
			$fave->free();
			unset($fave);
		}
	}

	# XXX: too many args; we need to move to named params or even a separate
	# class for notice streams

	//这个以后可以去掉
	static function getStream($qry, $cachekey, $offset=0, $limit=20, $since_id=0, $max_id=0, $order=null, $since=null) {

		if (common_config('memcached', 'enabled')) {

			# Skip the cache if this is a since, since_id or max_id qry
			if ($since_id > 0 || $max_id > 0 || $since) {
				return Notice::getStreamDirect($qry, $offset, $limit, $since_id, $max_id, $order, $since);
			} else {
				return Notice::getCachedStream($qry, $cachekey, $offset, $limit, $order);
			}
		}

		return Notice::getStreamDirect($qry, $offset, $limit, $since_id, $max_id, $order, $since);
	}

	static function getStreamDirect($qry, $offset, $limit, $since_id, $max_id, $order, $since) {

		$needAnd = false;
		$needWhere = true;

		if (preg_match('/\bWHERE\b/i', $qry)) {
			$needWhere = false;
			$needAnd = true;
		}

		if ($since_id > 0) {

			if ($needWhere) {
				$qry .= ' WHERE ';
				$needWhere = false;
			} else {
				$qry .= ' AND ';
			}

			$qry .= ' notice.id > ' . $since_id;
		}

		if ($max_id > 0) {

			if ($needWhere) {
				$qry .= ' WHERE ';
				$needWhere = false;
			} else {
				$qry .= ' AND ';
			}

			$qry .= ' notice.id <= ' . $max_id;
		}

		if ($since) {

			if ($needWhere) {
				$qry .= ' WHERE ';
				$needWhere = false;
			} else {
				$qry .= ' AND ';
			}

			$qry .= ' notice.created > \'' . date('Y-m-d H:i:s', $since) . '\'';
		}

		if ($needWhere) {
			$qry .= ' WHERE ';
			$needWhere = false;
		} else {
			$qry .= ' AND ';
		}
		$qry .= ' is_banned = 0'; //and is_delete = 0 

		# Allow ORDER override

		if ($order) {
			$qry .= $order;
		} else {
			$qry .= ' ORDER BY notice.created DESC, notice.id DESC ';
		}

		if (common_config('db','type') == 'pgsql') {
			$qry .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
		} else {
			$qry .= ' LIMIT ' . $offset . ', ' . $limit;
		}

		$notice = new Notice();

		$notice->query($qry);

		return $notice;
	}

	# XXX: this is pretty long and should probably be broken up into
	# some helper functions

	static function getCachedStream($qry, $cachekey, $offset, $limit, $order) {

		# If outside our cache window, just go to the DB

		if ($offset + $limit > NOTICE_CACHE_WINDOW) {
			return Notice::getStreamDirect($qry, $offset, $limit, null, null, $order, null);
		}

		# Get the cache; if we can't, just go to the DB

		$cache = common_memcache();

		if (!$cache) {
			return Notice::getStreamDirect($qry, $offset, $limit, null, null, $order, null);
		}

		# Get the notices out of the cache

		$notices = $cache->get(common_cache_key($cachekey));

		# On a cache hit, return a DB-object-like wrapper

		if ($notices !== false) {
			$wrapper = new ArrayWrapper(array_slice($notices, $offset, $limit));
			return $wrapper;
		}

		# If the cache was invalidated because of new data being
		# added, we can try and just get the new stuff. We keep an additional
		# copy of the data at the key + ';last'

		# No cache hit. Try to get the *last* cached version

		$last_notices = $cache->get(common_cache_key($cachekey) . ';last');

		if ($last_notices) {

			# Reverse-chron order, so last ID is last.

			$last_id = $last_notices[0]->id;

			# XXX: this assumes monotonically increasing IDs; a fair
			# bet with our DB.

			$new_notice = Notice::getStreamDirect($qry, 0, NOTICE_CACHE_WINDOW,
			$last_id, null, $order, null);

			if ($new_notice) {
				$new_notices = array();
				while ($new_notice->fetch()) {
					$new_notices[] = clone($new_notice);
				}
				$new_notice->free();
				$notices = array_slice(array_merge($new_notices, $last_notices),
				0, NOTICE_CACHE_WINDOW);

				# Store the array in the cache for next time

				$result = $cache->set(common_cache_key($cachekey), $notices);
				$result = $cache->set(common_cache_key($cachekey) . ';last', $notices);

				# return a wrapper of the array for use now

				return new ArrayWrapper(array_slice($notices, $offset, $limit));
			}
		}

		# Otherwise, get the full cache window out of the DB

		$notice = Notice::getStreamDirect($qry, 0, NOTICE_CACHE_WINDOW, null, null, $order, null);

		# If there are no hits, just return the value

		if (!$notice) {
			return $notice;
		}

		# Pack results into an array

		$notices = array();

		while ($notice->fetch()) {
			$notices[] = clone($notice);
		}

		$notice->free();

		# Store the array in the cache for next time

		$result = $cache->set(common_cache_key($cachekey), $notices);
		$result = $cache->set(common_cache_key($cachekey) . ';last', $notices);

		# return a wrapper of the array for use now

		$wrapper = new ArrayWrapper(array_slice($notices, $offset, $limit));

		return $wrapper;
	}


	//通过每个消息的缓存, 来获得此消息, 如果没有, 则直接查询数据库, 按照id逆序来查找
	//下面的函数则直接查询, 而不用id排序
	function getStreamByIds($ids)
	{
		$cache = common_memcache();

		if (!empty($cache)) {
			$notices = array();
			foreach ($ids as $id) {
				$n = Notice::staticGet('id', $id);
				if (!empty($n)) {
					$notices[] = $n;
				}
			}
			return new ArrayWrapper($notices);
		} else {
			$notice = new Notice();
//			$notice->whereAdd('id in (' . implode(', ', $ids) . ')');
//			$notice->orderBy('id DESC');
//
//			$notice->find();
//			return $notice;
			//有可能查询为空
			if (empty($ids)) {
                //if no IDs requested, just return the notice object
                return $notice;
            }
            $notice->whereAdd('id in (' . implode(', ', $ids) . ')');

            $notice->find();

            $temp = array();

            while ($notice->fetch()) {
                $temp[$notice->id] = clone($notice);
            }

            $wrapped = array();

            //不懂? 为什么要呢??
            foreach ($ids as $id) {
                if (array_key_exists($id, $temp)) {
                    $wrapped[] = $temp[$id];
                }
            }

            return new ArrayWrapper($wrapped);
		}
	}
	
	function getStreamByIdsdirect($ids)
	{
		$cache = common_memcache();

		if (!empty($cache)) {
			$notices = array();
			foreach ($ids as $id) {
				$n = Notice::staticGet('id', $id);
				if (!empty($n)) {
					$notices[] = $n;
				}
			}
			return new ArrayWrapper($notices);
		} else {
			$notice = new Notice();
			$notice->whereAdd('id in (' . implode(', ', $ids) . ')');
		//	$notice->orderBy('id DESC');

			$notice->find();
			return $notice;
		}
	}

	//topic_type=4时, 表示取出不包含部落的消息
	function publicStream($offset=0, $limit=20, $since_id=0, $max_id=0, $since=null,
							$content_type=0, $area_type=0, $topic_type=0, $first_tag=0)
	{
	
		if ($content_type && ! $first_tag ) {
			//缓存内容分类的消息
			$ids = Notice::stream(array('Notice', '_publicStreamDirect'),
			array($first_tag),  'public:' . $content_type,
			$offset, $limit, $since_id, $max_id, $since,
			$content_type, $area_type, $topic_type);
		} else if (! $content_type  && $first_tag) {
			//缓存话题分类的消息
			$ids = Notice::stream(array('Notice', '_publicStreamDirect'),
			array($first_tag),  'public:'. $first_tag,
			$offset, $limit, $since_id, $max_id, $since,
			$content_type, $area_type, $topic_type);
		} else if (! $content_type  && ! $first_tag) {
			//缓存无分类的消息
			$ids = Notice::stream(array('Notice', '_publicStreamDirect'),
			array($first_tag),  'public' ,
			$offset, $limit, $since_id, $max_id, $since,
			$content_type, $area_type, $topic_type);
		} else {
			$ids = Notice::_publicStreamDirect($first_tag, $offset, $limit, $since_id, $max_id, $since,
			$content_type, $area_type, $topic_type);
		}

		return Notice::getStreamByIds($ids);
	}

	//查找的条件, 在这里添加删除判断
	function _publicStreamDirect($first_tag=0, $offset=0, $limit=20, $since_id=0, $max_id=0, $since=null,
									$content_type=0, $area_type=0, $topic_type=0, $game_id=0)
	{
		$notice = new Notice();

		$notice->selectAdd(); // clears it
		$notice->selectAdd('id');

		$notice->orderBy('id DESC');

		if (!is_null($offset)) {
			$notice->limit($offset, $limit);
		}

		if ($since_id != 0) {
			$notice->whereAdd('id > ' . $since_id);
		}

		if ($max_id != 0) {
			$notice->whereAdd('id <= ' . $max_id);
		}

		if (!is_null($since)) {
			$notice->whereAdd('created > \'' . date('Y-m-d H:i:s', $since) . '\'');
		}
        	
		//等于0, 取出所有的消息
		if ($content_type != 0) {
			$notice->whereAdd('content_type = ' . $content_type);
		}
		
		if ($first_tag > 0) {
        	//$notice->whereAdd('EXISTS (SELECT * from notice_tag where notice.id=notice_tag.notice_id ' .
        	//'and second_tag_id in (select id from second_tag where first_tag_id%10=' . ($first_tag%10) . '))');
        	
			//现在一级话题都采用999x, 二级话题都是999xzz
        	$notice->whereAdd('id IN (SELECT notice_tag.notice_id from notice_tag WHERE ' .
        		'notice_tag.second_tag_id DIV 100=' . $first_tag . ')');
		} 

		if ($topic_type != 0) {
			if($topic_type == 4) {
				$notice->whereAdd('topic_type != 4');
			}else{
				$notice->whereAdd('topic_type = ' . $topic_type);
			}
		}
		
		if ($game_id != 0) {
    		$notice->whereAdd('server_id > ' . ($game_id * 100000) );
    		$notice->whereAdd('server_id < '. (($game_id + 1) * 100000));
		}
		
		//过滤被禁止的消息
		$notice->whereAdd('is_banned = 0');
		//过滤仅是回复的消息
		$notice->whereAdd('reply_only = 0');

		$ids = array();

		if ($notice->find()) {
			while ($notice->fetch()) {
				$ids[] = $notice->id;
			}
		}

		$notice->free();
		$notice = NULL;

		return $ids;
	}

	function conversationStream($id, $offset=0, $limit=20, $since_id=0, $max_id=0, $since=null,
								$content_type=0, $area_type=0, $topic_type=0)
	{
		$ids = Notice::stream(array('Notice', '_conversationStreamDirect'),
		array($id), 'notice:conversation_ids:'.$id,
		$offset, $limit, $since_id, $max_id, $since,
		$content_type, $area_type, $topic_type);

		return Notice::getStreamByIds($ids);
	}

	function _conversationStreamDirect($id, $offset=0, $limit=20, $since_id=0, $max_id=0, $since=null,
										$content_type=0, $area_type=0, $topic_type=0)
	{
		$notice = new Notice();

		$notice->selectAdd(); // clears it
		$notice->selectAdd('id');

		$notice->conversation = $id;

		$notice->orderBy('id DESC');

		if (!is_null($offset)) {
			$notice->limit($offset, $limit);
		}

		if ($since_id != 0) {
			$notice->whereAdd('id > ' . $since_id);
		}

		if ($max_id != 0) {
			$notice->whereAdd('id <= ' . $max_id);
		}

		if (!is_null($since)) {
			$notice->whereAdd('created > \'' . date('Y-m-d H:i:s', $since) . '\'');
		}

		$notice->whereAdd('is_banned = 0');
//		$notice->whereAdd('is_delete = 0');

		$ids = array();

		if ($notice->find()) {
			while ($notice->fetch()) {
				$ids[] = $notice->id;
			}
		}

		$notice->free();
		$notice = NULL;

		return $ids;
	}

	//回复某消息的消息列表
	//回复的删除, 回复原始消息的删除, 新的回复需要清空缓存
	function replyListStream($id, $offset=0, $limit=20, $since_id=0, $max_id=0, $since=null,
								$content_type=0, $area_type=0, $topic_type=0)
	{
		$ids = Notice::stream(array('Notice', '_replyListStreamDirect'),
		array($id),  'notice:replylist_ids:'.$id,
		$offset, $limit, $since_id, $max_id, $since,
		$content_type, $area_type, $topic_type);

		return Notice::getStreamByIds($ids);
	}

	function _replyListStreamDirect($id, $offset=0, $limit=20, $since_id=0, $max_id=0, $since=null,
										$content_type=0, $area_type=0, $topic_type=0)
	{
		$notice = new Notice();

		$notice->selectAdd(); // clears it
		$notice->selectAdd('id');

		$notice->reply_to = $id;

		$notice->orderBy('id ASC');

		if (!is_null($offset)) {
			$notice->limit($offset, $limit);
		}

		if ($since_id != 0) {
			$notice->whereAdd('id > ' . $since_id);
		}

		if ($max_id != 0) {
			$notice->whereAdd('id <= ' . $max_id);
		}

		if (!is_null($since)) {
			$notice->whereAdd('created > \'' . date('Y-m-d H:i:s', $since) . '\'');
		}

		$notice->whereAdd('is_banned = 0');
//		$notice->whereAdd('is_delete = 0');

		$ids = array();

		if ($notice->find()) {
			while ($notice->fetch()) {
				$ids[] = $notice->id;
			}
		}

		$notice->free();
		$notice = NULL;

		return $ids;
	}

    function whoGets($uname=null)
    {
        $c = self::memcache();

        if (!empty($c)) {
            $ni = $c->get(common_cache_key('notice:who_gets:'.$this->id));
            if ($ni !== false) {
                return $ni;
            }
        }

        $users = $this->getSubscribedUsers();
        
        $ni = array();

        foreach ($users as $id) {
            $ni[$id] = 1;
        }

   		 //被回复的id
		if($uname) {
			for($i=0; $i<count($uname); $i++) {
				$recipient_user = User::staticGet('uname', $uname[$i]);
				if (!array_key_exists($recipient_user->id, $ni)) {
					$ni[$recipient_user->id] = 1;
				}
			}
		}

        if (!empty($c)) {
            // XXX: pack this data better
            $c->set(common_cache_key('notice:who_gets:'.$this->id), $ni);
        }

        return $ni;
    }
    
	//插入改进, 特别是数据量很大时, 跟随者列表进行缓存
	function addToInboxes($uname=null)
	{
		$enabled = common_config('inboxes', 'enabled');
		if ($enabled === true || $enabled === 'transitional') {
			$groups = $this->saveGroups();
			if (empty($groups)){
				$ni = $this->whoGets($uname);

				// We remove the author (if they're a local user),
		        // since we'll have already done this in distribute()
//				if (array_key_exists($this->user_id, $ni)) {
//					unset($ni[$this->user_id]);
//				}

		        //如果有做queue, 则要去掉自己的id
				Notice_inbox::bulkInsert($this->id, $this->created, $ni);
			}
		}	
		return;
	}
	
    function distribute()
    {
        // We always insert for the author so they don't
        // have to wait

        $user = User::staticGet('id', $this->user_id);
        if (!empty($user)) {
            //Inbox::insertNotice($user->id, $this->id);
            //一条
            Notice_inbox::insertNotice($user->id, $this->created, $this->id, 1);
        }

        if (common_config('queue', 'inboxes')) {
            // If there's a failure, we want to _force_
            // distribution at this point.
            try {
                $qm = QueueManager::get();
                $qm->enqueue($this, 'distrib');
            } catch (Exception $e) {
                // If the exception isn't transient, this
                // may throw more exceptions as DQH does
                // its own enqueueing. So, we ignore them!
                try {
                    $handler = new DistribQueueHandler();
                    $handler->handle($this);
                } catch (Exception $e) {
                    common_log(LOG_ERR, "emergency redistribution resulted in " . $e->getMessage());
                }
                // Re-throw so somebody smarter can handle it.
                throw $e;
            }
        } else {
            $handler = new DistribQueueHandler();
            $handler->handle($this);
        }
    }

	function getSubscribedUsers()
	{
		$user = new User();
	
		$qry =
	          'SELECT id ' .
	          'FROM user JOIN subscription '.
	          'ON user.id = subscription.subscriber ' .
	          'WHERE subscription.subscribed = %d ';
	
		$user->query(sprintf($qry, $this->user_id));
	
		$ids = array();
	
		while ($user->fetch()) {
			$ids[] = $user->id;
		}
	
		$user->free();
	
		return $ids;
	}
	
	function saveGroups()
	{
		$groups = array();
	
		$enabled = common_config('inboxes', 'enabled');
		if ($enabled !== true && $enabled !== 'transitional') {
			return $groups;
		}
	
		/* extract all !group */
		$count = preg_match_all('/(?:^|\s)[!！]([A-Za-z0-9\x{4e00}-\x{9fa5}]{1,64})/u',
		strtolower($this->content),
		$match);
	
		if (!$count) {
			return $groups;
		}
		$profile = $this->getProfile();
		/* Add them to the database */
		foreach (array_unique($match[1]) as $uname) {
			$group = User_group::getForuname($uname);
	
			if (empty($group)) {
				continue;
			}
			//加群组热度
			$group->addGroupHeat(1);
			
			//给每个群所在用户增加1条未读消息
			$ids = $group->getUserMembers();
			//除自己之外的添加
			foreach ($ids as $id) {
				if($id != $this->user_id)
					Group_unread_notice::addUnread($id, $group->id);
			}
			
			if ($group->hasMember($profile)) {
				$result = $this->addToGroupInbox($group);
				if (!$result) {
					common_log_db_error($gi, 'INSERT', __FILE__);
				}
				$groups[] = clone($group);
			}
		}
		// Update notice to a group notice，topic_type equals to 4
		if(!empty($groups) && $this->topic_type != 4)
		{
			$orig = clone($this);
			$this->topic_type = 4;
			$result = $this->update($orig);
			if (!$result) {
				common_log_db_error($this, 'UPDATE', __FILE__);
				$this->serverError('不能更新此信息。');
			}
		}
		return $groups;
	}
	
	function addToGroupInbox($group)
	{
		$gi = Group_inbox::pkeyGet(array('group_id' => $group->id,
	                                         'notice_id' => $this->id));
	
		if (empty($gi)) {
			$gi = new Group_inbox();
			$gi->group_id  = $group->id;
			$gi->notice_id = $this->id;
			$gi->created   = $this->created;
	
			return $gi->insert();
		}
		return true;
	}
	


	function asAtomEntry($namespace=false, $source=false)
	{
		$profile = $this->getProfile();

		$xs = new XMLStringer(true);

		if ($namespace) {
			$attrs = array('xmlns' => 'http://www.w3.org/2005/Atom',
                           'xmlns:thr' => 'http://purl.org/syndication/thread/1.0');
		} else {
			$attrs = array();
		}

		$xs->elementStart('entry', $attrs);

		if ($source) {
			$xs->elementStart('source');
			$xs->element('title', null, $profile->uname . " - " . common_config('site', 'name'));
			$xs->element('link', array('href' => $profile->profileurl));
			$user = User::staticGet('id', $profile->id);
			if (!empty($user)) {
				$atom_feed = common_local_url('ApiTimelineUser',
				array('id' => $profile->uname,
                                              	'format' => 'atom'));
				//                                                    'method' => 'user_timeline',
				//                                                    'argument' => $profile->uname.'.atom'));
				$xs->element('link', array('rel' => 'self',
                                           'type' => 'application/atom+xml',
                                           'href' => $profile->profileurl));
				$xs->element('link', array('rel' => 'license',
                                           'href' => common_config('license', 'url')));
			}

			$xs->element('icon', null, $profile->avatarUrl(AVATAR_PROFILE_SIZE));
		}

		$xs->elementStart('author');
		$xs->element('name', null, $profile->uname);
		$xs->element('uri', null, $profile->profileurl);
		$xs->elementEnd('author');
		if ($source) {
			$xs->elementEnd('source');
		}
		$xs->element('title', null, $this->content);
		$xs->element('summary', null, $this->content);
		$xs->element('link', array('rel' => 'alternate',
                                   'href' => $this->bestUrl()));
		$xs->element('id', null, $this->uri);
		$xs->element('published', null, common_date_w3dtf($this->created));
		$xs->element('updated', null, common_date_w3dtf($this->modified));

		if ($this->reply_to) {
			$reply_notice = Notice::staticGet('id', $this->reply_to);
			if (!empty($reply_notice)) {
				$xs->element('link', array('rel' => 'related',
                                           'href' => $reply_notice->bestUrl()));
				$xs->element('thr:in-reply-to',
				array('ref' => $reply_notice->uri,
                                   'href' => $reply_notice->bestUrl()));
			}
		}

		$xs->element('content', array('type' => 'html'), $this->rendered);

		$tag = new Notice_tag();
		$tag->notice_id = $this->id;
		if ($tag->find()) {
			while ($tag->fetch()) {
				$xs->element('category', array('term' => $tag->tag));
			}
		}
		$tag->free();
		$xs->elementEnd('entry');
		return $xs->getString();
	}

	function bestUrl()
	{
		if (!empty($this->url)) {
			return $this->url;
		} else if (!empty($this->uri) && preg_match('/^https?:/', $this->uri)) {
			return $this->uri;
		} else {
			return common_path('discussionlist/' .$this->id);
		}
	}

	//重中之重, 缓存id以逆序排序, 去掉area_type, topic_type?
	function stream($fn, $args, $cachekey, $offset=0, $limit=20, $since_id=0,
						$max_id=0, $since=null, $content_type=0, $area_type=0, $topic_type=0)
	{
		$cache = common_memcache();

		//如果为空, 以某一个位置开始, 直接查询, 而不用缓存, 因为这个一般是随机的, 缓存无意义
		if (empty($cache) ||
		$since_id != 0 || $max_id != 0 || (!is_null($since) && $since > 0) ||
		is_null($limit) ||
		($offset + $limit) > NOTICE_CACHE_WINDOW) {
			return call_user_func_array($fn, array_merge($args, array($offset, $limit, $since_id,
													$max_id, $since, $content_type, $area_type, $topic_type)));
		}

		$idkey = common_cache_key($cachekey);
		$idstr = $cache->get($idkey);

		//如果缓存存在, 直接获得获得id列表, 为一串字符串, 以,隔开的
		if (!empty($idstr)) {
			// Cache hit! Woohoo!
			$window = explode(',', $idstr);
			$ids = array_slice($window, $offset, $limit);
			return $ids;
		}
		
		//如果不是通过删除清除的缓存, 可以把新增加的id查找出来, 重新加在前面 , 缓冲窗为61, 可以查3列, 已经很多了
		$laststr = $cache->get($idkey.';last');
		if (!empty($laststr)) {
			$window = explode(',', $laststr);
			$last_id = $window[0];
			//获取时间窗口更新的id
			$new_ids = call_user_func_array($fn, array_merge($args, array(0, NOTICE_CACHE_WINDOW,
															$last_id, 0, null,
															$content_type, $area_type, $topic_type)));

			$new_window = array_merge($new_ids, $window);

			$new_windowstr = implode(',', $new_window);

			$result = $cache->set($idkey, $new_windowstr);
			$result = $cache->set($idkey . ';last', $new_windowstr);

			$ids = array_slice($new_window, $offset, $limit);

			return $ids;
		}

		//如果普通缓存及;last缓存都不存在, 则直接查找, 重新组装字符串
		$window = call_user_func_array($fn, array_merge($args, array(0, NOTICE_CACHE_WINDOW,
														0, 0, null,
														$content_type, $area_type, $topic_type)));

		$windowstr = implode(',', $window);

		$result = $cache->set($idkey, $windowstr);
		$result = $cache->set($idkey . ';last', $windowstr);

		$ids = array_slice($window, $offset, $limit);

		return $ids;
	}

	function streamForNoticeInbox($fn, $args, $cachekey, $offset=0, $limit=20, $since_id=0,
									$max_id=0, $content_type=0)
	{
		$cache = common_memcache();

		if (empty($cache) ||
		$since_id != 0 || $max_id != 0 || is_null($limit) ||
		($offset + $limit) > NOTICE_CACHE_WINDOW) {
			return call_user_func_array($fn, array_merge($args, array($offset, $limit, $since_id,
			$max_id, $content_type)));
		}

		$idkey = common_cache_key($cachekey);

		$idstr = $cache->get($idkey);

		if (!empty($idstr)) {
			// Cache hit! Woohoo!
			$window = explode(',', $idstr);
			$ids = array_slice($window, $offset, $limit);
			return $ids;
		}

		$laststr = $cache->get($idkey.';last');

		if (!empty($laststr)) {
			$window = explode(',', $laststr);
			$last_id = $window[0];
			$new_ids = call_user_func_array($fn, array_merge($args, array(0, NOTICE_CACHE_WINDOW,
			$last_id, 0, $content_type)));

			$new_window = array_merge($new_ids, $window);

			$new_windowstr = implode(',', $new_window);

			$result = $cache->set($idkey, $new_windowstr);
			$result = $cache->set($idkey . ';last', $new_windowstr);

			$ids = array_slice($new_window, $offset, $limit);

			return $ids;
		}

		$window = call_user_func_array($fn, array_merge($args, array(0, NOTICE_CACHE_WINDOW,
		0, 0, $content_type)));

		$windowstr = implode(',', $window);

		$result = $cache->set($idkey, $windowstr);
		$result = $cache->set($idkey . ';last', $windowstr);

		$ids = array_slice($window, $offset, $limit);

		return $ids;
	}

	/*
	 //发消息的用户排行, 先剔除删除及屏蔽的消息
	 SELECT user_id, sum( 1 ) AS noticenum
	 FROM `notice`
	 WHERE is_delete = 0 AND is_banned = 0
	 GROUP BY user_id
	 ORDER BY noticenum DESC
	 LIMIT 0 , 30
	 */

	static function getNoticeOrder($limit=20, $since=null) {
		$notice = new Notice();
		$notice->selectAdd(); // clears it
		$notice->selectAdd('user_id');
		$notice->selectAdd('sum( 1 ) as noticenum');

//		$notice->whereAdd('is_delete = 0');
		$notice->whereAdd('is_banned = 0');
		if (!is_null($since)) {
			$notice->whereAdd('created > \'' . date('Y-m-d H:i:s', $since) . '\'');
		}

		$notice->limit(0, $limit);
		$notice->groupBy('user_id');
		$notice->orderBy('noticenum DESC');
		$notices = array();
		if ($notice->find()) {
			while ($notice->fetch()) {
				$notices[] = array('id'=>$notice->user_id, 'num'=>$notice->noticenum);
			}
		}
		$notice->free();
		return $notices;
	}

	function heatOrderStream($limit=NOTICES_PER_PAGE)
	{
		$cur = time();
		$dt = strftime('%Y-%m-%d', $cur);
		//当天0:0:0的总秒数
		$today = strtotime($dt);
		//前一天
		$yesterday = $today - 3600*24;
		$cachestr = date('Y:m:d', $yesterday);

		$ids = Notice::stream(array('Notice', '_heatOrderStreamDirect'),
		array($yesterday, $today),
                              'notice:heatorder:'.$cachestr,
		0, $limit, 0, 0, null, 0);

		return Notice::getStreamByIds($ids);
	}

	function _heatOrderStreamDirect($before, $after, $offset=0, $limit=20, $since_id=0,
									$max_id=0, $since=null, $content_type=0, $area_type=0, 
									$topic_type=0)
	{
		$notice = new Notice();
		$notice->selectAdd(); // clears it
		$notice->selectAdd('id');
		
		$notice->whereAdd('is_banned = 0');
		$notice->whereAdd('created >= \'' . $before . '\'');
		$notice->whereAdd('created < \'' . $after . '\'');

		$notice->limit(0, $limit);
		$notice->orderBy('heat DESC');
		$ids = array();
		if ($notice->find()) {
			while ($notice->fetch()) {
				$ids[] = array('id'=>$notice->id);
			}
		}
		$notice->free();
		return $ids;
	}

	static function addDing($id) {
		$notice = Notice::staticGet('id',$id);
		$orig = clone($notice);
		$noeice->ding =$notice->ding + 1;
		if(!$notice->update($orig)) {
			common_log_db_error($notice, 'UPDATE', __FILE__);
	        return false;
		}
		if($notice->topic_type != 4){
			Notice_heat::addHeat($id,1);
		}
		
		$notice->free();	
	}
	
	function getRecentone($user_id)
	{
		$notice = new Notice();
		$sql = 'select id from notice where user_id = ' . $user_id .' AND is_banned = 0 ORDER BY modified DESC LIMIT 0,1';
		$notice->query($sql);
		if (! $notice->fetch()) {
			return false;
		}
		$id = $notice->id;
		$notice->free();
		$notice_detail = Notice::staticGet('id',$id);
//		common_debug($notice_detail->rendered);
		return $notice_detail;
	}
	//参考profile的count, cache
	static function getDingCount($id) {
		$notice = new Notice();
		$sql = 'select ding from notice where id = ' . $id;
		$notice->query($sql);
		$notice->fetch();
		$ding = $notice->ding;
		$notice->free();
		return $ding;
	}
	
	static function getDissCount($id) {
		$notice = new Notice();
		$sql = 'select discussion_num from notice where id = ' . $id;
		$notice->query($sql);
		$notice->fetch();
		$discussion_num = $notice->discussion_num;
		$notice->free();
		return $discussion_num;
	}
	
	/*
	 //顶排行, 先剔除删除及屏蔽的消息
	 SELECT user_id, sum( ding ) AS dingnum
	 FROM `notice`
	 WHERE is_delete = 0 AND is_banned = 0
	 GROUP BY user_id
	 having dingnum > 0
	 ORDER BY dingnum DESC
	 LIMIT 0 , 30
	 */
	static function getDingOrder($limit=20, $since=null) {
		$notice = new Notice();
		$notice->selectAdd(); // clears it
		$notice->selectAdd('user_id');
		$notice->selectAdd('sum( ding ) as dingnum');

//		$notice->whereAdd('is_delete = 0');
		$notice->whereAdd('is_banned = 0');
		if (!is_null($since)) {
			$notice->whereAdd('created > \'' . date('Y-m-d H:i:s', $since) . '\'');
		}

		$notice->limit(0, $limit);
		$notice->groupBy('user_id');
		$notice->having('dingnum > 0');
		$notice->orderBy('dingnum DESC');
		$notices = array();
		if ($notice->find()) {
			while ($notice->fetch()) {
				$notices[] = array('id'=>$notice->user_id, 'num'=>$notice->dingnum);
			}
		}
		$notice->free();
		return $notices;
	}
	
	//获取根据转载排行的消息id列表
	function getRetweetNoticeOrder($limit=20, $game_id = null, $server_id = null, $since=null) {
		$notice = new Notice();
		$notice->selectAdd(); // clears it
		$notice->selectAdd('id');

//		$notice->whereAdd('is_delete = 0');
		$notice->whereAdd('is_banned = 0');
		$notice->whereAdd('topic_type != 4');
//		if (!is_null($since)) {
//			$notice->whereAdd('created > \'' . date('Y-m-d H:i:s', $since) . '\'');
//		}
		if(!is_null($game_id)) {
			$notice->whereAdd('user_id in (select id from user where game_id = '.$game_id.')');
		}
		if(!is_null($server_id)) {
			$notice->whereAdd('user_id in (select id from user where game_server_id = '.$server_id.')');
		}
		$notice->limit(0, $limit);
		$notice->orderBy('retweet_num DESC');
		$ids = array();
		if ($notice->find()) {
			while ($notice->fetch()) {
				$ids[] = $notice->id;
			}
		}
		$notice->free();
		return $ids;
	}
	
//获取根据转载排行的消息id列表
	function getDissNoticeOrder($limit=20, $game_id = null, $server_id = null,$since=null) {
		$notice = new Notice();
		$notice->selectAdd(); // clears it
		$notice->selectAdd('id');

//		$notice->whereAdd('is_delete = 0');
		$notice->whereAdd('is_banned = 0');
		$notice->whereAdd('topic_type != 4');
//		if (!is_null($since)) {
//			$notice->whereAdd('created > \'' . date('Y-m-d H:i:s', $since) . '\'');
//		}
		if(!is_null($game_id)) {
			$notice->whereAdd('user_id in (select id from user where game_id = '.$game_id.')');
		}
		if(!is_null($server_id)) {
			$notice->whereAdd('user_id in (select id from user where game_server_id = '.$server_id.')');
		}
		$notice->limit(0, $limit);
		$notice->orderBy('discussion_num DESC');
		$ids = array();
		if ($notice->find()) {
			while ($notice->fetch()) {
				$ids[] = $notice->id;
			}
		}
		$notice->free();
		return $ids;
	}
	
	static function getUseridsByContentTypeCount($content_type=0, $limit=10)
	{
		$notice = new Notice();
		$notice->selectAdd(); // clears it
		$notice->selectAdd('user_id');
		$notice->selectAdd('count(*) as noticenum');
		$notice->whereAdd('is_banned = 0');
		$notice->whereAdd('content_type = '. $content_type);

		$notice->limit(0, $limit);
		$notice->groupBy('user_id');
		$notice->orderBy('noticenum DESC');
		$notices = array();
		if ($notice->find()) {
			while ($notice->fetch()) {
				$notices[] = array('user_id'=>$notice->user_id, 'num'=>$notice->noticenum);
			}
		}
		$notice->free();
		return $notices;
	}
	
	function getRepliesuname($reply_to_uname=null) {
        // extract all @messages
        $cnt = preg_match_all('/(?:^|\s)@([A-Za-z0-9\x{4e00}-\x{9fa5}]{1,64})/eu', $this->content, $match);
        $names = array();
        if ($cnt) {
            $names = array_unique($match[1]);
        }

		$uname = array();
		
		for ($i=0; $i<count($names); $i++) {
			$nickname = $names[$i];
			$getuname = User::getUnameByNickname($nickname);
			if(!is_null($getuname)) 
				$uname[] = $getuname;
			else 
				continue;
		}
		
		if(!is_null($reply_to_uname)) {
			if(!in_array($reply_to_uname, $uname))
				$uname[] = $reply_to_uname;
		}

		return $uname;
	}

	function saveReplies($unames=null)
	{
		$sender = User::staticGet($this->user_id);
		$replied = array();
		
		if($unames) {
			for ($i=0; $i<count($unames); $i++) {
				$uname = $unames[$i];
				$recipient = User::relativeUser($sender, strtolower($uname), $this->created);
				if (!$recipient) {
					continue;
				}
				
				// Don't save replies from blocked profile to local user
				$recipient_user = User::staticGet('id', $recipient->id);
				if ($recipient_user && $recipient_user->hasBlocked($sender)) {
					continue;
				}
		
				$recipient_user->blowReplyCount();
				
				$reply = new Reply();
				$reply->notice_id = $this->id;
				$reply->user_id = $recipient->id;
				$reply->modified = common_sql_now();
				$reply->sender_id = $this->user_id;
				$id = $reply->insert();
				if (!$id) {
					$last_error = &PEAR::getStaticProperty('DB_DataObject','lastError');
					common_log(LOG_ERR, 'DB error inserting reply: ' . $last_error->message);
					return;
				} else {
					$replied[$recipient->id] = 1;
					$reply->blowReplyNumCache();
				}
			}
		}

		// Hash format replies, too
		$cnt = preg_match_all('/(?:^|\s)@#([A-Za-z0-9\x{4e00}-\x{9fa5}]{1,64})/u', $this->content, $match);
		if ($cnt) {
			foreach ($match[1] as $tag) {
				$tagged = Tagtions::getTagged($sender->id, $tag);
				foreach ($tagged as $t) {
					if (!$replied[$t->id]) {
						// Don't save replies from blocked profile to local user
						$t_user = User::staticGet('id', $t->id);
						if ($t_user && $t_user->hasBlocked($sender)) {
							continue;
						}
						$reply = new Reply();
						$reply->notice_id = $this->id;
						$reply->user_id = $t->id;
						$reply->sender_id = $sender->id;
						$id = $reply->insert();
						if (!$id) {
							common_log_db_error($reply, 'INSERT', __FILE__);
							return;
						} else {
							$replied[$recipient->id] = 1;
						}
					}
				}
			}
		}

		foreach (array_keys($replied) as $recipient) {
			$user = User::staticGet('id', $recipient);
			if ($user) {
				mail_notify_attn($sender, $user->getProfile(), $this);
			}
		}
	}
	
    /**
     * Determine which notice, if any, a new notice is in reply to.
     *
     * For conversation tracking, we try to see where this notice fits
     * in the tree. Rough algorithm is:
     *
     * if (reply_to is set and valid) {
     *     return reply_to;
     * } else if ((source not API or Web) and (content starts with "T NAME" or "@name ")) {
     *     return ID of last notice by initial @name in content;
     * }
     *
     * Note that all @uname instances will still be used to save "reply" records,
     * so the notice shows up in the mentioned users' "replies" tab.
     *
     * @param integer $reply_to   ID passed in by Web or API
     * @param integer $user_id ID of author
     * @param string  $source     Source tag, like 'web' or 'gwibber'
     * @param string  $content    Final notice content
     *
     * @return integer ID of replied-to notice, or null for not a reply.
     */

    static function getReplyTo($reply_to, $user_id, $source, $content)
    {
    	//这些不能设置reply_to
        static $lb = array('xmpp', 'mail', 'sms');

        // If $reply_to is specified, we check that it exists, and then
        // return it if it does

        if (!empty($reply_to)) {
            $reply_notice = Notice::staticGet('id', $reply_to);
            if (!empty($reply_notice)) {
                return $reply_to;
            }
        }

        // If it's not a "low bandwidth" source (one where you can't set
        // a reply_to argument), we return. This is mostly web and API
        // clients.

        //如果在Web及API客户端没有设置, 默认返回, 不是回复
        if (!in_array($source, $lb)) {
            return null;
        }

        // Is there an initial @ or T?

        //nickname
        //preg_match('/^T ([A-Z0-9\x{4e00}-\x{9fa5}]{1,64})/eu', $content, $match) ||            
        if (preg_match('/^@([A-Za-z0-9\x{4e00}-\x{9fa5}]{1,64})\s+/eu', $content, $match)) {
            $nickname = strtolower($match[1]);
        } else {
            return null;
        }

        // Figure out who that is.
    	//先查uname, 再查关注我/我关注的人的nickname, 再全局查nickname
    	$sender = User::staticGet('id', $user_id);
    	
    	$getuname = User::getUnameByNickname($nickname);
    	if($getuname) 
    		$recipient = User::relativeUser($sender, strtolower($getuname), common_sql_now());
    	else 
    		return null;

        if (empty($recipient)) {
            return null;
        }

        // Get their last notice
        $last = $recipient->getCurrentNotice();

        if (!empty($last)) {
            return $last->id;
        }
    }

	static function maxContent()
	{
		$contentlimit = common_config('notice', 'contentlimit');
		// null => use global limit (distinct from 0!)
		if (is_null($contentlimit)) {
			$contentlimit = common_config('site', 'textlimit');
		}
		return $contentlimit;
	}

	static function contentTooLong($content)
	{
		$contentlimit = self::maxContent();
		return ($contentlimit > 0 && !empty($content) && (mb_strlen($content) > $contentlimit));
	}
	
	//游戏的tag, 服务器的tag, 平台的tag
	//需要详细设计, first_tag, second_tag没同时出现
	//去掉缓存, 及时清理
    static function getTaggedNotices($offset=0, $limit=NOTICES_PER_PAGE, $since_id=0, 
    			$max_id=0, $since=null, $content_type=0, $first_tag=0, $game_id=0, $server_id=0, $second_tag=0)
    {
        if($game_id > 0 && $second_tag == 0 && $content_type == 0 && $first_tag == 0) {
        	$ids = Notice::stream(array('Notice', '_taggedNotices'),
                              array($first_tag, $game_id, $server_id, $second_tag),
                              'taggednotices:game:' . $game_id,
                              $offset, $limit, $since_id, $max_id, $since, 
                              $content_type);
            // , $area_type, $topic_type        	
        } else if($server_id > 0 && $second_tag == 0 && $content_type == 0 && $first_tag == 0) {
        	$ids = Notice::stream(array('Notice', '_taggedNotices'),
                              array($first_tag, $game_id, $server_id, $second_tag),
                              'taggednotices:server:' . $server_id,
                              $offset, $limit, $since_id, $max_id, $since, 
                              $content_type);
            // , $area_type, $topic_type          	
        } else {
        	$ids = Notice::_taggedNotices($first_tag, $game_id, $server_id, $second_tag, $offset, 
        			$limit, $since_id, $max_id, $since, 
        			$content_type);
        	// , $area_type, $topic_type  
        }

        return Notice::getStreamByIds($ids);
    }
    
	//加缓存
 	function _taggedNotices($first_tag=0, $game_id=0, $server_id=0, $second_tag=0, $offset=0, $limit=NOTICES_PER_PAGE, $since_id=0, 
    			$max_id=0, $since=null, $content_type=0)
    {
        $notice = new Notice();

        if($game_id > 0)
        	$query = "select notice.id from notice join user on user_id=user.id where user.game_id = ". $game_id . 
        			"  and topic_type != 4 and notice.is_banned = 0 and EXISTS (SELECT * from game_server where notice.server_id = game_server.id and " .
        			'game_big_zone_id in (select game_big_zone.id from game_big_zone where game_id = ' . $game_id . ')) ';
        else if($server_id > 0)
        	$query = "select notice.id from notice join user on user_id=user.id where user.game_server_id = ". $server_id . 
        		" and topic_type != 4 and notice.is_banned = 0 and notice.server_id = " . $server_id . ' ';
        else
        	$query = "select notice.id from notice where topic_type != 4 and notice.is_banned = 0 ";
          
         if ($first_tag > 0)
        	$query .= 'and EXISTS (SELECT * from notice_tag where notice.id=notice_id ' .
        	'and second_tag_id in (select id from second_tag where first_tag_id=' . $first_tag . ')) '; 
		
        if($second_tag > 0)
			$query .= 'and EXISTS (SELECT * from notice_tag where notice.id=notice_id ' .
        	'and second_tag_id =' . $second_tag. ') '; 
			
        if ($since_id != 0) {
            $query .= " and notice.id > $since_id ";
        }

        if ($max_id != 0) {
            $query .= " and notice.id < $max_id ";
        }
        
        if ($content_type != 0) {
        	$query .= ' and content_type =  ' . $content_type . ' ';
        }
        
        $query .= ' and reply_only = 0';

        if (!is_null($since)) {
            $query .= " and created > '" . date('Y-m-d H:i:s', $since) . "' ";
        }
        
        $query .= ' order by notice.id DESC ';

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
    
	static function getFirstTaggedNotices($offset=0, $limit=NOTICES_PER_PAGE, $since_id=0, 
    			$max_id=0, $since=null, $content_type=0, $first_tag=0, $game_id=0, $server_id=0)
    {
        if($game_id > 0) {
        	$ids = Notice::stream(array('Notice', '_firsttaggedNotices'),
                              array($first_tag, $game_id, $server_id),
                              'firsttaggednotices:game:' . $game_id,
                              $offset, $limit, $since_id, $max_id, $since, 
                              $content_type);
            // , $area_type, $topic_type        	
        } else if($server_id > 0) {
        	$ids = Notice::stream(array('Notice', '_firsttaggedNotices'),
                              array($first_tag, $game_id, $server_id),
                              'firsttaggednotices:server:' . $server_id,
                              $offset, $limit, $since_id, $max_id, $since, 
                              $content_type);
            // , $area_type, $topic_type          	
        } else {
        	$ids = Notice::_firsttaggedNotices($first_tag, $game_id, $server_id, $offset, 
        			$limit, $since_id, $max_id, $since, 
        			$content_type);
        	// , $area_type, $topic_type  
        }

        return Notice::getStreamByIds($ids);
    }
    
	//加缓存
 	function _firsttaggedNotices($first_tag=0, $game_id=0, $server_id=0, $offset=0, $limit=NOTICES_PER_PAGE, $since_id=0, 
    			$max_id=0, $since=null, $content_type=0, $area_type=0, $topic_type=0)
    {
        $notice = new Notice();

        if($game_id > 0)
        	$query = "select notice.id from notice join user on user_id=user.id where user.game_id = ". $game_id . 
        			"  and topic_type != 4 and notice.is_banned = 0 ";
        else if($server_id > 0)
        	$query = "select notice.id from notice join user on user_id=user.id where user.game_server_id = ". $server_id . " and topic_type != 4 and notice.is_banned = 0 ";
        else
        	$query = "select notice.id from notice where topic_type != 4 and notice.is_banned = 0 ";
          
         if ($first_tag > 0)
        	$query .= 'and EXISTS (SELECT * from notice_tag where notice.id=notice_id ' .
        	'and second_tag_id in (select id from second_tag where first_tag_id%10=' . $first_tag . '))'; 
			
        if ($since_id != 0) {
            $query .= " and notice.id > $since_id";
        }

        if ($max_id != 0) {
            $query .= " and notice.id < $max_id";
        }
        
        if ($content_type != 0) {
        	$query .= ' and content_type =  ' . $content_type;
        }

        if (!is_null($since)) {
            $query .= " and created > '" . date('Y-m-d H:i:s', $since) . "'";
        }
        
        $query .= ' order by notice.id DESC';

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
    
	static function getSecondTaggedNotices($offset=0, $limit=NOTICES_PER_PAGE, $since_id=0, 
    			$max_id=0, $since=null, $content_type=0, $second_tag_name='求助', $game_id=0, $server_id=0)
    {
        if($game_id > 0) {
        	$ids = Notice::stream(array('Notice', '_sectaggedNotices'),
                              array($second_tag_name, $game_id, $server_id),
                              'sectaggednotices:game:' . $game_id,
                              $offset, $limit, $since_id, $max_id, $since, 
                              $content_type);
            // , $area_type, $topic_type        	
        } else if($server_id > 0) {
        	$ids = Notice::stream(array('Notice', '_sectaggedNotices'),
                              array($second_tag_name, $game_id, $server_id),
                              'sectaggednotices:server:' . $server_id,
                              $offset, $limit, $since_id, $max_id, $since, 
                              $content_type);
            // , $area_type, $topic_type          	
        } else {
        	$ids = Notice::_sectaggedNotices($second_tag_name, $game_id, $server_id, $offset, 
        			$limit, $since_id, $max_id, $since, 
        			$content_type);
        	// , $area_type, $topic_type  
        }

        return Notice::getStreamByIds($ids);
    }
    
	//加缓存
 	function _sectaggedNotices($second_tag_name='求助', $game_id=0, $server_id=0, $offset=0, $limit=NOTICES_PER_PAGE, $since_id=0, 
    			$max_id=0, $since=null, $content_type=0, $area_type=0, $topic_type=0)
    {
        $notice = new Notice();

        if($game_id > 0)
        	$query = "select notice.id from notice join user on user_id=user.id where user.game_id = ". $game_id . 
        			"  and topic_type != 4 and notice.is_banned = 0 ";
        else if($server_id > 0)
        	$query = "select notice.id from notice join user on user_id=user.id where user.game_server_id = ". $server_id . " and topic_type != 4 and notice.is_banned = 0 ";
        else
        	$query = "select notice.id from notice where topic_type != 4 and notice.is_banned = 0 ";
          
         if ($second_tag_name != '')
        	$query .= 'and EXISTS (SELECT * from notice_tag where notice.id=notice_id ' .
        	'and second_tag_id in (select id from second_tag where second_tag.name="' . $second_tag_name. '"))'; 

        if ($since_id != 0) {
            $query .= " and id > $since_id";
        }

        if ($max_id != 0) {
            $query .= " and id < $max_id";
        }
        
        if ($content_type != 0) {
            $query .= ' and content_type = ' . $content_type;
        }
        
        $query .= ' and is_banned = 0';

        if (!is_null($since)) {
            $query .= " and created > '" . date('Y-m-d H:i:s', $since) . "'";
        }
        
        $query .= ' order by id DESC';

        if (!is_null($offset)) {
            $query .= " limit $offset, $limit";
        }
//        common_debug($query);
        $notice->query($query);
        $ids = array();
        while ($notice->fetch()) {
            $ids[] = $notice->id;
        }
        return $ids;
    }
    
    function getVideoTop20()
    {
 		$query = "SELECT user_id,count(*)AS num FROM notice WHERE content_type=3 AND topic_type != 4 AND notice.is_banned = 0 GROUP BY user_id ORDER BY num DESC LIMIT 0,100";
 		
 		$notice = new Notice();
    	$notice->query($query);
    	
    	$pops = array();
    	while ($notice->fetch()) {
    		$pops[] = $notice->user_id;
    	}
    	$notice->free();
    	
    	$pops = common_random_fetch($pops,20);
    	
    	return $pops;
    }
    
	function getPicTop20()
    {
 		$query = "SELECT user_id,count(*)AS num FROM notice WHERE content_type=4 AND topic_type != 4 AND notice.is_banned = 0 GROUP BY user_id ORDER BY num DESC LIMIT 0,100";
 		
 		$notice = new Notice();
    	$notice->query($query);
    	
    	$pops = array();
    	while ($notice->fetch()) {
    		$pops[] = $notice->user_id;
    	}
    	$notice->free();
    	
    	$pops = common_random_fetch($pops,20);
    	
    	return $pops;
    }
    
	function getTextTop20()
    {
 		$query = "SELECT user_id,count(*)AS num FROM notice WHERE content_type=1 AND topic_type != 4 AND notice.is_banned = 0 GROUP BY user_id ORDER BY num DESC LIMIT 0,100";
 		
 		$notice = new Notice();
    	$notice->query($query);
    	
    	$pops = array();
    	while ($notice->fetch()) {
    		$pops[] = $notice->user_id;
    	}
    	$notice->free();
    	
    	$pops = common_random_fetch($pops,20);
    	
    	return $pops;
    }
    
    function getMosttalkUsers($limit=10, $area='all',$neededid=null)
    {
    	$cur = time();
		$dt = strftime('%Y-%m-%d', $cur);
		//当天0:0:0的总秒数
		$today = strtotime($dt);

		$someday = $today - 3600*48;
		$time = date('Y-m-d H:i:s', $someday);
		
    	$notice = new Notice();
    	$qry = 'select user_id, count(*) as num from notice where created > "'.$time.'" and user_id != '.
    	    common_config('newuser', 'default_id');
    	if($neededid)
    	{
    		if($area == 'game')
       			$qry .= ' and user_id IN (select id from profile where completeness > 80 and is_vip = 0 and email not like "%@lshai.com" and game_id = '.$neededid.')';
        	else if($area == 'gameserver')
        		$qry .= ' and user_id IN (select id from profile where completeness > 80 and is_vip = 0 and email not like "%@lshai.com" and game_server_id = '.$neededid.')';
       	}
        else $qry .= ' and user_id IN (select id from profile where completeness > 80 and is_vip = 0 and email not like "%@lshai.com")';
    	
    	$qry .=' group by user_id ORDER BY num DESC LIMIT 0,'. $limit;
   // 	common_debug($qry);
    	$notice->query($qry);
		$mosttalkusers = array();
		while ($notice->fetch()) {
			$mosttalkusers[] = array('user_id'=>$notice->user_id,'noticenum'=>$notice->num);
		}
		$notice->free();
		return $mosttalkusers;
    }
    
    function getNoticenumbyGameandctype($content_type = 1,$game_id = '')
    {
    	$qry = 'SELECT count(*) as num FROM notice WHERE content_type='. $content_type. ' AND user_id in (SELECT id FROM user WHERE game_id=' .$game_id. ' )';
    	
    	$notice = new Notice();
    	$notice->query($qry);
    	$notice->fetch();
    	return $notice->num;
    }
    
	function getRetweetNum($user_id,$while='week') {
		switch($while)
		{//有待进一步确定
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

		$notice = new Notice();
		$query = "SELECT sum(1) AS num FROM notice a,notice b WHERE a.user_id = ".$user_id." and a.id=b.retweet_from and b.modified >"."\"".$time."\"";

		$notice->query($query);
		$notice->fetch();
		$num = $notice->num?$notice->num:0;
		$notice->free();
		return $num; //return update num
	}
	function getNoticeidsWithnodiss($user_id, $limit)
	{
		$notice = new Notice();
		$notice->selectAdd();
		$notice->selectAdd('id');
		$notice->whereAdd('discussion_num = 0');
 		$notice->whereAdd('topic_type != 4');
 		if ($user_id) {
 			$notice->whereAdd('user_id <> '.$user_id );
 		}
 		$notice->whereAdd('is_banned = 0');
 		//XXX：现在的消息的质量过滤条件是长度大于20；
		$notice->whereAdd('LENGTH(content) > 20');
 		$notice->orderBy('created desc');
 		$notice->limit(0, $limit);
 		
    	$notice->find();
		$ids = array();
		while($notice->fetch())
		{
			$ids[] = $notice->id;
		}
		$notice->free();
		
		return $ids;
	}
	
	static function getLatestVipNoticeIds($limit = false) {
		return common_stream('notice:latestvipnoticeids:' . $limit, array("Notice", "_getLatestVipNoticeIds"), array($limit), 3600);
	}
	
	static function _getLatestVipNoticeIds($limit) {
		$notice = new Notice();
		
		$profile = new Profile();
		$notice->joinAdd($profile);
		
		$notice->selectAdd();
		$notice->selectAdd('notice.id as nid');
		$notice->whereAdd('profile.is_vip = 1');
		$notice->orderBy('notice.created desc');
		if ($limit) {
			$notice->limit(0, $limit);
		}
		
		$notice->find();
		
		$nids = array();
		while ($notice->fetch()) {
			$nids[] = $notice->nid;
		}
		return $nids;
	}
	
	static function getOptimizedPublicTimeline($limit) {
		$notice = new Notice();
		
		$profile = new Profile();
		$notice->joinAdd($profile);
		
		$notice->selectAdd();
		
		$notice->selectAdd('notice.id as nid');
		$notice->whereAdd('notice.content_type = 1');
		$notice->whereAdd('notice.is_banned = 0');
		$notice->whereAdd('notice.reply_only = 0');
		$notice->whereAdd('retweet_from = 0');
		$notice->whereAdd('(profile.is_vip = 1 or profile.id < 100200 or profile.id in (100671, 100495, 101939, 106024))');
		$notice->orderBy('notice.created desc');
		if ($limit) {
			$notice->limit(0, $limit);
		}
		
		$notice->find();
		
		$nids = array();
		while ($notice->fetch()) {
			$nids[] = $notice->nid;
		}
		return $nids;
	}
	
	static function getYesterdayNotices() {
		$n = new Notice();
		$n->limit(0, 500);
		$n->orderBy('id desc');
		$n->find();
		
		return $n;
	}
	
	static function getLatestRetweetIds($offset = 0, $limit = 20, $gameid = 0) {
		$n = new Notice();
		$n->selectAdd();
		$n->selectAdd('distinct retweet_from');
		$n->whereAdd('retweet_from <> 0');
		$n->whereAdd('is_banned = 0');
		$n->whereAdd('reply_only = 0');
		$n->whereAdd('topic_type != 4');
		if ($gameid > 0) {
			$server_begin = $gameid * 100000;
    		$server_end = ($gameid + 1) * 100000;
    		$n->whereAdd('retweet_from IN ( SELECT notice.id FROM notice WHERE server_id > ' . $server_begin. 
    			' AND server_id < ' . $server_end . ')');
		}
		$n->orderBy('id desc');
		$n->limit($offset, $limit);
		$n->find();
		
		$ids = array();
		while ($n->fetch()) {
			$ids[] = $n->retweet_from;
		}
		
		return $ids;
	}
	
	static function getLatestDiscussionIds($offset = 0, $limit = 20, $gameid = 0) {
		$d = new Discussion();
		$d->selectAdd();
		$d->selectAdd('distinct notice_id');
		if ($gameid == 0) {
			$notice_server = '';
		} else {
			$notice_server = ' and server_id > ' . $gameid * 100000 . ' and server_id < ' . ($gameid + 1) * 100000;
		}
		$d->whereAdd('notice_id in (select id from notice where topic_type <> 4 and is_banned = 0 and reply_only = 0' . $notice_server . ')');
		$d->orderBy('id desc');
		$d->limit($offset, $limit);
		$d->find();
		
		$ids = array();
		while ($d->fetch()) {
			$ids[] = $d->notice_id;	
		}
		
		return $ids;
	}

	static function getLatestIds($offset = 0, $limit = 6, $gameid = 0) 
	{
		return self::_publicStreamDirect(0, $offset, $limit, 0, 0, null, 0, 0, 4, $gameid);
	}
	
	static function getLatestMusics($offset = 0, $limit = 6, $gameid = 0) 
	{
		return self::_publicStreamDirect(0, $offset, $limit, 0, 0, null, 2, 0, 4, $gameid);
	}
	
	static function getLatestVideos($offset = 0, $limit = 6, $gameid = 0) 
	{
		return self::_publicStreamDirect(0, $offset, $limit, 0, 0, null, 3, 0, 4, $gameid);
	}
	
	static function getLatestPictures($offset = 0, $limit = 6, $gameid = 0) 
	{
		return self::_publicStreamDirect(0, $offset, $limit, 0, 0, null, 4, 0, 4, $gameid);
	}
	
	//普通输入消息, 对一起一些字符及命令进行解析
	static function renderContent($text, $notice, $uname=null)
	{
		require_once INSTALLDIR . '/lib/renderhelper.php';
		
	    $r = htmlspecialchars($text);

	    //\x十六进制, 对字符为[\0-\8\11-\12\14-\25]取代为空
	    $r = preg_replace('/[\x{0}-\x{8}\x{b}-\x{c}\x{e}-\x{19}]/', '', $r);
	    $r = common_replace_urls_callback($r, 'common_linkify');
	    $r = preg_replace('/[\[【]([\sA-Za-z0-9_\-\《\》\.\x{4e00}-\x{9fa5}]{1,10})[\]】]/eu', "'['.common_tag_link('\\1', $notice->user_id).']'", $r);
	    
	    $id = $notice->user_id;
	    $r = preg_replace('/(^|\s+)@([A-Za-z0-9\x{4e00}-\x{9fa5}]{1,64})/eu', "'\\1@'.common_at_link($id, '\\2')", $r);
	    $r = preg_replace('/^T ([A-Z0-9\x{4e00}-\x{9fa5}]{1,64})/eu', "'T '.common_at_link($id, '\\1').' '", $r);
	    $r = preg_replace('/(^|[\s\.\,\:\;]+)@#([A-Za-z0-9\x{4e00}-\x{9fa5}]{1,64})/eu', "'\\1@#'.common_at_hash_link($id, '\\2')", $r);
		$r = preg_replace('/(^|[\s\.\,\:\;]+)(!|！)([A-Za-z0-9\x{4e00}-\x{9fa5}]{1,64})/eu', "common_group_link($id, '\\3')", $r);
	    // 做表情替换
	    $emotions = common_config('site', 'emotions');
	    foreach ($emotions as $e) {
	    	$xs = new XMLStringer();
	    	$xs->element('img', array('src' => $e['src'], 'title' => $e['text']));
	    	$replacement = $xs->getString();
	    	$r = preg_replace('/(\:'. $e['text'] . '\:)/u', $replacement, $r);
	    }
	    return $r;
	}

}
