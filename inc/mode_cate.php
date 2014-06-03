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
} else {
	$view -> setActiveNav ('index');
} 
$article -> getArticleList ();
// Pagination
$canonical -> calTotalPages ($article -> totalArticles);

$view -> doPagination ();
foreach ($article -> articleList as $aID => $row) {
	if ($row['aTags']) {
		$view -> setLoop ('singletag', $row['aAllTags']);
		$view -> setMaster ('tagwrapper');
		$view -> setWorkFlow (array ('singletag', 'tagwrapper'));
		$view -> generateOutput ();
		$article -> articleList[$aID]['tagsInView'] = $view -> outputContent;
	} 
} 

$view -> setLoop ('summary', $article -> articleList);

if (defined ('ajax')) {
	$view -> setMaster ('ajax-article-list');
	$view -> setWorkFlow (array ('summary', 'ajax-article-list'));
} else {
	$view -> setLoop ('navigation', bw :: $cateList);
	$view -> setLoop ('sociallink', bw :: getSocialLinks ());
	$view -> setLoop ('externallink', bw :: getExternalLinks ());
	$view -> setMaster ('page');
	$view -> setWorkFlow (array ('summary', 'navigation', 'sociallink', 'externallink', 'page'));
} 
$view -> finalize ();

