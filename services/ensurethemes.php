<?php

define('SHAISHAI', true);
define('INSTALLDIR', realpath(dirname(__FILE__) . '/..'));

require INSTALLDIR . '/lib/environment.php';
require INSTALLDIR . '/lib/cache.php';
require INSTALLDIR . '/lib/common.php';
require INSTALLDIR . '/lib/router.php';


$officialDesigns = Official_design::getOfficialDesigns();

$officialDesignNames = array();

while ($officialDesigns->fetch()) {
	$officialDesignNames[] = $officialDesigns->name;
}

$exceptions = array('default', 'game', '.', '..', '.svn');
$dir = dir(INSTALLDIR . '/theme');
while (false !== ($entry = $dir->read())) {
	if (! in_array($entry, $exceptions) &&
		! in_array($entry, $officialDesignNames)) {
		echo "creating record for theme : " . $entry . "\n";
		Official_design::saveNew($entry);
	}
}
$dir->close();
