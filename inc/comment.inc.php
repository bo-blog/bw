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

class bwComment {
	public $comList;
	public $totalCom;
	private $pageNum;
	private $parentComID;
	private $aID;
	private $listBlocked;
	private $myIP;

	public function __construct ()
	{
		global $canonical;
		$this -> pageNum = $canonical -> currentPage;
		$this -> comList = array();
		$this -> aID = $this -> listBlocked = false;
		$this -> totalCom = 0;
		$this -> myIP = getIP ();
	}

	public function alterAID ($aID)
	{
		$this -> aID = $aID;
		hook ('alterAID', 'Execute', $this);
	}

	public function alterPerPage ($num)
	{
		bw :: $conf['comPerLoad'] = floor ($num);
	}

	public function showBlocked ($status = false)
	{
		$this -> listBlocked = $status ? true : false;
	}

	public function showBlockedOnly ($status = false)
	{
		$this -> listBlocked = $status ? 'only' : true;
	}

	public function getComList ()
	{
		$currentTitleStart = ($this -> pageNum-1) * bw :: $conf['comPerLoad'];
		if ($this -> listBlocked) {
			$blockedStr = (string) $this -> listBlocked == 'only' ? 'comBlock=1' : '1=1';
		} else {
			$blockedStr = 'comBlock=0';
		}

		$qStr = $this -> aID === false ? "SELECT articles.aTitle,comments.* FROM comments INNER JOIN articles ON comments.comArtID = articles.aID WHERE {$blockedStr} ORDER BY comTime DESC LIMIT ?, ?" : "SELECT * FROM comments WHERE comArtID=? AND {$blockedStr} ORDER BY comTime DESC LIMIT ?, ?";

		if ($this -> aID === false) {
			$qBind = array ($currentTitleStart, bw :: $conf['comPerLoad']);
		} else {
			$qBind = array ($this -> aID, $currentTitleStart, bw :: $conf['comPerLoad']);
		}
		$allComs = bw :: $db -> getRows ($qStr, $qBind);
		foreach ($allComs as $comID => $row) {
			$row['comAvatarAvailable'] = $row['comAvatar'] ? 1 : 0;
			$row['comAvatar'] = $row['comAvatar'] ? $row['comAvatar'] : bw :: $conf['siteURL'] . '/conf/default.png';
			$this -> comList[$comID] = $row;
		}


		hook ('getComList', 'Execute', $this);
		$this -> getTotalComs ();
	}

	private function getTotalComs ()
	{
		if ($this -> listBlocked) {
			$blockedStr = (string) $this -> listBlocked == 'only' ? 'comBlock=1' : '1=1';
		} else {
			$blockedStr = 'comBlock=0';
		}
		if ($this -> aID === false) {
			$this -> totalCom = bw :: $db -> countRows ("SELECT articles.aTitle,comments.comID FROM comments INNER JOIN articles ON comments.comArtID = articles.aID WHERE {$blockedStr}");
		} else {
			$this -> totalCom = bw :: $db -> countRows ("SELECT articles.aTitle,comments.comID FROM comments INNER JOIN articles ON comments.comArtID = articles.aID WHERE comArtID=? AND {$blockedStr}", array ($this -> aID));
		}

		hook ('getTotalComs', 'Execute', $this);
	}

	public function setBlockStatus ($statusCode)
	{
		switch ($statusCode) {
			case 0:
				$this -> listBlocked = false;
				break;
			case 1:
				$this -> listBlocked = 'all';
				break;
			case 2:
				$this -> listBlocked = 'only';
				break;
			default:
				$this -> listBlocked = false;
		}
	}

	public function initComAKey ()
	{
		return md5 ($this -> aID . bw :: $conf['siteKey']);
	}

	public function initComSKey ()
	{
		$str = '';
		$strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$max = strlen ($strPol)-1;
		for ($i = 0;$i < 6; $i++) {
			$str .= $strPol[rand (0, $max)];
		}
		$_SESSION['SKey_' . $this -> aID] = $str;
		$_SESSION['OTime_' . $this -> aID] = time ();
		return $str;
	}

