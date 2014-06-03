<?php 
// Copyright: Byke
define ('bwVersion', '0.9.1');

if (!defined ('P')) {
	die ('Access Denied.');
} 

include_once (P . 'conf/info.php');
date_default_timezone_set ($conf['timeZone']);

bw :: init ();

class bw {
	public static $conf;
	public static $db;
	public static $cateData;
	public static $cateList;
	public static $extData;
	public static $extList;

	public static function init ()
	{
		global $conf;
		self :: $conf = &$conf;
		include_once (P . 'inc/db.php');
		self :: $db = new bwDatabase;
		self :: $cateData = self :: $cateList = self :: $extList = self :: $extData = array();
	} 

	public static function initCategories ()
	{
		$cateList = self :: $db -> getRows ('SELECT * FROM categories ORDER BY aCateOrder DESC');
		foreach ($cateList as $aRow) {
			self :: $cateList[$aRow['aCateURLName']] = $aRow;
			self :: $cateData[$aRow['aCateURLName']] = $aRow['aCateDispName'];
		} 
	} 

	public static function pageStat ($canonicalURL, $aID = false)
	{
		$canonicalURL = str_replace (self :: $conf['siteURL'], '', $canonicalURL);
		DBTYPE=='MySQL' ? self :: $db -> dbExec ('INSERT IGNORE INTO statistics (pageURL, sNum) VALUES (?, 0)', array ($canonicalURL)) : self :: $db -> dbExec ('INSERT OR IGNORE INTO statistics (pageURL, sNum) VALUES (?, 0)', array ($canonicalURL));
		self :: $db -> dbExec ('UPDATE statistics SET sNum=sNUM+1, lastView=? WHERE pageURL=?', array (date ('Y-m-d H:i:s'), $canonicalURL));
		if ($aID) {
			self :: $db -> dbExec ('UPDATE articles SET aReads=aReads+1 WHERE aID=?', array ($aID));
		} 
	} 

	public static function loadLanguage ()
	{
		global $conf;
		self :: $conf['siteLang'] = basename (self :: $conf['siteLang']);
		if (file_exists (P . 'lang/' . self :: $conf['siteLang'] . '.php')) {
			include_once (P . 'lang/' . self :: $conf['siteLang'] . '.php');
		} else {
			include_once (P . "lang/en.php");
		} 
	} 

	public static function loadExtensions ()
	{
		$extData = self :: $db -> getRows ('SELECT * FROM extensions WHERE extActivate=1 ORDER BY extOrder DESC');
		foreach ($extData as $aExt) {
			self :: $extData[$aExt['extID']] = $aExt;
			$allHooks = @explode (',', $aExt['extHooks']);
			foreach ($allHooks as $aHook) {
				self :: $extList[$aHook][] = $aExt['extID'];
			} 
			if (file_exists (P . 'extension/' . basename ($aExt['extID']) . '/do.php')) {
				include_once (P . 'extension/' . basename ($aExt['extID']) . '/do.php');
				$aExtID = 'ext_' . $aExt['extID'];
				$aExtID :: init();
			} 
		} 
	} 

	public static function getSocialLinks ()
	{
		$allSocial = array('sina-weibo', 'weixin', 'douban', 'instagram', 'renren', 'linkedin');
		$allSocialLinks = array();
		foreach ($allSocial as $aSocial) {
			if (isset (bw :: $conf['social-' . $aSocial])) {
				if (bw :: $conf['social-' . $aSocial]) {
					$allSocialLinks[] = array ('socialLinkID' => $aSocial, 'socialLinkURL' => bw :: $conf['social-' . $aSocial]);
				} 
			} 
		} 
		return $allSocialLinks;
	} 

	public static function getExternalLinks ()
	{
		$allLinks = $allLinks2 = array();
		if (isset (bw :: $conf['externalLinks'])) {
			$allLinks = parse_ini_string (bw :: $conf['externalLinks']);
		} 
		foreach ($allLinks as $linkURL => $linkName) {
			$allLinks2[] = array ('linkURL' => $linkURL, 'linkName' => $linkName);
		} 
		return $allLinks2;
	} 
} 

class bwCategory {
	public function getCategories ()
	{
		bw :: initCategories ();
	} 

