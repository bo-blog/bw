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
$view = new bwView;

if (isset ($canonical -> currentArgs['tValue'])) {
	$view -> setPageTitle ($conf['l']['page:Tags'] . ' - ' . $canonical -> currentArgs['tValue']);
	$view -> setActiveNav ('index');
} else {
	stopError ($conf['l']['admin:msg:NoContent']);
} 
$article -> getArticleListByTag ($canonical -> currentArgs['tValue']);
loadServices (); //Load Duoshuo
// Pagination
$canonical -> calTotalPages ($article -> totalArticles);

$view -> doPagination ();
$view -> setPassData (array ('articlesummary' => $article -> articleList));

if (defined ('ajax')) {
	$view -> setMaster ('ajax-article-list');
	$view -> setWorkFlow (array ('summary', 'ajax-article-list'));
} else {
	$view -> setPassData (array ('navigation' => bw :: $cateList, 'sociallink' => bw :: getSocialLinks (), 'externallink' => bw :: getExternalLinks (), 'tagClound' => bw :: getTagCloud ()));
	$view -> setMaster ('page');
	$view -> setWorkFlow (array ('summary', 'page'));
} 
$view -> finalize ();
