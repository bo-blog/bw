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

class bwArticle {
	public $articleList;
	public $totalArticles;
	private $pageNum;
	private $listCate;
	private $cutTime;

	public function __construct ()
	{
		global $canonical;
		$this -> pageNum = $canonical -> currentPage;
		$this -> articleList = array();
		$this -> listCate = 'all';
		$this -> totalArticles = 0;
		$this -> cutTime = date ('Y-m-d H:i:s');
		$this -> sinceTime = date ('1970-01-01 00:00:00');
	} 

	public function getArticleList ()
	{
		$currentTitleStart = ($this -> pageNum-1) * bw :: $conf['perPage'];

		$qStr = $this -> listCate == 'all' ? 'SELECT * FROM articles WHERE aCateURLName<>"_trash" AND aCateURLName<>"_page" AND aTime<=? AND aTime>? ORDER BY aTime DESC LIMIT ?, ?' : 'SELECT * FROM articles WHERE aCateURLName=? AND aTime<=? AND aTime>? ORDER BY aTime DESC LIMIT ?, ?';
		if ($this -> listCate != 'all') {
			$qBind = array ($this -> listCate, $this -> cutTime, $this -> sinceTime, $currentTitleStart, bw :: $conf['perPage']);
		} else {
			$qBind = array ($this -> cutTime, $this -> sinceTime, $currentTitleStart, bw :: $conf['perPage']);
		} 
		$allTitles = bw :: $db -> getRows ($qStr, $qBind);

		$this -> parseArticleList ($allTitles);
		hook ('getArticleList', 'Execute', $this);
		$this -> getTotalArticles ();
	} 

	public function getTrashedList ()
	{
		$this -> listCate = '_trash';
		hook ('getTrashedList', 'Execute', $this);
		$this -> getArticleList ();
	} 

	public function getSinglePageList ()
	{
		$this -> listCate = '_page';
		hook ('getSinglePageList', 'Execute', $this);
		$this -> getArticleList ();
	} 

	public function getHottestArticles ($howMany)
	{
		$qStr = $this -> listCate == 'all' ? 'SELECT * FROM articles WHERE aCateURLName<>"_trash" AND aCateURLName<>"_page" AND aTime<=? ORDER BY aReads DESC LIMIT 0, ' . $howMany : 'SELECT * FROM articles WHERE aCateURLName=? AND aTime<=? ORDER BY aReads DESC LIMIT 0, ' . $howMany;
		$qBind = $this -> listCate == 'all' ? array($this -> cutTime) : array($this -> listCate, $this -> cutTime);

		$allTitles = bw :: $db -> getRows ($qStr, $qBind);

		$this -> parseArticleList ($allTitles);
		hook ('getHottestArticles', 'Execute', $this);
		$this -> totalArticles = $howMany;
	} 

	public function getArticleListByTag ($tValue)
	{
		$currentTitleStart = ($this -> pageNum-1) * bw :: $conf['perPage'];
		$tagList = bw :: $db -> getSingleRow ('SELECT * FROM tags WHERE tValue=? LIMIT 0, 1', array($tValue));
		if (!isset ($tagList['tList'])) {
			stopError (bw :: $conf['l']['admin:msg:NoContent']);
		} 

		$allIDs = str_replace (']', '', substr ($tagList['tList'], 1));

		$allTitles = bw :: $db -> getRows ("SELECT * FROM articles WHERE aID IN ({$allIDs}) AND aCateURLName<>\"_trash\" AND aCateURLName<>\"_page\" AND aTime<=? AND aTime>? ORDER BY aTime DESC LIMIT ?, ?", array ($this -> cutTime, $this -> sinceTime, $currentTitleStart, bw :: $conf['perPage']));

		$this -> parseArticleList ($allTitles);
		hook ('getArticleListByTag', 'Execute', $this);
		$this -> totalArticles = bw :: $db -> countRows ("SELECT aID FROM articles WHERE aID IN ({$allIDs}) AND aCateURLName<>\"_trash\" AND aTime<=? AND aTime>?", array ($this -> cutTime, $this -> sinceTime));
	} 

