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

$view = new bwView;

if ($canonical -> currentArgs['mainAction'] == 'na') { //Authorize a mobile phone
	$knownDevices = array(
		'iphone'	 => $conf['l']['page:MPiPhone'],
		'ipad'	 => $conf['l']['page:MPiPad'],
		'android' => $conf['l']['page:MPAndroid'],
		'windows' => $conf['l']['page:MPWindows'],
		'blackberry' => $conf['l']['page:MPBlackberry'],
		'symbian' => $conf['l']['page:MPSymbian'],
		'palm' => $conf['l']['page:MPPalm'],
		'ipod'	 => $conf['l']['page:MPiPodTouch'],
	);

	$ua = strtolower ($_SERVER['HTTP_USER_AGENT']);
	foreach ($knownDevices as $devID => $devName) { 
		if (strpos ($ua, $devID) !== false) {
			$uaDev = $devName;
			break;
		}
	}
	if (!isset ($uaDev)) { 
		$uaDev = $conf['l']['page:MPDefault'];
	}
	$uaDev = $conf['authorName'] . $conf['l']['page:Conj'] . $uaDev;

	$view -> setMaster ('authmobile');
	$view -> setPassData (array ('deviceName' => $uaDev));
	$view -> setWorkFlow (array ('authmobile'));
	$authX = $view -> getOutput ();
	$view -> setMaster ('plainpage');
	$view -> setPassData (array ('plainContent' => $authX));
	$view -> setWorkFlow (array ('plainpage'));
	$view -> finalize ();
}

elseif ($canonical -> currentArgs['mainAction'] == 'nado') {
	if (!isset ($_POST['s_token']) || !isset ($_POST['s_myname'])) {
		stopError (bw :: $conf['l']['page:ComError1']);
	}
	$admin = new bwAdmin;
	$admin -> verifyToken ($_POST['s_token']);
	if (!$admin -> verified) {
		stopError (bw :: $conf['l']['page:AuthMobileError']);
	} else {
		$s_myname = htmlspecialchars ($_POST['s_myname']);
		$keyNewAdd = sha1($conf['siteKey'] . 'mobile' . $s_myname);
		$allMobileKeys = array ();
		if (file_exists (P . 'conf/mobileauth.php')) {
			include_once (P . 'conf/mobileauth.php');
		}
		$allMobileKeys[$s_myname]=$keyNewAdd;
		$valString = "<?php\r\n\$allMobileKeys=" . var_export ($allMobileKeys, true) . ";?>";
		$rS = file_put_contents (P . "conf/mobileauth.php", $valString);
		if ($rS) {
			$view -> setMaster ('authmobilefinish');
			$view -> setPassData (array ('deviceName' => $s_myname, 'deviceMobileToken' => $keyNewAdd));
			$view -> setWorkFlow (array ('authmobilefinish'));
			$authX = $view -> getOutput ();
			$view -> setMaster ('plainpage');
			$view -> setPassData (array ('plainContent' => $authX));
			$view -> setWorkFlow (array ('plainpage'));
			$view -> finalize ();
		} else {
			stopError ($conf['l']['admin:msg:ChangeNotSaved']);
		} 
	} 
}

elseif ($canonical -> currentArgs['mainAction'] == 'gona') { //Authorize a mobile phone
	$view -> setMaster ('authmobilego');
	$myIP = getIP ();
	$naOneTime = rand(100, 999);
	$view -> setPassData (array ('ipPC' => substr (md5 ($myIP[0] . $naOneTime . $conf['siteKey'] . 'login'), 3, 6)));
	$view -> setWorkFlow (array ('authmobilego'));
	$authX = $view -> getOutput ();
	$view -> setMaster ('plainpage');
	$view -> setPassData (array ('plainContent' => $authX));
	$view -> setWorkFlow (array ('plainpage'));
	$view -> finalize ();
}

elseif ($canonical -> currentArgs['mainAction'] == 'nalogin') { //Authorize a mobile phone
	$ipPC = $canonical -> currentArgs['subAction'];
	if (strlen ($ipPC) <> 6) {
		stopError (bw :: $conf['l']['page:AuthMobileError']);
	}
	$view -> setMaster ('authmobileconfirm');
	$myIP = getIP ();
	$view -> setPassData (array ('ipPC' => $ipPC));
	$view -> setWorkFlow (array ('authmobileconfirm'));
	$authX = $view -> getOutput ();
	$view -> setMaster ('plainpage');
	$view -> setPassData (array ('plainContent' => $authX));
	$view -> setWorkFlow (array ('plainpage'));
	$view -> finalize ();

}

