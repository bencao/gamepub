<?php
/**
 * LShai - a distributed microblogging tool
 */

session_name('SHAISID');

define('INSTALLDIR', dirname(__FILE__));
define('SHAISHAI', true);

require INSTALLDIR . '/lib/environment.php';
require INSTALLDIR . '/lib/cache.php';

// if cached page exist
if ($_SERVER['REQUEST_METHOD'] == 'GET'
	&& ($cache = common_memcache())) {
	
	$idkey = common_cache_key('statichtml:' . $_SERVER['REQUEST_URI']);
	
	$result = $cache->get($idkey);
	
	if ($result) {
		header('Content-Type: text/html; charset=utf-8');
		echo $result;
		exit();
	}
}

require INSTALLDIR . '/lib/common.php';
require INSTALLDIR . '/lib/router.php';

$user = null;
$action = null;

function handleError($error)
{
    if ($error->getCode() == DB_DATAOBJECT_ERROR_NODATA) {
        return;
    }

    $logmsg = "PEAR error: " . $error->getMessage();
    if (common_config('site', 'logdebug')) {
        $logmsg .= " : ". $error->getDebugInfo();
    }
    common_log(LOG_ERR, $logmsg);
    if (common_config('site', 'logdebug')) {
        $bt = $error->getBacktrace();
        foreach ($bt as $line) {
            common_log(LOG_ERR, $line);
        }
    }
    if ($error instanceof DB_DataObject_Error ||
        $error instanceof DB_Error) {
        $msg = sprintf('网站管理员(%s)正在调整服务. 请发消息通知管理员或者等待几分钟再试.',
                       common_config('site', 'email'));
    } else {
        $msg = '发生一些异常.';
    }

    $sac = new ServerErrorAction($msg, 500);
    $sac->handle();
    exit(-1);
}

function installPlugins() {
	// Install plugins
    foreach (common_config('site', 'plugins') as $plugin_name) {
    	require_once INSTALLDIR . '/plugins/' . $plugin_name . '_plugin.php';
    	$plugClassName = ucfirst($plugin_name) . '_plugin';
    	$plugObject = new $plugClassName();
    	$plugObject->install();
    }
}

function main()
{	
    global $user, $action;

    PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'handleError');

    $user = common_current_user();
    
//    installPlugins();

    $r = Router::get();
    $request_uri = array_key_exists('p', $_REQUEST) ? $_REQUEST['p'] : '/';
    $args = $r->map($request_uri);

    if (!$args) {
        $cac = new ClientErrorAction('未知页面', 404);
        $cac->handle($args);
        return;
    }

    $args = array_merge($args, $_REQUEST, array('shai_path' => $_SERVER['REQUEST_URI']));

    Event::handle('ArgsInitialize', array(&$args));

    $action = $args['action'];
    
    // auto redirect for logined user to his personal page
    if ($action == 'homepage2' && $user) {
        $url = common_path('home');
        common_redirect($url, 303);
        return;
    }
    
    Event::handle('InitializePlugin', array($args));

    $action_class = ucfirst($action).'Action';
    
//    common_debug('route to ' . $action_class);

    if (class_exists($action_class)) {
        $action_obj = new $action_class();

        try {
            if ($action_obj->prepare($args)) {
                $action_obj->handle($args);
            }
        } catch (NotLoggedInException $nex) {
        	$nac = new NotLoggedInAction($nex->getMessage());
        	$nac->handle($args);
        } catch (ClientException $cex) {
            $cac = new ClientErrorAction($cex->getMessage(), $cex->getCode());
            $cac->handle($args);
        } catch (ServerException $sex) { // snort snort guffaw
            $sac = new ServerErrorAction($sex->getMessage(), $sex->getCode());
            $sac->handle($args);
        } catch (CachedException $ceex) {
            // do nothing
        } catch (Exception $ex) {
            $sac = new ServerErrorAction($ex->getMessage());
            $sac->handle($args);
        }
    } else {
    	$cac = new ClientErrorAction('未知操作', 404);
        $cac->handle($args);
    }
}

if (function_exists('xhprof_enable')) {
	xhprof_enable(XHPROF_FLAGS_MEMORY);
}

main();

Event::handle('CleanupPlugin');

if (function_exists('xhprof_enable')) {
	$xhprof_data = xhprof_disable();
//	common_debug($xhprof_data);
}
