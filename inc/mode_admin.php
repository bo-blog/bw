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

@session_start ();

$admin = new bwAdmin;
$view = new bwView;

if ($canonical -> currentArgs['mainAction'] == '1') {
	if (isset ($_REQUEST['mobileToken']) && !isset($_SESSION['login-token'])) {
		if ($_REQUEST['mobileToken'] == sha1($conf['siteKey'] . 'mobile')) {
			$admin -> storeMobileToken ();
			$admin -> verified = true;
		} 
	} 
} 

if ($canonical -> currentArgs['mainAction'] <> 'login') {
	if (!array_key_exists ('login-token', $_SESSION)) {
		stopError ($conf['l']['admin:msg:NeedLogin']);
	} else {
		$admin -> verifySessionToken ($_SESSION['login-token']);
	} 
	if (!$admin -> verified) {
		stopError ($conf['l']['admin:msg:NeedLogin']);
	} 
} 

if ($canonical -> currentArgs['mainAction'] == '1') {
	ajaxSuccess ('');
} 

if ($canonical -> currentArgs['mainAction'] == 'login') {
	if ($canonical -> currentArgs['subAction'] == 'logout') {
		@session_destroy ();
		header ("Location: {$conf['siteURL']}/index.php?cleartoken");
		exit();
	} elseif ($canonical -> currentArgs['subAction'] == 'verify') {
		$s_token = $_REQUEST['s_token'];

		$isRem = $_REQUEST['isRem'];

		$admin -> verifyToken ($s_token);
		if (!$admin -> verified) {
			stopError ('');
		} else {
			$admin -> storeSessionToken ($s_token);
			if ($isRem == 1) {
				ajaxSuccess (sha1($conf['siteKey'] . 'mobile'));
			} else {
				ajaxSuccess ('');
			} 
		} 
	} else {
		$view -> setMaster ('adminlogin');
		$view -> setWorkFlow (array ('adminlogin'));
		$view -> finalize ();
	} 
} 

if ($canonical -> currentArgs['mainAction'] == 'center') {
	if ($canonical -> currentArgs['subAction'] == 'store') {
		if (!isset ($_REQUEST['smt'])) {
			stopError ('No data is submitted.');
		} 

		$acceptedKeys = array ('siteName', 'siteURL', 'authorName', 'authorIntro', 'siteKey', 'timeZone', 'siteTheme', 'siteLang', 'perPage', 'linkPrefixIndex', 'linkPrefixCategory', 'linkPrefixArticle', 'linkPrefixTag', 'social-sina-weibo', 'social-weixin', 'social-douban', 'social-instagram', 'social-renren', 'social-linkedin', 'externalLinks');
		$smt = dataFilter ($acceptedKeys, $_REQUEST['smt']);
		$smt = array_map ('htmlspecialchars', $smt);
		if (empty ($smt['siteKey'])) {
			$smt['siteKey'] = $conf['siteKey'];
		} else {
			$admin -> storeSessionToken ($smt['siteKey']);
			$smt['siteKey'] = sha1 ($smt['siteKey']);
		} 
		$valString = "<?php\r\n\$conf=" . var_export ($smt, true) . ";?>";
		$rS = file_put_contents (P . "conf/info.php", $valString);
		if ($rS) {
			clearCache ();
			ajaxSuccess ($conf['l']['admin:msg:ChangeSaved']);
		} else {
			stopError ($conf['l']['admin:msg:ChangeNotSaved']);
		} 
	} else {
		$view -> setMaster ('admin');
		$view -> setLoop ('themes', $view -> scanForThemes ());
		$view -> setWorkFlow (array ('themes', 'admincenter', 'admin'));
		$view -> finalize ();
	} 
} 

