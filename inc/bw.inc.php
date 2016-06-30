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
		self :: $db = new bwDatabase;
		self :: $cateData = self :: $cateList = self :: $extList = self :: $extData = array();

		if (!defined ('FORCE_UGLY_URL')) {
			define ('FORCE_UGLY_URL', 0);
		}
		if (FORCE_UGLY_URL == 1 || (self :: $conf['linkPrefixIndex'] == 'index.php' && false !==stripos ($_SERVER['SERVER_SOFTWARE'], 'nginx'))) { 
			self :: $conf['linkPrefixIndex'] = 'index.php?go=';
			self :: $conf['linkPrefixCategory'] = 'category.php?go=';
			self :: $conf['linkPrefixArticle'] = 'read.php?go=';
			self :: $conf['linkPrefixTag'] = 'tag.php?go=';
			self :: $conf['linkPrefixSend'] = 'send.php?go=';
			self :: $conf['linkPrefixAdmin'] = 'admin.php?go=';
			self :: $conf['linkPrefixPage'] = 'page.php?go=';
			self :: $conf['linkConj'] = '&';
		} elseif (self :: $conf['linkPrefixIndex'] == 'index.php') { 
			self :: $conf['linkPrefixCategory'] = 'category.php';
			self :: $conf['linkPrefixArticle'] = 'read.php';
			self :: $conf['linkPrefixTag'] = 'tag.php';
			self :: $conf['linkPrefixSend'] = 'send.php';
			self :: $conf['linkPrefixAdmin'] = 'admin.php';
			self :: $conf['linkPrefixPage'] = 'page.php';
			self :: $conf['linkConj'] = '?';
		} else {
			self :: $conf['linkPrefixCategory'] = 'category';
			self :: $conf['linkPrefixArticle'] = 'post';
			self :: $conf['linkPrefixTag'] = 'tag';
			self :: $conf['linkPrefixSend'] = 'send.php';
			self :: $conf['linkPrefixAdmin'] = 'admin.php';
			self :: $conf['linkPrefixPage'] = 'page';
			self :: $conf['linkConj'] = '?';
		}
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
		$allSocial = array('sina-weibo', 'twitter', 'weixin', 'facebook', 'douban', 'instagram', 'renren', 'linkedin');
		$allSocialNames = array(self :: $conf['l']['page:social:Weibo'], self :: $conf['l']['page:social:Twitter'], self :: $conf['l']['page:social:WeChat'], self :: $conf['l']['page:social:Facebook'], self :: $conf['l']['page:social:Douban'], self :: $conf['l']['page:social:Instagram'], self :: $conf['l']['page:social:Renren'], self :: $conf['l']['page:social:Linkedin']);
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
			$allLinks2[] = array ('linkURL' => urldecode ($linkURL), 'linkName' => $linkName);
		} 
		return $allLinks2;
	} 

	public static function getTagCloud ($num = 20)
	{
		$allTags = self :: $db -> getRows ('SELECT * FROM tags ORDER BY tCount DESC LIMIT 0, ?', array(floor ($num)));
		return $allTags;
	} 

	public static function getWidgets ($widgetType)
	{
		$allWidgets = self :: $db -> getRows ('SELECT * FROM extensions WHERE isWidget=1 AND extHooks=? ORDER BY extOrder DESC', array($widgetType));
		foreach ($allWidgets as $i => $oneWidget) {
			$allWidgets[$i]['extStorage'] = @json_decode ($allWidgets[$i]['extStorage'], true);
			is_array ($allWidgets[$i]['extStorage']) ? $allWidgets[$i] += $allWidgets[$i]['extStorage'] : false;
		} 
		return $allWidgets;
	} 
} 