	public function addCategories ($smt)
	{
		if (is_array ($smt)) {
			$dataLine = array();
			foreach ($smt as $aCateURLName => $aCateDispName) {
				$aCateURLName = urlencode ($aCateURLName);
				$aCateDispName = htmlspecialchars ($aCateDispName, ENT_QUOTES, 'UTF-8');
				if (array_key_exists ($aCateURLName, bw :: $cateData)) {
					stopError (bw :: $conf['l']['admin:msg:Existed']);
				} else {
					$dataLine[] = array(':aCateURLName' => $aCateURLName, ':aCateDispName' => $aCateDispName);
				} 
			} 
			$dataLineCounter = count ($dataLine);
			if ($dataLineCounter > 0) {
				bw :: $db -> dbExecBatch ("INSERT INTO categories (aCateURLName, aCateDispName, aCateCount, aCateOrder) VALUES (:aCateURLName, :aCateDispName, 0, {$dataLineCounter})", $dataLine);
				$this -> getCategories (); //Refresh immediately
				clearCache (); //Clear all cache
			} 
			hook ('addCategories', 'Execute', $smt);
		} 
	} 

	public function deleteCategories ($deletedCates)
	{
		foreach ($deletedCates as $delCate => $delCateName) {
			$delLine = bw :: $db -> getSingleRow ('SELECT * FROM categories WHERE aCateURLName=:delCate', array(':delCate' => $delCate));
			if ($delLine['aCateCounter'] == 0) {
				bw :: $db -> dbExec ('DELETE FROM categories WHERE aCateURLName=:delCate', array(':delCate' => $delCate));
			} else {
				stopError (bw :: $conf['l']['admin:msg:CategoryNotEmpty']);
			} 
		} 
		$this -> getCategories (); //Refresh immediately
		clearCache (); //Clear all cache
		hook ('deleteCategories', 'Execute', $deletedCates);
	} 

	public function orderCategories ($arrayOrder)
	{
		$dataLine = array();
		$i = count ($arrayOrder)-1;
		foreach ($arrayOrder as $order) {
			$dataLine[$i][':aCateOrder'] = $i;
			$dataLine[$i][':aCateURLName'] = $order;
			$i -= 1;
		} 
		bw :: $db -> dbExecBatch ('UPDATE categories SET aCateOrder=:aCateOrder WHERE aCateURLName=:aCateURLName', $dataLine);
		$this -> getCategories ();
		clearCache (); //Clear all cache
		hook ('orderCategories', 'Execute', $arrayOrder);
	} 
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

		$qStr = $this -> listCate == 'all' ? 'SELECT * FROM articles ORDER BY aTime DESC LIMIT ?, ?' : 'SELECT * FROM articles WHERE aCateURLName=? ORDER BY aTime DESC LIMIT ?, ?';
		if ($this -> listCate != 'all') {
			$qBind = array ($this -> listCate, $currentTitleStart, bw :: $conf['perPage']);
		} else {
			$qBind = array ($currentTitleStart, bw :: $conf['perPage']);
		}
		$allTitles = bw :: $db -> getRows ($qStr, $qBind);
	

		if (count ($allTitles) < 1) {
			stopError (bw :: $conf['l']['admin:msg:NoContent']);
		} 

		foreach ($allTitles as $aID => $row) {
			$this -> articleList[$aID] = $row;
			$this -> articleList[$aID]['aCateDispName'] = bw :: $cateData[$row['aCateURLName']];
			$this -> articleList[$aID]['aAllTags'] = stringToArray (@explode (',', $row['aTags']), 'tagValue');
		} 