elseif ($canonical -> currentArgs['mainAction'] == 'nacheck') { //Authorize a mobile phone
	$ipPC = $canonical -> currentArgs['subAction'];
	if (strlen ($ipPC) <> 6 || !isset ($_REQUEST['s_token'])) {
		stopError ('');
	}
	if (file_exists (P . 'conf/mobileauth.php')) {
		include_once (P . 'conf/mobileauth.php');
		if (in_array ($_REQUEST['s_token'], $allMobileKeys)) {
			bw :: $db -> dbExec ('REPLACE INTO cache (caID, caContent) VALUES (?, ?)', array ('nalogin', $ipPC));
			ajaxSuccess($ipPC);
		}
	}
	stopError ('');
}

elseif ($canonical -> currentArgs['mainAction'] == 'nasearch') { //Authorize a mobile phone
	if (!isset ($_REQUEST['inPC'])) {
		stopError ('6');
	}
	if (strlen ($_REQUEST['inPC']) <> 6) {
		stopError ('');
	}
	$inPC2 = bw :: $db -> getSingleRow ('SELECT * FROM cache WHERE caID=?', array('nalogin'));
	if ($inPC2) {
		if ($inPC2['caContent'] == $_REQUEST['inPC']) { 
			@session_start ();
			$admin = new bwAdmin;
			$admin -> storeMobileToken ();
			bw :: $db -> dbExec ('DELETE FROM cache WHERE caID=?', array ('nalogin'));
			ajaxSuccess ($admin -> getCSRFCode ('navibar'));
		}
	}
	stopError ('');
}

//Rest is comment

if ($conf['commentOpt']<>0) {
	loadServices ();
	if ($conf['commentOpt'] == 1 || $conf['commentOpt'] == 2) { //Build-in comment
		@session_start ();
		$comment = new bwComment;
	} elseif ($conf['commentOpt'] == 3) {
		die ('Access Denied.');
	}
} else {
	die ('Access Denied.');
} 

if ($canonical -> currentArgs['mainAction'] == 'comments') {
	if ($canonical -> currentArgs['subAction'] == 'submit') {
		$smt = $_REQUEST['smt'];
		if (isset ($smt['socialkey'])) {
			if ($smt['socialkey'] == 'sina'  && isset ($_SESSION['sina_token'])) {
				define ("WB_AKEY" , bw :: $conf['sinaAKey']);
				define ("WB_SKEY" , bw :: $conf['sinaSKey']);
				define ("WB_CALLBACK_URL" , bw :: $conf['siteURL'] . '/' . bw :: $conf['linkPrefixSend'] . '/sina/callback/');
				include_once (P . 'inc/script/sina-weibo/saetv2.ex.class.php');
				$c = new SaeTClientV2 (WB_AKEY, WB_SKEY, $_SESSION['sina_token']['access_token']);
				$uid_get = $c -> get_uid ();
				$user_message = $c-> show_user_by_id ($uid_get['uid']);
				$smt['userAvatar'] = $user_message['avatar_large'];
				$smt['userURL'] = 'http://weibo.com/' . $user_message['profile_url'];
				$smt['socialkey'] = 'weibo';
			}
			elseif ($smt['socialkey'] == 'administrator'  && isset ($_SESSION['login-token'])) {
				$admin = new bwAdmin;
				$admin -> verifySessionToken ($_SESSION['login-token']);
				if (!$admin -> verified) {
					unset ($smt['userAvatar']);
					$smt['socialkey'] = '';
				} 
				else {
					$smt['userAvatar'] = bw :: $conf['siteURL']. '/conf/profile.png';
				}
			}
			else {
				unset ($smt['userAvatar']);
				$smt['socialkey'] = '';
			}
		}
		if (strtolower (trim ($smt['userName'])) == strtolower (bw :: $conf['authorName']) && $smt['socialkey'] <> 'administrator') {
			stopError (bw :: $conf['l']['page:NameViolation']);
		} 
		if ($conf['commentOpt'] == 2 && !$smt['socialkey']) {
			stopError (bw :: $conf['l']['page:LoginRequiredError']);
		}
		$smt = $comment -> addComment ($smt);
		$view -> setMaster ('ajaxcomment');
		$view -> setPassData ($smt);
		$view -> setWorkFlow (array ('ajaxcomment'));
		$view -> finalize ();
	}
	if ($canonical -> currentArgs['subAction'] == 'check') {
		if (isset ($_SESSION['login-token'])) {
			$admin = new bwAdmin;
			$admin -> verifySessionToken ($_SESSION['login-token']);
			if ($admin -> verified) {
				ajaxSuccess ('');
			} 
		}
		stopError ('');
	}
	if ($canonical -> currentArgs['subAction'] == 'load') {
		if (!isset ($_REQUEST['aID'])) {
			stopError (bw :: $conf['l']['admin:msg:NotExist']);
		}
		$comment -> alterAID ($_REQUEST['aID']);
		$comment -> getComList ();
		$totalBatches = ceil ($comment -> totalCom / bw :: $conf['comPerLoad']);
		$view -> setPassData (array ('comments' => $comment -> comList));
		$view -> setPassData (array ('totalbatches' => $totalBatches, 'currentbatch' => $canonical -> currentPage));
		$view -> setMaster ('ajaxcommentgroup');
		$view -> setWorkFlow (array ('ajaxcommentgroup'));
		$view -> finalize ();
	}
}

