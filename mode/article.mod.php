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
$view -> setMetaData ($article -> articleList[$canonical -> currentArgs['aID']]['aTags']);

$aCateURLName = $article -> articleList[$canonical -> currentArgs['aID']]['aCateURLName'];
$view -> setActiveNav ($aCateURLName);
if (bw :: $cateList[$aCateURLName]['aCateTheme']) {
	$view -> setTheme (bw :: $cateList[$aCateURLName]['aCateTheme']);
}
$view -> setPassData ($article -> articleList[$canonical -> currentArgs['aID']]);
$view -> setPassData (array ('navigation' => bw :: $cateList, 'sociallink' => bw :: getSocialLinks (), 'externallink' => bw :: getExternalLinks (), 'tagClound' => bw :: getTagCloud ()));
$view -> setMaster ('page');

if ($conf['commentOpt']<>0) {
	loadServices ();
	if ($conf['commentOpt'] == 1 || $conf['commentOpt'] == 2) { //Build-in comment

		//Discarded on 2016/6/22
		$view -> setWorkFlow (array ('nocommentarea', 'article', 'page'));

		/*
		@session_start ();
		$comment = new bwComment;
		$comment -> alterAID ($canonical -> currentArgs['aID']);
		$comment -> getComList ();
		$view -> setPassData (array ('comments' => $comment -> comList));
		$comkey = md5 ($comment -> initComAKey () . $comment -> initComSKey ());
		$view -> setPassData (array ('comkey' => $comkey));
		$totalBatches = ceil ($comment -> totalCom / bw :: $conf['comPerLoad']);
		$view -> setPassData (array ('totalbatches' => $totalBatches, 'currentbatch' => $canonical -> currentPage));
		$view -> setWorkFlow (array ('ajaxcommentgroup', 'commentarea', 'article', 'page'));
		*/
	} elseif ($conf['commentOpt'] == 3) {
		if ($conf['duoshuoID']) {
			$view -> setWorkFlow (array ('duoshuoarea', 'article', 'page'));
		}
		elseif ($conf['disqusID']) {
			$view -> setWorkFlow (array ('disqusarea', 'article', 'page'));
		} else {
			$view -> setWorkFlow (array ('nocommentarea', 'article', 'page'));
		}
	} 
}  else {
	$view -> setWorkFlow (array ('nocommentarea', 'article', 'page'));
}

$view -> finalize ();
