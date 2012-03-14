<?php

/**
 * whether a string is a valid address
 * @param $url_string
 */
function isValidURL($url_string) {
	return ! empty ($url_string)
		&& Validate::uri($url_string, array('allowed_schemes' => array('http', 'https')));
}

/**
 * whether a string's length is between $min and $max
 * @param $string
 * @param $min
 * @param $max
 */
function isValidLength($string, $min, $max) {
	if ($min > $max) {
		throw new InvalidArgumentException("最短长度不能大于最大长度");
	}
	if ($min > 0 && is_null($string)) {
		return false;
	}
	$length = mb_strlen($string, 'utf-8');
	return $length >= $min && $length <= $max;
}

/**
 * whether a sex string is valid
 * @param $sex
 */
function isValidSex($sex) {
	return $sex == 'M' || $sex == 'F';
}

/**
 * whether a date string is a valid 'yyyy-dd-mm' date
 * @param $date
 */
function isValidDate($date) {
	return ! empty($date) 
            && (Validate::date($date, array('format' => '%Y-%m-%d'))
            	|| Validate::date($date, array('format' => '%Y-%n-%d')));
}

/**
 * whether a string is a valid email address
 * @param $email
 */
function isValidEmail($email) {
	return ! empty($email) 
			&& Validate::email($email);
}

function isValidUname($uname) {
	return ! empty($uname) 
			&& Validate::string($uname, array('min_length' => 5, 'max_length' => 20,
                                                          'format' => UNAME_FMT));
}

function isValidPassword($password) {
	if (empty($password)) {
		return false;
	}
	$len = strlen($password);
	return $len >= 5 && $len <= 64;
}

function isValidNickname($nickname) {
	return ! empty($nickname) && isValidLength($nickname, 1, 12) 
//			&& ! common_banwordCheck($nickname)
				;
}

function isValidFriendGroupName($name) {
	return ! empty($name)
			&& isValidLength($name, 1, 8);
}

function isNum($str) {
	return preg_match('/^\d+$/', $str);
}

function isAlpha($str) {
	return preg_match('/^([a-z][A-Z])+$/', $str);
}

?>