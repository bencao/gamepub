<?php
/**
 * Table Definition for avatar
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Avatar extends Memcached_DataObject
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'avatar';                          // table name
    public $user_id;                      // int(4)  primary_key not_null
    public $original;                        // tinyint(1)
    public $width;                           // int(4)  primary_key not_null
    public $height;                          // int(4)  primary_key not_null
    public $mediatype;                       // varchar(32)   not_null
    public $filename;                        // varchar(255)
    public $url;                             // varchar(255)  unique_key
    public $created;                         // datetime()   not_null
    public $modified;                        // timestamp()   not_null default_CURRENT_TIMESTAMP

    /* Static get */
    function staticGet($k,$v=null)
    { return Memcached_DataObject::staticGet('Avatar',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    # We clean up the file, too

    //定义, avatar配置下, 以file为根目录, $filename=10/00/00/default, 要传整个路径过来
    
    function delete()
    {
        $filename = $this->filename;
        if (parent::delete()) {
            @unlink(Avatar::path($filename, Avatar::subpath($this->user_id)));
        }
    }

    function &pkeyGet($kv)
    {
        return Memcached_DataObject::pkeyGet('Avatar', $kv);
    }

    // where should the avatar go for this user?

    static function filename($id, $extension, $size=null, $extra=null)
    {
        if ($size) {
            return $id . '-' . $size . (($extra) ? ('-' . $extra) : '') . $extension;
        } else {
            return $id . '-original' . (($extra) ? ('-' . $extra) : '') . $extension;
        }
    }

    static function path($filename, $subpath='10/00/00/default/')
    {
        $dir = common_config('avatar', 'dir');

        if ($dir[strlen($dir)-1] != '/') {
            $dir .= '/';
        }

        return $dir . $subpath . $filename;
    }
    
    static function filepath($filename)
    {
        $dir = common_config('avatar', 'dir');

        if ($dir[strlen($dir)-1] != '/') {
            $dir .= '/';
        }

        return $dir . $filename;
    }

    static function url($filename, $subpath='10/00/00/default/')
    {
        $path = common_config('avatar', 'path');

        if ($path[strlen($path)-1] != '/') {
            $path .= '/';
        }

        if ($path[0] != '/') {
            $path = '/'.$path;
        }

        $server = common_config('avatar', 'server');

        if (empty($server)) {
            $server = common_config('site', 'server');
        }

        // XXX: protocol

        return 'http://'.$server.$path.$subpath.$filename;
    }

    function displayUrl()
    {
        $server = common_config('avatar', 'server');
        if ($server) {
            return Avatar::url($this->filename, Avatar::subpath($this->user_id));
        } else {
            return $this->url;
        }
    }

    static function defaultImage($size, $id = 100000, $sex = 'M')
    {
        static $sizenames = array(AVATAR_PROFILE_SIZE => 'profile',
                                  AVATAR_STREAM_SIZE => 'stream',
                                  AVATAR_MINI_SIZE => 'mini');
        $seq = $id % 13;
        if ($seq > 10) {
        	return theme_path('a-' . $seq . $sex . '-' . $sizenames[$size] . '.jpg');	
        } else {
        	return theme_path('a-' . $seq . '-' . $sizenames[$size] . '.jpg');
        }
        
    }
    
    // path for profile avatar
    static function subpath($id) 
    {
    	return Avatar::divideByTwo($id) . 'default/';
    }
    
	static function csssubpath($id) 
    {
    	return self::ensureExist('file/' . Avatar::divideByTwo($id) . 'css/');
    }
    
	static function flashsubpath($id) 
    {
    	return self::ensureExist('file/' . Avatar::divideByTwo($id) . 'flash/');
    }
    
	static function dealsubpath($id) 
    {
    	return self::ensureExist('file/' . Avatar::divideByTwo($id) . 'deal/');
    }
    
    static function tmpsubpath($id) {
    	return self::ensureExist('file/tmp/' . Avatar::divideByTwo($id));
    }
    
    // path for group logo
    static function groupsubpath($id)
    {
    	return 'group/' . Avatar::divideByTwo($id) . 'default/';
    }
    
	static function groupcsssubpath($id) 
    {
    	return self::ensureExist('file/group/' . Avatar::divideByTwo($id) . 'css/');
    }
    
	static function divideByTwo($id) {
	    	$strid = '' . $id;
	    	$l = strlen($strid);
	    	$result = '';
	    	for ($i = 0; $i < $l; $i += 2) {
	    		$result .= substr($strid, $i, 2) . '/';
	    	}
	    	return $result;
	}
	
	static function ensureExist($path) {
		if (! file_exists($path)) {
			mkdir($path, 0777, true);
		}
		return $path;
	}
}
