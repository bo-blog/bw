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
	} else {
		$view -> setPassData (array ('logoutCSRFCode' => $admin -> getCSRFCode ('logout'), 'navCSRFCode' => $admin -> getCSRFCode ('navibar')));
	}
} 

if ($canonical -> currentArgs['mainAction'] == '1') {
	if (isset ($_SERVER['HTTP_X_REQUESTED_WITH'])) {
		if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
			ajaxSuccess ($admin -> getCSRFCode ('navibar'));
		} else {
			stopError (bw :: $conf['l']['admin:msg:CSRF']);
		}
	} else {
		stopError (bw :: $conf['l']['admin:msg:CSRF']);
	}
} 

if ($canonical -> currentArgs['mainAction'] == 'login') {
	if ($canonical -> currentArgs['subAction'] == 'logout') {
		$admin -> checkCSRFCode ('logout');
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
			$navCSRFCode = $admin -> getCSRFCode ('navibar');
			if ($isRem == 1) {
				ajaxSuccess (sha1($conf['siteKey'] . 'mobile') . '-'. $navCSRFCode);
			} else {
				ajaxSuccess ('-' . $navCSRFCode);
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
		$admin -> checkCSRFCode ('saveconfig');
		if (!isset ($_REQUEST['smt'])) {
			stopError ('No data is submitted.');
		} 

		$acceptedKeys = array ('siteName', 'siteURL', 'authorName', 'authorIntro', 'siteKey', 'timeZone', 'pageCache', 'commentOpt', 'comFrequency', 'comPerLoad', 'siteTheme', 'siteLang', 'perPage', 'linkPrefixIndex', 'linkPrefixCategory', 'linkPrefixArticle', 'linkPrefixTag', 'social-sina-weibo', 'social-weixin', 'social-douban', 'social-instagram', 'social-renren', 'social-linkedin', 'externalLinks');
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
		$admin -> checkCSRFCode ('navibar');
		$view -> setMaster ('admin');
		$view -> setPassData (array ('themeList' => $view -> scanForThemes (), 'CSRFCode' => $admin -> getCSRFCode ('saveconfig')));
		$view -> setWorkFlow (array ('admincenter', 'admin'));
		$view -> finalize ();
	} 
} 

if ($canonical -> currentArgs['mainAction'] == 'articles') {
	$article = new bwArticle;
	if ($canonical -> currentArgs['subAction'] == 'store') {
		$admin -> checkCSRFCode ('articlesave');
		if (!isset ($_REQUEST['smt'])) {
			stopError ($conf['l']['admin:msg:NoData']);
		} 
		$article -> addArticle ($_REQUEST['smt']);
		ajaxSuccess ($conf['l']['admin:msg:ChangeSaved']);
	} elseif ($canonical -> currentArgs['subAction'] == 'update') {
		$admin -> checkCSRFCode ('articlesave');
		if (!isset ($_REQUEST['smt'])) {
			stopError ($conf['l']['admin:msg:NoData']);
		} 
		$article -> updateArticle ($_REQUEST['smt']);
		ajaxSuccess ($conf['l']['admin:msg:ChangeSaved']);
	} elseif ($canonical -> currentArgs['subAction'] == 'modify') {
		$admin -> checkCSRFCode ('navibar');
		if (!isset ($_REQUEST['aID'])) {
			stopError ($conf['l']['admin:msg:NotExist']);
		} 
		$article -> fetchArticle ($_REQUEST['aID']);
		$view -> setMaster ('admin');
		$view -> setPassData ($article -> articleList[$_REQUEST['aID']]);
		$view -> setPassData (array ('admincatelist' => bw :: $cateList, 'upCSRFCode' => $admin -> getCSRFCode ('upload'), 'articleCSRFCode' => $admin -> getCSRFCode ('articlesave')));

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
		$view -> setWorkFlow (array ($uploader, 'adminwriter', 'admin'));
		$view -> finalize ();
	} elseif ($canonical -> currentArgs['subAction'] == 'new') {
		$admin -> checkCSRFCode ('newarticle');
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
		$view -> setPassData (array ('admincatelist' => bw :: $cateList, 'upCSRFCode' => $admin -> getCSRFCode ('upload'), 'articleCSRFCode' => $admin -> getCSRFCode ('articlesave')));
		$view -> setWorkFlow (array ($uploader, 'adminwriter', 'admin'));
		$view -> finalize ();
	} elseif ($canonical -> currentArgs['subAction'] == 'getqiniuuploadpart') {
		$admin -> checkCSRFCode ('upload');
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
		$admin -> checkCSRFCode ('articlesave');
		$article -> deleteArticle ($_REQUEST['aID']);
		header ("Location: {$conf['siteURL']}/admin.php/articles/");
	} elseif ($canonical -> currentArgs['subAction'] == 'uploader') {
		$admin -> checkCSRFCode ('upload');
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
		$view -> setPassData (array ('adminuploaded' => $files));
		$view -> setWorkFlow (array ('adminuploadinsert'));
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
		$view -> setPassData (array ('adminuploaded' => $files));
		$view -> setWorkFlow (array ('adminuploadinsert'));
		$view -> finalize ();
	} elseif ($canonical -> currentArgs['subAction'] == 'savecategories') {
		$admin -> checkCSRFCode ('category');
		if (!isset ($_REQUEST['smt'])) {
			stopError ($conf['l']['admin:msg:NoData']);
		} 
		$cates = new bwCategory;
		$cates -> bufferCacheClear ();
		$newCates = array_diff_key ($_REQUEST['smt'], bw :: $cateData);
		$cates -> addCategories ($newCates);
		$deletedCates = array_diff_key (bw :: $cateData, $_REQUEST['smt']);
		$cates -> deleteCategories ($deletedCates);
		$remainedCates = array_diff_key (bw :: $cateData, $deletedCates);
		$remainedAsNewCates = array ();
		foreach ($remainedCates as $k => $aRemainedCate) {
			$remainedAsNewCates[] = $_REQUEST['smt'][$k];
		}
		$cates -> renameCategories (array_keys ($remainedCates), array_values ($remainedCates), $remainedAsNewCates);
		$cates -> orderCategories (array_keys ($_REQUEST['smt']));
		$cates -> endBufferCache ();
		ajaxSuccess ($conf['l']['admin:msg:ChangeSaved']);
	} elseif ($canonical -> currentArgs['subAction'] == 'validatecategory') {
		$admin -> checkCSRFCode ('category');
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
		$admin -> checkCSRFCode ('navibar');
		// Pagination
		$article -> alterPerPage (20);
		$article -> getArticleList ();
		$canonical -> calTotalPages ($article -> totalArticles);
		$canonical -> paginableURL = bw :: $conf['siteURL'] . '/admin.php/articles//%d?CSRFCode=' . $admin -> getCSRFCode ('navibar');
		$view -> doPagination ();

		$view -> setMaster ('admin');
		$view -> setPassData (array ('adminarticlelist' =>$article -> articleList, 'admincatelist' => bw :: $cateList, 'newCSRFCode' => $admin -> getCSRFCode ('newarticle'), 'oldCSRFCode' => $admin -> getCSRFCode ('navibar'), 'cateCSRFCode' => $admin -> getCSRFCode ('category')));
		$view -> setWorkFlow (array ('adminarticlelist', 'admincategorylist', 'adminarticles', 'admin'));
		$view -> finalize ();
	} 
} 

if ($canonical -> currentArgs['mainAction'] == 'services') {
	if ($canonical -> currentArgs['subAction'] == 'store') {
		$admin -> checkCSRFCode ('services');
		if (!isset ($_REQUEST['smt'])) {
			stopError ($conf['l']['admin:msg:NoData']);
		} 
		$acceptedKeys = array ('duoshuoID', 'sinaAKey', 'sinaSKey', 'qiniuAKey', 'qiniuSKey', 'qiniuBucket', 'qiniuSync', 'qiniuUpload', 'qiniuDomain');
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
		$admin -> checkCSRFCode ('services');
		require_once (P . "inc/script/pclzip/pclzip.lib.php");
		$ff = 'storage/backup' . date("YmdHis") . '.zip';
		$archive = new PclZip (P . $ff);
		$v_list = DBTYPE == 'SQLite' ? $archive -> create('conf,' . DBNAME, PCLZIP_OPT_REMOVE_ALL_PATH) : $archive -> create('conf', PCLZIP_OPT_REMOVE_ALL_PATH);
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
		$admin -> checkCSRFCode ('navibar');
		loadServices ();
		$view -> setMaster ('admin');
		$view -> setPassData (array ('serviceCSRFCode' => $admin -> getCSRFCode ('services')));
		$view -> setWorkFlow (array ('adminservices', 'admin'));
		$view -> finalize ();
	} 
} 

if ($canonical -> currentArgs['mainAction'] == 'dashboard') {
	$admin -> checkCSRFCode ('navibar');
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
	$view -> setPassData (array ('newCSRFCode' => $admin -> getCSRFCode ('newarticle'), 'serviceCSRFCode' => $admin -> getCSRFCode ('services')));
	$view -> setWorkFlow (array ('admindashboard', 'admin'));
	$view -> finalize ();
} 

if ($canonical -> currentArgs['mainAction'] == 'extensions') {
	$allOpenHooks = array ('htmlhead', 'header', 'intro', 'mainAreaEnd', 'footer', 'beforeEnd', 'summaryDetail', 'articleDetail', 'commentArea');
	if ($canonical -> currentArgs['subAction'] == 'modify') {
		$admin -> checkCSRFCode ('extensions');
		if (!isset ($_REQUEST['extID']) || !isset ($_REQUEST['extActivate'])) {
			stopError ($conf['l']['admin:msg:NoData']);
		} else {
			$extID = basename ($_REQUEST['extID']);
			$extActivate = floor ($_REQUEST['extActivate']);
			bw :: $db -> dbExec ('UPDATE extensions SET extActivate=? WHERE extID=?', array ($extActivate, $extID));
			clearCache ();
			ajaxSuccess ($conf['l']['admin:msg:ChangeSaved']);
		}
	} elseif ($canonical -> currentArgs['subAction'] == 'remove') {
		$admin -> checkCSRFCode ('extensions');
		if (!isset ($_REQUEST['extID'])) {
			stopError ($conf['l']['admin:msg:NoData']);
		} else {
			$extID = basename ($_REQUEST['extID']);
			bw :: $db -> dbExec ('DELETE FROM extensions WHERE extID=?', array ($extID));
			clearCache ();
			ajaxSuccess ($conf['l']['admin:msg:ChangeSaved']);
		}
	} elseif ($canonical -> currentArgs['subAction'] == 'add') {
		$admin -> checkCSRFCode ('newext');
		if (!isset ($_REQUEST['extID'])) {
			stopError ($conf['l']['admin:msg:NoData']);
		} else {
			$extList = bw :: getAllExtensions ();
			$extID = basename ($_REQUEST['extID']);
			$extID = htmlspecialchars ($extID, ENT_QUOTES, 'UTF-8');
			if (array_key_exists ($extID, $extList)) {
				stopError ($conf['l']['admin:msg:Existed']);
			}
			if (!file_exists (P . 'extension/' . $extID . '/define.php') || !file_exists (P . 'extension/' . $extID . '/do.php')) {
				stopError ($conf['l']['admin:msg:ExtNotFound']);
			}
			$aExt = @parse_ini_file (P . 'extension/' . $extID . '/define.php');
			$aExt['extDesc'] = "name='{$aExt['name']}'\r\nintro='{$aExt['intro']}'\r\nauthor='{$aExt['author']}'\r\nurl='{$aExt['url']}'";
			bw :: $db -> dbExec ('INSERT INTO extensions (extID, extDesc, extHooks, extActivate, extOrder, isWidget) VALUES (?, ?, ?, 1, ?, 0)', array ($aExt['ID'], $aExt['extDesc'], $aExt['hooks'], count ($extList)+1));
			clearCache ();
			ajaxSuccess ($conf['l']['admin:msg:ChangeSaved']);
		}
	}  elseif ($canonical -> currentArgs['subAction'] == 'widget') {
		$admin -> checkCSRFCode ('newext');
		if (!isset ($_REQUEST['wgtID']) || empty ($_REQUEST['wgtID'])) {
			stopError ($conf['l']['admin:msg:NoData']);
		} else {
			$extOrder = floor ($_REQUEST['extOrder']);
			$extList = bw :: getAllWidgets ();
			$extID = htmlspecialchars ($_REQUEST['wgtID'], ENT_QUOTES, 'UTF-8');
			$patternWidgetHooks = array (
				'wghtmlhead' => 'value',
				'wgheader' => 'text,url,title,target',
				'wgsidebar' => 'title,value',
				'wgfooter' => 'value'
			);
			$extHooks = $_REQUEST['extHooks'];
			if (!array_key_exists ($extHooks, $patternWidgetHooks)) {
				stopError ($conf['l']['admin:msg:NoContent']);
			}
			$extStorage = array ();
			foreach (@explode (',', $patternWidgetHooks[$extHooks]) as $wgtCol) {
				$extStorage[$wgtCol] = ($wgtCol == 'value') ? $_REQUEST['wgt'.$wgtCol] : htmlspecialchars ($_REQUEST['wgt'.$wgtCol], ENT_QUOTES, 'UTF-8');
			} 
			if ($extOrder == -1) {
				if (array_key_exists ($extID, $extList)) {
					stopError ($conf['l']['admin:msg:Existed']);
				} 
				bw :: $db -> dbExec ('INSERT INTO extensions (extID, extDesc, extHooks, extActivate, extOrder, isWidget, extStorage) VALUES (?, "", ?, 1, ?, 1, ?)', array ($extID, $extHooks, count ($extList)+1, json_encode ($extStorage)));
			} else {
				if (!array_key_exists ($extID, $extList)) {
					stopError ($conf['l']['admin:msg:NotExist']);
				}
				bw :: $db -> dbExec ('UPDATE extensions SET extStorage=? WHERE extID=? AND isWidget=1', array (json_encode ($extStorage), $extID));
			}
			clearCache ();
			ajaxSuccess ($conf['l']['admin:msg:ChangeSaved']);
		}
	} elseif ($canonical -> currentArgs['subAction'] == 'savehooks') {
		$admin -> checkCSRFCode ('extensions');
		foreach ($allOpenHooks as $openHook) {
			@file_put_contents (P. 'conf/insert_'. $openHook. '.htm', $_REQUEST['smt'][$openHook]);
		}
		clearCache ();
		ajaxSuccess ($conf['l']['admin:msg:ChangeSaved']);
	}
	else {
		$admin -> checkCSRFCode ('navibar');
		$view -> setMaster ('admin');
		$view -> setPassData (array ('extList' => bw :: getAllExtensions (), 'newCSRFCode' => $admin -> getCSRFCode ('newext'), 'extCSRFCode' => $admin -> getCSRFCode ('extensions')));
		$view -> setPassData (array ('wgtListHtmlhead' => bw :: getWidgets ('wghtmlhead'), 'wgtListHeader' => bw :: getWidgets ('wgheader'), 'wgtListSiderbar' => bw :: getWidgets ('wgsidebar'), 'wgtListFooter' => bw :: getWidgets ('wgfooter')));
		foreach ($allOpenHooks as $openHook) {
			$allHooks['insert_'.$openHook] = @file_get_contents (P. 'conf/insert_'. $openHook. '.htm');
		}
		$view -> setPassData ($allHooks);
		$view -> setWorkFlow (array ('adminextensions', 'admin'));
		$view -> finalize ();
	}

}


if ($canonical -> currentArgs['mainAction'] == 'comments') {
	$comment = new bwComment;
	if ($canonical -> currentArgs['subAction'] == 'blockitem') {
		if (!$_REQUEST['comID'] || !$_REQUEST['aID']) {
			stopError ($conf['l']['admin:msg:NotExist']);
		} 
		$comment -> blockItem ($_REQUEST['comID'], $_REQUEST['aID']);
		ajaxSuccess($conf['l']['admin:msg:ChangeSaved']);
	}
	if ($canonical -> currentArgs['subAction'] == 'blockip') {
		if (!$_REQUEST['comID']) {
			stopError ($conf['l']['admin:msg:NotExist']);
		} 
		$comment -> blockIP ($_REQUEST['comID']);
		ajaxSuccess($conf['l']['admin:msg:ChangeSaved']);
	}
}

hook ('newAdminCategory', 'Execute', $canonical, $admin, $view);
