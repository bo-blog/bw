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
