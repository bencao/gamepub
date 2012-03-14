<?php
/**
 * LShai, the distributed microblogging tool
 *
 * URL routing utilities
 *
 * PHP version 5
 *
 * @category  URL
 * @package   LShai
 * @link      http://www.lshai.com
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once 'Net/URL/Mapper.php';

/**
 * URL Router
 *
 * Cheap wrapper around Net_URL_Mapper
 *
 * @category URL
 * @package  LShai
 */

class Router
{
    var $m = null;
    static $inst = null;
    static $bare = array('requesttoken', 'accesstoken', 'userauthorization',
                         'postnotice', 'updateprofile');

    static function get()
    {
        if (!Router::$inst) {
        	// load from cache
            $cache = common_memcache();
			if ($cache) {
				$idkey = common_cache_key('router');
				
				$result = $cache->get($idkey);
				
				if ($result) {
					Router::$inst = $result;
				} else {
					Router::$inst = new Router();
					$cache->set($idkey, Router::$inst, 0, 3600);
				}
			} else {
            	Router::$inst = new Router();
			}
        }
        return Router::$inst;
    }

    function __construct()
    {
        if (!$this->m) {
            $this->m = $this->initialize();
        }
    }

    function initialize()
    {
        $m = Net_URL_Mapper::getInstance();

        Event::handle('StartRouterInitialize', array($m));
        
        // In the "root"
        $m->connect('yesterdaynotices', array('action' => 'yesterdaynotices'));
        $m->connect('shareoutlink', array('action' => 'shareoutlink'));
        
        $m->connect('public', array('action' => 'public'));
        $m->connect('public?filter_content=:filter_content', 
                    array('action' => 'public'), 
                    array('filter_content'=>'[0-9]'));
		$m->connect('public?tag=:tag',
                    array('action' => 'public'),
                    array('tag' => '[0-9]{1,10}')); 
                                  
        $m->connect('hotnotice', array('action' => 'hotnotice'));
//        $m->connect('hotnotice?type=:type', array('action' => 'hotnotice'),
//        			array('type' => '[a-z]{2,7}'));
        $m->connect('hotnotice/video', array('action' => 'hotnotice', 'type' => 'video'));
        $m->connect('hotnotice/music', array('action' => 'hotnotice', 'type' => 'music'));
        $m->connect('hotnotice/photo', array('action' => 'hotnotice', 'type' => 'photo'));
        $m->connect('hotnotice/text', array('action' => 'hotnotice', 'type' => 'text'));
        
        $m->connect('hottopics', array('action' => 'hottopics'));			
        $m->connect('hottopics?tag=:tag',
                    array('action' => 'hottopics'),
                    array('tag' => '[0-9]{1,10}'));
        $m->connect('hottopics/:name',
                    array('action' => 'hottopics'),
                    array('name' => '.+'));
        $m->connect('halloffame', array('action' => 'halloffame'));
        $m->connect('funnypeople',array('action' => 'funnypeople'));
        $m->connect('funnypeople?area=:area',array('action' => 'funnypeople'),
        			array('area' => '[0-9]'));
        $m->connect('funnypeople/all',array('action' => 'funnypeople','area' => 'all'));
        $m->connect('funnypeople/game',array('action' => 'funnypeople','area' => 'game'));
        $m->connect('funnypeople/gameserver',array('action' => 'funnypeople','area' => 'gameserver'));
        $m->connect('experiences',array('action' => 'experiences'));
        $m->connect('experiences?area=:area',array('action' => 'experiences'),
        			array('area' => '[0-9]'));
        $m->connect('experiences/all',array('action' => 'experiences','area' => 'all'));
        $m->connect('experiences/game',array('action' => 'experiences','area' => 'game'));
        $m->connect('experiences/gameserver',array('action' => 'experiences','area' => 'gameserver'));
//        $m->connect('requestforhelp',array('action' => 'requestforhelp'));
		$m->connect('wenda', array('action' => 'wenda'));
		$m->connect('wenda/new', array('action' => 'newwenda'));
		$m->connect('wenda/:qid', array('action' => 'showwenda'), array('qid' => '[0-9]+'));
        $m->connect('requestforhelp?area=:area',array('action' => 'requestforhelp'),
        			array('area' => '[0-9]'));
        $m->connect('requestforhelp/all',array('action' => 'requestforhelp','area' => 'all'));
        $m->connect('requestforhelp/game',array('action' => 'requestforhelp','area' => 'game'));
        $m->connect('requestforhelp/gameserver',array('action' => 'requestforhelp','area' => 'gameserver'));
        $m->connect('citypeople',array('action' => 'citypeople'));
        $m->connect('citypeople?area=:area',array('action' => 'citypeople'),
        			array('area' => '[0-9]'));
        $m->connect('citypeople/all',array('action' => 'citypeople','area' => 'all'));
        $m->connect('citypeople/game',array('action' => 'citypeople','area' => 'game'));
        $m->connect('citypeople/gameserver',array('action' => 'citypeople','area' => 'gameserver'));
        $m->connect('rank',array('action' => 'rank'));
        $m->connect('rank/game',array('action' => 'rank','type' => 'game'));
        $m->connect('rank/user',array('action' => 'rank','type' => 'user'));
        $m->connect('rank/retweet',array('action' => 'rank','type' => 'retweet'));
        $m->connect('rank/discuss',array('action' => 'rank','type' => 'discuss'));
        
        $m->connect('', array('action' => 'homepage2'));
        
        $m->connect('clients', array('action' => 'clients'));
        $m->connect('clients/:client', array('action' => 'clients'));
        
        $m->connect('mplayer/show', array('action' => 'showmplayer'));
        
        $m->connect('showtime', array('action' => 'showtime'));

        $m->connect('doc/:type/:title', array('action' => 'doc'));
        		
		$ajax = array('getsubscribe', 'getgroupsubscribe', 'urlcatch', 'readmorenotice','ifnotexistuser', 'ifexistuser', 'ifexistgroup',
				'randverifypic', 'getqqverifypic', 'favehidden', 'setreadreply', 'getserversbybzid',
				'updatesubscriptionstag', 'uploadfile', 'resendmail', 'edittaggroup','getallgames',
				'getgamebycategory', 'getbzsbygame', 'getdtbygame','getjobsbygame', 'getqqverifypic', 'getsecondtagbyfirsttag',
				'getunreadinfo', 'addmusic', 'delmusic', 'recentmusics', 'randommusics', 'favormusic', 'unfavormusic',
				'handlefloatbar', 'ignorereminds', 'ignoregroupreminds', 'getprizeuser', 'hofrandvip',
				'getthemecontent', 'getsubscription', 'increaseclientdown', 'publictimeline', 'publictopicnotice', 'increasegamewebclick',
				'answernew', 'answermodify', 'answerbest', 'answeruseful','recentnewstimeline', 'wendatimeline',
				'gamedealnew', 'gamedealclose', 'filtergamegroups');
		
		foreach ($ajax as $a) {
			$m->connect('ajax/'.$a, array('action' => $a));
		}

		$m->connect('register', array('action' => 'register'));
		$m->connect('register/invite', array('action' => 'inviteregister'));
		$m->connect('register/followsomepeople', array('action' => 'followsomepeople'));
        
        // main stuff is repetitive

        $main = array('login', 'logout', 'subscribe',
                      'unsubscribe', 'confirmaddress', 'recoverpassword',
                      'invite', 'block', 'unblock', 'illegalreport', 'userfeedback', 'sysmessage',
        			'reportstatus', 'tagother', 'invitewithpass',
        			'missionstep1', 'missionstep2', 'missionstep3', 'missionstep4', 'applyvip');
        
        foreach ($main as $a) {
            $m->connect('main/'.$a, array('action' => $a));
        }   
        
        $m->connect('main/missionlist/:mtype', array('action' => 'missionlist'),
                    array('mtype' => '.+'));
        
        $m->connect('main/renamefavorgroup/:id', array('action' => 'renamefavorgroup'),
                    array('id' => '[0-9]+'));
                    
        $m->connect('main/showfavorgroup/:id', array('action' => 'showfavorgroup'),
                    array('id' => '[0-9]+'));
                    
        $m->connect('main/deletefavorgroup/:id', array('action' => 'deletefavorgroup'),
                    array('id' => '[0-9]+'));

        // exceptional

        foreach (Router::$bare as $action) {
            $m->connect('index.php?action=' . $action, array('action' => $action));
        }

        // settings

        foreach (array('profile', 'avatar', 'password', 'email', 'design', 'interest', 'game', 'feed') as $s) {
            $m->connect('settings/'.$s, array('action' => $s.'settings'));
        }

        // search
        foreach (array('group', 'people', 'notice', 'wenda') as $s) {
            $m->connect('search/'.$s, array('action' => $s.'search'));
        }
        
        //flash
        $m->connect('flash/list', array('action' => 'flashgame'));
        $m->connect('flash/list/:cat', array('action' => 'flashgame'), array('cat' => '[a-z]+'));
        $m->connect('flash/upload', array('action' => 'flashupload'));
        $m->connect('flash/:id', array('action' => 'flashplay'), array('id' => '[0-9]+'));
        $m->connect('flash/mine', array('action' => 'flashmine'));
        $m->connect('flash/fullscreen', array('action' => 'flashfullscreen'));
        $m->connect('flash/admin', array('action' => 'flashadmin'));
        
        //share
        $m->connect('share/getvideo/:id', array('action' => 'getvideo'),
        				array('id' => '[0-9]+'));

       	$m->connect('uploadvideo', array('action' => 'uploadvideo'));
       	$m->connect('getvideostatus', array('action' => 'getvideostatus'));

        $m->connect('notice/new', array('action' => 'newnotice'));
        $m->connect('notice/replyat/:atuname',
                    array('action' => 'newnotice'),
                    array('atuname' => '[A-Za-z0-9_-]+'));            
                    
        $m->connect('notice/retweet', array('action' => 'newretweet'));            
        $m->connect('notice/favor', array('action' => 'favor'));            
        $m->connect('notice/disfavor', array('action' => 'disfavor'));                 
        $m->connect('notice/delete', array('action' => 'deletenotice'));
        
        $m->connect('discuss/new', array('action' => 'newdiscuss'));
        $m->connect('discuss/new?discussto=:discussto',
                    array('action' => 'newdiscuss'),
                    array('discussto' => '[A-Za-z0-9_-]+'));
		$m->connect('discuss/delete/:id',
                    array('action' => 'deletediscuss'),
                    array('id' => '[0-9]{6,10}'));
                    
        // conversation

        $m->connect('conversation/:id',
                    array('action' => 'conversation'),
                    array('id' => '[0-9]+'));

        $m->connect('message/new', array('action' => 'newmessage'));
        $m->connect('message/new?to=:to', array('action' => 'newmessage'), array('to' => '[A-Za-z0-9_-]+'));
        $m->connect('message/:message',
                    array('action' => 'showmessage'),
                    array('message' => '[0-9]+'));
        $m->connect('message/deleteinbox', array('action' => 'deleteinbox'));
        $m->connect('message/deleteoutbox', array('action' => 'deleteoutbox'));
                 
        
        //游戏专版
        $m->connect('game/:gameid', array('action' => 'recentnews'), array('gameid' => '[0-9]+'));
        $m->connect('game/:gameid/', array('action' => 'recentnews'), array('gameid' => '[0-9]+'));
        $m->connect('game/:gameid/friends',
                    array('action' => 'gamefriends'),
                    array('gameid' => '[0-9]+'));                    
        $m->connect('game/:gameid/search/people',
                    array('action' => 'gamepeopleserach'),
                    array('gameid' => '[0-9]+'));
        $m->connect('game/:gameid/webnav',
                    array('action' => 'gamewebnav'),
                    array('gameid' => '[0-9]+'));
        $m->connect('game/:gameid/webnew',
                    array('action' => 'gamewebnew'),
                    array('gameid' => '[0-9]+'));            
		$m->connect('game/:gameid/deal',
					array('action' => 'gamedeal'),
					array('gameid' => '[0-9]+')); 
		$m->connect('game/:gameid/experiences', 
					array('action' => 'gameexperiences'), 
					array('gameid' => '[0-9]+'));
        
        $m->connect('gameserver/:gameserverid',
                    array('action' => 'gameserver'),
                    array('gameserverid' => '[0-9]+'));
		$m->connect('gameserver/:gameserverid?filter_content=:filter_content',
                    array('action' => 'gameserver'),
                    array('gameserverid' => '[0-9]+', 'filter_content' => '[0-9]{1,2}'));                    
        $m->connect('gameserver/:gameserverid?tag=:tag',
                    array('action' => 'gameserver'),
                    array('gameserverid' => '[0-9]+', 'tag' => '[0-9]{1,10}'));     
       	$m->connect('gameserver/:gameserverid?second_tag=:second_tag',
                    array('action' => 'gameserver'),
                    array('gameserverids' => '[0-9]+', 'second_tag' => '[0-9]{1,10}')); 
		$m->connect('gameserver/:gameserverid?filter_content=:filter_content&second_tag=:second_tag',
                    array('action' => 'gameserver'),
                    array('gameserverid' => '[0-9]+', 'filter_content' => '[0-9]{1,2}',
                    		'second_tag' => '[0-9]{1,10}'));

        // groups
        foreach (array('members', 'invitation', 'logo', 'rss', 'designsettings', 'application', 
                       'acceptinvitation', 'rejectinvitation', 'approve', 
        			   'reject', 'updatecode', 'blacklist', 'makeadmin', 'canceladmin',
        			   'block', 'unblock', 'invite', 'batchinvite', 'updatecode',
        			   'edit', 'editpost', 'deletepost', 'leave', 'join', 'applyjoin') as $n) {
            $m->connect('group/:id/'.$n,
                        array('action' => 'group'.$n),
                        array('id' => '[0-9]+'));
        }
        $m->connect('group/:id/invitation/:newgroupok',
                        array('action' => 'groupinvitation'),
                        array('id' => '[0-9]+', 'newgroupok' => '[a-zA-Z0-9]+'));
        $m->connect('group/:id/tag/:tag',
                    array('action'=> 'grouptag'),
                    array('id' => '[0-9]+'),
                    array('tag' => '[a-zA-Z0-9\x{4e00}-\x{9fa5}]+'));

        $m->connect('group/:id',
                    array('action' => 'showgroup'),
                    array('id' => '[0-9]+'));
        $m->connect('group/:id?filter_content=:filter_content',
                    array('action' => 'showgroup'),
                    array('id' => '[0-9]+', 'filter_content' => '[0-9]{1,2}'));
		$m->connect('group/:id?tag=:tag',
                    array('action' => 'showgroup'),
                    array('id' => '[0-9]+', 'tag' => '[0-9]{1,10}'));
                    
        $m->connect('groups', array('action' => 'groups'));
        $m->connect('groups/new', array('action' => 'newgroup'));
        $m->connect('groups/life/new', array('action' => 'newlifegroup'));
        $m->connect('groups/game/new', array('action' => 'newgamegroup'));
        $m->connect('groups/game', array('action' => 'gamegroups'));
        $m->connect('groups/life', array('action' => 'lifegroups'));
        $m->connect('groups/audit', array('action' => 'auditgroups'));
        $m->connect('groups/audit/cancel', array('action' => 'auditgroupcancel'));
        $m->connect('groups/audit/:newgroupok', array('action' => 'auditgroups', 'newgroupok' => '[a-zA-Z0-9]+'));

         // Twitter-compatible API

        // statuses API

        $m->connect('api/statuses/public_timeline.:format',
                    array('action' => 'ApiTimelinePublic',
                    'format' => '(xml|json|rss|atom)'));

        $m->connect('api/statuses/friends_timeline.:format',
                    array('action' => 'ApiTimelineFriends',
                          'format' => '(xml|json|rss|atom)'));

        $m->connect('api/statuses/friends_timeline/:id.:format',
                    array('action' => 'ApiTimelineFriends',
                          'id' => '[a-zA-Z0-9]+',
                          'format' => '(xml|json|rss|atom)'));
        $m->connect('api/statuses/home_timeline.:format',
                    array('action' => 'ApiTimelineFriends',
                          'format' => '(xml|json|rss|atom)'));

        $m->connect('api/statuses/home_timeline/:id.:format',
                    array('action' => 'ApiTimelineFriends',
                          'id' => '[a-zA-Z0-9]+',
                          'format' => '(xml|json|rss|atom)'));

        $m->connect('api/statuses/user_timeline.:format',
                    array('action' => 'ApiTimelineUser',
                    'format' => '(xml|json|rss|atom)'));

        $m->connect('api/statuses/user_timeline/:id.:format',
                    array('action' => 'ApiTimelineUser',
                    'id' => '[a-zA-Z0-9]+',
                    'format' => '(xml|json|rss|atom)'));

        $m->connect('api/statuses/mentions.:format',
                    array('action' => 'ApiTimelineMentions',
                    'format' => '(xml|json|rss|atom)'));

        $m->connect('api/statuses/mentions/:id.:format',
                    array('action' => 'ApiTimelineMentions',
                    'id' => '[a-zA-Z0-9]+',
                    'format' => '(xml|json|rss|atom)'));

        $m->connect('api/statuses/replies.:format',
                    array('action' => 'ApiTimelineMentions',
                    'format' => '(xml|json|rss|atom)'));

        $m->connect('api/statuses/replies/:id.:format',
                    array('action' => 'ApiTimelineMentions',
                    'id' => '[a-zA-Z0-9]+',
                    'format' => '(xml|json|rss|atom)'));

        $m->connect('api/statuses/friends.:format',
                     array('action' => 'ApiUserFriends',
                           'format' => '(xml|json)'));

        $m->connect('api/statuses/friends/:id.:format',
                    array('action' => 'ApiUserFriends',
                    'id' => '[a-zA-Z0-9]+',
                    'format' => '(xml|json)'));

        $m->connect('api/statuses/followers.:format',
                     array('action' => 'ApiUserFollowers',
                           'format' => '(xml|json)'));

        $m->connect('api/statuses/followers/:id.:format',
                    array('action' => 'ApiUserFollowers',
                    'id' => '[a-zA-Z0-9]+',
                    'format' => '(xml|json)'));

        $m->connect('api/statuses/show.:format',
                    array('action' => 'ApiStatusesShow',
                          'format' => '(xml|json)'));

        $m->connect('api/statuses/show/:id.:format',
                    array('action' => 'ApiStatusesShow',
                          'id' => '[0-9]+',
                          'format' => '(xml|json)'));

        $m->connect('api/statuses/update.:format',
                    array('action' => 'ApiStatusesUpdate',
                          'format' => '(xml|json)'));

        $m->connect('api/statuses/destroy.:format',
                  array('action' => 'ApiStatusesDestroy',
                        'format' => '(xml|json)'));

        $m->connect('api/statuses/destroy/:id.:format',
                  array('action' => 'ApiStatusesDestroy',
                        'id' => '[0-9]+',
                        'format' => '(xml|json)'));
                  
        $m->connect('api/statuses/retweet.:format',
                  array('action' => 'ApiStatusesRetweet',
                        'format' => '(xml|json)'));
                  
		$m->connect('api/statuses/discuss.:format',
                  array('action' => 'ApiStatusesDiscuss',
                        'format' => '(xml|json)'));

        ///:id 'id' => '[0-9]+',
        $m->connect('api/statuses/discuss_list.:format',
                  array('action' => 'ApiStatusesDiscussList',                  		
                        'format' => '(xml|json)'));
                           
        // users

        $m->connect('api/users/show/:id.:format',
                    array('action' => 'ApiUserShow',
                          'id' => '[a-zA-Z0-9]+',
                          'format' => '(xml|json)'));

        $m->connect('api/users/:method',
                    array('action' => 'api',
                          'apiaction' => 'users'),
                    array('method' => 'show(\.(xml|json))?'));

        // direct messages
        
        $m->connect('api/direct_messages.:format',
                    array('action' => 'ApiDirectMessage',
                          'format' => '(xml|json|rss|atom)'));

        $m->connect('api/direct_messages/sent.:format',
                    array('action' => 'ApiDirectMessage',
                          'format' => '(xml|json|rss|atom)',
                          'sent' => true));

        $m->connect('api/direct_messages/new.:format',
                     array('action' => 'ApiDirectMessageNew',
                           'format' => '(xml|json)'));

        $m->connect('api/direct_messages/destroy.:format',
                     array('action' => 'ApiDirectMessageDestroy',
                           'format' => '(xml|json)'));
                     
        $m->connect('api/direct_messages/destroy/:id.:format',
                  array('action' => 'ApiDirectMessageDestroy',
                        'id' => '[0-9]+',
                        'format' => '(xml|json)'));        

		$m->connect('api/system_messages.:format',
                    array('action' => 'ApiSystemMessage',
                          'format' => '(xml|json)'));
                                      
        // friendships

        $m->connect('api/friendships/show.:format',
                    array('action' => 'ApiFriendshipsShow',
                          'format' => '(xml|json)'));

        $m->connect('api/friendships/exists.:format',
                    array('action' => 'ApiFriendshipsExists',
                          'format' => '(xml|json)'));

        $m->connect('api/friendships/create.:format',
                    array('action' => 'ApiFriendshipsCreate',
                          'format' => '(xml|json)'));

        $m->connect('api/friendships/destroy.:format',
                     array('action' => 'ApiFriendshipsDestroy',
                          'format' => '(xml|json)'));

        $m->connect('api/friendships/create/:id.:format',
                    array('action' => 'ApiFriendshipsCreate',
                          'id' => '[a-zA-Z0-9]+',
                          'format' => '(xml|json)'));

        $m->connect('api/friendships/destroy/:id.:format',
                    array('action' => 'ApiFriendshipsDestroy',
                    'id' => '[a-zA-Z0-9]+',
                    'format' => '(xml|json)'));

        // Social graph

        $m->connect('api/friends/ids/:id.:format',
                    array('action' => 'ApiUserFriends',
                          'ids_only' => true));

        $m->connect('api/followers/ids/:id.:format',
                    array('action' => 'ApiUserFollowers',
                          'ids_only' => true));

        $m->connect('api/friends/ids.:format',
                    array('action' => 'ApiUserFriends',
                          'ids_only' => true));

        $m->connect('api/followers/ids.:format',
                     array('action' => 'ApiUserFollowers',
                          'ids_only' => true));

        // account

        $m->connect('api/account/verify_credentials.:format',
                    array('action' => 'ApiAccountVerifyCredentials'));

       	$m->connect('api/account/update_profile.:format',
                   array('action' => 'ApiAccountUpdateProfile'));

         $m->connect('api/account/update_profile_image.:format',
                   	array('action' => 'ApiAccountUpdateProfileImage'));

         $m->connect('api/account/update_profile_background_image.:format',
                    array('action' => 'ApiAccountUpdateProfileBackgroundImage'));

         $m->connect('api/account/update_profile_colors.:format',
                    array('action' => 'ApiAccountUpdateProfileColors'));

         $m->connect('api/account/update_delivery_device.:format',
                    array('action' => 'ApiAccountUpdateDeliveryDevice'));

        // special case where verify_credentials is called w/out a format

        $m->connect('api/account/verify_credentials',
                    array('action' => 'ApiAccountVerifyCredentials'));

        $m->connect('api/account/rate_limit_status.:format',
                    array('action' => 'ApiAccountRateLimitStatus'));

		$m->connect('api/account/autologin',
                    array('action' => 'ApiAccountAutoLogin'));
                                        
        // favorites

        $m->connect('api/favorites.:format',
                    array('action' => 'ApiTimelineFavorites',
                    'format' => '(xml|json|rss|atom)'));

        $m->connect('api/favorites/:id.:format',
                    array('action' => 'ApiTimelineFavorites',
                          'id' => '[a-zA-Z0-9]+',
                          'format' => '(xmljson|rss|atom)'));

        $m->connect('api/favorites/create/:id.:format',
                    array('action' => 'ApiFavoriteCreate',
                          'id' => '[a-zA-Z0-9]+',
                          'format' => '(xml|json)'));
                    
        $m->connect('api/favorites/favegroup/:id.:format',
                    array('action' => 'ApiFavoriteFaveGroup',
                    	  'id' => '[a-zA-Z0-9]+',
                          'format' => '(xml|json)'));

        $m->connect('api/favorites/destroy/:id.:format',
                    array('action' => 'ApiFavoriteDestroy',
                          'id' => '[a-zA-Z0-9]+',
                          'format' => '(xml|json)'));

        // blocks

        $m->connect('api/blocks/create/:id.:format',
                    array('action' => 'ApiBlockCreate',
                          'id' => '[a-zA-Z0-9]+',
                          'format' => '(xml|json)'));

        $m->connect('api/blocks/destroy/:id.:format',
                    array('action' => 'ApiBlockDestroy',
                          'id' => '[a-zA-Z0-9]+',
                          'format' => '(xml|json)'));
        // help

        $m->connect('api/help/test.:format',
                    array('action' => 'ApiHelpTest',
                          'format' => '(xml|json)'));
                    
//		$m->connect('api/test/tag.:format',
//                    array('action' => 'ApiGetTag',
//                          'format' => 'xml'));
        // lshai

//        $m->connect('api/lshai/version.:format',
//                    array('action' => 'ApiLShaiVersion',
//                          'format' => '(xml|json)'));
//
//        $m->connect('api/lshai/config.:format',
//                    array('action' => 'ApiLShaiConfig',
//                   'format' => '(xml|json)'));

        // Groups and tags are newer than 0.8.1 so no backward-compatibility
        // necessary

        // Groups
        //'list' has to be handled differently, as php will not allow a method to be named 'list'

//        $m->connect('api/groups/timeline/:id.:format',
//                    array('action' => 'ApiTimelineGroup',
//                          'id' => '[a-zA-Z0-9]+',
//                          'format' => '(xmljson|rss|atom)'));

//        $m->connect('api/groups/show.:format',
//                    array('action' => 'ApiGroupShow',
//                    'format' => '(xml|json)'));
//
//        $m->connect('api/groups/show/:id.:format',
//                    array('action' => 'ApiGroupShow',
//                          'id' => '[a-zA-Z0-9]+',
//                          'format' => '(xml|json)'));

//        $m->connect('api/groups/join.:format',
//                    array('action' => 'ApiGroupJoin',
//                          'id' => '[a-zA-Z0-9]+',
//                          'format' => '(xml|json)'));
//
//        $m->connect('api/groups/join/:id.:format',
//                    array('action' => 'ApiGroupJoin',
//                    'format' => '(xml|json)'));
//
//        $m->connect('api/groups/leave.:format',
//                    array('action' => 'ApiGroupLeave',
//                          'id' => '[a-zA-Z0-9]+',
//                          'format' => '(xml|json)'));
//
//        $m->connect('api/groups/leave/:id.:format',
//                    array('action' => 'ApiGroupLeave',
//                          'format' => '(xml|json)'));
//
//        $m->connect('api/groups/is_member.:format',
//                    array('action' => 'ApiGroupIsMember',
//                          'format' => '(xml|json)'));

        $m->connect('api/groups/list.:format',
                    array('action' => 'ApiGroupList',
                          'format' => '(xml|json|rss|atom)'));

        $m->connect('api/groups/list/:id.:format',
                    array('action' => 'ApiGroupList',
                          'id' => '[a-zA-Z0-9]+',
                          'format' => '(xml|json|rss|atom)'));

//        $m->connect('api/groups/list_all.:format',
//                    array('action' => 'ApiGroupListAll',
//                          'format' => '(xml|json|rss|atom)'));

//        $m->connect('api/groups/membership.:format',
//                    array('action' => 'ApiGroupMembership',
//                         'format' => '(xml|json)'));
//
//        $m->connect('api/groups/membership/:id.:format',
//                    array('action' => 'ApiGroupMembership',
//                           'id' => '[a-zA-Z0-9]+',
//                           'format' => '(xml|json)'));
//                           
//        $m->connect('api/groups/create.:format',
//                    array('action' => 'ApiGroupCreate',
//                          'format' => '(xml|json)'));
        // Tags
        $m->connect('api/tags/timeline/:tag.:format',
                    array('action' => 'ApiTimelineTag',
                          'format' => '(xmljson|rss|atom)'));
	
        $m->connect('api/hottopic.:format',
                    array('action' => 'ApiHottopic',
                          'format' => '(xmljson)'));
                    
        // search
        $m->connect('api/search.atom', array('action' => 'twitapisearchatom'));
        $m->connect('api/search.json', array('action' => 'twitapisearchjson'));
        $m->connect('api/trends.json', array('action' => 'twitapitrends'));
        
        //log
        $m->connect('api/log/update.:format',
                    array('action' => 'ApiLogUpdate',
                          'format' => '(xml|json)'));


        //路径都要加在uname前面
        
        // user stuff

        // showall - check another people's all notices
        // showreplies - check another people's replies
        foreach (array('subscriptions', 'subscribers', 'blacklist', 'showall', 
                       'replies', 'showreplies', 'inbox', 'outbox', 
                       'admingroups', 'showfavorites', 'checkfavorites', 'showretweet') as $a) {
            $m->connect(':uname/'.$a,
                        array('action' => $a),
                        array('uname' => '[a-zA-Z0-9_]{1,64}'));
        }

        $m->connect('home', array('action' => 'home'));
        $m->connect('home?gtag=:gtag',
                        array('action' => 'home'),
                        array('gtag' => '[a-zA-Z0-9\x{4e00}-\x{9fa5}]+'));

		$m->connect('discussionlist',
						array('action' => 'discussionlist'));
		$m->connect('discussionlist/:notice_id',
						array('action' => 'discussionlist'),
						array('notice_id' => '[0-9]{6,10}'));
        
		$m->connect(':uname',
                    	array('action' => 'showstream'),
                    	array('uname' => '[a-zA-Z0-9_]{1,64}'));
                    	
         Event::handle('EndRouterInitialize', array($m));
        return $m;
    }

    function map($path)
    {
        try {
            $match = $this->m->match($path);
        } catch (Net_URL_Mapper_InvalidException $e) {
            common_log(LOG_ERR, "Problem getting route for $path - " .
                       $e->getMessage());
//            $cac = new ClientErrorAction("Page not found.", 404);
//            $cac->handle();
            return false;
        }

        return $match;
    }

    function build($action, $args=null, $params=null, $fragment=null)
    {
        $action_arg = array('action' => $action);

        if ($args) {
            $args = array_merge($action_arg, $args);
        } else {
            $args = $action_arg;
        }

        $url = $this->m->generate($args, $params, $fragment);

        // Due to a bug in the Net_URL_Mapper code, the returned URL may
        // contain a malformed query of the form ?p1=v1?p2=v2?p3=v3. We
        // repair that here rather than modifying the upstream code...

        $qpos = strpos($url, '?');
        if ($qpos !== false) {
            $url = substr($url, 0, $qpos+1) .
              str_replace('?', '&', substr($url, $qpos+1));
        }
        return $url;
    }
}
