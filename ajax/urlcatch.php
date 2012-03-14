<?php

if (!defined('SHAISHAI')) { exit(1); }

class UrlcatchAction extends ShaiAction
{
	function handle($args)
	{
		 parent::handle($args);
		 $url = $this->trimmed('url');

		 /*
		  把这些错误都去除掉, 只返回提示信息
		 <br /> <b>Warning</b>:  fopen(http://docs.google.com/Doc?docid=
		 0Aectw_89PzVLZGY0NXRzZ184ZGN6cmZoZDk&hl=en) [<a href='function.fopen'>
		 function.fopen</a>]: failed to open stream: Unable to find the socket transport
		  "ssl" - did you forget to enable it when you configured PHP? in <b>D:\httproot
		 \shaishai\ajax\urlcatch.php</b> on line <b>9</b><br /> 不能读取远程文件, 或URL输入错误.
		 */
		 //header('Content-type: text/xml');
		 //使用curl获得响应头, curl -I http://feeds.feedburner.com/dbanotes
		 
	    try { 
			 $file = fopen ($url, "r");
			if (!$file) {
				//echo "不能读取远程文件, 或URL输入错误.";
				$this->serverError('不能读取远程文件, 或URL输入错误.');
			}
			$code  = 'ISO-8859-1';
			while (!feof ($file)) {
				$line = fgets ($file, 1024);
				if (eregi ("charset=(.*)\"", $line, $out)) {
					$code = $out[1];
					//echo "$code";
					break;
				}
			}
	
			while (!feof ($file)) {
				$line = fgets ($file, 1024);
				if (eregi ("<title>(.*)</title>", $line, $out)) {
					$title = $out[1];
					if(strtolower($code) != "utf-8") {
						//需要配置iconv
						//$title =iconv( $code, "utf-8" ,$title);
//						common_debug($code);
						mb_convert_encoding($title, "utf-8", $code); 
					}
					//echo "$title";
					header('Content-type: text/xml'); 
    				echo '<p class="title">' . $title . '</p>';
					break;
				}
			}
			fclose($file);
	    } catch (Exception $e) {
        	//$newnoticeView->showForm($args, $e->getMessage());
            //$testcac = new ErrorTest();
			//$testcac->showError($e->getMessage());
			echo '<p class="error">' . $e->getMessage() . '</p>';
            return;
       }
	}
}