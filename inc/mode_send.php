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
				define ("WB_CALLBACK_URL" , bw :: $conf['siteURL'] . '/send.php/sina/callback/');
				include_once (P . 'inc/sdk/sina-weibo/saetv2.ex.class.php');
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
		$view = new bwView;
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
	define ("WB_CALLBACK_URL" , bw :: $conf['siteURL'] . '/send.php/sina/callback/');
	include_once (P . 'inc/sdk/sina-weibo/saetv2.ex.class.php');
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
