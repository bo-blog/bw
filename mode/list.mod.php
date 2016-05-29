<?php
/**
* 
* @link http://bw.bo-blog.com
* @copyright (c) 2015 bW Development Team
* @license MIT
*/
if (!defined ('P')) {
	die ('Access Denied.');
} 


//This is a hidden mode. It is inteded to be used in Mill project.
if (!isset ($_REQUEST['list'])) {
	$listMode = 'archives';
} 
elseif ($_REQUEST['list'] == 'archives') {
	$listMode = 'archives';
}
elseif ($_REQUEST['list'] == 'tags') {
	$listMode = 'tags';
}
elseif ($_REQUEST['list'] == 'category') {
	$listMode = 'category';
}
else {
	exit ();
}

$article = new bwArticle;
$view = new bwView;

$article -> alterPerPage (50000);
$view -> setActiveNav ($listMode);


$groupedArticles = array ();

if ($listMode == 'archives') {
	$article -> getArticleList ();
	$allStatURL = array ();
	foreach ($article -> articleList as $oneArticle) {
		$YYYY = substr ($oneArticle['aTime'], 0, 4);
		$groupedArticles[$YYYY][] = $oneArticle;
		$columnName[$YYYY] = $YYYY;
		$columnID[$YYYY] = $YYYY;
		isset ($columnCount[$YYYY]) ?  $columnCount[$YYYY]++ : $columnCount[$YYYY] = 1;
		$allStatURL[$oneArticle['aID']] = "{$conf['siteURL']}/{$conf['linkPrefixArticle']}/{$oneArticle['aID']}/";
	}
	file_put_contents (P. 'conf/allAIDs.php', "<?php\r\n\$lf=" . var_export ($allStatURL, true) . ";");
	krsort ($groupedArticles);
}
if ($listMode == 'tags') {
	$allTags = bw :: $db -> getRows ('SELECT tValue, tCount FROM tags ORDER BY tCount DESC');
	foreach ($allTags as $aTag) {
		$article -> getArticleListByTag ($aTag['tValue']);
		$groupedArticles[$aTag['tValue']] = $article -> articleList;
		$columnName[$aTag['tValue']] = $aTag['tValue'];
		$columnID[$aTag['tValue']] = $aTag['tValue'];
		$columnCount[$aTag['tValue']] = $aTag['tCount'];
	} 
} 
if ($listMode == 'category') { 
	$article -> getArticleList ();
	foreach ($article -> articleList as $oneArticle) {
		$cate = $oneArticle['aCateURLName'];
		$groupedArticles[$cate][] = $oneArticle;
		$columnName[$cate] = $oneArticle['aCateDispName'];
		$columnID[$cate] = $oneArticle['aCateURLName'];
		isset ($columnCount[$cate]) ?  $columnCount[$cate]++ : $columnCount[$cate] = 1;
	} 
	foreach (bw :: $cateList as $onecate) {
		if (isset ($groupedArticles[$onecate['aCateURLName']])) {
			$returnGroupArticles[$onecate['aCateURLName']] = $groupedArticles[$onecate['aCateURLName']] ;
		}
	}
	$groupedArticles = $returnGroupArticles;
}


$partOut = '';
foreach ($groupedArticles as $col => $val) {
	$view -> setMaster ('groupcolumn');
	$view -> setPassData (array ('groupedArticles' => $val, 'columnName' => $columnName[$col], 'columnID' => $columnID[$col], 'columnCount' => $columnCount[$col]));
	$view -> setWorkFlow (array ('groupcolumn'));
	$partOut.= $view -> getOutput ();
	$view -> resetPassData ();
}

$view -> setMaster ('page');
$view -> setPassData (array ('navigation' => bw :: $cateList, 'sociallink' => bw :: getSocialLinks (), 'externallink' => bw :: getExternalLinks (), 'tagClound' => bw :: getTagCloud ()));
$view -> setPassData (array ('listContent' => $partOut));
$view -> setWorkFlow (array ('page'));
 
$view -> finalize ();