if ($canonical -> currentArgs['mainAction'] == 'articles') {
	$article = new bwArticle;
	if ($canonical -> currentArgs['subAction'] == 'store') {
		if (!isset ($_REQUEST['smt'])) {
			stopError ($conf['l']['admin:msg:NoData']);
		} 
		$article -> addArticle ($_REQUEST['smt']);
		ajaxSuccess ($conf['l']['admin:msg:ChangeSaved']);
	} elseif ($canonical -> currentArgs['subAction'] == 'update') {
		if (!isset ($_REQUEST['smt'])) {
			stopError ($conf['l']['admin:msg:NoData']);
		} 
		$article -> updateArticle ($_REQUEST['smt']);
		ajaxSuccess ($conf['l']['admin:msg:ChangeSaved']);
	} elseif ($canonical -> currentArgs['subAction'] == 'modify') {
		if (!isset ($_REQUEST['aID'])) {
			stopError ($conf['l']['admin:msg:NotExist']);
		} 
		$article -> fetchArticle ($_REQUEST['aID']);
		$view -> setMaster ('admin');
		$view -> setPassData ($article -> articleList[$_REQUEST['aID']]);
		$view -> setLoop ('admincatelist', bw :: $cateList);

		loadServices ();
		if ($conf['qiniuBucket'] && $conf['qiniuUpload'] == '1') {
			require_once (P . "inc/qiniu.php");
			$qiniuClient = new qiniuClient (QINIU_AK, QINIU_SK);
			$fStoreName = 'storage/' . substr (md5 (rand (1, 99999) . time()), 10, 8);
			$flags = array ('scope' => $conf['qiniuBucket'] . ':' . $fStoreName, 'deadline' => 3600 + time(), 'returnUrl' => "{$conf['siteURL']}/admin.php/articles/qiniuuploader/", 'returnBody' => json_encode(array('fname' => '$(key)')));

			$qiniuFileToken = $qiniuClient -> uploadToken($flags);
			$view -> setPassData (array ('qiniuFileToken' => $qiniuFileToken, 'qiniuKey' => $fStoreName));
			$uploader = 'adminqiniuupload';
		} else {
			$uploader = 'admincommonupload';
		} 
		$view -> setWorkFlow (array ('admincatelist', $uploader, 'adminwriter', 'admin'));
		$view -> finalize ();
	} elseif ($canonical -> currentArgs['subAction'] == 'new') {
		loadServices ();
		if ($conf['qiniuBucket'] && $conf['qiniuUpload'] == '1') {
			require_once (P . "inc/qiniu.php");
			$qiniuClient = new qiniuClient (QINIU_AK, QINIU_SK);
			$fStoreName = 'storage/' . substr (md5 (rand (1, 99999) . time()), 10, 8);
			$flags = array ('scope' => $conf['qiniuBucket'] . ':' . $fStoreName, 'deadline' => 3600 + time(), 'returnUrl' => "{$conf['siteURL']}/admin.php/articles/qiniuuploader/", 'returnBody' => json_encode(array('fname' => '$(key)')));
			$qiniuFileToken = $qiniuClient -> uploadToken($flags);
			$view -> setPassData (array ('qiniuFileToken' => $qiniuFileToken, 'qiniuKey' => $fStoreName));
			$uploader = 'adminqiniuupload';
		} else {
			$uploader = 'admincommonupload';
		} 

		$view -> setMaster ('admin');
		$view -> setLoop ('admincatelist', bw :: $cateList);
		$view -> setWorkFlow (array ('admincatelist', $uploader, 'adminwriter', 'admin'));
		$view -> finalize ();
	} elseif ($canonical -> currentArgs['subAction'] == 'getqiniuuploadpart') {
		loadServices ();
		if ($conf['qiniuBucket'] && $conf['qiniuUpload'] == '1') {
			require_once (P . "inc/qiniu.php");
			$qiniuClient = new qiniuClient (QINIU_AK, QINIU_SK);
			$fStoreName = 'storage/' . substr (md5 (rand (1, 99999) . time()), 10, 8);
			$flags = array ('scope' => $conf['qiniuBucket'] . ':' . $fStoreName, 'deadline' => 3600 + time(), 'returnUrl' => "{$conf['siteURL']}/admin.php/articles/qiniuuploader/", 'returnBody' => json_encode(array('fname' => '$(key)')));
			$qiniuFileToken = $qiniuClient -> uploadToken($flags);
			$view -> setPassData (array ('qiniuFileToken' => $qiniuFileToken, 'qiniuKey' => $fStoreName));
			$uploader = 'adminqiniuupload';
			$view -> setMaster ('adminqiniuupload');
			$view -> setWorkFlow (array ('adminqiniuupload'));
			$view -> finalize ();
		} 
	} elseif ($canonical -> currentArgs['subAction'] == 'getautocomplete') {
		$allTags = bw :: $db -> getRows ('SELECT tValue FROM tags ORDER BY tCount DESC LIMIT 0, 100');
		$outTags = array();
		foreach ($allTags as $aTag) {
			$outTags[] = $aTag['tValue'];
		} 
		die (json_encode ($outTags));
	} elseif ($canonical -> currentArgs['subAction'] == 'delete') {
		$article -> deleteArticle ($_REQUEST['aID']);
		header ("Location: {$conf['siteURL']}/admin.php/articles/");
	} elseif ($canonical -> currentArgs['subAction'] == 'uploader') {
		if (count ($_FILES) < 1) {
			exit ();
		} 

		$files = array();

		foreach ($_FILES["uploadFile"]["error"] as $key => $error) {
			if ($error == UPLOAD_ERR_OK) {
				$tmp_name = $_FILES["uploadFile"]["tmp_name"][$key];
				$fOriginalName = $_FILES["uploadFile"]["name"][$key];
				$fExtName = pathinfo ($fOriginalName, PATHINFO_EXTENSION);
				$fStoreName = substr (md5 (rand (10, 99) . $fOriginalName), 10, 8) . '.' . $fExtName;

				if (in_array (strtolower ($fExtName), array('gif', 'jpg', 'png', 'bmp', 'jpeg', 'jpe'))) {
					move_uploaded_file ($tmp_name, P . "storage/{$fStoreName}");
					$files[]['fileURL'] = "{$conf['siteURL']}/storage/{$fStoreName}";
				} 
			} 
		} 

		$view -> setMaster ('adminuploadinsert');
		$view -> setLoop ('adminuploaded', $files);
		$view -> setWorkFlow (array ('adminuploaded', 'adminuploadinsert'));
		$view -> finalize ();
	} elseif ($canonical -> currentArgs['subAction'] == 'qiniuuploader') {
		$files = array();
		loadServices ();
		require_once (P . "inc/qiniu.php");
		$qiniuClient = new qiniuClient (QINIU_AK, QINIU_SK);
		$uploadReturn = json_decode ($qiniuClient -> urlsafe_base64_decode($_REQUEST['upload_ret']), true);
		if (isset ($uploadReturn['fname'])) {
			$files[]['fileURL'] = $conf['qiniuDomain'] ? "{$conf['qiniuDomain']}/{$uploadReturn['fname']}" : "http://{$conf['qiniuBucket']}.qiniudn.com/{$uploadReturn['fname']}";
		} 
		$view -> setMaster ('adminuploadinsert');
		$view -> setLoop ('adminuploaded', $files);
		$view -> setWorkFlow (array ('adminuploaded', 'adminuploadinsert'));
		$view -> finalize ();
	} elseif ($canonical -> currentArgs['subAction'] == 'savecategories') {
		if (!isset ($_REQUEST['smt'])) {
			stopError ($conf['l']['admin:msg:NoData']);
		} 
		$cates = new bwCategory;
		$newCates = array_diff_key ($_REQUEST['smt'], bw :: $cateData);
		$cates -> addCategories ($newCates);
		$deletedCates = array_diff_key (bw :: $cateData, $_REQUEST['smt']);
		$cates -> deleteCategories ($deletedCates);
		$cates -> orderCategories (array_keys($_REQUEST['smt']));
		ajaxSuccess ($conf['l']['admin:msg:ChangeSaved']);
	} elseif ($canonical -> currentArgs['subAction'] == 'validatecategory') {
		if (!isset ($_REQUEST['smt'])) {
			stopError ($conf['l']['admin:msg:NoData']);
		} 
		if (empty ($_REQUEST['smt']['aCateURLName']) || empty ($_REQUEST['smt']['aCateDispName'])) {
			stopError ($conf['l']['admin:msg:NoData']);
		} 
		$smt['aCateURLName'] = urlencode ($_REQUEST['smt']['aCateURLName']);
		$smt['aCateDispName'] = htmlspecialchars ($_REQUEST['smt']['aCateDispName']);
		if (array_key_exists ($smt['aCateURLName'], bw :: $cateData)) {
			stopError ($conf['l']['admin:msg:Existed']);
		} 
		$view -> setMaster ('admincategorylist');
		$view -> setPassData ($smt);
		$view -> setWorkFlow (array ('admincategorylist'));
		$view -> finalize ();
	} else {
		// Pagination
		$article -> alterPerPage (20);
		$article -> getArticleList ();
		$canonical -> calTotalPages ($article -> totalArticles);
		$view -> doPagination ();

		$view -> setMaster ('admin');
		$view -> setLoop ('adminarticlelist', $article -> articleList);
		$view -> setLoop ('admincategorylist', bw :: $cateList);
		$view -> setWorkFlow (array ('adminarticlelist', 'admincategorylist', 'adminarticles', 'admin'));
		$view -> finalize ();
	} 
} 

