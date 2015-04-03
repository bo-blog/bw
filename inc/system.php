<?php
/**
* 
* @link http://bw.bo-blog.com
* @copyright (c) 2014 bW Development Team
* @license MIT
*/
define ('bwVersion', '0.9.5 alpha');

if (!defined ('P')) {
	die ('Access Denied.');
} 

if (!file_exists (P . 'conf/info.php')) {
	header ('Location: ' . P . 'install/index.php');
	exit ();
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
		DBTYPE == 'MySQL' ? self :: $db -> dbExec ('INSERT IGNORE INTO statistics (pageURL, sNum) VALUES (?, 0)', array ($canonicalURL)) : self :: $db -> dbExec ('INSERT OR IGNORE INTO statistics (pageURL, sNum) VALUES (?, 0)', array ($canonicalURL));
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
			if (!$aExt['isWidget']) {
				if (file_exists (P . 'extension/' . basename ($aExt['extID']) . '/do.php')) {
					include_once (P . 'extension/' . basename ($aExt['extID']) . '/do.php');
					$aExtID = 'ext_' . $aExt['extID'];
					$aExtID :: init();
				} 
			}
		} 
	} 

	public static function getAllExtensions ()
	{
		$extDataReturn = array ();
		$extData = self :: $db -> getRows ('SELECT * FROM extensions WHERE isWidget=0 ORDER BY extOrder DESC');
		foreach ($extData as $aExt) {
			$aExt['extDesc'] = @parse_ini_string ($aExt['extDesc']);
			$aExt['extName'] = $aExt['extDesc']['name'];
			$aExt['extIntro'] = $aExt['extDesc']['intro'];
			$aExt['extAuthor'] = $aExt['extDesc']['author'];
			$aExt['extURL'] = $aExt['extDesc']['url'];
			$extDataReturn[$aExt['extID']] = $aExt;
		} 
		return $extDataReturn;
	} 

	public static function getAllWidgets ()
	{
		$extDataReturn = array ();
		$extData = self :: $db -> getRows ('SELECT * FROM extensions WHERE isWidget=1 ORDER BY extOrder DESC');
		foreach ($extData as $aExt) {
			$extDataReturn[$aExt['extID']] = $aExt;
		} 
		return $extDataReturn;
	} 

	public static function getSocialLinks ()
	{
		$allSocial = array('sina-weibo', 'weixin', 'douban', 'instagram', 'renren', 'linkedin');
		$allSocialNames = array(self :: $conf['l']['page:social:Weibo'], self :: $conf['l']['page:social:WeChat'], self :: $conf['l']['page:social:Douban'], self :: $conf['l']['page:social:Instagram'], self :: $conf['l']['page:social:Renren'], self :: $conf['l']['page:social:Linkedin']);
		$allSocialLinks = array();
		foreach ($allSocial as $i => $aSocial) {
			if (isset (self :: $conf['social-' . $aSocial])) {
				if (self :: $conf['social-' . $aSocial]) {
					$allSocialLinks[] = array ('socialLinkID' => $aSocial, 'socialLinkURL' => self :: $conf['social-' . $aSocial], 'socialLinkName' => $allSocialNames[$i]);
				} 
			} 
		} 
		return $allSocialLinks;
	} 

	public static function getExternalLinks ()
	{
		$allLinks = $allLinks2 = array();
		if (isset (self :: $conf['externalLinks'])) {
			$allLinks = parse_ini_string (self :: $conf['externalLinks']);
		} 
		foreach ($allLinks as $linkURL => $linkName) {
			$allLinks2[] = array ('linkURL' => $linkURL, 'linkName' => $linkName);
		} 
		return $allLinks2;
	} 

	public static function getTagCloud ($num=20)
	{
		$allTags = self :: $db -> getRows ('SELECT * FROM tags ORDER BY tCount DESC LIMIT 0, ?', array(floor ($num)));
		return $allTags;
	} 

	public static function getWidgets ($widgetType)
	{
		$allWidgets = self :: $db -> getRows ('SELECT * FROM extensions WHERE isWidget=1 AND extHooks=? ORDER BY extOrder DESC', array($widgetType));
		foreach ($allWidgets as $i => $oneWidget) {
			$allWidgets[$i]['extStorage'] = @json_decode ($allWidgets[$i]['extStorage'], true);
			is_array ($allWidgets[$i]['extStorage']) ? $allWidgets[$i]+=$allWidgets[$i]['extStorage'] : false;
		}
		return $allWidgets;
	}
} 