if ($canonical -> currentArgs['mainAction'] == 'sina') {
	define ("WB_AKEY" , bw :: $conf['sinaAKey']);
	define ("WB_SKEY" , bw :: $conf['sinaSKey']);
	define ("WB_CALLBACK_URL" , bw :: $conf['siteURL'] . '/' . bw :: $conf['linkPrefixSend'] . '/sina/callback/');
	include_once (P . 'inc/script/sina-weibo/saetv2.ex.class.php');
	if ($canonical -> currentArgs['subAction'] == 'start') {
		$o = new SaeTOAuthV2 (WB_AKEY, WB_SKEY);
		if (!isset ($_REQUEST['aID'])) {
			stopError (bw :: $conf['l']['page:SinaError']);
		}
		$code_url = $o -> getAuthorizeURL (WB_CALLBACK_URL . '?aID=' . $_REQUEST['aID']);
		header ("Location: $code_url");
		exit ();
	}
	if ($canonical -> currentArgs['subAction'] == 'callback') {
		$o = new SaeTOAuthV2 (WB_AKEY, WB_SKEY);
		if (!isset ($_REQUEST['code'])) {
			stopError (bw :: $conf['l']['page:SinaError']);
		}
		$keys = array ();
		$keys['code'] = $_REQUEST['code'];
		$keys['redirect_uri'] = WB_CALLBACK_URL . '?aID=' . $_REQUEST['aID'];
		try {
			$token = $o -> getAccessToken('code', $keys) ;
		} catch (OAuthException $e) {
			stopError (bw :: $conf['l']['page:SinaError']);
		}
		if ($token) {
			$_SESSION['sina_token'] = $token;
			setcookie ('weibojs_'.$o->client_id, http_build_query ($token));
			header ('Location: ' . bw :: $conf['siteURL'] . '/read.php/' . $_REQUEST['aID'] . '/');
			exit ();
		} else {
			stopError (bw :: $conf['l']['page:SinaError']);
		}
	}
	if ($canonical -> currentArgs['subAction'] == 'check') {
		if (!isset ($_SESSION['sina_token'])) {
			stopError ('Not logged in.');
		}
		$c = new SaeTClientV2 (WB_AKEY, WB_SKEY, $_SESSION['sina_token']['access_token']);
		$uid_get = $c -> get_uid ();
		$user_message = $c-> show_user_by_id ($uid_get['uid']);
		ajaxSuccess ($user_message);
	}
	if ($canonical -> currentArgs['subAction'] == 'end') {
		if (!isset ($_REQUEST['aID'])) {
			stopError (bw :: $conf['l']['page:SinaError']);
		}
		unset ($_SESSION['sina_token']);
		header ('Location: ' . bw :: $conf['siteURL'] . '/read.php/' . $_REQUEST['aID'] . '/');
		exit ();
	}
}