if ($canonical -> currentArgs['mainAction'] == 'services') {
	if ($canonical -> currentArgs['subAction'] == 'store') {
		if (!isset ($_REQUEST['smt'])) {
			stopError ($conf['l']['admin:msg:NoData']);
		} 
		$acceptedKeys = array ('duoshuoID', 'qiniuAKey', 'qiniuSKey', 'qiniuBucket', 'qiniuSync', 'qiniuUpload', 'qiniuDomain');
		$smt = dataFilter ($acceptedKeys, $_REQUEST['smt']);
		$smt = array_map ('htmlspecialchars', $smt);
		if ($smt['qiniuBucket']) {
			require_once (P . "inc/qiniu.php");
			$qiniuClient = new qiniuClient ($smt['qiniuAKey'], $smt['qiniuSKey']);
			$result = $qiniuClient -> listFiles($smt['qiniuBucket'], $limit = 1);
			if (!$result) {
				stopError ($conf['l']['admin:msg:QiniuError'] . $qiniuClient -> err);
			} 
		} 
		$valString = "<?php\r\n\$conf+=" . var_export ($smt, true) . ";?>";
		$rS = file_put_contents (P . "conf/services.php", $valString);
		if ($rS) {
			ajaxSuccess ($conf['l']['admin:msg:ChangeSaved']);
		} else {
			stopError ($conf['l']['admin:msg:ChangeNotSaved']);
		} 
	} elseif ($canonical -> currentArgs['subAction'] == 'backup') {
		require_once (P . "inc/script/pclzip/pclzip.lib.php");
		$ff = 'storage/backup' . date("YmdHis") . '.zip';
		$archive = new PclZip (P . $ff);
		$v_list = $archive -> create('conf,' . DBNAME, PCLZIP_OPT_REMOVE_ALL_PATH);
		if ($v_list == 0) {
			stopError ($conf['l']['admin:msg:PclzipError'] . $archive -> errorInfo(true));
		} else {
			header ("Location: {$conf['siteURL']}/{$ff}");
		} 
	} 
	/**
	* elseif ($canonical->currentArgs['subAction']=='sync')
	* {
	* loadServices ();
	* require_once (P."inc/qiniu.php");
	* $qiniuClient=new qiniuClient (QINIU_AK, QINIU_SK);
	* if ($conf['qiniuBucket'] && $handle=opendir (P.'data/'))
	* {
	* while (false!==($file=readdir ($handle)))
	* {
	* if (!is_dir (P.'data/'.$file))
	* {
	* qiniuUpload ('data/'.$file);
	* }
	* }
	* }
	* qiniuUpload ("conf/list_all.php");
	* qiniuUpload ("conf/categories.php");
	* foreach (bw::$cateData as $aCateURLName=>$aCateDispName)
	* {
	* if (file_exists (P."conf/list_{$aCateURLName}.php"))
	* {
	* qiniuUpload ("conf/list_{$aCateURLName}.php");
	* }
	* }
	* header ("Location: https://portal.qiniu.com/");
	* }
	*/

	else {
		loadServices ();
		$view -> setMaster ('admin');
		$view -> setWorkFlow (array ('adminservices', 'admin'));
		$view -> finalize ();
	} 
} 

