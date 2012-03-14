<?php
/**
 * Table Definition for group_block
 *
 * LShai - a distributed LShai microblogging tool
 */

if (!defined('SHAISHAI')) { exit(1); }

require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Group_block extends Memcached_DataObject
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'group_block';                     // table name
    public $group_id;                        // int(4)  primary_key not_null
    public $blocked;                         // int(4)  primary_key not_null
    public $blocker;                         // int(4)   not_null
    public $modified;                        // timestamp()   not_null default_CURRENT_TIMESTAMP

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Group_block',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function &pkeyGet($kv)
    {
        return Memcached_DataObject::pkeyGet('Group_block', $kv);
    }

    static function isBlocked($group, $user)
    {
        $block = Group_block::pkeyGet(array('group_id' => $group->id,
                                            'blocked' => $user->id));
        return !empty($block);
    }

    static function blockUser($group, $user, $blocker)
    {
        // Insert the block

        $block = new Group_block();

        $block->query('BEGIN');

        $block->group_id = $group->id;
        $block->blocked  = $user->id;
        $block->blocker  = $blocker->id;

        $result = $block->insert();

        if (!$result) {
            common_log_db_error($block, 'INSERT', __FILE__);
            return null;
        }

        // Delete membership if any

        $member = new Group_member();

        $member->group_id   = $group->id;
        $member->user_id = $user->id;

        if ($member->find()) {
            $result = $member->delete();
            if (!$result) {
                common_log_db_error($member, 'DELETE', __FILE__);
                return null;
            }
        }

        // Commit, since both have been done

        $block->query('COMMIT');

        return $block;
    }

    static function unblockUser($group, $user)
    {
        $block = Group_block::pkeyGet(array('group_id' => $group->id,
                                            'blocked' => $user->id));

        if (empty($block)) {
            return null;
        }

        $result = $block->delete();

        if (!$result) {
            common_log_db_error($block, 'DELETE', __FILE__);
            return null;
        }

        return true;
    }

}