	public function alterCate ($cateID)
	{
		if (!array_key_exists ($cateID, bw :: $cateData)) {
			stopError (bw :: $conf['l']['admin:msg:NotExist']);
		} else {
			$this -> listCate = $cateID;
		} 
		hook ('alterCate', 'Execute', $this);
	} 

	public function fetchArticle ($aID, $inTrash = false)
	{
		$this -> articleList[$aID] = $inTrash ? bw :: $db -> getSingleRow ('SELECT * FROM articles WHERE aID=?', array($aID)) : bw :: $db -> getSingleRow ('SELECT * FROM articles WHERE aID=? AND aCateURLName<>"_trash" AND aCateURLName<>"_page"', array($aID));

		if (!isset ($this -> articleList[$aID]['aID'])) {
			stopError (bw :: $conf['l']['admin:msg:NotExist']);
		} 
		if ($inTrash) {//Draft or single page
			$this -> articleList[$aID]['aCateDispName'] = $this -> articleList[$aID]['aCateURLName'] == '_trash' ? bw :: $conf['l']['admin:item:TrashBin'] : bw :: $conf['l']['page:SinglePage'];
		}
		else {
			$this -> articleList[$aID]['aCateDispName'] = bw :: $cateData[$this -> articleList[$aID]['aCateURLName']];
		}
		$this -> articleList[$aID]['aAllTags'] = stringToArray (@explode (',', $this -> articleList[$aID]['aTags']), 'tagValue');
		if (isset (bw :: $conf['commentOpt'])) {
			if (bw :: $conf['commentOpt'] == 0 || bw :: $conf['commentOpt'] == 3) { // If using non-built-in comment system, give an empty string instead of 0 for the attribute aComments
				$this -> articleList[$aID]['aComments'] = '';
			} 
		} 
		hook ('fetchArticle', 'Execute', $this);
	} 

	public function alterPerPage ($num)
	{
		bw :: $conf['perPage'] = floor ($num);
	} 

	public function alterPageNum ($num)
	{
		$this -> pageNum = floor ($num);
	} 

	public function setCutTime ($timeStamp)
	{
		$this -> cutTime = $timeStamp == 0 ? '9999-12-31 23:59:59' : date ('Y-m-d H:i:s', $timeStamp);
	} 

	public function setSinceTime ($timeStamp)
	{
		$this -> sinceTime = $timeStamp == 0 ? '1970-01-01 00:00:00' : date ('Y-m-d H:i:s', $timeStamp);
	} 

	public function addArticle ($smt)
	{
		$smt = $this -> checkArticleData ($smt);

		$taID = bw :: $db -> getSingleRow ('SELECT * FROM articles WHERE aID=?', array($smt['aID']));
		if (isset ($taID['aID'])) {
			stopError (bw :: $conf['l']['admin:msg:Existed']);
		} 

		if ($smt['aTags']) {
			$this -> addArticleIntoTags ($smt['aID'], htmlspecialchars ($smt['aTags'], ENT_QUOTES, 'UTF-8'));
		} 

		bw :: $db -> dbExec ('INSERT INTO articles (aID, aTitle, aCateURLName, aTime, aTags, aReads, aContent) VALUES (?, ?, ?, ?, ?, 0, ?)', array ($smt['aID'], $smt['aTitle'], $smt['aCateURLName'], $smt['aTime'], $smt['aTags'], $smt['aContent']));
		$this -> updateCateCount ($smt['aCateURLName'], 1);
		clearCache (); //Clear all cache
		hook ('addArticle', 'Execute', $smt);
		return true;
	} 

