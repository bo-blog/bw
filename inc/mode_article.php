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

loadServices (); //Load Duoshuo

$view = new bwView;
$view -> setPageTitle ($article -> articleList[$canonical -> currentArgs['aID']]['aTitle']);
$view -> setActiveNav ($article -> articleList[$canonical -> currentArgs['aID']]['aCateURLName']);
$view -> setPassData ($article -> articleList[$canonical -> currentArgs['aID']]);
$view -> setPassData (array ('navigation' => bw :: $cateList, 'sociallink' => bw :: getSocialLinks (), 'externallink' => bw :: getExternalLinks (), 'tagClound' => bw :: getTagCloud ()));
$view -> setMaster ('page');
$view -> setWorkFlow (array ('article', 'page'));
$view -> finalize ();
