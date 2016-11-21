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
				$this -> currentScript = $conf['linkPrefixIndex'];
				break;
			case 'cate':
				$this -> argsPattern = array ('cateID', 'pageNum');
				$this -> loaderID = 'cate';
				$this -> currentScript = $conf['linkPrefixCategory'];
				break;
			case 'article':
				$this -> argsPattern = array ('aID');
				$this -> loaderID = 'article';
				$this -> currentScript = $conf['linkPrefixArticle'];
				break;
			case 'page':
				$this -> argsPattern = array ('aID');
				$this -> loaderID = 'page';
				$this -> currentScript = $conf['linkPrefixPage'];
				break;
			case 'tag':
				$this -> argsPattern = array ('tValue', 'pageNum');
				$this -> loaderID = 'tag';
				$this -> currentScript = $conf['linkPrefixTag'];
				break;
			case 'admin':
				$this -> argsPattern = array ('mainAction', 'subAction', 'pageNum');
				$this -> loaderID = 'admin';
				$this -> currentScript = 'admin.php';
				break;
			case 'send':
				$this -> argsPattern = array ('mainAction', 'subAction', 'pageNum');
				$this -> loaderID = 'send';
				$this -> currentScript = 'send.php';
				break;
			case 'list':
				$this -> argsPattern = array ('pageNum');
				$this -> loaderID = 'list';
				$this -> currentScript = $conf['linkPrefixIndex'];
				break;
			case 'api':
				$this -> argsPattern = array ('mainAPI', 'subAPI', 'pref');
				$this -> loaderID = 'api';
				$this -> currentScript = 'api.php';
				break;
			default:
				$this -> loaderID = 'error';
				$this -> currentScript = $conf['linkPrefixIndex'];
				hook ('setLoader', 'Execute', $this);
				stopError ('Requested mode does not exist.');
		} 

		$siteURLTmp = parse_url ($conf['siteURL'], PHP_URL_PATH) . '/';

		if (isset ($_REQUEST['go'])) { 
			$requestedURL = explode ('/', $_REQUEST['go']);
		} 	
		else {
			if ($siteURLTmp == '/') {
				$requestedURL = explode ('/', $_SERVER['PHP_SELF']);
				array_shift ($requestedURL);
			} else {
				$requestedURL = explode ('/', str_replace ($siteURLTmp, '', $_SERVER['PHP_SELF']));
			} 
		} 
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

			if (!file_exists (P . "mode/{$this -> loaderID}.mod.php")) {
				stopError ("Invalid parameter.");
			} 
			return P . "mode/{$this -> loaderID}.mod.php";
		} 
	} 
} 
