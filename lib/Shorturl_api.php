<?php
/*
 * LShai - a distributed microblogging tool
 */

if (!defined('SHAISHAI')) { exit(1); }

class ShortUrlApi
{
    protected $service_url;
    protected $long_limit = 27;

    function __construct($service_url)
    {
        $this->service_url = $service_url;
    }

    function shorten($url)
    {
        if ($this->is_long($url)) return $this->shorten_imp($url);
        return $url;
    }

    protected function shorten_imp($url) {
        return "To Override";
    }

    private function is_long($url) {
        //return strlen($url) >= common_config('site', 'shorturllength');
        return true;
    }

    protected function http_post($data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->service_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if (($code < 200) || ($code >= 400)) return false;
        return $response;
    }

    protected function http_get($url) {
        $encoded_url = urlencode($url);
        return file_get_contents("{$this->service_url}$encoded_url");
    }

    protected function tidy($response) {
        $response = str_replace('&nbsp;', ' ', $response);
        $config = array('output-xhtml' => true);
        $tidy = new tidy;
        $tidy->parseString($response, $config, 'utf8');
        $tidy->cleanRepair();
        return (string)$tidy;
    }
}

class SGamePub extends ShortUrlApi
{
	function __construct()
    {
        parent::__construct('http://s.gamepub.cn/index.php?r=plain&url=');
    }

    protected function shorten_imp($url) {
    	if (strpos($url, common_config('site', 'server')) === false && strpos($url, 'http://s.gamepub.cn') === false) {
    		$response = $this->http_get($url);
        	if ($response) 
        		return $response;
    	}

        return $url;
    }
}

