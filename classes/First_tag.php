<?php
/**
 * Table Definition for first_tag
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class First_tag extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'first_tag';                       // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $name;                            // string(255)  multiple_key
    public $game_id;

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('First_tag',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    function getUniformFirstTags()
    {
    	return array(9993 => '交流', 9995 => '秀场', 9997 => '游戏');
    }
    
    function getFirstTags($game_id)
    {
    	return common_stream('firsttag:tag:' . $game_id, array("First_tag", "_getFirstTags"), array($game_id), 24 * 3600);
    } 
    
    function _getFirstTags($game_id, $offset=0, $limit=null)
    {
        $qry =
          'SELECT * ' .
          'FROM first_tag  where name != "自定义" and game_id = ' . $game_id . ' ORDER BY id ASC ';

        if ($offset) {
            if (common_config('db','type') == 'pgsql') {
                $qry .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
            } else {
                $qry .= ' LIMIT ' . $offset . ', ' . $limit;
            }
        }

        $ft = new First_tag();
        $cnt = $ft->query($qry);
        
        $fts = array();
        
        while ($ft->fetch()) {
            $fts[$ft->id] = $ft->name;
        }
        $ft->free();
        unset($ft);
        return $fts;
    }    
}
