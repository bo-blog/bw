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

loadServices ();

if (isset (bw :: $conf['APIOpen'])) {
	if (!bw :: $conf['APIOpen']) {
		stopError ('API disabled.');
	}
} 
else {
	stopError ('API disabled.');
}

$api = new bwApi;
$api -> auth (bw :: $conf['basicAPI'], bw :: $conf['advancedAPI']);
$api -> go ($canonical -> currentArgs['mainAPI'], $canonical -> currentArgs['subAPI'], $canonical -> currentArgs['pref']);