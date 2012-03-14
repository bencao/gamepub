<?php

define('SHAISHAI', true);
define('INSTALLDIR', realpath(dirname(__FILE__) . '/..'));

require INSTALLDIR . '/lib/environment.php';
require INSTALLDIR . '/lib/cache.php';
require INSTALLDIR . '/lib/common.php';
require INSTALLDIR . '/lib/router.php';

function create_guid()
{
    $microTime = microtime();
	list($a_dec, $a_sec) = explode(" ", $microTime);

	$dec_hex = sprintf("%x", $a_dec* 1000000);
	$sec_hex = sprintf("%x", $a_sec);

	ensure_length($dec_hex, 5);
	ensure_length($sec_hex, 6);

	$guid = "";
	$guid .= $dec_hex;
	$guid .= create_guid_section(3);
	$guid .= '-';
	$guid .= create_guid_section(4);
	$guid .= '-';
	$guid .= create_guid_section(4);
	$guid .= '-';
	$guid .= create_guid_section(4);
	$guid .= '-';
	$guid .= $sec_hex;
	$guid .= create_guid_section(6);

	return $guid;

}

function create_guid_section($characters)
{
	$return = "";
	for($i=0; $i<$characters; $i++)
	{
		$return .= sprintf("%x", mt_rand(0,15));
	}
	return $return;
}

function ensure_length(&$string, $length)
{
	$strlen = strlen($string);
	if($strlen < $length)
	{
		$string = str_pad($string,$length,"0");
	}
	else if($strlen > $length)
	{
		$string = substr($string, 0, $length);
	}
}

$fb = new Feedback();
$fb->whereAdd('sended = 0');
$fb->orderBy('id asc');
$fb->find();

$feedbacksToSend = array();
$feedbackIds = array();
while ($fb->fetch()) {
	$feedbackIds[] = $fb->id;
	
	$em = array(
		'ptype' => $fb->ptype,
		'subject' => 'GamePub用户反馈' . $fb->id,
		'description' => $fb->description,
		'email' => $fb->email,
		'created' => $fb->created,
		'nickname' => 'anonymous'
	);
	if ($fb->report_user_id) {
		$profile = Profile::staticGet('id', $fb->report_user_id);
		
		if ($profile) {
			$em['nickname'] = $profile->nickname;
			if (empty($em['email'])) {
				$em['email'] = $profile->email;
			}
		}
	}
	$feedbacksToSend[] = $em;
}

$fb->free();

$fb->query('UPDATE feedback set sended = 1 where id in (' . implode(',', $feedbackIds) . ')');
$fb->free();

$source      = 'Web Normal';
$assigned_to = '1';
$status      = 'New';
//$subject     = common_config('site', 'name') . '用户反馈';
$customerid  = 'e1f39ee1-47be-b2e3-a901-4ac9fa8e04b9';

$db = DB::connect("mysql://shaier:shaier@localhost/sugarcrm");
$db->query("SET NAMES 'utf8'");
if (DB::isError($db)) {
    die ($db->getMessage());
}

foreach ($feedbacksToSend as $fts) {
	$id = create_guid();
	$sql  = 'insert into cases(id, name, date_entered, date_modified, modified_user_id, created_by, description, assigned_user_id, status) values (?, ?, ?, ?, ?, ?, ?, ?, ?)';
	$data = array($id, $fts['subject'], $fts['created'], $fts['created'], $customerid, $customerid, $fts['description'], $assigned_to, $status);
	$res =& $db->query($sql, $data);
	// Always check that result is not an error
	if (PEAR::isError($res)) {
	    die($res->getMessage());
	}
	
	$sql = 'insert into cases_cstm(id_c, source_c,  user_nickname_c, email_c, type_c) values (?, ?, ?, ?, ?)';
	$data = array($id, $source, $fts['nickname'], $fts['email'], $fts['ptype']);
	$res =& $db->query($sql, $data);
	// Always check that result is not an error
	if (PEAR::isError($res)) {
		die($res->getMessage());
	}
}
        
$db->disconnect();