	public function updateArticle ($smt)
	{
		$smt = $this -> checkArticleData ($smt);
		$this -> fetchArticle ($smt['originID'], true);
		$old = $this -> articleList[$smt['originID']];

		if ($smt['aID'] <> $old['aID']) {
			stopError (bw :: $conf['l']['admin:msg:NoChangeID']);
		} 

		if ($smt['aTags'] <> $old['aTags']) {
			$smt['aTags'] = htmlspecialchars ($smt['aTags'], ENT_QUOTES, 'UTF-8');
			$oldTags = @explode (',', $old['aTags']);
			$currentTags = @explode (',', $smt['aTags']);
			$newTags = array_diff ($currentTags, $oldTags);
			$deleteTags = array_diff ($oldTags, $currentTags);
			$this -> addArticleIntoTags ($smt['aID'], $newTags);
			$this -> deleteArticleFromTags ($smt['aID'], $deleteTags); 
		} 

		bw :: $db -> dbExec ('UPDATE articles SET aTitle=?, aCateURLName=?, aTime=?, aContent=?, aTags=? WHERE aID=?', array ($smt['aTitle'], $smt['aCateURLName'], $smt['aTime'], $smt['aContent'], $smt['aTags'], $smt['aID']));

		if ($smt['aCateURLName'] <> $old['aCateURLName']) {
			$this -> updateCateCount ($smt['aCateURLName'], 1);
			$this -> updateCateCount ($old['aCateURLName'], -1);
		} 
		clearCache (); //Clear all cache
		hook ('updateArticle', 'Execute', $smt);
		return true;
	} 

	public function deleteArticle ($aID)
	{
		$taID = bw :: $db -> getSingleRow ('SELECT * FROM articles WHERE aID=?', array($aID));
		if (!isset ($taID['aID'])) {
			stopError (bw :: $conf['l']['admin:msg:NotExist']);
		} 

		if ($taID['aTags']) {
			$this -> deleteArticleFromTags ($aID, $taID['aTags']);
		} 

		bw :: $db -> dbExec ('DELETE FROM articles WHERE aID=?', array ($aID));
		$this -> updateCateCount ($taID['aCateURLName'], -1);
		clearCache (); //Clear all cache
		hook ('deleteArticle', 'Execute', $aID);
		return true;
	} 

	public function deleteArticleBatch ($aIDList)
	{
		if (is_array ($aIDList)) {
			foreach ($aIDList as $aID) {
				$taID = bw :: $db -> getSingleRow ('SELECT * FROM articles WHERE aID=?', array($aID));
				if (!isset ($taID['aID'])) {
					continue;
				} 
				if ($taID['aTags']) {
					$this -> deleteArticleFromTags ($aID, $taID['aTags']);
				} 
				bw :: $db -> dbExec ('DELETE FROM articles WHERE aID=?', array ($aID));
				$this -> updateCateCount ($taID['aCateURLName'], -1);
			}
		}
		clearCache (); //Clear all cache
		hook ('deleteArticleBatch', 'Execute', $aIDList);
		return true;
	} 

	public function changeAsDraft ($aIDList)
	{
		if (is_array ($aIDList)) {
			$countChangedCate = array ();
			foreach ($aIDList as $aID) {
				$taID = bw :: $db -> getSingleRow ('SELECT * FROM articles WHERE aID=?', array($aID));
				if (!isset ($taID['aID'])) {
					continue;
				} 
				if ($taID['aCateURLName'] <> '_trash') {
					bw :: $db -> dbExec ('UPDATE articles SET aCateURLName="_trash" WHERE aID=?', array ($taID['aID']));
					$this -> updateCateCount ($taID['aCateURLName'], -1);
				} 
			}
		} 
		clearCache (); //Clear all cache
		hook ('changeAsDraft', 'Execute', $aIDList);
		return true;
	} 