if ($canonical -> currentArgs['mainAction'] == 'dashboard') {
	$statVals = array();
	$statVals['totalArticles'] = bw :: $db -> countRows ('SELECT aID FROM articles');
	$statVals['totalReads'] = bw :: $db -> getSingleRow ('SELECT SUM(aReads) FROM articles');
	$statVals['totalReads'] = $statVals['totalReads']['SUM(aReads)'];
	$statVals['totalPVs'] = bw :: $db -> getSingleRow ('SELECT SUM(sNum) FROM statistics');
	$statVals['totalPVs'] = $statVals['totalPVs']['SUM(sNum)'];
	$statVals['sinceWhen'] = bw :: $db -> getSingleRow ('SELECT aTime FROM articles ORDER BY aTime ASC LIMIT 0, 1');
	$statVals['sinceWhen'] = $statVals['sinceWhen']['aTime'];

	$article = new bwArticle;
	$article -> getHottestArticles (5);
	foreach ($article -> articleList as $row) {
		$statVals['whatsHottest'][] = "<a href=\"{$conf['siteURL']}/{$conf['linkPrefixArticle']}/{$row['aID']}/\">{$row['aTitle']}</a>";
	} 
	$statVals['whatsHottest'] = '<li>' . implode('</li><li>', $statVals['whatsHottest']) . '</li>';

	$recents = bw :: $db -> getRows ('SELECT * FROM statistics ORDER BY lastView DESC LIMIT 0, 5');
	foreach ($recents as $row) {
		$statVals['latestViews'][] = "<a href=\"{$conf['siteURL']}{$row['pageURL']}\">{$row['pageURL']}</a>";
	} 
	$statVals['latestViews'] = '<li>' . implode('</li><li>', $statVals['latestViews']) . '</li>';

	$statVals['thisVersion'] = bwVersion;
	$statVals['PHPVersion'] = PHP_VERSION;
	$statVals['serverInfo'] = $_SERVER['SERVER_SOFTWARE'];
	$view -> setMaster ('admin');
	$view -> setPassData ($statVals);
	$view -> setLoop ('themes', $view -> scanForThemes ());
	$view -> setWorkFlow (array ('themes', 'admindashboard', 'admin'));
	$view -> finalize ();
} 

hook ('newAdminCategory', 'Execute', $canonical, $admin, $view);
