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

$article = new bwArticle;

$article -> fetchArticle ($canonical -> currentArgs['aID'], true);

$view = new bwView;
$view -> setPageTitle ($article -> articleList[$canonical -> currentArgs['aID']]['aTitle']);
$article -> articleList[$canonical -> currentArgs['aID']]['aContent'] = $view -> commonParser ($article -> articleList[$canonical -> currentArgs['aID']]['aContent']);
$view -> setActiveNav ('index');
$view -> setPassData ($article -> articleList[$canonical -> currentArgs['aID']]);
$view -> setPassData (array ('navigation' => bw :: $cateList, 'sociallink' => bw :: getSocialLinks (), 'externallink' => bw :: getExternalLinks (), 'tagClound' => bw :: getTagCloud ()));
$view -> setMaster ('page');
$view -> setWorkFlow (array ('singlepage', 'page'));
$view -> finalize ();
