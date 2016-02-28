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

	public function __construct ()
	{
		global $canonical;
		$this -> pageNum = $canonical -> currentPage;
		$this -> articleList = array();
		$this -> listCate = 'all';
		$this -> totalArticles = 0;
	} 

	public function getArticleList ()
	{
		$currentTitleStart = ($this -> pageNum-1) * bw :: $conf['perPage'];

		$qStr = $this -> listCate == 'all' ? 'SELECT * FROM articles WHERE aCateURLName<>"0" AND aTime<=? ORDER BY aTime DESC LIMIT ?, ?' : 'SELECT * FROM articles WHERE aCateURLName=? AND aTime<=? ORDER BY aTime DESC LIMIT ?, ?';
		if ($this -> listCate != 'all') {
			$qBind = array ($this -> listCate, date ('Y-m-d H:i:s'), $currentTitleStart, bw :: $conf['perPage']);
		} else {
			$qBind = array (date ('Y-m-d H:i:s'), $currentTitleStart, bw :: $conf['perPage']);
		} 
		$allTitles = bw :: $db -> getRows ($qStr, $qBind);

		$this -> parseArticleList ($allTitles);
		hook ('getArticleList', 'Execute', $this);
		$this -> getTotalArticles ();
	} 

	public function getHottestArticles ($howMany)
	{
		$qStr = $this -> listCate == 'all' ? 'SELECT * FROM articles WHERE aCateURLName<>"0" AND aTime<=? ORDER BY aReads DESC LIMIT 0, ' . $howMany : 'SELECT * FROM articles WHERE aCateURLName=? AND aTime<=? ORDER BY aReads DESC LIMIT 0, ' . $howMany;
		$qBind = $this -> listCate == 'all' ? array(date ('Y-m-d H:i:s')) : array($this -> listCate, date ('Y-m-d H:i:s'));

		$allTitles = bw :: $db -> getRows ($qStr, $qBind);

		$this -> parseArticleList ($allTitles);
		hook ('getHottestArticles', 'Execute', $this);
		$this -> getTotalArticles ();
	} 

	public function getArticleListByTag ($tValue)
	{
		$currentTitleStart = ($this -> pageNum-1) * bw :: $conf['perPage'];
		$tagList = bw :: $db -> getSingleRow ('SELECT * FROM tags WHERE tValue=? LIMIT 0, 1', array($tValue));
		if (!isset ($tagList['tList'])) {
			stopError (bw :: $conf['l']['admin:msg:NoContent']);
		} 

		$allIDs = str_replace (']', '', substr ($tagList['tList'], 1));

		$allTitles = bw :: $db -> getRows ("SELECT * FROM articles WHERE aID IN ({$allIDs})  AND aTime<=? ORDER BY aTime DESC LIMIT ?, ?", array (date ('Y-m-d H:i:s'), $currentTitleStart, bw :: $conf['perPage']));

		$this -> parseArticleList ($allTitles);
		hook ('getArticleListByTag', 'Execute', $this);
		$this -> totalArticles = $tagList['tCount'];
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

	public function fetchArticle ($aID)
	{
		$this -> articleList[$aID] = bw :: $db -> getSingleRow ('SELECT * FROM articles WHERE aID=?', array($aID));

		if (!isset ($this -> articleList[$aID]['aID'])) {
			stopError (bw :: $conf['l']['admin:msg:NotExist']);
		} 
		$this -> articleList[$aID]['aCateDispName'] = bw :: $cateData[$this -> articleList[$aID]['aCateURLName']];
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
		$this -> fetchArticle ($smt['originID']);
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

	private function parseArticleList ($allTitles)
	{
		if (count ($allTitles) < 1) {
			stopError (bw :: $conf['l']['admin:msg:NoContent']);
		} 
		$this -> articleList = array ();

		foreach ($allTitles as $aID => $row) {
			$this -> articleList[$aID] = $row;
			$this -> articleList[$aID]['aCateDispName'] = bw :: $cateData[$row['aCateURLName']];
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
			$this -> totalArticles = bw :: $db -> countRows ('SELECT aID FROM articles WHERE aCateURLName<>"0" AND aTime<=?', array (date ('Y-m-d H:i:s')));
		} else {
			$this -> totalArticles = bw :: $db -> countRows ('SELECT aID FROM articles WHERE aCateURLName=? AND aTime<=?', array ($this -> listCate, date ('Y-m-d H:i:s')));
		} 
		hook ('getTotalArticles', 'Execute', $this);
	} 

	private function updateCateCount ($aCateURLName, $var)
	{
		$qStr = $var > 0 ? 'UPDATE categories SET aCateCount=aCateCount+?' : 'UPDATE categories SET aCateCount=aCateCount-?';
		$qStr .= ' WHERE aCateURLName=?';
		bw :: $db -> dbExec ($qStr, array (abs(floor ($var)), $aCateURLName));
		hook ('updateCateCount', 'Execute', $aCateURLName, $var);
	} 

	private function checkArticleData ($smt)
	{
		$acceptedKeys = array ('aTitle', 'aID', 'aContent', 'aCateURLName', 'aTime', 'aTags');
		if (isset ($smt['originID'])) {
			$acceptedKeys[] = 'originID';
		} 
		$smt = dataFilter ($acceptedKeys, $smt);
		if (empty ($smt['aTitle']) || $smt['aID'] === '' || empty ($smt['aContent']) || $smt['aCateURLName'] === '') {
			stopError (bw :: $conf['l']['admin:msg:NoData']);
		} 
		if (!array_key_exists ($smt['aCateURLName'], bw :: $cateData)) {
			stopError (bw :: $conf['l']['admin:msg:NotExist']);
		} 
		if (empty ($smt['aTime'])) {
			$smt['aTime'] = date ('Y-m-d H:i:s');
		} else {
			$smt['aTime'] = date ('Y-m-d H:i:s', strtotime ($smt['aTime']));
		} 
		$smt['aTitle'] = htmlspecialchars ($smt['aTitle'], ENT_QUOTES, 'UTF-8');
		$smt['aID'] = urlencode ($smt['aID']);
		return $smt;
	} 
} 