class bwCategory {
	private $cacheClear;

	public function __construct () 
	{
		$this -> cacheClear = true;
	} 

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
				if ($this -> cacheClear) {
					$this -> getCategories (); //Refresh immediately
					clearCache (); //Clear all cache
				}
			} 
			hook ('addCategories', 'Execute', $smt);
		} 
	} 

	public function deleteCategories ($deletedCates)
	{
		foreach ($deletedCates as $delCate => $delCateName) {
			$delLine = bw :: $db -> getSingleRow ('SELECT * FROM categories WHERE aCateURLName=:delCate', array(':delCate' => $delCate));
			if ($delLine['aCateCount'] == 0) {
				bw :: $db -> dbExec ('DELETE FROM categories WHERE aCateURLName=:delCate', array(':delCate' => $delCate));
			} else {
				stopError (bw :: $conf['l']['admin:msg:CategoryNotEmpty']);
			} 
		} 
		if ($this -> cacheClear) {
			$this -> getCategories (); //Refresh immediately
			clearCache (); //Clear all cache
		}
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
		if ($this -> cacheClear) {
			$this -> getCategories (); //Refresh immediately
			clearCache (); //Clear all cache
		}
		hook ('orderCategories', 'Execute', $arrayOrder);
	} 

	public function renameCategories ($arrayCateID, $arrayOldNames, $arrayNewNames)
	{
		if (count ($arrayCateID) <> count ($arrayOldNames) || count ($arrayCateID) <> count ($arrayNewNames)) {
			return;
		}
		for ($i = 0; $i < count ($arrayCateID); $i++) {
			$arrayNewNames[$i] = htmlspecialchars ($arrayNewNames[$i], ENT_QUOTES, 'UTF-8');
			if ($arrayOldNames[$i] <> $arrayNewNames[$i]) {
				bw :: $db -> dbExec ('UPDATE categories SET aCateDispName=? WHERE aCateURLName=?', array ($arrayNewNames[$i], $arrayCateID[$i]));
			}
		}
		if ($this -> cacheClear) {
			$this -> getCategories (); //Refresh immediately
			clearCache (); //Clear all cache
		}
		hook ('renameCategories', 'Execute', array ($arrayCateID, $arrayOldNames, $arrayNewNames));
	} 

	public function bufferCacheClear ()
	{
		$this -> cacheClear = false;
	}

	public function endBufferCache ()
	{
		$this -> getCategories (); //Refresh immediately
		clearCache (); //Clear all cache
		$this -> cacheClear = true;
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
			if (bw :: $conf['commentOpt'] == 0 || bw :: $conf['commentOpt'] == 3) { //If using non-built-in comment system, give an empty string instead of 0 for the attribute aComments
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

	private function parseArticleList ($allTitles) 
	{
		if (count ($allTitles) < 1) {
			stopError (bw :: $conf['l']['admin:msg:NoContent']);
		} 

		foreach ($allTitles as $aID => $row) {
			$this -> articleList[$aID] = $row;
			$this -> articleList[$aID]['aCateDispName'] = bw :: $cateData[$row['aCateURLName']];
			$this -> articleList[$aID]['aAllTags'] = stringToArray (@explode (',', $row['aTags']), 'tagValue');
			if (isset (bw :: $conf['commentOpt'])) {
			if (bw :: $conf['commentOpt'] == 0 || bw :: $conf['commentOpt'] == 3) { //If using non-built-in comment system, give an empty string instead of 0 for the attribute aComments
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
		if (empty ($smt['aTitle']) || $smt['aID']==='' || empty ($smt['aContent']) || $smt['aCateURLName']==='') {
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

class bwComment {
	public $comList;
	public $totalCom;
	private $pageNum;
	private $parentComID;
	private $aID;
	private $listBlocked;
	private $myIP;

	public function __construct ()
	{
		global $canonical;
		$this -> pageNum = $canonical -> currentPage;
		$this -> comList = array();
		$this -> aID = $this -> listBlocked = false;
		$this -> totalCom = 0;
		$this -> myIP = getIP ();
	} 

	public function alterAID ($aID)
	{
		$this -> aID = $aID;
		hook ('alterAID', 'Execute', $this);
	} 

	public function alterPerPage ($num)
	{
		bw :: $conf['perPage'] = floor ($num);
	} 

	public function getComList ()
	{
		$currentTitleStart = ($this -> pageNum-1) * bw :: $conf['comPerLoad'];
		if ($this -> listBlocked) {
			$blockedStr = $this -> listBlocked == 'only' ? 'comBlock=1' : '1=1';
		} else {
			$blockedStr = 'comBlock=0';
		}

		$qStr = $this -> aID === false ? "SELECT * FROM comments WHERE {$blockedStr} ORDER BY comTime DESC LIMIT ?, ?" : "SELECT * FROM comments WHERE comArtID=? AND {$blockedStr} ORDER BY comTime DESC LIMIT ?, ?";

		if ($this -> aID === false) {
			$qBind = array ($currentTitleStart, bw :: $conf['comPerLoad']);
		} else {
			$qBind = array ($this -> aID, $currentTitleStart, bw :: $conf['comPerLoad']);
		} 
		$allComs = bw :: $db -> getRows ($qStr, $qBind);

		foreach ($allComs as $comID => $row) {
			$row['comAvatar'] = $row['comAvatar'] ? $row['comAvatar'] : bw :: $conf['siteURL'] . '/conf/default.png';
			$this -> comList[$comID] = $row;
		} 
		hook ('getComList', 'Execute', $this);
		$this -> getTotalComs ();
	} 


	private function getTotalComs ()
	{
		if ($this -> listBlocked) {
			$blockedStr = $this -> listBlocked == 'only' ? 'comBlock=1' : '1=1';
		} else {
			$blockedStr = 'comBlock=0';
		}
		if ($this -> aID === false) {
			$this -> totalCom = bw :: $db -> countRows ("SELECT comID FROM comments WHERE {$blockedStr}");
		} else {
			$this -> totalCom = bw :: $db -> countRows ("SELECT comID FROM comments WHERE comArtID=? AND {$blockedStr}", array ($this -> aID));
		} 
		hook ('getTotalComs', 'Execute', $this);
	} 

	public function setBlockStatus ($statusCode) 
	{
		switch ($statusCode) {
			case 0:
				$this -> listBlocked = false;
			break;
			case 1:
				$this -> listBlocked = 'all';
			break;
			case 2:
				$this -> listBlocked = 'only';
			break;
			default:
				$this -> listBlocked = false;
		}
	} 

	public function initComAKey ()
	{
		return md5 ($this -> aID . bw :: $conf['siteKey']);
	} 

	public function initComSKey ()
	{
		$str = '';
		$strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$max = strlen ($strPol)-1;
		for ($i=0;$i<6; $i++) {
			$str.= $strPol[rand (0, $max)];
		}
		$_SESSION['SKey_' . $this -> aID] = $str;
		$_SESSION['OTime_' . $this -> aID] = time ();
		return $str;
	}

	public function addComment ($smt)
	{
		$smt = $this -> checkComData ($smt);
		if (!$smt['aID']) {
			stopError (bw :: $conf['l']['admin:msg:NotExist']);
		} else {
			$this -> aID = $smt['aID'];
		} 

		if (!isset ($_SESSION['SKey_' . $this -> aID]) || !isset ($_SESSION['OTime_' . $this -> aID])) {
			stopError (bw :: $conf['l']['admin:msg:AntiSpam']);
		} 

		if (time () - $_SESSION['OTime_' . $this -> aID] < floor (bw :: $conf['comFrequency'])) {
			stopError (sprintf (bw :: $conf['l']['admin:msg:AntiSpam2'], floor (bw :: $conf['comFrequency']) - (time () - $_SESSION['OTime_' . $this -> aID])));
		}

		if (md5 ($this -> initComAKey () . $_SESSION['SKey_' . $this -> aID]) <> $smt['comkey']) {
			stopError (bw :: $conf['l']['admin:msg:AntiSpam']);
		}

		$taID = bw :: $db -> getSingleRow ('SELECT * FROM articles WHERE aID=?', array ($this -> aID));
		if (!isset ($taID['aID'])) {
			stopError (bw :: $conf['l']['admin:msg:NotExist']);
		} 

		bw :: $db -> dbExec ('INSERT INTO comments (comName, comTime, comIP1, comIP2, comAvatar, comContent, comArtID, comParentID, comSource, comURL, comBlock) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array ($smt['userName'], date ('Y-m-d H:i:s'), $this -> myIP[0], $this -> myIP[1], $smt['userAvatar'], $smt['userContent'], $smt['aID'], 0, $smt['socialkey'], $smt['userURL'], 0));
		bw :: $db -> dbExec ('UPDATE articles SET aComments=aComments+1 WHERE aID=?', array ($smt['aID']));

		$_SESSION['OTime_' . $this -> aID] = time ();

		clearCache (); //Clear all cache
		hook ('addComment', 'Execute', $smt);
		
		$smt2 = array ('comID' =>bw :: $db -> dbLastInsertId (), 'comName' => $smt['userName'], 'comTime' => date ('Y-m-d H:i:s'), 'comAvatar' => $smt['userAvatar'] ? $smt['userAvatar'] : bw :: $conf['siteURL'] . '/conf/default.png', 'comContent' => $smt['userContent'], 'comArtID' => $smt['aID'], 'comURL' => $smt['userURL'], 'comSource' => $smt['socialkey']);
		return $smt2;

	}

	private function checkComData ($smt)
	{
		$acceptedKeys = array ('userName', 'userURL', 'userContent', 'aID', 'comkey', 'socialkey', 'userAvatar');
		$smt = dataFilter ($acceptedKeys, $smt);
		if (empty ($smt['aID']) || $smt['userName']==='' || empty ($smt['userContent']) || empty ($smt['comkey'])) {
			stopError (bw :: $conf['l']['admin:msg:NoData']);
		} 
		$smt['userName'] = htmlspecialchars ($smt['userName'], ENT_QUOTES, 'UTF-8');
		$smt['userURL'] = htmlspecialchars ($smt['userURL'], ENT_QUOTES, 'UTF-8');
		$smt['userContent'] = htmlspecialchars ($smt['userContent'], ENT_QUOTES, 'UTF-8');
		return $smt;
	} 

	public function blockItem ($comID, $aID) 
	{
		bw :: $db -> dbExec ('UPDATE comments SET comBlock=1 WHERE comID=?', array ($comID));
		bw :: $db -> dbExec ('UPDATE articles SET aComments=aComments-1 WHERE aID=?', array ($aID));
		clearCache (); //Clear all cache
		hook ('blockItem', 'Execute', $comID, $aID);
	} 

	public function blockIP ($comID) 
	{
		$taID = bw :: $db -> getSingleRow ('SELECT * FROM comments WHERE comID=?', array ($comID));
		$allAffectedCom = array ();
		if ($taID['comIP1']) {
			$allAffectedCom = bw :: $db -> getColumns ('SELECT * FROM comments WHERE comBlock=0 AND (comIP1=? OR comIP2=?)', array ($taID['comIP1'], $taID['comIP1']));
		} 
		if ($taID['comIP2']) {
			$allAffectedCom = array_merge_recursive ($allAffectedCom, bw :: $db -> getColumns ('SELECT * FROM comments WHERE comBlock=0 AND (comIP1=? OR comIP2=?)', array ($taID['comIP2'], $taID['comIP2'])));
		} 
		
/*		print_r ($allAffectedCom['comArtID']); 
		print ('<br>');
		print_r (array_count_values ($allAffectedCom['comArtID'])); 
		print ('<br>');
		print ('('.implode (',', array_unique ($allAffectedCom['comID'])).')');
		print ('<br>');
		die();*/

		if (count ($allAffectedCom) > 0) {
			$allAffectedArticles = array_count_values ($allAffectedCom['comArtID']);
			$allAffectedComments = '('.implode (',', array_unique ($allAffectedCom['comID'])).')';
			bw :: $db -> dbExec ('UPDATE comments SET comBlock=1 WHERE comID in '.$allAffectedComments);
			foreach ($allAffectedArticles as $affAID => $affCount) {
				bw :: $db -> dbExec ('UPDATE articles SET aComments=aComments-? WHERE aID=?', array ($affCount, $affAID));
			} 
			clearCache (); //Clear all cache
		} 
		hook ('blockIP', 'Execute', $comID);
	} 
}

class bwView {
	public $viewWorkFlow;
	private $parts;
	private $modContent;
	public $outputContent;
	private $themeDir;
	private $passData;
	private $masterMod;
	private $loopEach;
	public static $markdownParser;
	private $themeInternal;

	public function __construct ()
	{
		global $conf;
		$this -> viewWorkFlow = $this -> modContent = $this -> loopData = $this -> loopEach = $this -> themeInternal = $this -> parts = $this -> viewHooks = $this -> passData = array();
		$this -> outputContent = $this -> masterMod = '';
		self :: $markdownParser = null;
		$this -> setTheme ($conf['siteTheme']);
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
		include_once (P . "theme/default/components.php");
		if (file_exists ($this -> themeDir . "/components.php")) {
			include_once ($this -> themeDir . "/components.php");
		} 
		$this -> parts = (count($this -> parts)) ? $this -> parts : $parts;

		hook ('generateOutputInit', 'Execute', $this);

		foreach ($this -> viewWorkFlow as $viewMod) {
			$viewMod = basename ($viewMod);
			if (file_exists ("{$this->themeDir}/{$viewMod}.php")) {
				ob_start ();
				include ("{$this->themeDir}/{$viewMod}.php");
				$obContent = ob_get_clean ();
			} elseif (array_key_exists ($viewMod, $this -> parts)) {
				$obContent = $this -> parts[$viewMod];
			} elseif (file_exists (P . "theme/default/{$viewMod}.php")) {
				ob_start ();
				include (P . "theme/default/{$viewMod}.php");
				$obContent = ob_get_clean ();
			} else {
				continue;
			} 
			// load looped data
			$this -> modContent[$viewMod] = $this -> commonParser ($obContent);
		} 
		$this -> outputContent = trim (@$this -> modContent[$this -> masterMod] ?: '');
		$this -> outputContent = preg_replace_callback ('/\[\[=(.+?)\]\]/', array($this, 'strInLang'), $this -> outputContent);
		hook ('generateOutputDone', 'Execute', $this);
	} 

	private function passLoop ($param)
	{ 
		// print_r ($param);
		$return = '';
		if (isset ($this -> passData[$param[1]])) {
			foreach ($this -> passData[$param[1]] as $this -> loopEach) {
				$return .= preg_replace_callback ('/\[\[::(.+?)\]\]/', array($this, 'strInLoop'), $param[2]);
			} 
		} 
		return ($return);
	} 

	private function loadElement ($param)
	{
		$obContent = ' ';
		$viewMod = basename ($param[1]);
		if (in_array ($viewMod, $this -> viewWorkFlow) && file_exists ("{$this->themeDir}/{$viewMod}.php")) {
			ob_start ();
			include ("{$this->themeDir}/{$viewMod}.php");
		} elseif (in_array ($viewMod, $this -> viewWorkFlow) && file_exists (P. "theme/default/{$viewMod}.php")) {
			ob_start ();
			include (P. "theme/default/{$viewMod}.php");
		} else {
			return $obContent;
		}
		$obContent = ob_get_clean ();
		$obContent = preg_replace_callback ('/\[\[::load, (.+?)\]\]/', array($this, 'loadElement'), $obContent);
		return $obContent;
	} 

	private function commonParser ($text)
	{
		$text = preg_replace_callback ('/\[\[::load, (.+?)\]\]/', array($this, 'loadElement'), $text);
		$text = preg_replace_callback ('/\[\[::loop, (.+?)\]\](.+?)\[\[::\/loop\]\]/s', array($this, 'passLoop'), $text);
		$text = preg_replace_callback ('/\[\[::(.+?)\]\]/', array($this, 'strInTheme'), $text);
		return $text;
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
		} elseif (array_key_exists ($key, bw :: $conf) && $key <> 'siteKey') {
			$return = bw :: $conf[$key];
		} elseif (strpos ($key, 'ext_') === 0) {
			$return = $this -> addHookIntoView (str_replace ('ext_', '', $key));
		} elseif (strpos ($key, 'widget_') === 0) {
			$return = $this -> addWidgetIntoView (str_replace ('widget_', '', $key));
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
		return "Unknown string: $key";
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
		header ("Content-Type: text/html; charset=UTF-8");
		$this -> generateOutput ();
		$this -> outputView ();
		hook ('finalize', 'Execute', $this);
		if (defined ('docache') && $this -> masterMod <> 'error') {
			bw :: $db -> dbExec ('INSERT INTO cache (caID, caContent) VALUES (?, ?)', array (docache, $this -> outputContent));
		} 
		exit ();
	} 

	public function addHookIntoView ($hookInterface)
	{
		if (!isset ($this -> themeInternal['insert_'.$hookInterface])) 
		{
			$this -> themeInternal['insert_'.$hookInterface]=@file_get_contents (P. 'conf/insert_'. basename ($hookInterface). '.htm');
		} 
		$return = hook ($hookInterface, 'Insert'). $this -> themeInternal['insert_'.$hookInterface];
		$return = $this -> commonParser ($return);
		return $return;
	} 

	public function addWidgetIntoView ($hookInterface)
	{
		if (isset (bw :: $extList[$hookInterface]) && isset ($this -> parts[$hookInterface])) {
			$return = '';
			$keyMaker = function ($key) {
				return '[[::'.$key.']]';
			};
			foreach (bw :: $extList[$hookInterface] as $aWidget) {
				$output = widget ($hookInterface, bw :: $extData[$aWidget]['extStorage']);
				if (is_array ($output)) {
					$outputKeys = array_map ($keyMaker, array_keys ($output));
					$return.= str_replace ($outputKeys, array_values ($output), $this -> parts[$hookInterface]);
				}
			}
			return $return;
		}
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
		if (!$timestamp) {
			$timestamp = time ();
		} else {
			$timestamp = strtotime ($timestamp);
		}
		return date ($format, $timestamp);
	} 

	private function theme_formatText ($mode, $text)
	{
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

		$text = $this -> textFormatter ($text); 
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
		return str_replace ('\'', '\\\'', htmlspecialchars (str_replace (array("\r\n", "\r", "\n"), "\\r", $text), ENT_COMPAT, 'UTF-8'));
	} 

	private function theme_formatTags ($format, $aTags)
	{
		$aAllTags = @explode (',', $aTags);
		$return = '';
		if (count ($aAllTags) > 0) {
			foreach ($aAllTags as $tagValue) {
				$return .= str_replace (array ('[::tagValue]', '[::tagInURL]'), array ($tagValue, urlencode ($tagValue)), $format);
			} 
			$return = str_replace (array ('[::', ']'), array ('[[::', ']]'), $return);
			$return = preg_replace_callback ('/\[\[::(.+?)\]\]/', array($this, 'strInTheme'), $return);
		} 
		return $return;
	} 

	private function theme_hasTags ($format, $aTags)
	{
		return $aTags ? $format : '';
	} 

	public static function textFormatter ($text)
	{
		if (!is_object (self :: $markdownParser)) {
			include_once (P . 'inc/parsedown.php');
			self :: $markdownParser = new Parsedown();
		} 
		$text = self :: $markdownParser -> text ($text); 
		// Start customized markdown
		// xiami music loader
		$text = preg_replace ("/!~!(.+?)\[xiami\]/", "<span class=\"xiamiLoader\" data-src=\"$1\" data-root=\"" . bw :: $conf['siteURL'] . "\"></span>", $text); 
		// Youku loader
		$text = preg_replace ("/!~!(.+?)\[youku\]/", "<iframe src=\"http://player.youku.com/embed/$1\"  frameborder='0' class=\"videoFrame\"></iframe>", $text); 
		// Geolocation from Baidu
		$text = preg_replace ("/!~!(.+?)\[location\]/", "<span class=\"icon-location geoLocator\"></span> <span class=\"geoLocator\">$1</span>", $text); 
		// !!URL = music
		$text = preg_replace ("/!!<a href=\"(.+?)\">(.+?)<\/a>/", "<audio controls><source src=\"$1\" type=\"audio/mpeg\">Your browser does not support the audio element.</audio>", $text);
		$text = hook ('textParser', 'Replace', $text);
		return $text;
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
			case 'send':
				$this -> argsPattern = array ('mainAction', 'subAction', 'pageNum');
				$this -> loaderID = 'send';
				break;
			/*case 'page':
				$this -> argsPattern = array ('aID');
				$this -> loaderID = 'article';
				break;*/
			default:
				$this -> loaderID = 'error';
				stopError ('Requested mode does not exist.');
		} 

		$siteURLTmp = parse_url ($conf['siteURL'], PHP_URL_PATH) . '/';

		if ($siteURLTmp == '/') {
			$requestedURL = explode ('/', $_SERVER['PHP_SELF']);
			array_shift ($requestedURL);
		} else {
			$requestedURL = explode ('/', str_replace ($siteURLTmp, '', $_SERVER['PHP_SELF']));
		}

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
		if (bw :: $conf['pageCache'] == '1') {
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

		if (M != 'admin' && M != 'send') {
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

	public function loader ()
	{
		if ($this -> cache) { // Cached content: direct output
			if (!defined ('ajax')) {
				die ($this -> cache);
			} else {
				die (json_encode (array ('error' => 0, 'returnMsg' => $this -> cache)));
			} 
		} else {
			hook ('newIndexPage', 'Execute', $this);

			if (!file_exists (P . "inc/mode_{$this -> loaderID}.php")) {
				stopError ("Invalid parameter.");
			} 
			return P . "inc/mode_{$this -> loaderID}.php";
		} 
	} 
}

class bwAdmin {
	private $conf;
	public $token;
	public $verified;

	public function __construct ()
	{
		bw :: $conf['myIP'] = getIP ();
		bw :: $conf['myIP'] = bw :: $conf['myIP'][0];
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

	public function getCSRFCode ($actionCode)
	{
		if (isset ($_SESSION['login-token'])) {
			return substr (md5 (bw :: $conf['myIP'] . bw :: $conf['siteKey'] . $actionCode), 0, 8);
		} else {
			stopError (bw :: $conf['l']['admin:msg:NeedLogin']);
		} 
	} 

	public function checkCSRFCode ($actionCode)
	{
		if (!isset ($_SESSION['login-token'])) {
			stopError (bw :: $conf['l']['admin:msg:NeedLogin']);
		} elseif (!isset ($_REQUEST['CSRFCode'])) {
			stopError (bw :: $conf['l']['admin:msg:CSRF']);
		} elseif (substr (md5 (bw :: $conf['myIP'] . bw :: $conf['siteKey'] . $actionCode), 0, 8) <> $_REQUEST['CSRFCode']) {
			stopError (bw :: $conf['l']['admin:msg:CSRF']);
		} 
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
	$realip = $realip2 = '';
	if (isset ($_SERVER)) {
		if (isset ($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			$realip2 = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
			$realip2 = $_SERVER["HTTP_CLIENT_IP"];
		}
		$realip = $_SERVER["REMOTE_ADDR"];
	} else {
		if (getenv ("HTTP_X_FORWARDED_FOR")) {
			$realip2 = getenv ("HTTP_X_FORWARDED_FOR");
		} else if (getenv ("HTTP_CLIENT_IP")) {
			$realip2 = getenv ("HTTP_CLIENT_IP");
		}
		$realip = getenv ("REMOTE_ADDR");
	} 
	if (!$realip) {
		$realip = $realip2;
	}
	$realip = basename ($realip);
	$realip2 = basename ($realip2);
	return array ($realip, $realip2);
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
		if (DBTYPE == 'MySQL') {
			bw :: $db -> dbExec ('TRUNCATE TABLE cache');
		} else {
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

function widget ($widgetHook, $storedValue)
{
	$storedValue = @json_decode ($storedValue, true);
	if (!is_array ($storedValue)) {
		return false;
	}
	$patternWidgetHooks = array (
		'wghtmlhead' => 'value',
		'wgheader' => 'text,url,title,target',
		'wgsidebar' => 'title,value',
		'wgfooter' => 'value'
	);
	if (array_key_exists ($widgetHook, $patternWidgetHooks)) {
		$patterns=@explode (',', $patternWidgetHooks[$widgetHook]);
		$output = array();
		foreach ($patterns as $pattern) {
			$output[$pattern] = isset ($storedValue[$pattern]) ? $storedValue[$pattern] : '';
		}
		return $output;
	}
	return false;
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

