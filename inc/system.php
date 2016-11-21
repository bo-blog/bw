<?php
/**
* 
* @link http://bw.bo-blog.com
* @copyright (c) 2015 bW Development Team
* @license MIT
*/
define ('bwVersion', '1.2.1');
define ('bwInternalVersion', '1210');
define ('bwUpdate', 'https://bo-blog.github.io/bw-update/');

if (!defined ('P')) {
	die ('Access Denied.');
} 

if (!file_exists (P . 'conf/info.php')) {
	header ('Location: ' . P . 'install/index.php');
	exit ();
} 

include_once (P . 'conf/info.php');
date_default_timezone_set ($conf['timeZone']);

spl_autoload_register (function ($class) {
	$classFile = P. 'inc/' . strtolower (substr ($class, 2) . '.inc.php');
	file_exists ($classFile) && require_once ($classFile);
});

include_once (P . 'inc/bw.inc.php');

bw :: init ();

function stopError ($errMsg)
{
	global $view;
	if (!is_object ($view)) {
		$view = new bwView;
	} 
	$view -> haltWithError ($errMsg);
} 

function ajaxSuccess ($successMsg)
{
	global $view;
	if (!is_object ($view)) {
		$view = new bwView;
	} 
	$view -> haltWithSuccess ($successMsg);
} 

