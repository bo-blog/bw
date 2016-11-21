<?php 
/**
* 
* @link http://bw.bo-blog.com
* @copyright (c) 2016 bW Development Team
* @license MIT
*/
if (!defined ('P')) {
	die ('Access Denied.');
} 

class bwApi {
	private $silent;
	private $authStatus;
	private $responseCode;
	private $responseHTML;
	private $requestMethod;
	private $subAPI;
	private $pref;
	private $partial;
	public function __construct ()
	{
		$this -> authStatus = 0;
		$this -> silent = 0;
		$this -> subAPI = $this -> pref = ''; 
		if (!isset ($_SERVER['QUERY_STRING'])) {
			$this -> partial = array ();
		} 
		else {
			parse_str ($_SERVER['QUERY_STRING'], $this -> partial);
		}
		if (!isset ($_SERVER['HTTP_ACCEPT'])) {
			$this -> responseHTML = false;
		} else {
			if (strpos ($_SERVER['HTTP_ACCEPT'], 'text/html') !== false) {
				$this -> responseHTML = true;
			} 
			else {
				$this -> responseHTML = false;
			}
		}
		
		$this -> responseCode = Array(  
			100 => 'Continue',  
			101 => 'Switching Protocols',  
			200 => 'OK',  
			201 => 'Created',  
			202 => 'Accepted',  
			203 => 'Non-Authoritative Information',  
			204 => 'No Content',  
			205 => 'Reset Content',  
			206 => 'Partial Content',  
			300 => 'Multiple Choices',  
			301 => 'Moved Permanently',  
			302 => 'Found',  
			303 => 'See Other',  
			304 => 'Not Modified',  
			305 => 'Use Proxy',  
			306 => '(Unused)',  
			307 => 'Temporary Redirect',  
			400 => 'Bad Request',  
			401 => 'Unauthorized',  
			402 => 'Payment Required',  
			403 => 'Forbidden',  
			404 => 'Not Found',  
			405 => 'Method Not Allowed',  
			406 => 'Not Acceptable',  
			407 => 'Proxy Authentication Required',  
			408 => 'Request Timeout',  
			409 => 'Conflict',  
			410 => 'Gone',  
			411 => 'Length Required',  
			412 => 'Precondition Failed',  
			413 => 'Request Entity Too Large',  
			414 => 'Request-URI Too Long',  
			415 => 'Unsupported Media Type',  
			416 => 'Requested Range Not Satisfiable',  
			417 => 'Expectation Failed',  
			500 => 'Internal Server Error',  
			501 => 'Not Implemented',  
			502 => 'Bad Gateway',  
			503 => 'Service Unavailable',  
			504 => 'Gateway Timeout',  
			505 => 'HTTP Version Not Supported'  
		);  
		if (!isset ($_SERVER['REQUEST_METHOD'])) {
			$this -> throwError (400);
		}
		else {
			$this -> requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
/*			
2016/9/10 - Only two APIs are supported so far, block other requests temporarily
			if (!in_array ($this -> requestMethod, array ('GET', 'PUT', 'DELETE', 'POST'))) {
				$this -> throwError (405);
			}
*/
			if ($this -> requestMethod <> 'get') { 
				$this -> throwError (405);
			}
		}
	}

	public function auth ($arrayBasic, $arrayAdvanced) 
	{
		$APIKey = $APISecret = '';
		if (!isset ($_SERVER['PHP_AUTH_USER']) || !isset ($_SERVER['PHP_AUTH_PW'])) {
			if (isset ($_SERVER['HTTP_AUTHORIZATION'])) {
				@list ($APIKey, $APISecret) = @explode (':', @base64_decode (str_replace ('Basic ', '', $_SERVER['HTTP_AUTHORIZATION'])));
			}
			else {
				$this -> throwError (401);
			}
		} 
		else {
			$APIKey = $_SERVER['PHP_AUTH_USER'];
			$APISecret = $_SERVER['PHP_AUTH_PW'];
			if (in_array ($APIKey, $arrayBasic)) {
				$type = 'basic';
			}
			elseif (in_array ($APIKey, $arrayAdvanced)) {
				$type = 'advanced';
			}
			else {
				$this -> throwError (401);
			}			
		}
		$authSecret = sha1 ($APIKey . bw :: $conf['siteKey'] . "API");
		$this -> authStatus = $authSecret == $APISecret ? $type : false;
		if (!$this -> authStatus) {
			$this -> throwError (401);
		}
		$this -> authStatus = 'basic';
	}

	public function getRandomKey ()
	{
		return ('o_' . sha1 (bw :: $conf['siteKey'] . 'KEY' . rand (10000, 99999))); 
	}

	private function check ($required='basic')
	{
		if ($required <> $this -> authStatus) {
			$this -> throwError (403);
		}
	}

	public function go ($mainAPI, $subAPI, $pref) {
		$apiAct = $this -> requestMethod . '_' . $mainAPI;
		$this -> subAPI = $subAPI;
		$this -> pref = $pref;
		$this -> $apiAct ();
	}

	
	private function throwError ($errCode, $errMsg=false)
	{
		if ($errCode == 401) {
			$this -> needAuth ();
		}

		if (!$this -> silent) {
			$msgBody = array (
				'error' => 1,
				'message' => !$errMsg ? $this -> responseCode [$errCode] : $errMsg,
			);
			$this -> done ($errCode, $msgBody);
		}
	} 

