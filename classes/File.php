<?php
/*
 * LShai - a distributed microblogging tool
 */

if (!defined('SHAISHAI')) { exit(1); }

require_once INSTALLDIR.'/classes/Memcached_DataObject.php';
require_once INSTALLDIR.'/classes/File_redirection.php';
require_once INSTALLDIR.'/classes/File_oembed.php';
require_once INSTALLDIR.'/classes/File_thumbnail.php';
require_once INSTALLDIR.'/classes/File_to_post.php';
//require_once INSTALLDIR.'/classes/File_redirection.php';

/**
 * Table Definition for file
 */

class File extends Memcached_DataObject
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'file';                            // table name
    public $id;                              // int(4)  primary_key not_null
    public $url;                             // varchar(255)  unique_key
    public $mimetype;                        // varchar(50)
    public $size;                            // int(4)
    public $title;                           // varchar(255)
    public $date;                            // int(4)
    public $protected;                       // int(4)
    public $filename;                        // varchar(255)
    public $modified;                        // timestamp()   not_null default_CURRENT_TIMESTAMP

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('File',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function isProtected($url) {
        return 'http://www.facebook.com/login.php' === $url;
    }

    function getAttachments($post_id) {
        $query = "select file.* from file join file_to_post on (file_id = file.id) join notice on (post_id = notice.id) where post_id = " . $this->escape($post_id);
        $this->query($query);
        $att = array();
        while ($this->fetch()) {
            $att[] = clone($this);
        }
        $this->free();
        return $att;
    }

    function saveNew($redir_data, $given_url) {
        $x = new File;
        $x->url = $given_url;
        if (!empty($redir_data['protected'])) $x->protected = $redir_data['protected'];
        if (!empty($redir_data['title'])) $x->title = $redir_data['title'];
        if (!empty($redir_data['type'])) $x->mimetype = $redir_data['type'];
        if (!empty($redir_data['size'])) $x->size = intval($redir_data['size']);
        if (isset($redir_data['time']) && $redir_data['time'] > 0) $x->date = intval($redir_data['time']);
        $file_id = $x->insert();

        if (isset($redir_data['type'])
            && (('text/html' === substr($redir_data['type'], 0, 9) || 'application/xhtml+xml' === substr($redir_data['type'], 0, 21)))
            && ($oembed_data = File_oembed::_getOembed($given_url))
            && isset($oembed_data['json'])) {
            File_oembed::saveNew($oembed_data['json'], $file_id);
        }
        
        return $x;
    }

    function processNew($given_url, $notice_id) {
        if (empty($given_url)) return -1;   // error, no url to process
        $given_url = File_redirection::_canonUrl($given_url);
//        common_debug($given_url);
        if (empty($given_url)) return -1;   // error, no url to process
        $file = File::staticGet('url', $given_url);
        if (empty($file)) {
            $file_redir = File_redirection::staticGet('url', $given_url);
            if (empty($file_redir)) {
//                common_debug("processNew() '$given_url' not a known redirect.\n");
                $redir_data = File_redirection::where($given_url);
                if (is_array($redir_data)) {
                    $redir_url = $redir_data['url'];
                } elseif (is_string($redir_data)) {
                    $redir_url = $redir_data;
                } else {
                    throw new ServerException("Can't process url '$given_url'");
                }
                
                if ($redir_url === $given_url || strlen($redir_url) > 255) {
                    $x = File::saveNew($redir_data, $given_url);
                    $file_id = $x->id;
                } else {
                    $x = File::processNew($redir_url, $notice_id);
                    $file_id = $x->id;
                    File_redirection::saveNew($redir_data, $file_id, $given_url);
                }
            } else {
                $file_id = $file_redir->file_id;
            }
        } else {
            $file_id = $file->id;
            $x = $file;
        }

        if (empty($x)) {
            $x = File::staticGet($file_id);
            if (empty($x)) die('Impossible!');
        }

        File_to_post::processNew($file_id, $notice_id);
        return $x;
    }

    //月度,年度配额以后可以加上去
    function isRespectsQuota($user,$fileSize) {
        if ($fileSize > common_config('attachments', 'file_quota')) {
            return sprintf('我们现在最大只支持1M的图片文件, 您现在的文件大于1M.');
        }

//        $query = "select sum(size) as total from file join file_to_post on file_to_post.file_id = file.id join notice on file_to_post.post_id = notice.id where user_id = {$user->id} and file.url like '%/notice/%/file'";
//        $this->query($query);
//        $this->fetch();
//        $total = $this->total + $fileSize;
//        if ($total > common_config('attachments', 'user_quota')) {
//            return sprintf('文件太大, 超出了用户%d字节的配额.', common_config('attachments', 'user_quota'));
//        }
//
//        $query .= ' month(modified) = month(now()) and year(modified) = year(now())';
//        $this->query($query);
//        $this->fetch();
//        $total = $this->total + $fileSize;
//        if ($total > common_config('attachments', 'monthly_quota')) {
//            return sprintf('文件太大, 超出了您%d字节的月度配额.', common_config('attachments', 'monthly_quota'));
//        }

        return true;
    }

    static function path($filename, $subpath)
    {
        $dir = common_config('attachments', 'dir');

        if ($dir[strlen($dir)-1] != '/') {
            $dir .= '/';
        }

        return $dir . $subpath . $filename;
    }
    
    static function dir($subpath)
    {
        $dir = common_config('attachments', 'dir');

        if ($dir[strlen($dir)-1] != '/') {
            $dir .= '/';
        }

        return $dir . $subpath;
    }

    static function url($filename, $subpath)
    {
        $path = common_config('attachments', 'path');

        if ($path[strlen($path)-1] != '/') {
            $path .= '/';
        }

        if ($path[0] != '/') {
            $path = '/'.$path;
        }

        $server = common_config('attachments', 'server');

        if (empty($server)) {
            $server = common_config('site', 'server');
        }

        // XXX: protocol

        return 'http://'.$server.$path.$subpath.$filename;
    }
    
    static function pathFromUrl($url)
    {
    	$path = common_config('attachments', 'path');

        if ($path[strlen($path)-1] != '/') {
            $path .= '/';
        }

        if ($path[0] != '/') {
            $path = '/'.$path;
        }

        $server = common_config('attachments', 'server');

        if (empty($server)) {
            $server = common_config('site', 'server');
        }

        $domain = 'http://'.$server.$path;
		if (strrpos($url, $domain) != false) {
			return str_replace($domain, '', $url);
		} else {
			return null;
		}
    } 

    function isEnclosure(){
        if(isset($this->filename)){
            return true;
        }
        $notEnclosureMimeTypes = array('text/html','application/xhtml+xml');
        $mimetype = strtolower($this->mimetype);
        $semicolon = strpos($mimetype,';');
        if($semicolon){
            $mimetype = substr($mimetype,0,$semicolon);
        }
        return(! in_array($mimetype,$notEnclosureMimeTypes));
    }
    
    static function monthSubpath($id) 
    {
    	$month = strftime('%Y%m', time());
    	$subpath = substr($id, 0, 2). '/' .substr($id, 2, 2). '/' .substr($id, 4, 2). '/'. $month . '/';
        Imagefile::mkdirs(self::dir($subpath));
        return $subpath;
    }
    
    static function photoRandomName($ext)
    {
    	$datestamp = strftime('%Y%m%dT%H%M%S', time());
        $random = strtolower(common_confirmation_code(32));
        //只需32位随机 $uname-$datestamp-
        return "$datestamp-$random.$ext";
    }
}