	public function addComment ($smt)
	{
		$smt = $this -> checkComData ($smt);
		if (!$smt['aID']) {
			stopError (bw :: $conf['l']['admin:msg:NotExist']);
		} else {
			$this -> aID = $smt['aID'];
		}

		if (!isset ($_SESSION['SKey_' . $this -> aID]) || !isset ($_SESSION['OTime_' . $this -> aID])) {
			stopError (bw :: $conf['l']['admin:msg:AntiSpam']);
		}

		if (time () - $_SESSION['OTime_' . $this -> aID] < floor (bw :: $conf['comFrequency'])) {
			stopError (sprintf (bw :: $conf['l']['admin:msg:AntiSpam2'], floor (bw :: $conf['comFrequency']) - (time () - $_SESSION['OTime_' . $this -> aID])));
		}

		if (md5 ($this -> initComAKey () . $_SESSION['SKey_' . $this -> aID]) <> $smt['comkey']) {
			stopError (bw :: $conf['l']['admin:msg:AntiSpam']);
		}

		$taID = bw :: $db -> getSingleRow ('SELECT * FROM articles WHERE aID=?', array ($this -> aID));
		if (!isset ($taID['aID'])) {
			stopError (bw :: $conf['l']['admin:msg:NotExist']);
		}

		bw :: $db -> dbExec ('INSERT INTO comments (comName, comTime, comIP1, comIP2, comAvatar, comContent, comArtID, comParentID, comSource, comURL, comBlock) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array ($smt['userName'], date ('Y-m-d H:i:s'), $this -> myIP[0], $this -> myIP[1], $smt['userAvatar'], $smt['userContent'], $smt['aID'], 0, $smt['socialkey'], $smt['userURL'], 0));
		bw :: $db -> dbExec ('UPDATE articles SET aComments=aComments+1 WHERE aID=?', array ($smt['aID']));

		$_SESSION['OTime_' . $this -> aID] = time ();

		hook ('addComment', 'Execute', $smt);

		$caID = 'newcomments-' . date ('Ymd');
		$counter = getCache ($caID);
		if (!$counter) {
			$counter = 0;
		}
		if ($counter < notifyLimit) {
			$msgPlus = $counter == notifyLimit -1 ? bw :: $conf['l']['admin:msg:ReachEmailLimit1'] : bw :: $conf['l']['admin:msg:AutoNoReply'];
			$subject = $smt['userName'] . ' ' . bw :: $conf['l']['admin:msg:AddedCommentTo'] . ' [' . $taID['aTitle'] . ']';
			$body = date ('Y-m-d H:i:s') . ' | ' . $smt['userName'] . '<br><a href="' . bw :: $conf['siteURL'] . '/' . bw :: $conf['linkPrefixArticle'] . '/' . $smt['aID'] . '/#comWrap-' . bw :: $db -> dbLastInsertId () . '"><b>' . $taID['aTitle']. '</b></a><br><br>' . $smt['userContent'] . '<br><br><hr><i>' . $msgPlus . '</i>';
			$url = bw :: $conf['siteURL'] . '/' . bw :: $conf['linkPrefixSend'] . '/mailbot/';
			$postdata = array ('subject' => $subject, 'body' => $body, 'token' => md5 (bw :: $conf['siteKey'] . $subject), 'rule' => 'newcomments');
			curlPost ($url, $postdata, 1);
			setCache ($caID, $counter + 1);
		}

		$smt2 = array ('comID' => bw :: $db -> dbLastInsertId (), 'comName' => $smt['userName'], 'comTime' => date ('Y-m-d H:i:s'), 'comAvatar' => $smt['userAvatar'] ? $smt['userAvatar'] : bw :: $conf['siteURL'] . '/conf/default.png', 'comContent' => $smt['userContent'], 'comArtID' => $smt['aID'], 'comURL' => $smt['userURL'], 'comSource' => $smt['socialkey']);
		return $smt2;
	}

	private function checkComData ($smt)
	{
		$acceptedKeys = array ('userName', 'userURL', 'userContent', 'aID', 'comkey', 'socialkey', 'userAvatar');
		$smt = dataFilter ($acceptedKeys, $smt);
		if (empty ($smt['aID']) || $smt['userName'] === '' || empty ($smt['userContent']) || empty ($smt['comkey'])) {
			stopError (bw :: $conf['l']['admin:msg:NoData']);
		}
		$smt['userName'] = htmlspecialchars ($smt['userName'], ENT_QUOTES, 'UTF-8');
		$smt['userURL'] = htmlspecialchars (strtolower($smt['userURL']), ENT_QUOTES, 'UTF-8');
		if (!strpos ($smt['userURL'], '@') && substr ($smt['userURL'], 0, 4) != "http") {
			$smt['userURL'] = 'http://' . $smt['userURL'];
		}
		$smt['userContent'] = htmlspecialchars ($smt['userContent'], ENT_QUOTES, 'UTF-8');
		return $smt;
	}

	public function blockItem ($comID, $aID)
	{
		bw :: $db -> dbExec ('UPDATE comments SET comBlock=1 WHERE comID=?', array ($comID));
		bw :: $db -> dbExec ('UPDATE articles SET aComments=aComments-1 WHERE aID=?', array ($aID));
		clearCache (); //Clear all cache
		hook ('blockItem', 'Execute', $comID, $aID);
	}

	public function blockItemBatch ($comIDList, $comArtIDList = false)
	{
		if (is_array ($comIDList)) {
			$whereIn = str_repeat ("?,", count ($comIDList) -1) . "?";
			bw :: $db -> dbExec ("UPDATE comments SET comBlock=1 WHERE comID IN ($whereIn)", $comIDList);
		}
		if (is_array ($comArtIDList)) {
			$aIDList = array_count_values ($comArtIDList);
			foreach ($aIDList as $aID => $num) {
				bw :: $db -> dbExec ("UPDATE articles SET aComments=aComments-{$num} WHERE aID=?", array ($aID));
			}
		}
		clearCache (); //Clear all cache
		hook ('blockItemBatch', 'Execute', $comIDList, $comArtIDList);
		return true;
	}

	public function blockIP ($comID)
	{
		$taID = bw :: $db -> getSingleRow ('SELECT * FROM comments WHERE comID=?', array ($comID));
		$allAffectedCom = array ();
		if ($taID['comIP1']) {
			$allAffectedCom = bw :: $db -> getColumns ('SELECT * FROM comments WHERE comBlock=0 AND (comIP1=? OR comIP2=?)', array ($taID['comIP1'], $taID['comIP1']));
		}
		if ($taID['comIP2']) {
			$allAffectedCom = array_merge_recursive ($allAffectedCom, bw :: $db -> getColumns ('SELECT * FROM comments WHERE comBlock=0 AND (comIP1=? OR comIP2=?)', array ($taID['comIP2'], $taID['comIP2'])));
		}

		if (count ($allAffectedCom) > 0) {
			$allAffectedArticles = array_count_values ($allAffectedCom['comArtID']);
			$allAffectedComments = '(' . implode (',', array_unique ($allAffectedCom['comID'])) . ')';
			bw :: $db -> dbExec ('UPDATE comments SET comBlock=1 WHERE comID in ' . $allAffectedComments);
			foreach ($allAffectedArticles as $affAID => $affCount) {
				bw :: $db -> dbExec ('UPDATE articles SET aComments=aComments-? WHERE aID=?', array ($affCount, $affAID));
			}
			bw :: $db -> dbExec ('UPDATE articles SET aComments=0 WHERE aComments<0'); //Fix a bug causing comments less than zero
			clearCache (); //Clear all cache
		}
		hook ('blockIP', 'Execute', $comID);
	}

	public function delBlockedBatch ($comIDList)
	{
		array_walk ($comIDList, function ($comID) {
			$comID = is_integer ($comID) ? $comID : -1;
		});
		$allAffectedComments = '(' . implode (',', array_unique ($comIDList)) . ')';
		bw :: $db -> dbExec ('DELETE FROM comments WHERE comID IN ' . $allAffectedComments .' AND comBlock=1');
		hook ('delBlockedBatch', 'Execute', $comIDList);
	}

	public function restoreItemBatch ($comIDList, $comArtIDList = false)
	{
		if (is_array ($comIDList)) {
			$whereIn = str_repeat ("?,", count ($comIDList) -1) . "?";
			bw :: $db -> dbExec ("UPDATE comments SET comBlock=0 WHERE comID IN ($whereIn)", $comIDList);
		}
		if (is_array ($comArtIDList)) {
			$aIDList = array_count_values ($comArtIDList);
			foreach ($aIDList as $aID => $num) {
				bw :: $db -> dbExec ("UPDATE articles SET aComments=aComments+{$num} WHERE aID=?", array ($aID));
			}
		}
		clearCache (); //Clear all cache
		hook ('resoreItemBatch', 'Execute', $comIDList, $comArtIDList);
		return true;
	}
}