		hook ('getArticleList', 'Execute', $this);
		$this -> getTotalArticles ();
	} 

	public function getHottestArticles ($howMany)
	{
		$qStr = $this -> listCate == 'all' ? 'SELECT * FROM articles ORDER BY aReads DESC LIMIT 0, ' . $howMany : 'SELECT * FROM articles WHERE aCateURLName=:aCateURLName ORDER BY aReads DESC LIMIT 0, ' . $howMany;
		$qBind = $this -> listCate == 'all' ? array() : array(':aCateURLName' => $this -> listCate);

		$allTitles = bw :: $db -> getRows ($qStr, $qBind);

		if (count ($allTitles) < 1) {
			stopError (bw :: $conf['l']['admin:msg:NoContent']);
		} 

		foreach ($allTitles as $aID => $row) {
			$this -> articleList[$aID] = $row;
			$this -> articleList[$aID]['aCateDispName'] = bw :: $cateData[$row['aCateURLName']];
			$this -> articleList[$aID]['aAllTags'] = stringToArray (@explode (',', $row['aTags']), 'tagValue');
		} 
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

		$allTitles = bw :: $db -> getRows ("SELECT * FROM articles WHERE aID IN ({$allIDs}) ORDER BY aTime DESC LIMIT :currentTitleStart, :perPage", array (':currentTitleStart' => $currentTitleStart, ':perPage' => bw :: $conf['perPage']));

		foreach ($allTitles as $aID => $row) {
			$this -> articleList[$aID] = $row;
			$this -> articleList[$aID]['aCateDispName'] = bw :: $cateData[$row['aCateURLName']];
			$this -> articleList[$aID]['aAllTags'] = stringToArray (@explode (',', $row['aTags']), 'tagValue');
		} 

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
			/**
			* Do we really want the user to change the ID? Cause a lot of problems, like Tags
			* 2014/6/1: The decision is NO.
			* $taID=bw::$db->getSingleRow ('SELECT * FROM articles WHERE aID=?', array($smt['aID']));
			* if (isset ($taID['aID']))
			* {
			* stopError (bw::$conf['l']['admin:msg:Existed']);
			* }
			*/
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
			// stopError ('New: '.implode (', ', $newTags).' Remove: '.implode (', ', $deleteTags));
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

	private function getTotalArticles ()
	{
		if ($this -> listCate == 'all') {
			$this -> totalArticles = bw :: $db -> countRows ('SELECT aID FROM articles');
		} else {
			$this -> totalArticles = bw :: $cateList[$this -> listCate]['aCateCount'];
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
		if (empty ($smt['aTitle']) || empty ($smt['aID']) || empty ($smt['aContent']) || empty ($smt['aCateURLName'])) {
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

class bwView {
	public $viewWorkFlow;
	private $parts;
	private $modContent;
	public $outputContent;
	private $themeDir;
	private $loopData;
	private $passData;
	private $masterMod;
	private $loopEach;
	private $markdownParser;
	private $themeInternal;

	public function __construct ()
	{
		global $conf;
		$this -> viewWorkFlow = $this -> modContent = $this -> loopData = $this -> loopEach = $this -> themeInternal = $this -> parts = $this -> viewHooks = $this -> passData = array();
		$this -> outputContent = $this -> masterMod = '';
		$this -> markdownParser = null;
		$this -> themeDir = P . 'theme/default';
		$this -> passData['pageTitle'] = '';
	} 

	public function setTheme ($setThemeDir)
	{
		$setThemeDir = basename ($setThemeDir);
		if (!file_exists (P . "theme/{$setThemeDir}/info.php")) {
			$setThemeDir = 'default';
		} else {
			$this -> themeDir = P . "theme/{$setThemeDir}";
		} 
	} 

	public function setLoop ($modName, $modData)
	{
		$this -> loopData[$modName] = $modData;
	} 

	public function setMaster ($modName)
	{
		$this -> masterMod = $modName;
	} 

	public function setPassData ($arrayData)
	{
		$this -> passData += $arrayData;
	} 

	public function setWorkFlow ($arrayWorkFlow)
	{
		if (is_array ($arrayWorkFlow)) {
			$this -> viewWorkFlow = $arrayWorkFlow;
		} 
	} 

	public function generateOutput ()
	{
		if (file_exists ($this -> themeDir . "/components.php")) {
			include_once ($this -> themeDir . "/components.php");
			$this -> parts = (count($this -> parts)) ? $this -> parts : $parts;
		} 

		hook ('generateOutputInit', 'Execute', $this);

		foreach ($this -> viewWorkFlow as $viewMod) {
			$viewMod = basename ($viewMod);
			if (file_exists ("{$this->themeDir}/{$viewMod}.php")) {
				ob_start ();
				include ("{$this->themeDir}/{$viewMod}.php");
				$obContent = ob_get_clean ();
			} elseif (array_key_exists ($viewMod, $this -> parts)) {
				$obContent = $this -> parts[$viewMod];
			} else {
				continue;
			} 
			// load looped data
			if (array_key_exists ($viewMod, $this -> loopData)) {
				$this -> modContent[$viewMod] = '';
				foreach ($this -> loopData[$viewMod] as $this -> loopEach) {
					$this -> modContent[$viewMod] .= preg_replace_callback ('/\[\[::(.+?)\]\]/', array($this, 'strInLoop'), $obContent);
				} 
			} else {
				$this -> modContent[$viewMod] = preg_replace_callback ('/\[\[::(.+?)\]\]/', array($this, 'strInTheme'), $obContent);
			} 
		} 
		$this -> outputContent = trim ($this -> modContent[$this -> masterMod]);
		$this -> outputContent = preg_replace_callback ('/\[\[=(.+?)\]\]/', array($this, 'strInLang'), $this -> outputContent);
		hook ('generateOutputDone', 'Execute', $this);
	} 

	private function strInTheme ($param, $isLoop = false)
	{
		$return = '';
		$key = $param[1];
		if (strpos ($key, ', ')) {
			@list ($key, $funcWalk, $funcParam) = explode (', ', $key);
			$needWalk = true;
		} else {
			$needWalk = false;
		} 
		if (array_key_exists ($key, $this -> modContent)) {
			$return = $this -> modContent[$key];
		} elseif (array_key_exists ($key, bw :: $conf)) {
			$return = bw :: $conf[$key];
		} elseif (strpos ($key, 'ext_') === 0) {
			$return = $this -> addHookIntoView (str_replace ('ext_', '', $key));
		} elseif ($isLoop) {
			if (array_key_exists ($key, $this -> loopEach)) {
				$return = $this -> loopEach[$key];
			} 
		} elseif (array_key_exists ($key, $this -> passData)) {
			$return = $this -> passData[$key];
		} 

		if ($needWalk) {
			$return = call_user_func (array($this, 'theme_' . $funcWalk), $funcParam, $return);
		} 
		return $return;
	} 

	private function strInLoop ($param)
	{
		return $this -> strInTheme ($param, true);
	} 

	private function strInLang ($param)
	{
		$key = $param[1];
		if (array_key_exists ($key, bw :: $conf['l'])) {
			return bw :: $conf['l'][$key];
		} 
		return '';
	} 

	public function doPagination ()
	{
		global $canonical;
		$paginationElements = $paginationVals = array();
		if ($canonical -> currentPage > 1) {
			$paginationElements[] = 'prevpage';
			$paginationVals['prevPageLink'] = sprintf ($canonical -> paginableURL, $canonical -> currentPage-1);
		} 

		if ($canonical -> currentPage < $canonical -> totalPages) {
			$paginationElements[] = 'nextpage';
			$paginationVals['nextPageLink'] = sprintf ($canonical -> paginableURL, $canonical -> currentPage + 1);
		} 

		$paginationElements[] = 'firstpage';
		$paginationVals['firstPageLink'] = sprintf ($canonical -> paginableURL, 1);

		if ($canonical -> totalPages > 1) {
			$paginationElements[] = 'finalpage';
			$paginationVals['finalPageLink'] = sprintf ($canonical -> paginableURL, $canonical -> totalPages);
		} 

		$paginationElements[] = 'pagination';

		hook ('doPagination', 'Execute', $paginationVals);

		$this -> setMaster ('pagination');
		$this -> setPassData ($paginationVals);

		$this -> setWorkFlow ($paginationElements);
		$this -> generateOutput ();
	} 

	public function setPageTitle ($mainTitle)
	{
		$this -> passData['pageTitle'] = $mainTitle . ' | ';
		$this -> passData['pageTitle'] = hook ('setPageTitle', 'Replace', $this -> passData['pageTitle']);
	} 

	public function setActiveNav ($navID)
	{
		bw :: $conf['activeNav'] = $navID;
	} 

	public function outputView ()
	{
		hook ('outputView', 'Execute', $this);
		if (defined ('ajax')) {
			echo (json_encode (array ('error' => 0, 'returnMsg' => $this -> outputContent)));
		} else {
			echo ($this -> outputContent);
		} 
	} 

	public function finalize ()
	{
		$this -> generateOutput ();
		$this -> outputView ();
		hook ('finalize', 'Execute', $this);
		if (defined ('docache')) {
			bw :: $db -> dbExec ('INSERT INTO cache (caID, caContent) VALUES (?, ?)', array (docache, $this -> outputContent));
		} 
		exit ();
	} 

	public function addHookIntoView ($hookInterface)
	{
		return hook ($hookInterface, 'Insert');
	} 

	public function haltWithError ($errMsg)
	{
		if (defined ('ajax')) {
			die (json_encode (array ('error' => 1, 'returnMsg' => $errMsg)));
		} 
		$this -> setMaster ('error');
		$this -> setPassData (array('errorMessage' => $errMsg));
		$this -> setWorkFlow (array('error'));
		$this -> finalize ();
	} 

	public function haltWithSuccess ($successMsg) // Only used in ajax mode
	{
		if (defined ('ajax')) {
			die (json_encode (array ('error' => 0, 'returnMsg' => $successMsg)));
		} 
	} 
	// Theme functions
	public function scanForThemes ()
	{
		$themes = array();
		if ($handle = opendir (P . 'theme/')) {
			while (false !== ($file = readdir ($handle))) {
				if (is_dir (P . 'theme/' . $file)) {
					if (file_exists (P . 'theme/' . $file . '/info.php')) {
						include_once (P . 'theme/' . $file . '/info.php');
						$themes[$theme['themeName']] = $theme;
						$themes[$theme['themeName']]['themeDir'] = $file;
					} 
				} 
			} 
		} 
		return $themes;
	} 

	private function theme_dateFormat ($format, $timestamp)
	{
		return date ($format, strtotime ($timestamp));
	} 

	private function theme_formatText ($mode, $text)
	{
		if (!is_object ($this -> markdownParser)) {
			include_once (P . 'inc/parsedown.php');
			$this -> markdownParser = new Parsedown();
		} 

		if ($mode == 'less') {
			$textcutter = strpos ($text, '+++');
			if ($textcutter === false) {
				$this -> themeInternal['hasMore'] = false;
			} else {
				$this -> themeInternal['hasMore'] = true;
				$text = substr ($text, 0, $textcutter);
			} 
		} else {
			$text = str_replace ('+++', '<a name="more"></a>', $text);
		} 

		$text = $this -> markdownParser -> text ($text); 
		// Start customized markdown
		// xiami music loader
		$text = preg_replace ("/!~!(.+?)\[xiami\]/", "<span class=\"xiamiLoader\" data-src=\"$1\" data-root=\"" . bw :: $conf['siteURL'] . "\"></span>", $text); 
		// Youku loader
		$text = preg_replace ("/!~!(.+?)\[youku\]/", "<iframe src=\"http://player.youku.com/embed/$1\"  frameborder='0' class=\"videoFrame\"></iframe>", $text); 
		// !!URL = music
		$text = preg_replace ("/!!<a href=\"(.+?)\">(.+?)<\/a>/", "<audio controls><source src=\"$1\" type=\"audio/mpeg\">Your browser does not support the audio element.</audio>", $text);
		$text = hook ('textParser', 'Replace', $text);
		return $text;
	} 

	private function theme_readMore ($format, $aID)
	{
		if ($this -> themeInternal['hasMore']) {
			$link = bw :: $conf['siteURL'] . '/' . bw :: $conf['linkPrefixArticle'] . "/{$aID}/#more";
			$link = hook ('readMoreLink', 'Replace', $link);
			return sprintf ($format, $link, bw :: $conf['l']['page:More']);
		} 
	} 

	private function theme_URLEncode ($unuse, $text)
	{
		return urlencode ($text);
	} 

	private function theme_safeConvert ($unuse, $text)
	{
		$text = hook ('safeConvert', 'Replace', $text);
		return htmlspecialchars ($text, 'UTF-8');
	} 
} 

class bwCanonicalization {
	private $perPage;
	public $currentPage;
	private $argsPattern;
	public $currentArgs;
	public $currentScript;
	public $loaderID;
	public $canonicalURL;
	public $paginableURL;
	public $totalPages;
	public $cache;

	public function __construct ()
	{
		global $conf;
		$this -> perPage = &$conf['perPage'];

		if (!defined ('M')) {
			define ('M', 'index');
		} 
		switch (M) {
			case 'index':
				$this -> argsPattern = array ('pageNum');
				$this -> loaderID = 'cate';
				break;
			case 'cate':
				$this -> argsPattern = array ('cateID', 'pageNum');
				$this -> loaderID = 'cate';
				break;
			case 'article':
				$this -> argsPattern = array ('aID');
				$this -> loaderID = 'article';
				break;
			case 'tag':
				$this -> argsPattern = array ('tValue', 'pageNum');
				$this -> loaderID = 'tag';
				break;
			case 'admin':
				$this -> argsPattern = array ('mainAction', 'subAction', 'pageNum');
				$this -> loaderID = 'admin';
				break;
			default:
				$this -> loaderID = 'error';
				stopError ('Requested mode does not exist.');
		} 

		$requestedURL = @explode ('/', str_replace (parse_url ($conf['siteURL'], PHP_URL_PATH) . '/', '', $_SERVER['PHP_SELF']));
		$this -> currentScript = $requestedURL[0];
		array_shift ($requestedURL);
		if (count ($requestedURL) > count ($this -> argsPattern)) {
			$this -> currentArgs = array_slice ($requestedURL, 0, count ($this -> argsPattern));
		} else {
			$this -> currentArgs = array_pad ($requestedURL, count ($this -> argsPattern), 1);
		} 
		$this -> currentArgs = array_combine ($this -> argsPattern, $this -> currentArgs);

		if (array_key_exists ('pageNum', $this -> currentArgs)) {
			$this -> currentPage = $this -> currentArgs['pageNum'] = max($this -> currentArgs['pageNum'], 1);
			$paginableArgs = $this -> currentArgs;
			$paginableArgs['pageNum'] = '%d';
		} else {
			$this -> currentPage = 1;
			$paginableArgs = $this -> currentArgs;
		} 

		$this -> canonicalURL = $conf['canonicalURL'] = $conf['siteURL'] . '/' . $this -> currentScript . '/' . implode ('/', $this -> currentArgs) . '/';
		$this -> paginableURL = $conf['siteURL'] . '/' . $this -> currentScript . '/' . implode ('/', $paginableArgs) . '/';

		if (isset ($_REQUEST['ajax'])) {
			define ('ajax', 1);
		} 

		$this -> cache = false;
		if (DISABLE_CACHE == 0) {
			if (M == 'index' || M == 'article') {
				$plusAjax = defined ('ajax') ? 'ajax' : 'page';
				$cacheKey = md5 ($this -> canonicalURL . $plusAjax);
				$cached = bw :: $db -> getSingleRow ('SELECT * FROM cache WHERE caID=? LIMIT 0, 1', array($cacheKey));
				if (isset ($cached['caID'])) {
					$this -> cache = $cached['caContent'];
				} else {
					define ('docache', $cacheKey);
				} 
			} 
		} 

		if (!$this -> cache) {
			bw :: loadLanguage ();
			bw :: initCategories ();
			bw :: loadExtensions ();
		} 

		if (M != 'admin') {
			bw :: pageStat ($this -> canonicalURL, M == 'article' ? $this -> currentArgs['aID'] : false);
		} 

		hook ('canonicalized', 'Execute', $this);
	} 

	public function setPerPage ($num)
	{
		$this -> perPage = floor ($num);
	} 

	public function setTotalPages ($num)
	{
		$this -> totalPages = floor ($num);
	} 

	public function calTotalPages ($totalNum)
	{
		$this -> totalPages = ceil ($totalNum / $this -> perPage);
	} 
} 

class bwAdmin {
	private $conf;
	public $token;
	public $verified;

	public function __construct ()
	{
		bw :: $conf['myIP'] = getIP ();
		$this -> initToken ();
		$this -> verified = false;
	} 

	private function initToken ()
	{
		$this -> token = md5(bw :: $conf['siteKey'] . bw :: $conf['myIP']);
	} 

	public function verifyToken ($sToken)
	{
		if (md5(sha1($sToken) . bw :: $conf['myIP']) == $this -> token) {
			$this -> verified = true;
		} else {
			$this -> verified = false;
		} 
	} 

	public function verifySessionToken ($sToken)
	{
		if ($sToken == $this -> token) {
			$this -> verified = true;
		} else {
			$this -> verified = false;
		} 
	} 

	public function storeSessionToken ($sToken)
	{
		$_SESSION['login-token'] = md5(sha1($sToken) . bw :: $conf['myIP']);
	} 

	public function storeMobileToken ()
	{
		$_SESSION['login-token'] = md5(bw :: $conf['siteKey'] . bw :: $conf['myIP']);
	} 
} 

function stopError ($errMsg)
{
	global $view;
	if (!is_object ($view)) {
		$view = new bwView;
	} 
	$view -> haltWithError ($errMsg);
} 

function ajaxSuccess ($successMsg)
{
	global $view;
	if (!is_object ($view)) {
		$view = new bwView;
	} 
	$view -> haltWithSuccess ($successMsg);
} 

function getIP ()
{
	$realip = '';
	if (isset ($_SERVER)) {
		if (isset ($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			$realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
			$realip = $_SERVER["HTTP_CLIENT_IP"];
		} else {
			$realip = $_SERVER["REMOTE_ADDR"];
		} 
	} else {
		if (getenv ("HTTP_X_FORWARDED_FOR")) {
			$realip = getenv ("HTTP_X_FORWARDED_FOR");
		} else if (getenv ("HTTP_CLIENT_IP")) {
			$realip = getenv ("HTTP_CLIENT_IP");
		} else {
			$realip = getenv ("REMOTE_ADDR");
		} 
	} 
	$realip = basename ($realip);
	return $realip;
} 

function dataFilter ($reservedKeys, $submitData)
{
	$reservedArray = array_fill_keys($reservedKeys, null);
	$returnArray = array_intersect_key ($submitData, $reservedArray);
	return array_merge ($reservedArray, $returnArray);
} 

function loadServices ()
{
	if (!defined ('S')) {
		if (file_exists (P . "conf/services.php")) {
			global $conf;
			include_once (P . "conf/services.php");
		} 
		define ('S', 1); 
		// Qiniu
		define ('QINIU_AK', $conf['qiniuAKey']);
		define ('QINIU_SK', $conf['qiniuSKey']);

		hook ('loadServices', 'Execute');
	} 
} 

function qiniuUpload ($filePath)
{
	global $qiniuClient, $conf;
	if (defined ('S')) {
		loadServices ();
	} 
	if (!is_object ($qiniuClient)) {
		require_once (P . "inc/qiniu.php");
		$qiniuClient = new qiniuClient (QINIU_AK, QINIU_SK);
	} 
	$fStoreNameFull = FPATH . "/{$filePath}";
	$result = $qiniuClient -> uploadFile($fStoreNameFull, $conf['qiniuBucket'], $filePath);
	return $result;
} 

function clearCache ($caID = false)
{
	if ($caID) {
		bw :: $db -> dbExec ('DELETE FROM cache WHERE caID=?', array ($caID));
	} else {
		if (DBTYPE=='MySQL') {
			bw :: $db -> dbExec ('TRUNCATE TABLE cache');
		}
		else {
			bw :: $db -> dbExec ('DROP TABLE cache');
			bw :: $db -> dbExec ('CREATE TABLE cache (caID CHAR (32) PRIMARY KEY NOT NULL, caContent TEXT)');
		}
	} 
} 

function hook ()
{
	$numArgs = func_num_args ();
	if ($numArgs < 2) {
		return;
	} 
	$hookInterface = func_get_arg (0);
	$hookType = func_get_arg (1);
	if (!isset (bw :: $extList[$hookInterface])) {
		$return = $hookType == 'Replace' ? func_get_arg (2) : '';
		return $return;
	} 
	switch ($hookType) {
		case 'Replace':
			if ($numArgs == 3) {
				$input = func_get_arg (2);
				foreach (bw :: $extList[$hookInterface] as $extID) {
					$extID = 'ext_' . $extID;
					$input = $extID :: $hookInterface($input);
				} 
				return $input;
			} 
			break;

		case 'Insert':
			$return = '';
			foreach (bw :: $extList[$hookInterface] as $extID) {
				$extID = 'ext_' . $extID;
				$return .= $extID :: $hookInterface();
			} 
			return $return;
			break;

		case 'Execute':
			if ($numArgs > 2) {
				$args = array_slice (func_get_args (), 2);
				foreach (bw :: $extList[$hookInterface] as $extID) {
					$extID = 'ext_' . $extID;
					call_user_func_array (array($extID, $hookInterface), $args);
				} 
			} else {
				foreach (bw :: $extList[$hookInterface] as $extID) {
					$extID = 'ext_' . $extID;
					$extID :: $hookInterface();
				} 
			} 
			break;
	} 
} 

function stringToArray ($array, $key = false)
{
	$newArray = array();
	if ($key) {
		foreach ($array as $val) {
			$newArray[] = array ($key => $val);
		} 
	} else {
		foreach ($array as $val) {
			$newArray[] = array ($val);
		} 
	} 
	return $newArray;
} 

if (@get_magic_quotes_gpc()) {
	function stripslashes_deep($value)
	{
		$value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
		return $value;
	} 

	$_REQUEST = array_map('stripslashes_deep', $_REQUEST);
} 

