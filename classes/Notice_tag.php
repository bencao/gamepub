<?php
/*
 * LShai - a distributed microblogging tool
 */

require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Notice_tag extends Memcached_DataObject
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'notice_tag';                      // table name
    public $notice_id;                       // int(4)  primary_key not_null
    public $second_tag_id;				 // int(4)
    public $created;                         // datetime()   not_null

    /* Static get */
    function staticGet($k,$v=null)
    { return Memcached_DataObject::staticGet('Notice_tag',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    static function getStream($second_tag_id, $offset=0, $limit=20) {

        $ids = Notice::stream(array('Notice_tag', '_streamDirect'),
                              array($second_tag_id),
                              'notice_tag:notice_ids:' . $second_tag_id,
                              $offset, $limit);

        return Notice::getStreamByIds($ids);
    }

    function _streamDirect($second_tag_id, $offset, $limit, $since_id, 
    		$max_id, $since, $content_type, $area_type, $topic_type)
    {
        $nt = new Notice_tag();

        $nt->selectAdd();
        $nt->selectAdd('notice_id');

        $nt->whereAdd('second_tag_id = ' . $second_tag_id);
        
        if ($since_id != 0) {
            $nt->whereAdd('notice_id > ' . $since_id);
        }

        if ($max_id != 0) {
            $nt->whereAdd('notice_id < ' . $max_id);
        }

        if (!is_null($since)) {
            $nt->whereAdd('created > \'' . date('Y-m-d H:i:s', $since) . '\'');
        }

        $nt->orderBy('notice_id DESC');

        if (!is_null($offset)) {
            $nt->limit($offset, $limit);
        }

        $ids = array();

        if ($nt->find()) {
            while ($nt->fetch()) {
                $ids[] = $nt->notice_id;
            }
        }

        return $ids;
    }
    
	static function getGroupStream($second_tag_id, $group_id, $offset=0, $limit=20) {

        $ids = Notice::stream(array('Notice_tag', '_streamGroupDirect'),
                              array($second_tag_id, $group_id),
                              'notice_tag:notice_ids:' . $second_tag_id.':'.$group_id,
                              $offset, $limit);

        return Notice::getStreamByIds($ids);
    }

    function _streamGroupDirect($second_tag_id, $group_id, $offset, $limit, $since_id, $max_id, $since)
    {
        $nt = new Notice_tag();
        $gi = new Group_inbox();

        $nt->joinAdd($gi);
        $nt->selectAdd();
        $nt->selectAdd('notice_tag.notice_id');

        $nt->whereAdd('notice_tag.second_tag_id = ' . $second_tag_id);
        $nt->whereAdd('group_inbox.group_id = ' . $group_id);
        
        if ($since_id != 0) {
            $nt->whereAdd('notice_tag.notice_id > ' . $since_id);
        }

        if ($max_id != 0) {
            $nt->whereAdd('notice_tag.notice_id < ' . $max_id);
        }

        if (!is_null($since)) {
            $nt->whereAdd('notice_tag.created > \'' . date('Y-m-d H:i:s', $since) . '\'');
        }

        $nt->orderBy('notice_tag.notice_id DESC');

        if (!is_null($offset)) {
            $nt->limit($offset, $limit);
        }

        $ids = array();

        if ($nt->find()) {
            while ($nt->fetch()) {
                $ids[] = $nt->notice_id;
            }
        }

        return $ids;
    }

    function blowCache($blowLast=false)
    {
        $cache = common_memcache();
        if ($cache) {
            $idkey = common_cache_key('notice_tag:notice_ids:' . $this->second_tag_id);
            $cache->delete($idkey);
            if ($blowLast) {
                $cache->delete($idkey.';last');
            }
        }
    }

    function &pkeyGet($kv)
    {
        return Memcached_DataObject::pkeyGet('Notice_tag', $kv);
    }
}
