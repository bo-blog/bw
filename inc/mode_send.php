<?php 
/**
* 
* @link http://bw.bo-blog.com
* @copyright (c) 2014 bW Development Team
* @license MIT
*/
if (!defined ('P')) {
	die ('Access Denied.');
} 

$view = new bwView;

if ($conf['commentOpt']<>0) {
	loadServices (); //Load Baidu API or Duoshuo
	if ($conf['commentOpt'] == 1 || $conf['commentOpt'] == 2) { //Build-in comment
		@session_start ();
		$comment = new bwComment;
	} elseif ($conf['commentOpt'] == 3) {
		die ('Access Denied.');
	}
} else {
	die ('Access Denied.');
} 

if ($canonical -> currentArgs['mainAction'] == 'comments') {
	if ($canonical -> currentArgs['subAction'] == 'submit') {
		$smt = $comment -> addComment ($_REQUEST['smt']);
		$view = new bwView;
		$view -> setMaster ('ajaxcomment');
		$view -> setPassData ($smt);
		$view -> setWorkFlow (array ('ajaxcomment'));
		$view -> finalize ();
	}
}

