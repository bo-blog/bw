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

$article = new bwArticle;

$article -> fetchArticle ($canonical -> currentArgs['aID']);

$view = new bwView;
$view -> setPageTitle ($article -> articleList[$canonical -> currentArgs['aID']]['aTitle']);
$view -> setActiveNav ($article -> articleList[$canonical -> currentArgs['aID']]['aCateURLName']);
$view -> setPassData ($article -> articleList[$canonical -> currentArgs['aID']]);
$view -> setPassData (array ('navigation' => bw :: $cateList, 'sociallink' => bw :: getSocialLinks (), 'externallink' => bw :: getExternalLinks (), 'tagClound' => bw :: getTagCloud ()));

$areaName = 'nocommentarea';
if ($conf['commentOpt']<>0) {
	loadServices (); //Load Baidu API or Duoshuo
	if ($conf['commentOpt'] == 1 || $conf['commentOpt'] == 2) { //Build-in comment
		@session_start ();
		$comment = new bwComment;
		$comment -> alterAID ($canonical -> currentArgs['aID']);
		$comment -> getComList ();
		$view -> setPassData (array ('comments' => $comment -> comList));
		$comkey = md5 ($comment -> initComAKey () . $comment -> initComSKey ());
		$view -> setPassData (array ('comkey' => $comkey));
		$areaName = 'commentarea';
	} elseif ($conf['commentOpt'] == 3) {
		$areaName = 'duoshuoarea';
	}
}

$view -> setMaster ('page');
$view -> setWorkFlow (array ($areaName, 'article', 'page')); //Built-in commentary system in development
$view -> finalize ();