	public static function addArticleIntoTags ($aID, $allTags)
	{
		if (is_string ($allTags)) {
			$allTags = @explode (',', $allTags);
		} 
		foreach ($allTags as $aTag) {
			$tVal = bw :: $db -> getSingleRow ('SELECT * FROM tags WHERE tValue=? LIMIT 0, 1', array($aTag));
			if (isset ($tVal['tList'])) {
				$allArticles = @json_decode ($tVal['tList'], true);
				if (!in_array ($aID, $allArticles)) {
					$allArticles[] = $aID;
					bw :: $db -> dbExec ('UPDATE tags SET tList=?, tCount=tCount+1 WHERE tValue=?', array(json_encode($allArticles), $aTag));
				} 
			} else {
				bw :: $db -> dbExec ('INSERT INTO tags (tValue, tList, tCount) VALUES (?, ?, 1)', array($aTag, json_encode(array ($aID))));
			} 
		} 
	} 

	public static function deleteArticleFromTags ($aID, $allTags)
	{
		if (is_string ($allTags)) {
			$allTags = @explode (',', $allTags);
		} 
		foreach ($allTags as $aTag) {
			$tVal = bw :: $db -> getSingleRow ('SELECT * FROM tags WHERE tValue=? LIMIT 0, 1', array($aTag));
			if (isset ($tVal['tList'])) {
				$allArticles = @json_decode ($tVal['tList'], true);
				$aIDKey = array_search ($aID, $allArticles);

				if ($aIDKey !== false && $aIDKey !== null) {
					unset ($allArticles[$aIDKey]);
					if (count ($allArticles) > 0) {
						bw :: $db -> dbExec ('UPDATE tags SET tList=?, tCount=tCount-1 WHERE tValue=?', array(json_encode($allArticles), $aTag));
					} else {
						bw :: $db -> dbExec ('DELETE FROM tags WHERE tValue=?', array($aTag));
					} 
				} 
			} 
		} 
	} 

	public function getTitleList ($howmany)
	{
		$qStr = $this -> listCate == 'all' ? 'SELECT aID FROM articles WHERE aCateURLName<>"_trash" AND aCateURLName<>"_page" AND aTime<=? ORDER BY aTime DESC LIMIT 0, ?' : 'SELECT aID FROM articles WHERE aCateURLName=? AND aTime<=? ORDER BY aTime DESC LIMIT 0, ?';
		if ($this -> listCate != 'all') {
			$qBind = array ($this -> listCate, $this -> cutTime, $howmany);
		} else {
			$qBind = array ($this -> cutTime, $howmany);
		} 
		$allTitles = bw :: $db -> getColumns ($qStr, $qBind);
		$qStr = $this -> listCate == 'all' ? 'SELECT aTitle FROM articles WHERE aCateURLName<>"_trash" AND aCateURLName<>"_page" AND aTime<=? ORDER BY aTime DESC LIMIT 0, ?' : 'SELECT aTitle FROM articles WHERE aCateURLName=? AND aTime<=? ORDER BY aTime DESC LIMIT 0, ?';
		if ($this -> listCate != 'all') {
			$qBind = array ($this -> listCate, $this -> cutTime, $howmany);
		} else {
			$qBind = array ($this -> cutTime, $howmany);
		} 
		$allTitles2 = bw :: $db -> getColumns ($qStr, $qBind);
		if (isset ($allTitles['aID'])) {
			return array_combine ($allTitles['aID'], $allTitles2['aTitle']);
		}
		hook ('getTitleList', 'Execute', $this);
	} 

	private function parseArticleList ($allTitles)
	{
		if (count ($allTitles) < 1) {
			$this -> articleList = array ();
			return;
		} 
		$this -> articleList = array ();

		foreach ($allTitles as $aID => $row) {
			$this -> articleList[$aID] = $row;
			if ($row['aCateURLName'] == '_trash') {
				$this -> articleList[$aID]['aCateDispName'] =  bw :: $conf['l']['admin:item:TrashBin'];
			} elseif ($row['aCateURLName'] == '_page') {
				$this -> articleList[$aID]['aCateDispName'] =  bw :: $conf['l']['page:SinglePage'];
			} else {
				$this -> articleList[$aID]['aCateDispName'] =  bw :: $cateData[$row['aCateURLName']];
			}
			$this -> articleList[$aID]['aAllTags'] = stringToArray (@explode (',', $row['aTags']), 'tagValue');
			if (isset (bw :: $conf['commentOpt'])) {
				if (bw :: $conf['commentOpt'] == 0 || bw :: $conf['commentOpt'] == 3) { // If using non-built-in comment system, give an empty string instead of 0 for the attribute aComments
					$this -> articleList[$aID]['aComments'] = '';
				} 
			} 
		} 
	} 