function getIp () {
	$realip = $realip2 = '';
	if (isset ($_SERVER["HTTP_CLIENT_IP"])) {
		$realip = filterIP ($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : false;
	} 
	if (!$realip && isset ($_SERVER['REMOTE_ADDR'])) {
		$realip = filterIP ($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
	} 
	if (isset ($_SERVER["HTTP_X_FORWARDED_FOR"])) {
		$realip2 = filterIP ($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $realip;
	}
	if (!$realip && $realip2) {
		$realip = $realip2;
	} 
	if (!$realip) {
		$realip = 'unknown';
	}
	if (!$realip2) {
		$realip2 = $realip;
	} 
	return array ($realip, $realip2);
}

function filterIP ($str){
	$ipc = 0;
	$ipm = array ();
	$ip = @explode (',', $str);
	for($i=0; $i<count ($ip); $i++) {  
		$ipc = preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', trim ($ip[$i]), $ips);
		isset ($ips[0]) && $ipm[] = $ips[0];
	} 
	if ($ipc > 0) {
		return (@implode (', ', $ipm));
	} else {
		return '';
	}
}  

function dataFilter ($reservedKeys, $submitData)
{
	$reservedArray = array_fill_keys($reservedKeys, null);
	$returnArray = array_intersect_key ($submitData, $reservedArray);
	return array_merge ($reservedArray, $returnArray);
} 

function loadServices ()
{
	if (!defined ('S')) {
		if (file_exists (P . "conf/services.php")) {
			global $conf;
			include_once (P . "conf/services.php");
		} 
		define ('S', 1); 
		// Qiniu
		define ('QINIU_AK', $conf['qiniuAKey']);
		define ('QINIU_SK', $conf['qiniuSKey']);

		hook ('loadServices', 'Execute');
	} 
} 

function qiniuUpload ($filePath)
{
	global $qiniuClient, $conf;
	if (defined ('S')) {
		loadServices ();
	} 
	if (!is_object ($qiniuClient)) {
		require_once (P . "inc/script/qiniu/QiniuClient.php");
		$qiniuClient = new qiniuClient (QINIU_AK, QINIU_SK);
	} 
	$fStoreNameFull = FPATH . "/{$filePath}";
	$result = $qiniuClient -> uploadFile($fStoreNameFull, $conf['qiniuBucket'], $filePath);
	return $result;
} 

function clearCache ($caID = false, $forced = false)
{
	global $conf;
	if ($conf['pageCache'] || $forced) {
		if ($caID) {
			bw :: $db -> dbExec ('DELETE FROM cache WHERE caID=?', array ($caID));
		} else {
			if (DBTYPE == 'MySQL') {
				bw :: $db -> dbExec ('TRUNCATE TABLE cache');
			} else {
				bw :: $db -> dbExec ('DROP TABLE cache');
				bw :: $db -> dbExec ('CREATE TABLE cache (caID CHAR (32) PRIMARY KEY NOT NULL, caContent TEXT)');
			} 
		} 
	}
} 

function hook ()
{
	$numArgs = func_num_args ();
	if ($numArgs < 2) {
		return;
	} 
	$hookInterface = func_get_arg (0);
	$hookType = func_get_arg (1);
	if (!isset (bw :: $extList[$hookInterface])) {
		$return = $hookType == 'Replace' ? func_get_arg (2) : '';
		return $return;
	} 
	switch ($hookType) {
		case 'Replace':
			if ($numArgs == 3) {
				$input = func_get_arg (2);
				foreach (bw :: $extList[$hookInterface] as $extID) {
					$extID = 'ext_' . $extID;
					$input = $extID :: $hookInterface($input);
				} 
				return $input;
			} 
			break;

		case 'Insert':
			$return = '';
			foreach (bw :: $extList[$hookInterface] as $extID) {
				$extID = 'ext_' . $extID;
				$return .= $extID :: $hookInterface();
			} 
			return $return;
			break;

		case 'Execute':
			if ($numArgs > 2) {
				$args = array_slice (func_get_args (), 2);
				foreach (bw :: $extList[$hookInterface] as $extID) {
					$extID = 'ext_' . $extID;
					call_user_func_array (array($extID, $hookInterface), $args);
				} 
			} else {
				foreach (bw :: $extList[$hookInterface] as $extID) {
					$extID = 'ext_' . $extID;
					$extID :: $hookInterface();
				} 
			} 
			break;
	} 
} 

function widget ($widgetHook, $storedValue)
{
	$storedValue = @json_decode ($storedValue, true);
	if (!is_array ($storedValue)) {
		return false;
	} 
	$patternWidgetHooks = array ('wghtmlhead' => 'value',
		'wgheader' => 'text,url,title,target',
		'wgsidebar' => 'title,value',
		'wgfooter' => 'value'
		);
	if (array_key_exists ($widgetHook, $patternWidgetHooks)) {
		$patterns = @explode (',', $patternWidgetHooks[$widgetHook]);
		$output = array();
		foreach ($patterns as $pattern) {
			$output[$pattern] = isset ($storedValue[$pattern]) ? $storedValue[$pattern] : '';
		} 
		return $output;
	} 
	return false;
} 

function stringToArray ($array, $key = false)
{
	$newArray = array();
	if ($key) {
		foreach ($array as $val) {
			$newArray[] = array ($key => $val);
		} 
	} else {
		foreach ($array as $val) {
			$newArray[] = array ($val);
		} 
	} 
	return $newArray;
} 

function curlRetrieve ($URL, $timeOut = 5)
{
	$ch = curl_init ();
	curl_setopt ($ch, CURLOPT_URL, $URL);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_TIMEOUT, $timeOut);
	curl_setopt ($ch, CURLOPT_HEADER, false);
	$fileContents = curl_exec ($ch);
	curl_close ($ch);
	return $fileContents;
} 

function fileReplaceRecursive ($readDir, $destDir)
{
	$handle = opendir ($readDir);
	while (false !== ($file = readdir ($handle))) {
		if ($file == '.' || $file == '..') {
			continue;
		} 
		if (!is_dir ($readDir . $file)) {
			rename ($readDir . $file, $destDir . $file);
		} else {
			fileReplaceRecursive ($readDir . $file . '/', $destDir . $file . '/');
		}
	} 
} 

function rrmdir ($src) {
	$dir = opendir ($src);
	while(false !== ( $file = readdir ($dir))) {
		if (($file != '.' ) && ($file != '..' )) {
			$full = $src . '/' . $file;
			if (is_dir ($full) ) {
				rrmdir ($full);
			}
			else {
				@unlink ($full);
			}
		}
	}
	closedir ($dir);
	@rmdir ($src);
}

function dochmod ($dir) { 
	$openDir = opendir ($dir);
	while ($readDir = @readdir ($openDir))
	{
		if ($readDir != "." && $readDir != "..") { 
			is_dir ("$dir/$readDir") ? dochmod ("$dir/$readDir") : chmod ("$dir/$readDir", 0777); 
		} 
	}
} 

if (@get_magic_quotes_gpc()) {
	function stripslashes_deep($value)
	{
		$value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
		return $value;
	} 

	$_REQUEST = array_map('stripslashes_deep', $_REQUEST);
} 

