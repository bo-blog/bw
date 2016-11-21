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
		if (file_exists (P . 'conf/mobileauth.php')) {
			include_once (P . 'conf/mobileauth.php');
			if (in_array ($_REQUEST['mobileToken'], $allMobileKeys)) {
				$admin -> storeMobileToken ();
				$admin -> verified = true;
				$_SESSION['authmobile'] = 1;
			}
		}
	} 
} 

if ($canonical -> currentArgs['mainAction'] <> 'login') {
	if (!array_key_exists ('login-token', $_SESSION)) {
		if (defined ('ajax')) {
			stopError ($conf['l']['admin:msg:NeedLogin']);
		}
		$view -> setMaster ('adminloginpage');
		$view -> setWorkFlow (array ('adminloginpage'));
		$authX = $view -> getOutput ();
		$view -> setMaster ('plainpage');
		$view -> setPassData (array ('plainContent' => $authX));
		$view -> setWorkFlow (array ('plainpage'));
		$view -> finalize ();
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
	if (defined ('ajax')) {
		if (isset ($_SERVER['HTTP_X_REQUESTED_WITH'])) {
			if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
				ajaxSuccess ($admin -> getCSRFCode ('navibar'));
			} else {
				stopError (bw :: $conf['l']['admin:msg:CSRF']);
			} 
		} else {
			stopError (bw :: $conf['l']['admin:msg:CSRF']);
		} 
	} else {
		header ("Location: {$conf['siteURL']}/{$conf['linkPrefixAdmin']}/dashboard/{$conf['linkConj']}CSRFCode=" . $admin -> getCSRFCode ('navibar'));
	}
} 