	private function done ($statusCode, $outputArray, $extraHeader = false)
	{
		$output = 'HTTP/1.1 '  . $statusCode . ' ' . $this -> responseCode[$statusCode];
		if (!isset ($outputArray['error']) && !$this -> silent) {
			$outputArray['error'] = 0;
			$outputArray['message'] = 'No error.';
		}
		header ($output);
		$extraHeader && header ($extraHeader);
		if ($this -> responseHTML) {
			PHP_VERSION > '5.4' && die ("<pre>" . htmlspecialchars (json_encode ($outputArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) . "</pre>"); 
		}
		header ('Content-Type: application/json'); 
		die (json_encode ($outputArray));
	}

	private function needAuth () {
		header ('HTTP/1.1 401 Unauthorized.');
		header ('WWW-Authenticate: Basic realm="Secure Area"');
		header ('Content-Type: text/html'); 
		die ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
		 "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
		<HTML>
		<HEAD>
		<TITLE>Error</TITLE>
		<META HTTP-EQUIV="Content-Type" CONTENT="text/html">
		</HEAD>
		<BODY><H1>401 Unauthorized.</H1></BODY>
		</HTML>');
	}

	public function silentError ($status = 1) 
	{
		$this -> silent = $status ? 1 : 0;
	} 

	public function __call ($name, $arg) {
		$this -> throwError (406, "Unsupported request: {$name}.");
	}

	private function get_articles () {
		$this -> check ();
		if ($this -> subAPI == 1) { //Get articles list
			$howmany = isset ($this -> partial['num']) ? max (1, floor ($this -> partial['num'])) : 5;
			$startTime = isset ($this -> partial['since']) ? strtotime ($this -> partial['since']) : false;
			$pageNum = isset ($this -> partial['p']) ? max (1, floor ($this -> partial['p'])) : 1;
			$article = new bwArticle;
			if ($startTime) { //Get articles later than this time
				$article -> setSinceTime ($startTime);
			}
			
			$article -> alterPageNum ($pageNum);
			$article -> alterPerPage ($howmany);
			$article -> getArticleList ();
			$outputs = array ();
			$i = 0;

			$totalPages = ceil ($article -> totalArticles / $howmany);
			if ($pageNum > $totalPages) {
				$this -> throwError (404, 'Page number does not exist.');
			}
			$extraHeader = "Link: <" . bw :: $conf['siteURL'] . "/api.php/articles?num={$howmany}&p=1>; rel=\"first\", ";
			$extraHeader.= "<" . bw :: $conf['siteURL'] . "/api.php/articles?num={$howmany}&p={$totalPages}>; rel=\"last\"";
			if ($pageNum + 1 <= $totalPages) {
				$extraHeader.= ",  <" . bw :: $conf['siteURL'] . "/api.php/articles?num={$howmany}&p=" . ($pageNum + 1) . ">; rel=\"next\"";
			}
			if ($pageNum > 1) {
				$extraHeader.= ",  <" . bw :: $conf['siteURL'] . "/api.php/articles?num={$howmany}&p=" . ($pageNum - 1) . ">; rel=\"prev\"";
			}

			foreach ($article -> articleList as $item) {
				$outputs[$i]['ID'] = $item['aID'];
				$outputs[$i]['title'] = $item['aTitle'];
				$outputs[$i]['date'] = $item['aTime'];
				$outputs[$i]['content'] = bwView :: textFormatter (str_replace ('+++', '', $item['aContent']));
				$outputs[$i]['category'] = $item['aCateDispName'];
				$item['aTags'] && $outputs[$i]['tags'] = @explode (',', $item['aTags']);
				$outputs[$i]['permal-link'] = bw :: $conf['siteURL'] . '/' . bw :: $conf['linkPrefixArticle'] ."/{$item['aID']}/";
				$outputs[$i]['author'] = bw :: $conf['authorName'];
				$outputs[$i]['about'] = bw :: $conf['authorIntro'];
				$outputs[$i]['homepage'] = bw :: $conf['siteURL'];
				$i++;
			}

			$this -> done (200, array ("articles" => $outputs), $extraHeader);
			
		} 
		else {
			$article = new bwArticle;
			$article -> fetchArticle ($this -> subAPI);
			$item = $article -> articleList[$this -> subAPI];
			$outputs = array ();
			$i = 0;
			$outputs[$i]['title'] = $item['aTitle'];
			$outputs[$i]['date'] = $item['aTime'];
			$outputs[$i]['content'] = bwView :: textFormatter (str_replace ('+++', '', $item['aContent']));
			$outputs[$i]['category'] = $item['aCateDispName'];
			$item['aTags'] && $outputs[$i]['tags'] = @explode (',', $item['aTags']);
			$outputs[$i]['permal-link'] = bw :: $conf['siteURL'] . '/' . bw :: $conf['linkPrefixArticle'] ."/{$item['aID']}/";
			$outputs[$i]['author'] = bw :: $conf['authorName'];
			$outputs[$i]['about'] = bw :: $conf['authorIntro'];
			$outputs[$i]['homepage'] = bw :: $conf['siteURL'];
			$this -> done (200, array ("articles" => $outputs));
		}
	}

	private function get_users () {
		$this -> check ();
		if ($this -> subAPI <> 1) {
			if (strtolower ($this -> subAPI) <> strtolower (bw :: $conf['authorName'])) {
				$this -> throwError (404, 'User does not exist.');
			}
		}
		$outputs = array ();
		$outputs[]= array ('name' => bw :: $conf['authorName'], 'about' => bw :: $conf['authorIntro'], 'homepage' => bw :: $conf['siteURL'], 'site' => bw :: $conf['siteName'], 'avatar' => bw :: $conf['siteURL'] . '/conf/default.png');
		$this -> done (200, array ("users" => $outputs));
	}

	private function get_ () {  
		$outputs = array ();
		$output['users'] = bw :: $conf['siteURL'] . '/api.php/users';
		$output['articles'] = bw :: $conf['siteURL'] . '/api.php/articles?p=1&num=5&since=1990-01-01';
		$this -> silentError ();
		$this -> done (200, $output);
	}
} 
