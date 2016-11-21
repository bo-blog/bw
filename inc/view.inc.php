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
		$this -> outputContent = $this -> masterMod = $this -> themeDir = '';
		self :: $markdownParser = null;
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

	public function resetPassData ()
	{
		$this -> passData = array ();
	} 

	public function setWorkFlow ($arrayWorkFlow)
	{
		if (is_array ($arrayWorkFlow)) {
			$this -> viewWorkFlow = $arrayWorkFlow;
		} 
	} 

	public function generateOutput ()
	{
		if (!$this -> themeDir) {
			if (defined ('M')) {
				global $conf;
				M == 'admin' ? $this -> setTheme ('default') : $this -> setTheme ($conf['siteTheme']);
			} else {
				$this -> setTheme ('default');
			}
		}
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
		$return = '';
		if (isset ($this -> passData[$param[1]])) {
			foreach ($this -> passData[$param[1]] as $this -> loopEach) {
				$return .= preg_replace_callback ('/\[\[::(.+?)\]\]/', array($this, 'strInLoop'), $param[2]);
			} 
		} 
		return ($return);
	} 

	private function passCondition ($param, $strInLoop = false)
	{ 
		if ($strInLoop) {
			$param[1] = preg_replace_callback ('/\[::(.+?)\]/', array($this, 'strInLoop'), $param[1]);
		} 
		else {
			$param[1] = preg_replace_callback ('/\[::(.+?)\]/', array($this, 'strInTheme'), $param[1]);
		}
		eval ("\$return = {$param[1]} ? \$param[2] : '';");
		return ($return);
	} 

	private function passConditionInLoop ($param) {
		return $this -> passCondition ($param, true);
	}

	private function loadElement ($param)
	{
		$obContent = ' ';
		$viewMod = basename ($param[1]);
		if (in_array ($viewMod, $this -> viewWorkFlow) && file_exists ("{$this->themeDir}/{$viewMod}.php")) {
			ob_start ();
			include ("{$this->themeDir}/{$viewMod}.php");
		} elseif (in_array ($viewMod, $this -> viewWorkFlow) && file_exists (P . "theme/default/{$viewMod}.php")) {
			ob_start ();
			include (P . "theme/default/{$viewMod}.php");
		} else {
			return $obContent;
		} 
		$obContent = ob_get_clean ();
		$obContent = preg_replace_callback ('/\[\[::load, (.+?)\]\]/', array($this, 'loadElement'), $obContent);
		return $obContent;
	} 

	public function commonParser ($text)
	{
		$text = preg_replace_callback ('/\[\[::load, (.+?)\]\]/', array($this, 'loadElement'), $text);
		$text = preg_replace_callback ('/\[\[::loop, (.+?)\]\](.+?)\[\[::\/loop\]\]/s', array($this, 'passLoop'), $text);
		$text = preg_replace_callback ('/\[\[::if, (.+?)\]\](.+?)\[\[::\/if]\]/s', array($this, 'passCondition'), $text);
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
		}  elseif (array_key_exists ($key, $this -> passData)) {
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

	public function setMetaData ($meta)
	{
		$this -> passData['metaData'] = $meta;
		$this -> passData['metaData'] = hook ('setMetaData', 'Replace', $this -> passData['metaData']);
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
		@header ("Content-Type: text/html; charset=UTF-8");
		$this -> generateOutput ();
		$this -> outputView ();
		hook ('finalize', 'Execute', $this);
		if (defined ('docache') && $this -> masterMod <> 'error') {
			bw :: $db -> dbExec ('INSERT INTO cache (caID, caContent) VALUES (?, ?)', array (docache, $this -> outputContent));
		} 
		exit ();
	} 

	public function getOutput ($clearAtOnce=true) 
	{
		$this -> generateOutput ();
		$return = $this -> outputContent;
		if ($clearAtOnce) {
			$this -> outputContent = '';
		}
		return $return;
	}

	public function addHookIntoView ($hookInterface)
	{
		if (!isset ($this -> themeInternal['insert_' . $hookInterface])) {
			$this -> themeInternal['insert_' . $hookInterface] = @file_get_contents (P . 'conf/insert_' . basename ($hookInterface) . '.htm');
		} 
		$return = hook ($hookInterface, 'Insert') . $this -> themeInternal['insert_' . $hookInterface];
		$return = $this -> commonParser ($return);
		return $return;
	} 

	public function addWidgetIntoView ($hookInterface)
	{
		if (isset (bw :: $extList[$hookInterface]) && isset ($this -> parts[$hookInterface])) {
			$return = '';
			$keyMaker = function ($key)
			{
				return '[[::' . $key . ']]';
			} ;
			foreach (bw :: $extList[$hookInterface] as $aWidget) {
				$output = widget ($hookInterface, bw :: $extData[$aWidget]['extStorage']);
				if (is_array ($output)) {
					$outputKeys = array_map ($keyMaker, array_keys ($output));
					$return .= str_replace ($outputKeys, array_values ($output), $this -> parts[$hookInterface]);
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
		if (M == 'api') {
			header ('HTTP/1.1 400 Bad Request');
			header ('Content-Type: application/json'); 
			die (json_encode (array ('error' => 1, 'message' => $errMsg)));
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
		$themes = array ();
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

	private function theme_datePast ($format, $timestamp)
	{
		if (!$timestamp) {
			$timestamp = time ();
			$past = 0;
		} else {
			$timestamp = strtotime ($timestamp);
			$past = time () - $timestamp;
		} 
		if ($past < 120) {
			$return = 'Just now';
		} elseif ($past < 3600) {
			$return = floor ($past / 60) . ' minutes ago';
		} elseif ($past < 86400) {
			$int1 = floor ($past / 3600);
			$int2 = floor ($past % 3600 / 60);
			$return = $int1 == 1 ? $int1 . ' hour' : $int1 . ' hours';
			if ($int2 > 0) {
				$return.= $int2 == 1 ? $int2 . ' minute' : $int1 . ' minutes';
			}
			$return.= ' ago';
		} elseif ($past < 259200) {
			$int1 = floor ($past / 86400);
			$int2 = floor ($past % 86400 / 3600);
			$return = $int1 == 1 ? $int1 . ' day' : $int1 . ' days';
			if ($int2 > 0) {
				$return.= $int2 == 1 ? $int2 . ' hour' : $int1 . ' hours';
			}
			$return.= ' ago';
		} elseif ($past < 31536000) {
			$int1 = floor ($past / 259200);
			$int2 = floor ($past % 259200 / 86400);
			$return = $int1 == 1 ? $int1 . ' month' : $int1 . ' months';
			if ($int2 > 0) {
				$return.= $int2 == 1 ? $int2 . ' day' : $int1 . ' days';
			}
			$return.= ' ago';
		} else {
			$int1 = floor ($past / 31536000);
			$int2 = floor ($past % 31536000 / 259200);
			$return = $int1 == 1 ? $int1 . ' year' : $int1 . ' years';
			if ($int2 > 0) {
				$return.= $int2 == 1 ? $int2 . ' month' : $int1 . ' months';
			}
			$return.= ' ago';
		}
		return $return;
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

	private function theme_hasMore ($format, $aID)
	{
		if (!$this -> themeInternal['hasMore']) {
			return $format;
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
		if (!$aTags) {
			return '';
		}
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
			include_once (P . 'inc/script/parsedown/Parsedown.php');
			self :: $markdownParser = new Parsedown;
		} 
		$text = self :: $markdownParser -> text ($text); 
		// Start customized markdown
		// xiami music loader
		$text = preg_replace ("/!~!(.+?)\[xiami\]/", "<span class=\"xiamiLoader\" data-src=\"$1\" data-root=\"" . bw :: $conf['siteURL'] . "\"></span>", $text); 
		// Wangyi Yun Yinyue loader
		$text = preg_replace ("/!~!(.+?)\[wangyiyun]/", "<p><iframe frameborder=\"no\" border=\"0\" marginwidth=\"0\" marginheight=\"0\" width='330' height='86' src=\"http://music.163.com/outchain/player?type=2&id=$1&auto=0&height=66\"></iframe></p>", $text); 
		// Youku loader
		$text = preg_replace ("/!~!(.+?)\[youku\]/", "<iframe src=\"http://player.youku.com/embed/$1\"  frameborder='0' class=\"videoFrame\"></iframe>", $text); 
		// Geolocation from Baidu
		$text = preg_replace ("/!~!(.+?)\[location\]/", "<span class=\"icon-location geoLocator\"></span> <span class=\"geoLocator\">$1</span>", $text); 
		// !!URL = music
		$text = preg_replace ("/!!<a href=\"(.+?)\">(.+?)<\/a>/", "<audio controls><source src=\"$1\" type=\"audio/mpeg\">Your browser does not support the audio element.</audio>", $text);
		$text = str_replace ("\n", '<br/>', $text);
		$text = preg_replace ("/<\/(.+?)><br\/>/", "</$1>", $text);

		//Image aligned to left or right
		$text = preg_replace ("/<img (.+?) alt=\"-R\"/", "<img $1 class=\"RImg\" alt=\"\"", $text); 
		$text = preg_replace ("/<img (.+?) alt=\"-L\"/", "<img $1 class=\"LImg\" alt=\"\"", $text); 

		//Image Gallery
		$varAlbumID = rand (100000, 999999);
		$text = preg_replace ("/<img (.+?) alt=\"-Album:(.+?)\"/", "<img $1 class=\"ImgAlbum Alb" . $varAlbumID . "\" data-desc=\"$2\" data-album=\"" . $varAlbumID . "\"", $text); 

		$text = hook ('textParser', 'Replace', $text);
		return $text;
	} 
} 