if ($canonical -> currentArgs['mainAction'] == 'login') {
	if ($canonical -> currentArgs['subAction'] == 'logout') {
		if (array_key_exists ('authmobile', $_SESSION)) {
			stopError (bw :: $conf['l']['admin:msg:CannotLogout']);
		}
		$admin -> checkCSRFCode ('logout');
		@session_destroy ();
		header ("Location: {$conf['siteURL']}/index.php?cleartoken");
		exit();
	} elseif ($canonical -> currentArgs['subAction'] == 'verify') {
		$s_token = $_REQUEST['s_token'];

		$admin -> verifyToken ($s_token);
		if (!$admin -> verified) {
			stopError ('');
		} else {
			$admin -> storeSessionToken ($s_token);
			$navCSRFCode = $admin -> getCSRFCode ('navibar');
			ajaxSuccess ('-' . $navCSRFCode);
		} 
	} else {
		$view -> setTheme (bw :: $conf['siteTheme']);
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
		$acceptedKeys = array ('siteName', 'siteURL', 'authorName', 'authorIntro', 'siteKey', 'timeZone', 'pageCache', 'commentOpt', 'comFrequency', 'comPerLoad', 'autoSave', 'siteTheme', 'siteLang', 'perPage', 'linkPrefixIndex', 'linkPrefixCategory', 'linkPrefixArticle', 'linkPrefixTag', 'social-sina-weibo', 'social-weixin', 'social-twitter', 'social-facebook', 'social-douban', 'social-instagram', 'social-renren', 'social-linkedin', 'externalLinks');
		$smt = dataFilter ($acceptedKeys, $_REQUEST['smt']);
		$outputExternal = '';
		for ($i = 0; $i < count ($smt['externalLinks']['lnkname']); $i++) {
			if ($smt['externalLinks']['lnkname'][$i] != '' && $smt['externalLinks']['lnkurl'][$i] != '') {
				$outputExternal.= urlencode ($smt['externalLinks']['lnkurl'][$i]) . '="' . htmlspecialchars ($smt['externalLinks']['lnkname'][$i], ENT_QUOTES, 'UTF-8') . "\"\r\n";
			}
		}
		$smt['externalLinks'] = '';
		$smt = array_map ('htmlspecialchars', $smt);
		$smt['externalLinks'] = trim ($outputExternal);
		if (empty ($smt['siteKey'])) {
			$smt['siteKey'] = $conf['siteKey'];
		} else {
			$admin -> storeSessionToken ($smt['siteKey']);
			$smt['siteKey'] = sha1 ($smt['siteKey']);
		} 
		$smt['siteURL'] = substr ($smt['siteURL'], -1) == '/' ? substr ($smt['siteURL'], 0, strlen ($smt['siteURL']) - 1) : $smt['siteURL'];
		$valString = "<?php\r\n\$conf=" . var_export ($smt, true) . ";";
		$rS = file_put_contents (P . "conf/info.php", $valString);
		if ($rS) {
			clearCache (false, true);
			ajaxSuccess ($conf['l']['admin:msg:ChangeSaved']);
		} else {
			stopError ($conf['l']['admin:msg:ChangeNotSaved']);
		} 
	} elseif ($canonical -> currentArgs['subAction'] == 'avatarupload') {
		$admin -> checkCSRFCode ('upload');
		if (count ($_FILES) < 1) {
			exit ();
		} 
		if ($_FILES["uploadFile"]["error"] == UPLOAD_ERR_OK) {
			$fExtName = pathinfo ($_FILES["uploadFile"]["name"], PATHINFO_EXTENSION);
			if (in_array (strtolower ($fExtName), array('gif', 'jpg', 'png', 'bmp', 'jpeg', 'jpe'))) {
				move_uploaded_file ($_FILES["uploadFile"]["tmp_name"], P . "conf/profile.png");
			} 
		}
		exit ();
	}  elseif ($canonical -> currentArgs['subAction'] == 'cancelauth') {
		$admin -> checkCSRFCode ('saveconfig');
		if (!isset ($_REQUEST['devID'])) {
			stopError ('');
		} 
		$allMobileKeys = array ();
		if (file_exists (P . 'conf/mobileauth.php')) {
			include_once (P . 'conf/mobileauth.php');
			if (isset($allMobileKeys[$_REQUEST['devID']])) {
				unset ($allMobileKeys[$_REQUEST['devID']]);
				$valString = "<?php\r\n\$allMobileKeys=" . var_export ($allMobileKeys, true) . ";?>";
				$rS = file_put_contents (P . "conf/mobileauth.php", $valString);
			}
			ajaxSuccess ('');
		}
	} else {
		$allMobileKeys = $mobileKeys = array ();
		if (file_exists (P . 'conf/mobileauth.php')) {
			include_once (P . 'conf/mobileauth.php');
			foreach ($allMobileKeys as $devID => $seq) {
				$mobileKeys[] = array ('devID' => $devID, 'seq' => $seq);
			}
		}
		$allLinks2 = array ();
		if (isset ($conf['externalLinks'])) {
			$allLinks = parse_ini_string ($conf['externalLinks']);
		} 
		foreach ($allLinks as $linkURL => $linkName) {
			$allLinks2[] = array ('linkURL' => urldecode ($linkURL), 'linkName' => $linkName, 'linkID' => rand (10000, 99999));
		} 

		$admin -> checkCSRFCode ('navibar');
		$view -> setMaster ('admin');
		$view -> setPassData (array ('themeList' => $view -> scanForThemes (), 'mobileKeys' => $mobileKeys, 'CSRFCode' => $admin -> getCSRFCode ('saveconfig'), 'upCSRFCode' => $admin -> getCSRFCode ('upload')));
		$view -> setPassData (array ('allLinks' => $allLinks2));
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
		if (isset ($_REQUEST['ispage'])) {
			$_REQUEST['smt']['aCateURLName'] = '_page';
		}
		if (isset ($_REQUEST['autosave'])) {
			$_REQUEST['smt']['aCateURLName'] = '_trash';
		}
		$article -> addArticle ($_REQUEST['smt']);
		ajaxSuccess (isset ($_REQUEST['autosave']) ? $conf['l']['admin:msg:AutoSaved'] : $conf['l']['admin:msg:ChangeSaved']);
	} elseif ($canonical -> currentArgs['subAction'] == 'update') {
		$admin -> checkCSRFCode ('articlesave');
		if (!isset ($_REQUEST['smt'])) {
			stopError ($conf['l']['admin:msg:NoData']);
		} 
		if (isset ($_REQUEST['ispage'])) {
			$_REQUEST['smt']['aCateURLName'] = '_page';
		}
		if (isset ($_REQUEST['autosave'])) {
			$_REQUEST['smt']['aCateURLName'] = '_trash';
		}
		$article -> updateArticle ($_REQUEST['smt']);
		ajaxSuccess (isset ($_REQUEST['autosave']) ? $conf['l']['admin:msg:AutoSaved'] : $conf['l']['admin:msg:ChangeSaved']);
	} elseif ($canonical -> currentArgs['subAction'] == 'modify') {
		$admin -> checkCSRFCode ('navibar');
		if (!isset ($_REQUEST['aID'])) {
			stopError ($conf['l']['admin:msg:NotExist']);
		} 
		$article -> fetchArticle ($_REQUEST['aID'], true);
		$view -> setMaster ('admin');
		$view -> setPassData ($article -> articleList[$_REQUEST['aID']]);
		$view -> setPassData (array ('writermode' => $article -> articleList[$_REQUEST['aID']]['aCateURLName'] != '_page' ? 'article' : 'singlepage', 'admincatelist' => bw :: $cateList, 'upCSRFCode' => $admin -> getCSRFCode ('upload'), 'articleCSRFCode' => $admin -> getCSRFCode ('articlesave'), 'cateCSRFCode' => $admin -> getCSRFCode ('category')));

		loadServices ();
		if ($conf['qiniuBucket'] && $conf['qiniuUpload'] == '1') {
			require_once (P . "inc/script/qiniu/QiniuClient.php");
			$qiniuClient = new qiniuClient (QINIU_AK, QINIU_SK);
			$fStoreName = 'storage/' . substr (md5 (rand (1, 99999) . time()), 10, 8);
			$flags = array ('scope' => $conf['qiniuBucket'] . ':' . $fStoreName, 'deadline' => 3600 + time(), 'returnUrl' => "{$conf['siteURL']}/{$conf['linkPrefixAdmin']}/articles/qiniuuploader/", 'returnBody' => json_encode(array('fname' => '$(key)')));

			$qiniuFileToken = $qiniuClient -> uploadToken($flags);
			$view -> setPassData (array ('qiniuFileToken' => $qiniuFileToken, 'qiniuKey' => $fStoreName));
			$uploader = 'adminqiniuupload';
		} elseif ($conf['qiniuUpload'] == '2') {
			$uploader = 'adminaliyunupload';
			$policy = '{"expiration": "2120-01-01T12:00:00.000Z","conditions":[{"bucket": "'. bw :: $conf['aliyunBucket'] .'" },["content-length-range", 0, 104857600]]}';
			$view -> setPassData (array ('policy' => base64_encode ($policy), 'signature' => base64_encode (hash_hmac ('sha1', $policy, bw :: $conf['aliyunSKey'], true))));
		} else {
			$uploader = 'admincommonupload';
		} 
		$view -> setPassData (array ('articleTemplate' => $article -> getArticleTemplateList()));
		$view -> setWorkFlow (array ($uploader, 'adminwriter', 'admin'));
		$view -> finalize ();
	} elseif ($canonical -> currentArgs['subAction'] == 'new' || $canonical -> currentArgs['subAction'] == 'newpage') {
		$admin -> checkCSRFCode ('newarticle');
		loadServices ();
		if ($conf['qiniuBucket'] && $conf['qiniuUpload'] == '1') {
			require_once (P . "inc/script/qiniu/QiniuClient.php");
			$qiniuClient = new qiniuClient (QINIU_AK, QINIU_SK);
			$fStoreName = 'storage/' . substr (md5 (rand (1, 99999) . time()), 10, 8);
			$flags = array ('scope' => $conf['qiniuBucket'] . ':' . $fStoreName, 'deadline' => 3600 + time(), 'returnUrl' => "{$conf['siteURL']}/{$conf['linkPrefixAdmin']}/articles/qiniuuploader/", 'returnBody' => json_encode(array('fname' => '$(key)', 'ftype' => '$(mimeType)')));
			$qiniuFileToken = $qiniuClient -> uploadToken ($flags);
			$view -> setPassData (array ('qiniuFileToken' => $qiniuFileToken, 'qiniuKey' => $fStoreName));
			$uploader = 'adminqiniuupload';
		} elseif ($conf['qiniuUpload'] == '2') {
			$uploader = 'adminaliyunupload';
			$policy = '{"expiration": "2120-01-01T12:00:00.000Z","conditions":[{"bucket": "'. bw :: $conf['aliyunBucket'] .'" },["content-length-range", 0, 104857600]]}';
			$policy = base64_encode ($policy);
			$view -> setPassData (array ('policy' => $policy, 'signature' => base64_encode (hash_hmac ('sha1', $policy, bw :: $conf['aliyunSKey'], true))));
		}  else {
			$uploader = 'admincommonupload';
		} 

		$view -> setMaster ('admin');
		$view -> setPassData (array ('writermode' => $canonical -> currentArgs['subAction'] == 'new' ? 'article' : 'singlepage', 'admincatelist' => bw :: $cateList, 'upCSRFCode' => $admin -> getCSRFCode ('upload'), 'articleCSRFCode' => $admin -> getCSRFCode ('articlesave'), 'cateCSRFCode' => $admin -> getCSRFCode ('category')));
		$view -> setPassData (array ('articleTemplate' => $article -> getArticleTemplateList()));
		$view -> setWorkFlow (array ($uploader, 'adminwriter', 'admin'));
		$view -> finalize ();
	} elseif ($canonical -> currentArgs['subAction'] == 'getqiniuuploadpart') {
		$admin -> checkCSRFCode ('upload');
		loadServices ();
		if ($conf['qiniuBucket'] && $conf['qiniuUpload'] == '1') {
			require_once (P . "inc/script/qiniu/QiniuClient.php");
			$qiniuClient = new qiniuClient (QINIU_AK, QINIU_SK);
			$fStoreName = 'storage/' . substr (md5 (rand (1, 99999) . time()), 10, 8) . '_' . basename (str_replace ('\\', '/', $_REQUEST['fname']));
			$flags = array ('scope' => $conf['qiniuBucket'] . ':' . $fStoreName, 'deadline' => 3600 + time(), 'returnUrl' => "{$conf['siteURL']}/{$conf['linkPrefixAdmin']}/articles/qiniuuploader/", 'returnBody' => json_encode(array('fname' => '$(key)', 'ftype' => '$(mimeType)')));
			$qiniuFileToken = $qiniuClient -> uploadToken($flags) . '<<<' . $fStoreName;
			ajaxSuccess ($qiniuFileToken);
		} 
	} elseif ($canonical -> currentArgs['subAction'] == 'getautocomplete') {
		$allTags = bw :: $db -> getRows ('SELECT tValue FROM tags ORDER BY tCount DESC LIMIT 0, 100');
		$outTags = array();
		foreach ($allTags as $aTag) {
			$outTags[] = $aTag['tValue'];
		} 
		die ('var lastTags='.json_encode ($outTags).';');
	}  elseif ($canonical -> currentArgs['subAction'] == 'getpinyin') {
		if (!isset ($_REQUEST['str']) || !function_exists('mb_convert_encoding')) {
			stopError ('');
		}
		include_once (P . 'inc/script/pinyin/pinyin.php');
		$PY = new toPinyin;
		$pinyin = $PY -> stringToPinyin ($_REQUEST['str']);
		ajaxSuccess ($pinyin);
	} elseif ($canonical -> currentArgs['subAction'] == 'loadtpl') {
		if (isset ($_REQUEST['tpl'])) {
			$tplFile = P . 'inc/template/' . basename ($_REQUEST['tpl']) . '.tpl.php';
			if (file_exists ($tplFile)) {
				$tplContent = file_get_contents ($tplFile);
				$l = bw :: $conf['siteLang'];
				preg_match ("/<{$l}: definition>([\s\S]+?)<\/{$l}: definition>/", $tplContent, $tplInsert);
				if (isset ($tplInsert[1])) {
					die ($tplInsert[1]);
				}
			} 
		}
		exit ();
	}  elseif ($canonical -> currentArgs['subAction'] == 'gettitlelist') {
		$allTitles = $article -> getTitleList (1000);
		$outTitles = array();
		foreach ($allTitles as $aID => $aTitle) {
			$outTitles[] = $aTitle;
		} 
		die ('var allTitles='.json_encode ($outTitles).';var allFullList='.json_encode (array_flip ($allTitles)).';');
	}  elseif ($canonical -> currentArgs['subAction'] == 'getpreviewhtml') {
		$admin -> checkCSRFCode ('articlesave');
		if (!isset ($_REQUEST['smt'])) {
			stopError ($conf['l']['admin:msg:NoData']);
		} 
		ajaxSuccess (bwView :: textFormatter ($_REQUEST['smt']['aContent']));

	} elseif ($canonical -> currentArgs['subAction'] == 'delete') {
		$admin -> checkCSRFCode ('articlesave');
		$article -> deleteArticle ($_REQUEST['aID']);
		header ("Location: {$conf['siteURL']}/{$conf['linkPrefixAdmin']}/articles/{$conf['linkConj']}CSRFCode=" . $admin -> getCSRFCode ('navibar'));
	} elseif ($canonical -> currentArgs['subAction'] == 'batchdel') {
		$admin -> checkCSRFCode ('navibar');
		$aIDList = @explode ('<', $_REQUEST['aID']);
		$article -> deleteArticleBatch ($aIDList);
		ajaxSuccess ('');
	}  elseif ($canonical -> currentArgs['subAction'] == 'batchdraft') {
		$admin -> checkCSRFCode ('navibar');
		$aIDList = @explode ('<', $_REQUEST['aID']);
		$article -> changeAsDraft ($aIDList);
		ajaxSuccess ('');
	} elseif ($canonical -> currentArgs['subAction'] == 'uploader') {
		$admin -> checkCSRFCode ('upload');
		if (count ($_FILES) < 1) {
			exit ();
		} 

		$picfiles = $files = array();

		foreach ($_FILES["uploadFile"]["error"] as $key => $error) {
			if ($error == UPLOAD_ERR_OK) {
				$tmp_name = $_FILES["uploadFile"]["tmp_name"][$key];
				$fOriginalName = $_FILES["uploadFile"]["name"][$key];
				$fExtName = pathinfo ($fOriginalName, PATHINFO_EXTENSION);
				$fStoreName = substr (md5 (rand (10, 99) . $fOriginalName), 10, 8) . '.' . $fExtName;

				if (in_array (strtolower ($fExtName), array('gif', 'jpg', 'png', 'bmp', 'jpeg', 'jpe'))) {
					move_uploaded_file ($tmp_name, P . "storage/{$fStoreName}");
					$picfiles[]['fileURL'] = "{$conf['siteURL']}/storage/{$fStoreName}";
				} elseif (!in_array (strtolower ($fExtName), array('php', 'php3', 'asp', 'aspx', 'jsp', 'cgi', 'py', 'cf'))) {
					move_uploaded_file ($tmp_name, P . "storage/{$fStoreName}");
					$files[]['fileURL'] = "{$conf['siteURL']}/storage/{$fStoreName}";
				}
			} 
		} 

		$view -> setMaster ('adminuploadinsert');
		$view -> setPassData (array ('adminuploadedpic' => $picfiles, 'adminuploadedfile' => $files));
		$view -> setWorkFlow (array ('adminuploadinsert'));
		$view -> finalize ();
	} elseif ($canonical -> currentArgs['subAction'] == 'qiniuuploader') {
		$picfiles = $files = array();
		loadServices ();
		require_once (P . "inc/script/qiniu/QiniuClient.php");
		$qiniuClient = new qiniuClient (QINIU_AK, QINIU_SK);
		$uploadReturn = json_decode ($qiniuClient -> urlsafe_base64_decode($_REQUEST['upload_ret']), true);
		if (isset ($uploadReturn['fname'])) {
			$qiniuThumbCall = '?imageView2/2/w/800';
			if (strpos ($uploadReturn['ftype'], 'image') !== false) {
				$picfiles[]['fileURL'] = $conf['qiniuDomain'] ? "{$conf['qiniuDomain']}/{$uploadReturn['fname']}{$qiniuThumbCall}" : "http://{$conf['qiniuBucket']}.qiniudn.com/{$uploadReturn['fname']}{$qiniuThumbCall}";
			} else {
				$files[]['fileURL'] = $conf['qiniuDomain'] ? "{$conf['qiniuDomain']}/{$uploadReturn['fname']}" : "http://{$conf['qiniuBucket']}.qiniudn.com/{$uploadReturn['fname']}";
			}
		} 
		$view -> setMaster ('adminuploadinsert');
		$view -> setPassData (array ('adminuploadedpic' => $picfiles, 'adminuploadedfile' => $files));
		$view -> setWorkFlow (array ('adminuploadinsert'));
		$view -> finalize ();
	}  elseif ($canonical -> currentArgs['subAction'] == 'aliyunuploader') {
		$admin -> checkCSRFCode ('upload');
		loadServices ();
		$picfiles = $files = array();
		if (isset ($_REQUEST['filename'])) {
			$fOriginalName = basename (urlencode($_REQUEST['filename']));
			$fExtName = pathinfo ($fOriginalName, PATHINFO_EXTENSION);
			$fStoreName = "http://{$conf['aliyunBucket']}.{$conf['aliyunRegion']}.aliyuncs.com/storage/{$fOriginalName}";

			if (in_array (strtolower ($fExtName), array('gif', 'jpg', 'png', 'bmp', 'jpeg', 'jpe'))) {
				$picfiles[]['fileURL'] = $fStoreName;
			} elseif (!in_array (strtolower ($fExtName), array('php', 'php3', 'asp', 'aspx', 'jsp', 'cgi', 'py', 'cf'))) {
				$files[]['fileURL'] = $fStoreName;
			}
			$view -> setMaster ('adminuploadinsert');
			$view -> setPassData (array ('adminuploadedpic' => $picfiles, 'adminuploadedfile' => $files));
			$view -> setWorkFlow (array ('adminuploadinsert'));
			$view -> finalize ();
		} 
	} elseif ($canonical -> currentArgs['subAction'] == 'savecategories') {
		$admin -> checkCSRFCode ('category');
		if (!isset ($_REQUEST['smt'])) {
			stopError ($conf['l']['admin:msg:NoData']);
		} 
		//stopError (print_r($_REQUEST['smt2'], true));
		$smt2 = isset ($_REQUEST['smt2']) ? $_REQUEST['smt2'] : array ();
		$cates = new bwCategory;
		$cates -> bufferCacheClear ();
		$newCates = array_diff_key ($_REQUEST['smt'], bw :: $cateData);

		$cates -> addCategories ($newCates, $smt2);
		$deletedCates = array_diff_key (bw :: $cateData, $_REQUEST['smt']);
		$cates -> deleteCategories ($deletedCates);
		$remainedCates = array_diff_key (bw :: $cateData, $deletedCates);
		$remainedAsNewCates = $remainedNewCatesTheme = array ();
		foreach ($remainedCates as $k => $aRemainedCate) {
			$remainedAsNewCates[] = $_REQUEST['smt'][$k];
			$remainedNewCatesTheme[] = isset ($smt2[$k]) ? $smt2[$k] : '';
		} 
		$cates -> renameCategories (array_keys ($remainedCates), array_values ($remainedCates), $remainedAsNewCates);
		$cates -> updateCategoryThemes (array_keys ($remainedCates), $remainedNewCatesTheme);
		$cates -> orderCategories (array_keys ($_REQUEST['smt']));
		$cates -> endBufferCache ();
		ajaxSuccess ($conf['l']['admin:msg:ChangeSaved']);
	} elseif ($canonical -> currentArgs['subAction'] == 'validatecategory' || $canonical -> currentArgs['subAction'] == 'newcatenow') {
		$admin -> checkCSRFCode ('category');
		if (!isset ($_REQUEST['smt'])) {
			stopError ($conf['l']['admin:msg:NoData']);
		} 
		if (empty ($_REQUEST['smt']['aCateDispName'])) {
			stopError ($conf['l']['admin:msg:NoData']);
		} 
		if (empty ($_REQUEST['smt']['aCateURLName'])) { 
			$smt['aCateURLName'] = urlencode ($_REQUEST['smt']['aCateDispName']);
		} 
		else { 
			$smt['aCateURLName'] = urlencode ($_REQUEST['smt']['aCateURLName']);
		} 
		if ($smt['aCateURLName'] == '_trash' || $smt['aCateURLName'] == '_page') {
			stopError ($conf['l']['admin:msg:NoData']);
		}
		$smt['aCateDispName'] = htmlspecialchars ($_REQUEST['smt']['aCateDispName']);
		if (isset ($_REQUEST['smt']['aCateTheme'])) {
			if (!empty ($_REQUEST['smt']['aCateTheme'])) {
				$smt['aCateTheme'] = htmlspecialchars ($_REQUEST['smt']['aCateTheme']);
			}
		} else {
			$smt['aCateTheme'] = null;
		}
		if (array_key_exists ($smt['aCateURLName'], bw :: $cateData)) {
			stopError ($conf['l']['admin:msg:Existed']);
		} 
		if ($canonical -> currentArgs['subAction'] == 'validatecategory') {
			$view -> setMaster ('admincategorylist');
			$view -> setPassData ($smt);
			$view -> setWorkFlow (array ('admincategorylist'));
			$view -> finalize ();
		} else {
			$cates = new bwCategory;
			$cates -> addCategories (array ($smt['aCateURLName'] => $smt['aCateDispName']));
			$cates -> endBufferCache ();
			ajaxSuccess ('');
		}
	} else {
		$admin -> checkCSRFCode ('navibar'); 
		$article -> setCutTime (0);
		// Pagination
		$article -> alterPerPage (20);
		$article -> getArticleList ();
		$canonical -> calTotalPages ($article -> totalArticles);
		$canonical -> paginableURL = bw :: $conf['siteURL'] . '/' . bw :: $conf['linkPrefixAdmin'] . '/articles/list/%d' . bw :: $conf['linkConj'] . 'CSRFCode=' . $admin -> getCSRFCode ('navibar');
		$view -> doPagination ();
		$adminarticlelist = $article -> articleList;

		$article -> alterPerPage (10000000);
		$article -> getTrashedList ();
		$admindraftlist = $article -> articleList;

		$article -> getSinglePageList ();
		$adminsinglepagelist = $article -> articleList;


		$view -> setMaster ('admin');
		$view -> setPassData (array ('adminarticlelist' => $adminarticlelist, 'admindraftlist' => $admindraftlist, 'adminsinglepagelist' => $adminsinglepagelist, 'admincatelist' => bw :: $cateList, 'themeList' => $view -> scanForThemes (), 'newCSRFCode' => $admin -> getCSRFCode ('newarticle'), 'oldCSRFCode' => $admin -> getCSRFCode ('navibar'), 'cateCSRFCode' => $admin -> getCSRFCode ('category')));
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
		$acceptedKeys = array ('duoshuoID', 'disqusID', 'sinaAKey', 'sinaSKey', 'qiniuAKey', 'qiniuSKey', 'qiniuBucket', 'qiniuSync', 'qiniuUpload', 'qiniuDomain', 'APIOpen', 'basicAPI', 'advancedAPI', 'aliyunAKey', 'aliyunSKey', 'aliyunBucket', 'aliyunRegion');
		$smt = dataFilter ($acceptedKeys, $_REQUEST['smt']);
		$basicAPI = @explode ('<>', $smt['basicAPI']);
		$advancedAPI = @explode ('<>', $smt['advancedAPI']);
		$smt = array_map ('htmlspecialchars', $smt);
		$smt['basicAPI'] = array_filter ($basicAPI, 'strlen');
		$smt['advancedAPI'] = array_filter ($advancedAPI, 'strlen');
		if ($smt['qiniuBucket'] == '1') {
			require_once (P . "inc/script/qiniu/QiniuClient.php");
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
	} elseif ($canonical -> currentArgs['subAction'] == 'backup' || $canonical -> currentArgs['subAction'] == 'sync') {
		$admin -> checkCSRFCode ('services');
		require_once (P . "inc/script/pclzip/pclzip.lib.php");
		$ff = 'storage/backup' . date("YmdHis") . '.zip';
		$archive = new PclZip (P . $ff);
		if (strtolower (DBTYPE) == 'mysql') {
			include_once (P. 'inc/script/dbmanage/DbManage.class.php');
			$db = new DBManage (DBADDR, DBUSERNAME, DBPASSWORD, DBNAME, 'utf8');
			$db->backup ('', P. 'conf/', '');
		}
		$v_list = DBTYPE == 'SQLite' ? $archive -> create('conf,' . DBNAME, PCLZIP_OPT_REMOVE_ALL_PATH) : $archive -> create('conf', PCLZIP_OPT_REMOVE_ALL_PATH);
		if ($v_list == 0) {
			stopError ($conf['l']['admin:msg:PclzipError'] . $archive -> errorInfo(true));
		} else {
			if ($canonical -> currentArgs['subAction'] == 'backup') {
				header ("Location: {$conf['siteURL']}/{$ff}");
			} 
			else { 
				loadServices ();
				require_once (P . "inc/script/qiniu/QiniuClient.php");
				$qiniuClient = new qiniuClient (QINIU_AK, QINIU_SK);
				$result = $qiniuClient -> uploadFile (P . $ff, $conf['qiniuBucket'], $ff);
				@unlink (P . $ff);
				header ("Location: {$conf['siteURL']}/{$conf['linkPrefixAdmin']}/services/{$conf['linkConj']}CSRFCode=" . $admin -> getCSRFCode ('navibar'));
				exit ();
			} 
		} 
	}  elseif ($canonical -> currentArgs['subAction'] == 'reset') {
		$admin -> checkCSRFCode ('services');
		dochmod ('.');
		header ("Location: {$conf['siteURL']}/{$conf['linkPrefixAdmin']}/services/{$conf['linkConj']}CSRFCode=" . $admin -> getCSRFCode ('navibar'));
		exit ();
	}  elseif ($canonical -> currentArgs['subAction'] == 'getnewapikey') {
		$admin -> checkCSRFCode ('services');
		$APIKey = 'o_' . sha1 (bw :: $conf['siteKey'] . 'KEY' . rand (10000, 99999));
		$authSecret = sha1 ($APIKey . bw :: $conf['siteKey'] . "API");
		ajaxSuccess ($APIKey . '-' . $authSecret);
	}
	else {
		$admin -> checkCSRFCode ('navibar');
		loadServices ();
		if (isset ($conf['basicAPI'])) {
			$basicAPI = array ();
			foreach ((array) $conf['basicAPI'] as $itm => $val) {
				$basicAPI[] = array ('apiID' => 'basic' . $itm, 'apiKey' => $val, 'apiSecret' => sha1 ($val . bw :: $conf['siteKey'] . "API"));
			}
			$view -> setPassData (array ('basicAPI' => $basicAPI));
		}
		if (isset ($conf['advancedAPI'])) {
			$advancedAPI = array ();
			foreach ((array) $conf['advancedAPI'] as $itm => $val) {
				$advancedAPI[] = array ('apiID' => 'advanced' . $itm, 'apiKey' => $val, 'apiSecret' => sha1 ($val . bw :: $conf['siteKey'] . "API"));
			}
			$view -> setPassData (array ('advancedAPI' => $advancedAPI));
		}
		$view -> setMaster ('admin');
		$view -> setPassData (array ('serviceCSRFCode' => $admin -> getCSRFCode ('services')));
		$view -> setWorkFlow (array ('adminservices', 'admin'));
		$view -> finalize ();
	} 
} 

if ($canonical -> currentArgs['mainAction'] == 'dashboard') {
	if ($canonical -> currentArgs['subAction'] == 'update' || $canonical -> currentArgs['subAction'] == 'updatedirect') {
		$admin -> checkCSRFCode ('update');
		if ($canonical -> currentArgs['subAction'] == 'update') {
			if (!isset ($_REQUEST['dlURL']) || !isset ($_REQUEST['hash'])) {
				stopError ($conf['l']['admin:msg:NoData']);
			} 
			$packageContent = curlRetrieve ($_REQUEST['dlURL']);
			if (!$packageContent) {
				stopError ($conf['l']['admin:msg:UpdateDownloadFail']);
			} 
			else {
				file_put_contents (P . 'update/dlupkg_tmp.zip', $packageContent);
				@chmod (P . 'update/dlupkg_tmp.zip', 0777);
				if (md5_file (P . 'update/dlupkg_tmp.zip') <> strtolower ($_REQUEST['hash'])) {
					stopError ($conf['l']['admin:msg:UpdateDownloadFail']);
				}
			}
			$pkgName = P . 'update/dlupkg_tmp.zip';
		}
		
		else {
			if (!file_exists (P . 'update/manual_update.zip')) {
				stopError ($conf['l']['admin:msg:UpdatePackageMissing']);
			}
			$pkgName = P . 'update/manual_update.zip';
		}

		include_once (P . "inc/zip.inc.php");
		bwZip :: zipRead ($pkgName, 1, 1);
		@unlink ($pkgName);
		$sqlUpdater = P . 'update/update.' . strtolower (DBTYPE) . '.sql';
		if (file_exists ($sqlUpdater)) { 
			$allSqls = @file ($sqlUpdater);
			bw :: $db -> silentError (true);
			foreach ($allSqls as $sql) {
				bw :: $db -> dbExec ($sql);
			}
			@unlink ($sqlUpdater);
		}
		clearCache ();
		header ("Location: {$conf['siteURL']}/{$conf['linkPrefixAdmin']}/dashboard/{$conf['linkConj']}CSRFCode=" . $admin -> getCSRFCode ('navibar') . "#UpdateSuccess");
		exit ();
	} else {
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
		$statVals['whatsHottest'] = array ();
		foreach ($article -> articleList as $row) {
			$statVals['whatsHottest'][] = "<a href=\"{$conf['siteURL']}/{$conf['linkPrefixArticle']}/{$row['aID']}/\">{$row['aTitle']}</a>";
		} 
		$statVals['whatsHottest'] = '<li>' . @implode('</li><li>', $statVals['whatsHottest']) . '</li>';

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
		$view -> setPassData (array ('newCSRFCode' => $admin -> getCSRFCode ('newarticle'), 'serviceCSRFCode' => $admin -> getCSRFCode ('services'), 'updateCSRFCode' => $admin -> getCSRFCode ('update')));
		$view -> setWorkFlow (array ('admindashboard', 'admin'));
		$view -> finalize ();
	} 
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
			$extID = urldecode (basename ($_REQUEST['extID']));
			bw :: $db -> dbExec ('DELETE FROM extensions WHERE extID=?', array ($extID));
			if (file_exists (P . 'extension/' . $extID . '/do.php')) {
				include (P . 'extension/' . $extID . '/do.php');
				$callableClass = 'ext_' . $extID;
				if (method_exists ($callableClass , 'uninstall')) {
					$callableClass :: uninstall (); 
				}
			}
			clearCache ();
			ajaxSuccess ($conf['l']['admin:msg:ChangeSaved']);
		} 
	} elseif ($canonical -> currentArgs['subAction'] == 'removetheme') {
		$admin -> checkCSRFCode ('extensions');
		if (!isset ($_REQUEST['themeID'])) {
			stopError ($conf['l']['admin:msg:NoData']);
		} else {
			$themeID = urldecode (basename ($_REQUEST['themeID']));
			if ($themeID == 'default' || $themeID == bw :: $conf['siteTheme']) {
				stopError ('Cannot remove default theme or the theme in use.');
			}
			@rrmdir (P . 'theme/' . basename ($themeID) . '/');
			clearCache ();
			ajaxSuccess ($conf['l']['admin:msg:ChangeSaved']);
		} 
	} elseif ($canonical -> currentArgs['subAction'] == 'add') {
		$admin -> checkCSRFCode ('newext');
		if (!isset ($_REQUEST['extID'])) {
			stopError ($conf['l']['admin:msg:NoData']);
		} else {
			$extList = bw :: getAllExtensions ();
			$extID = urldecode (basename ($_REQUEST['extID']));
			$extID = htmlspecialchars ($extID, ENT_QUOTES, 'UTF-8');
			if (array_key_exists ($extID, $extList)) {
				stopError ($conf['l']['admin:msg:Existed']);
			} 
			if (!file_exists (P . 'extension/' . $extID . '/define.php') || !file_exists (P . 'extension/' . $extID . '/do.php')) {
				stopError ($conf['l']['admin:msg:ExtNotFound']);
			} 
			$aExt = @parse_ini_file (P . 'extension/' . $extID . '/define.php');
			$aExt['extDesc'] = "name='{$aExt['name']}'\r\nintro='{$aExt['intro']}'\r\nauthor='{$aExt['author']}'\r\nurl='{$aExt['url']}'";
			bw :: $db -> dbExec ('INSERT INTO extensions (extID, extDesc, extHooks, extActivate, extOrder, isWidget) VALUES (?, ?, ?, 1, ?, 0)', array ($aExt['ID'], $aExt['extDesc'], $aExt['hooks'], count ($extList) + 1));
			include (P . 'extension/' . basename ($extID) . '/do.php');
			$callableClass = 'ext_' . $extID;
			if (method_exists ($callableClass , 'setup')) {
				$callableClass :: setup (); 
			}
			clearCache ();
			if (defined ('ajax')) {
				ajaxSuccess ($conf['l']['admin:msg:ChangeSaved']);
			} else {
				header ("Location: {$conf['siteURL']}/{$conf['linkPrefixAdmin']}/extensions/{$conf['linkConj']}CSRFCode=" . $admin -> getCSRFCode ('navibar'));
				exit ();
			}
		} 
	} elseif ($canonical -> currentArgs['subAction'] == 'widget') {
		$admin -> checkCSRFCode ('newext');
		if (!isset ($_REQUEST['wgtID']) || empty ($_REQUEST['wgtID'])) {
			stopError ($conf['l']['admin:msg:NoData']);
		} else {
			$extOrder = floor ($_REQUEST['extOrder']);
			$extList = bw :: getAllWidgets ();
			$extID = htmlspecialchars ($_REQUEST['wgtID'], ENT_QUOTES, 'UTF-8');
			$patternWidgetHooks = array ('wghtmlhead' => 'value',
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
				$extStorage[$wgtCol] = ($wgtCol == 'value') ? $_REQUEST['wgt' . $wgtCol] : htmlspecialchars ($_REQUEST['wgt' . $wgtCol], ENT_QUOTES, 'UTF-8');
			} 
			if ($extOrder == -1) {
				if (array_key_exists ($extID, $extList)) {
					stopError ($conf['l']['admin:msg:Existed']);
				} 
				bw :: $db -> dbExec ('INSERT INTO extensions (extID, extDesc, extHooks, extActivate, extOrder, isWidget, extStorage) VALUES (?, "", ?, 1, ?, 1, ?)', array ($extID, $extHooks, count ($extList) + 1, json_encode ($extStorage)));
			} else {
				if (!array_key_exists ($extID, $extList)) {
					stopError ($conf['l']['admin:msg:NotExist']);
				} 
				bw :: $db -> dbExec ('UPDATE extensions SET extStorage=? WHERE extID=? AND isWidget=1', array (json_encode ($extStorage), $extID));
			} 
			clearCache ();
			ajaxSuccess ($conf['l']['admin:msg:ChangeSaved']);
		} 
	} elseif ($canonical -> currentArgs['subAction'] == 'savewidgetsort') {
		$admin -> checkCSRFCode ('extensions');
		if (!isset ($_REQUEST['sortstr'])) {
			stopError ($conf['l']['admin:msg:NotExist']);
		}
		$allWidgets = @explode ('<>', $_REQUEST['sortstr']);
		$allWidgets = array_reverse ($allWidgets);
		$dataLine = array();
		$i = 1;
		foreach ($allWidgets as $extID) {
			$dataLine[$i][':extOrder'] = $i;
			$dataLine[$i][':extID'] = $extID;
			$i += 1;
		} 

		bw :: $db -> dbExecBatch ('UPDATE extensions SET extOrder=:extOrder WHERE extID=:extID', $dataLine);
		clearCache ();
		ajaxSuccess ($conf['l']['admin:msg:ChangeSaved']);
	}  elseif ($canonical -> currentArgs['subAction'] == 'savehooks') {
		$admin -> checkCSRFCode ('extensions');
		foreach ($allOpenHooks as $openHook) {
			@file_put_contents (P . 'conf/insert_' . $openHook . '.htm', $_REQUEST['smt'][$openHook]);
		} 
		clearCache ();
		ajaxSuccess ($conf['l']['admin:msg:ChangeSaved']);
	}  elseif ($canonical -> currentArgs['subAction'] == 'exporttheme') { 
		$admin -> checkCSRFCode ('extensions');
		if (!isset ($_REQUEST['themeID'])) {
			stopError ($conf['l']['admin:msg:NoData']);
		}
		$themeID = basename ($_REQUEST['themeID']);
		if (!file_exists (P . "theme/{$themeID}/info.php")) {
			stopError ($conf['l']['admin:msg:NotExist']);
		}
		include (P. 'inc/zip.inc.php');
		bwZip :: zipFolder (P . "theme/{$themeID}/" , P . 'storage/theme_'.$themeID.'.pkg');
		header ("Location: {$conf['siteURL']}/storage/theme_{$themeID}.pkg");
	}  elseif ($canonical -> currentArgs['subAction'] == 'exportextension') { 
		$admin -> checkCSRFCode ('extensions');
		if (!isset ($_REQUEST['extID'])) {
			stopError ($conf['l']['admin:msg:NoData']);
		}
		$extID = basename ($_REQUEST['extID']);
		if (!file_exists (P . "extension/{$extID}/define.php")) {
			stopError ($conf['l']['admin:msg:NotExist']);
		} else {
			file_put_contents (P . "extension/autoinstall.txt", $extID);
		}
		include (P. 'inc/zip.inc.php');
		bwZip :: zipFolder (P . "extension/{$extID}/" , P . 'storage/extension_'.$extID.'.pkg', array (P . "extension/autoinstall.txt"));
		unlink (P . "extension/autoinstall.txt");
		header ("Location: {$conf['siteURL']}/storage/extension_{$extID}.pkg");
	} elseif ($canonical -> currentArgs['subAction'] == 'installpkg') { 
		$admin -> checkCSRFCode ('newext');
		if (!isset ($_FILES['userfile']) || !isset ($_REQUEST['pkgType'])) {
			stopError ($conf['l']['admin:msg:PkgError']);
		} 
		if ($_REQUEST['pkgType']!='theme' && $_REQUEST['pkgType']!='extension') {
			stopError ($conf['l']['admin:msg:PkgError']);
		} 
		if (!$_FILES['userfile']['tmp_name']) {
			stopError ($conf['l']['admin:msg:PkgError']);
		} 
		if (pathinfo ($_FILES["userfile"]["name"], PATHINFO_EXTENSION) != 'pkg') {
			stopError ($conf['l']['admin:msg:PkgError']);
		} 
		$fName = P . 'storage/theme_' . rand (100000, 999999) . '.pkg';

		if (move_uploaded_file ($_FILES['userfile']['tmp_name'],  $fName)) {
			include (P. 'inc/zip.inc.php');
			bwZip :: zipRead ($fName, true, false, P . $_REQUEST['pkgType'] . '/');
			if ($_REQUEST['pkgType']=='extension') {
				if (file_exists (P . "extension/autoinstall.txt")) {
					$extID = file_get_contents (P . "extension/autoinstall.txt");
					unlink (P . "extension/autoinstall.txt");
					clearCache ();
					header ("Location: {$conf['siteURL']}/{$conf['linkPrefixAdmin']}/extensions/add/{$conf['linkConj']}extID={$extID}&CSRFCode=" . $admin -> getCSRFCode ('newext'));
					exit ();
				}
			}
		} 
		header ("Location: {$conf['siteURL']}/{$conf['linkPrefixAdmin']}/extensions/{$conf['linkConj']}CSRFCode=" . $admin -> getCSRFCode ('navibar'));
	} elseif ($canonical -> currentArgs['subAction'] == 'selecttheme') { 
		if (!isset ($_REQUEST['themeID'])) {
			stopError ($conf['l']['admin:msg:NoData']);
		} 
		$themeID = basename ($_REQUEST['themeID']);
		if (!file_exists (P . "theme/{$themeID}/info.php")) {
			stopError ($conf['l']['admin:msg:NotExist']);
		}
		file_put_contents (P . 'conf/info.php', str_replace ("'siteTheme' => '{$conf['siteTheme']}'", "'siteTheme' => '{$themeID}'", file_get_contents (P . 'conf/info.php')));
		clearCache ();
		ajaxSuccess ($conf['l']['admin:msg:ChangeSaved']);
	}
	
	else {
		$admin -> checkCSRFCode ('navibar');
		$view -> setMaster ('admin');
		$view -> setPassData (array ('themeList' => $view -> scanForThemes (), 'extList' => bw :: getAllExtensions (), 'newCSRFCode' => $admin -> getCSRFCode ('newext'), 'extCSRFCode' => $admin -> getCSRFCode ('extensions')));
		$view -> setPassData (array ('wgtListHtmlhead' => bw :: getWidgets ('wghtmlhead'), 'wgtListHeader' => bw :: getWidgets ('wgheader'), 'wgtListSiderbar' => bw :: getWidgets ('wgsidebar'), 'wgtListFooter' => bw :: getWidgets ('wgfooter')));
		foreach ($allOpenHooks as $openHook) {
			$allHooks['insert_' . $openHook] = @file_get_contents (P . 'conf/insert_' . $openHook . '.htm');
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

if ($canonical -> currentArgs['mainAction'] == 'market') {
	if ($canonical -> currentArgs['subAction'] == 'detail') { 
		if (!isset ($_SESSION['enable_load_market'])) {
			stopError ($conf['l']['admin:msg:NotExist']);
		}
		$admin -> checkCSRFCode ('install' . $_SESSION['enable_load_market']);
		if (!isset ($_REQUEST['dlu']) || !isset ($_REQUEST['dir']) || !isset ($_REQUEST['guid'])) {
			stopError ($conf['l']['admin:msg:NotExist']);
		}
		$view -> setMaster ('marketdetail');
		$tDir = basename ($_REQUEST['dir']);
		if (file_exists (P . "theme/{$tDir}/info.php")) {
			include (P . "theme/{$tDir}/info.php");
			$owned = $theme['guid'] == $_REQUEST['guid'] ? 1 : 0;
		}
		else {
			$owned = 0;
		}
		$view -> setPassData (array ('itemID' => $_REQUEST['dlu'], 'owned' => $owned, 'installCSRFCode' => $admin -> getCSRFCode ('install' . $_SESSION['enable_load_market'])));
		$view -> setWorkFlow (array ('marketdetail'));
		$authX = $view -> getOutput ();
		$view -> setMaster ('admin');
		$view -> setPassData (array ('adminplainpage' => $authX));
		$view -> setWorkFlow (array ('admin'));
		$view -> finalize ();
	} elseif ($canonical -> currentArgs['subAction'] == 'installpkg') { 
		$returnError = true;
		if (isset ($_SESSION['enable_load_market']) && $admin -> getCSRFCode ('install' . $_SESSION['enable_load_market']) == $_REQUEST['CSRFCode'] && isset ($_REQUEST['dl'])) {
			$returnError = false;
		}
		if ($returnError) {
			$view -> setMaster ('marketinstallfailure');
			$view -> setWorkFlow (array ('marketinstallfailure'));
			$view -> finalize ();
		} else {

			$view -> setMaster ('marketinstallsuccess');
			$view -> setWorkFlow (array ('marketinstallsuccess'));
			$view -> finalize ();
		}
	} else {
		if (!isset ($_SESSION['enable_load_market'])) {
			$_SESSION['enable_load_market'] = $rndCode = rand (1000, 9999);
		}
		else {
			$rndCode = $_SESSION['enable_load_market'];
		}
		$admin -> checkCSRFCode ('navibar');
		$view -> setMaster ('admin');
		$view -> setPassData (array ('installCSRFCode' => $admin -> getCSRFCode ('install' . $rndCode)));
		$view -> setWorkFlow (array ('adminmarket', 'admin'));
		$view -> finalize ();
	} 
} 

hook ('newAdminCategory', 'Execute', $canonical, $admin, $view);

stopError ($conf['l']['admin:msg:NeedLogin']);