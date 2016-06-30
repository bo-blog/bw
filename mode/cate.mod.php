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

if (isset ($canonical -> currentArgs['cateID'])) {
	$article -> alterCate($canonical -> currentArgs['cateID']);
	$view -> setPageTitle (bw :: $cateData[$canonical -> currentArgs['cateID']]);
	$view -> setActiveNav ($canonical -> currentArgs['cateID']);
	if (bw :: $cateList[$canonical -> currentArgs['cateID']]['aCateTheme']) {
		$view -> setTheme (bw :: $cateList[$canonical -> currentArgs['cateID']]['aCateTheme']);
	}
} else {
	$view -> setActiveNav ('index');
} 
$view -> setMetaData (bw :: $conf['siteName']);
$article -> getArticleList ();

// Pagination
$canonical -> calTotalPages ($article -> totalArticles);
$view -> doPagination ();
// Pass Values
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
