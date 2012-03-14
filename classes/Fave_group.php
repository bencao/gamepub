<?php
/**
 * Table Definition for fave
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Fave_group extends Memcached_DataObject
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'fave_group';      	// table name
    public $id;                       		// int(4)  primary_key not_null
    public $name;							// varchar(20) not null
    public $user_id;                        // int(4)  primary_key not_null
    public $created;						// datetime()   not_null
    public $modified;                       // timestamp()   not_null default_CURRENT_TIMESTAMP

    /* Static get */
    function staticGet($k,$v=null)
    { return Memcached_DataObject::staticGet('Fave_group',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    //default = 'GamePub夹';
    //本文件不用缓存
    static function addNew($user, $name) {
        $faveGroup = new Fave_group();
        $faveGroup->user_id = $user->id;
        $faveGroup->name = $name;
        $faveGroup->created = common_sql_now();
        if (!$faveGroup->insert()) {
            common_log_db_error($fave_group, 'INSERT', __FILE__);
            return false;
        }
        return $faveGroup;
    }

    function &pkeyGet($kv)
    {
        return Memcached_DataObject::pkeyGet('Fave_group', $kv);
    }

    static function getFaveGroup($user_id, $favegroupName=null)
    {
    	$faveGroup = new Fave_group();
    	$qry = null;
       	if(!is_null($favegroupName)) {
            $qry  = 'SELECT * FROM fave_group ';
            $qry .= 'WHERE user_id = ' . $user_id . ' and name = \'' . $favegroupName . '\' ';
        } else {
        	$qry  = 'SELECT * FROM fave_group ';
            $qry .= 'WHERE user_id = ' . $user_id . ' ';
        }
       
        $qry .= 'ORDER BY created asc ';

        $faveGroup->query($qry);

        $faveGroups = array();

        while ($faveGroup->fetch()) {
            $faveGroups[] = clone($faveGroup);
        }

        //common_debug($faveGroups);
        
        $faveGroup->free();
        //new ArrayWrapper($retweets);
        return $faveGroups; 
    }
    
    static function renameFaveGroup($id, $favegroupName)
    {
    	$faveGroup = new Fave_group();
    	$qry = null;

       	$qry =  'update fave_group set name = \'' . $favegroupName . '\' ' .
       			'WHERE id = ' . $id;

       	$faveGroup->query($qry);
        $faveGroup->free();
        unset($faveGroup);
    }
    
    //删除收藏夹及其内容
    static function deleteFaveGroup($id)
    {
    	
    	$faveGroup = new Fave_group();
    	
    	$faveGroup->query('BEGIN');
    	
    	//先删除此收藏夹里面的消息
    	Fave::deleteFaves($id);

       	$qry =  'delete from fave_group where id = ' . $id;

       	$faveGroup->query($qry);
       	
       	$faveGroup->query('COMMIT');
       	
        $faveGroup->free();
        unset($faveGroup);
    }
    
    //建立的缓存, 应当是fave:ids_by_user:fave_group, 是以favegroup来查询的
    function blowFavesCache()
    {
        $cache = common_memcache();
        if ($cache) {
            $cache->delete(common_cache_key('fave:ids_by_fave_group:'.$this->id));
            $cache->delete(common_cache_key('fave:ids_by_fave_group:'.$this->id.';last'));
        }
    }
}
