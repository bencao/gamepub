<?php
/**
 * Table Definition for fave
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Fave extends Memcached_DataObject
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'fave';                            // table name
    public $notice_id;                       // int(4)  primary_key not_null
    public $user_id;                         // int(4)  primary_key not_null
    public $modified;                        // timestamp()   not_null default_CURRENT_TIMESTAMP
	public $favegroup_id;					 // foreign reference fave_group
	
    /* Static get */
    function staticGet($k,$v=null)
    { return Memcached_DataObject::staticGet('Fave',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    static function addNew($user, $notice, $favegroupId) {
        $fave = new Fave();
        $fave->query('BEGIN');
        $fave->user_id = $user->id;
        $fave->notice_id = $notice->id;
        $fave->favegroup_id = $favegroupId;
        if (!$fave->insert()) {
            common_log_db_error($fave, 'INSERT', __FILE__);
            return false;
        }
        
        $fave_notice = Notice::staticGet('id', $notice->id);
//        //评论增加热度4
//        $origFave = clone($fave_notice);
//    	$fave_notice->heat += 4;
//    	$fave_notice->update($origFave);
    	
    	//$notice_heat = new Notice_heat();	
    	if($fave_notice->topic_type != 4)	{
    	Notice_heat::addHeat($fave_notice->id,3);
    	}
		$fave->query('COMMIT');
		
    	//blow
    	$favgroup = Fave_group::staticGet('id', $favegroupId);
    	$favgroup->blowFavesCache();
    	    	
        return $fave;
    }

    function &pkeyGet($kv)
    {
        return Memcached_DataObject::pkeyGet('Fave', $kv);
    }

    //在查看别人的收藏时用到, 也是建立了缓存, 在新建及删除时使用
    function stream($user_id, $offset=0, $limit=NOTICES_PER_PAGE, $own=false)
    {
        $ids = Notice::stream(array('Fave', '_streamDirect'),
                              array($user_id, $own),
                              ($own) ? 'fave:ids_by_user_own:'.$user_id :
                              'fave:ids_by_user:'.$user_id,
                              $offset, $limit);
        return $ids;
    }

    function _streamDirect($user_id, $own, $offset, $limit, $since_id, 
    		$max_id, $since, $content_type, $area_type, $topic_type)
    {
        $fav = new Fave();
        $qry = null;

        if ($own) {
             $qry =  'SELECT fave.* FROM fave ';
             $qry .= 'INNER JOIN notice ON fave.notice_id = notice.id ';
             $qry .= 'WHERE fave.user_id = ' . $user_id . ' ';
             $qry .= 'AND notice.is_banned = 0 '; //AND notice.is_delete =  0 
      	} else {
             $qry =  'SELECT fave.* FROM fave ';
             $qry .= 'INNER JOIN notice ON fave.notice_id = notice.id ';
             $qry .= 'WHERE fave.user_id = ' . $user_id . ' ';
//             $qry .= 'AND notice.is_local != ' . NOTICE_GATEWAY . ' ';
             $qry .= 'AND notice.is_banned = 0 '; //AND notice.is_delete =  0 
        }

        if ($since_id != 0) {
            $qry .= 'AND notice_id > ' . $since_id . ' ';
        }

        if ($max_id != 0) {
            $qry .= 'AND notice_id <= ' . $max_id . ' ';
        }

        if (!is_null($since)) {
            $qry .= 'AND modified > \'' . date('Y-m-d H:i:s', $since) . '\' ';
        }

        // NOTE: we sort by fave time, not by notice time!

        $qry .= 'ORDER BY modified DESC ';

        if (!is_null($offset)) {
            $qry .= "LIMIT $offset, $limit";
        }

        $fav->query($qry);

        $ids = array();

        while ($fav->fetch()) {
            $ids[] = $fav->notice_id;
        }

        $fav->free();
        unset($fav);

        return $ids;
    }
    
    //删除此收藏夹内的所有的收藏
    static function deleteFaves($favegroupid)
    {
    	$favgroup = Fave_group::staticGet('id', $favegroupid);
    	
    	$deffavgroup = new Fave_group();
    	$deffavgroup->whereAdd("name = '我的收藏'");
    	$deffavgroup->whereAdd("user_id = " . $favgroup->user_id);
    	$deffavgroup->find();
    	
    	$hasDef = $deffavgroup->fetch();
    	if ($hasDef && $favegroupid != $deffavgroup->id) {
	    	$fav = new Fave();
	       	$qry = 'update fave set favegroup_id = ' . $deffavgroup->id . ' WHERE favegroup_id = ' . $favegroupid;
	       	$fav->query($qry);
	        $fav->free();
	        
    		$deffavgroup->blowFavesCache(true);
    	} else {
    		$fav = new Fave();
	       	$qry = 'delete from WHERE favegroup_id = ' . $favegroupid;
	       	$fav->query($qry);
	        $fav->free();
    	}
        
        $favgroup->blowFavesCache(true);
        
        unset($fav);	
    }
    
    //而是对此建立缓存, 添加删除都要清理缓存
    static function getFaveGroupById($favegroupid, $offset, $limit=NOTICES_PER_PAGE)
    {
        $ids = Notice::stream(array('Fave', '_streamFaveGroup'),
                              array($favegroupid),
                              'fave:ids_by_fave_group:'.$favegroupid,
                              $offset, $limit);
        
        return Notice::getStreamByIds($ids); 	
    }
    
    function _streamFaveGroup($favegroupid, $offset, $limit, $since_id, 
    		$max_id, $since, $content_type=0, $area_type=0, $topic_type=0)
    {
    	$fav = new Fave();
        $qry = null;
       	
        $qry =  'select notice_id from fave WHERE favegroup_id = ' . $favegroupid;
       	
    	if ($since_id != 0) {
            $qry .= ' AND notice_id > ' . $since_id;
        }

        if ($max_id != 0) {
            $qry .= ' AND notice_id <= ' . $max_id;
        }

        if (!is_null($since)) {
            $qry .= ' AND modified > \'' . date('Y-m-d H:i:s', $since) . '\'';
        }
        
        $qry .= ' ORDER BY modified DESC';
        
       	if (!is_null($offset)) {
            $qry .= " LIMIT $offset, $limit";
        }

        $fav->query($qry);

        $ids = array();

        while ($fav->fetch()) {
            $ids[] = $fav->notice_id;
        }

        $fav->free();
        unset($fav);
        return $ids;
    }
}
