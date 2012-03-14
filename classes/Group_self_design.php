<?php
/**
 * Table Definition for group_self_design
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Group_self_design extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'group_self_design';               // table name
    public $group_id;                        // int(11)  multiple_key
    public $design_id;                       // int(11)  

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Group_self_design',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
 	function deleteDesign($group_id, $design_id) {
 		
    	$gsd = new Group_self_design();
    	$gsd->design_id = $design_id;
    	$gsd->group_id = $group_id;
    	$gsd->query('BEGIN');
    	
    	if (! $gsd->delete()) {
    		$gsd->query('ROLLBACK');
    		return false;
    	}
    	
    	$design = new Design();
    	$design->id = $design_id;
    	if (! $design->delete()) {
    		$gsd->query('ROLLBACK');
    		return false;
    	}
    	
    	// 当删除的是当前群组正在使用的皮肤时，
    	// 将当前群组皮肤设置为群主所玩游戏的默认皮肤
    	$group = User_group::staticGet('id', $group_id);
    	$game = Game::staticGet('id', $group->getOwner()->game_id);
    	
    	if ($group->design_id == $design_id) {
    		$orig = clone($group);
    		$group->design_id = $game->design_id;
    		$group->update($orig);
    	}
    	
    	$gsd->query('COMMIT');
    	
    	return true;
    }
    
    function _getDesignIdsByGroupId($group_id) {
    	$gsd = new Group_self_design();
    	$gsd->whereAdd('group_id = ' . $group_id);
    	$gsd->find();
    	
    	$ids = array();
    	while ($gsd->fetch()) {
    		$ids[] = $gsd->design_id;
    	}
    	$gsd->free();
    	
    	return $ids;
    }
    
    function getDesignsByGroup($group) {
    	$d = new Design();
    	$d->whereAdd('id in (' . implode(',', self::_getDesignIdsByGroupId($group->id)) . ')');
    	
    	$d->find();
    	
    	return $d;
    }
    
    function saveNew($group, $design) {
    	$gsd = new self();
	    $gsd->design_id = $design->id;
	    $gsd->group_id = $group->id;
	    $result = $gsd->insert();
	    
	    if (! $result) {
	    	 common_log_db_error($gsd, 'INSERT', __FILE__);
	    	 return false;
	    }
	    return true;
	    
    }
    
    function removeDesign()
    {
        $design = new Design();

        $design->id = $this->design_id;

        if ($design->find()) {
            while ($design->fetch()) {
                $design->delete();
            }
        }
        $design->free();
    }
}
