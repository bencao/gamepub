<?php
/*
 * LShai - a distributed microblogging tool
 */

if (!defined('SHAISHAI')) { exit(1); }

require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

/**
 * Table Definition for file_thumbnail
 */

class File_thumbnail extends Memcached_DataObject
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'file_thumbnail';                  // table name
    public $file_id;                         // int(4)  primary_key not_null
    public $url;                             // varchar(255)  unique_key
    public $width;                           // int(4)
    public $height;                          // int(4)
    public $modified;                        // timestamp()   not_null default_CURRENT_TIMESTAMP

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('File_thumbnail',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function sequenceKey()
    {
        return array(false, false, false);
    }

    function saveNew($data, $file_id) {
        $tn = new File_thumbnail;
        $tn->file_id = $file_id;
        $tn->url = $data['thumbnail_url'];
        $tn->width = intval($data['thumbnail_width']);
        $tn->height = intval($data['thumbnail_height']);
        $tn->insert();
    }
}