	private function getTotalArticles ()
	{
		if ($this -> listCate == 'all') {
			$this -> totalArticles = bw :: $db -> countRows ('SELECT aID FROM articles WHERE aCateURLName<>"_trash" AND aCateURLName<>"_page" AND aTime<=?', array ($this -> cutTime));
		} else {
			$this -> totalArticles = bw :: $db -> countRows ('SELECT aID FROM articles WHERE aCateURLName=? AND aTime<=?', array ($this -> listCate, $this -> cutTime));
		} 
		hook ('getTotalArticles', 'Execute', $this);
	} 

	private function updateCateCount ($aCateURLName, $var)
	{
		if ($aCateURLName != '_trash') { 
			$qStr = $var > 0 ? 'UPDATE categories SET aCateCount=aCateCount+?' : 'UPDATE categories SET aCateCount=aCateCount-?';
			$qStr .= ' WHERE aCateURLName=?';
			bw :: $db -> dbExec ($qStr, array (abs(floor ($var)), $aCateURLName));
		}
		hook ('updateCateCount', 'Execute', $aCateURLName, $var);
	} 

	private function checkArticleData ($smt)
	{
		$acceptedKeys = array ('aTitle', 'aID', 'aContent', 'aCateURLName', 'aTime', 'aTags');
		if (isset ($smt['originID'])) {
			$acceptedKeys[] = 'originID';
		} 
		$smt = dataFilter ($acceptedKeys, $smt);
		if (empty ($smt['aTitle']) || $smt['aID'] === '' || empty ($smt['aContent'])) {
			stopError (bw :: $conf['l']['admin:msg:NoData']);
		} 
		if (!array_key_exists ($smt['aCateURLName'], bw :: $cateData) && $smt['aCateURLName'] != '_trash' && $smt['aCateURLName'] != '_page') {
			stopError (bw :: $conf['l']['admin:msg:NotExist'] . ': ' . $smt['aCateURLName']);
		} 
		if (empty ($smt['aTime'])) {
			$smt['aTime'] = $this -> cutTime;
		} else {
			$smt['aTime'] = date ('Y-m-d H:i:s', strtotime ($smt['aTime']));
		} 
		$smt['aTitle'] = htmlspecialchars ($smt['aTitle'], ENT_QUOTES, 'UTF-8');
		$smt['aID'] = urlencode ($smt['aID']);
		return $smt;
	} 

	public function getArticleTemplateList ()
	{
		$l = bw :: $conf['siteLang'];
		$templateSets = array();
		if ($handle = opendir (P . 'inc/template/')) {
			while (false !== ($file = readdir ($handle))) {
				if (strstr (P . 'inc/template/' . $file, '.tpl.php')) {
					$tpl = file_get_contents (P . 'inc/template/' . $file);
					//$tplValid = preg_match ("/<{$l}: name>(.+?)<\/{$l}: name>([\s\S]+?)<{$l}: definition>([\s\S]+?)<\/{$l}: definition>/", $tpl, $tplDefs);
					$tplValid = preg_match ("/<{$l}: name>(.+?)<\/{$l}: name>/", $tpl, $tplDefs);
					if ($tplValid == 1) {
						$templateSets[$tplDefs[1]] = array ('name' => $tplDefs[1], 'file' => str_replace ('.tpl.php', '', $file));
					}
				} 
			} 
		} 
		return $templateSets;

	}
} 

