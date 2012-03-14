<?php 
/**
 * Table Definition for link
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Music_history extends Memcached_DataObject
{
	public $__table = 'music_history';                            // table name
	public $id;
	public $user_id;
	public $notice_id;
	public $created;

	/* Static get */
	function staticGet($k,$v=null)
	{ return Memcached_DataObject::staticGet('Music_history',$k,$v); }
	
	function saveNew($uid, $nid) {
//		$m = new Music_history();
//		$m->whereAdd('user_id = ' . $uid);
//		$m->whereAdd('notice_id = ' . $nid);
//		$m->find();
//		
//		if ($m->N > 0) {
//			// 已存在
//			return false;
//		}
//		
		$mh = new Music_history();
		$mh->user_id = $uid;
		$mh->notice_id = $nid;
		$mh->created = common_sql_now();
		$mh->insert();
		return true;
	}
	
	function deleteMusic($uid, $nid) {
		$m = new Music_history();
		$m->whereAdd('user_id = ' . $uid);
		$m->whereAdd('notice_id = ' . $nid);
		$m->find();
		
		if ($m->N == 0) {
			// 不存在
			return false;
		}
		
		$mh = new Music_history();
		$mh->user_id = $uid;
		$mh->notice_id = $nid;
		$mh->delete();
		return true;
	}
	
	function getRecentMusicNotices($uid, $offset, $limit) {
		$m = new Music_history();
		$m->whereAdd('user_id = ' . $uid);
		$m->orderBy('id desc');
		$m->limit($offset, $limit);
		$m->find();
		
		if ($m->N == 0) {
			// 不存在
			return false;
		}
		
		$notices = array();
		// prevent duplicate
		$displayed = array();
		while ($m->fetch()) {
			// 重复的歌曲仅显示一次
			if (! in_array($m->notice_id, $displayed)) {
				$notices[] = Notice::staticGet('id', $m->notice_id);
				$displayed[] = $m->notice_id;
			}
		}
		$m->free();
		
		return $notices;
	}
	
}

?>