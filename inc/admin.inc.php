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

class bwAdmin {
	private $conf;
	public $token;
	public $verified;

	public function __construct ()
	{
		if (isset ($_SERVER['HTTP_USER_AGENT'])) {
			bw :: $conf['myUA'] = $_SERVER['HTTP_USER_AGENT'];
		} else {
			if (!isset ($_COOKIE['TMP_GUID'])) { //Not a security key. Can be any value.
				$tmpKey = uniqid ();
				setcookie ('TMP_GUID', $tmpKey);
				bw :: $conf['myUA'] = $tmpKey;
			}
			else {
				bw :: $conf['myUA'] = $_COOKIE['TMP_GUID'];
			}
		}
		$this -> initToken ();
		$this -> verified = false;
	} 

	private function initToken ()
	{
		$this -> token = md5(bw :: $conf['siteKey'] . bw :: $conf['myUA']);
	} 

	public function verifyToken ($sToken)
	{
		if (md5(sha1($sToken) . bw :: $conf['myUA']) == $this -> token) {
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
		$_SESSION['login-token'] = md5(sha1($sToken) . bw :: $conf['myUA']);
	} 

	public function storeMobileToken ()
	{
		$_SESSION['login-token'] = md5(bw :: $conf['siteKey'] . bw :: $conf['myUA']);
	} 

	public function getCSRFCode ($actionCode)
	{
		if (isset ($_SESSION['login-token'])) {
			return substr (md5 (bw :: $conf['myUA'] . bw :: $conf['siteKey'] . $actionCode), 0, 8);
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
		} elseif (substr (md5 (bw :: $conf['myUA'] . bw :: $conf['siteKey'] . $actionCode), 0, 8) <> $_REQUEST['CSRFCode']) {
			stopError (bw :: $conf['l']['admin:msg:CSRF']);
		} 
	}
} 
