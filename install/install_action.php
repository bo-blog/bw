<?php 
// Copyright: Byke
if (isset ($_REQUEST['step'])) {
	$step = floor ($_REQUEST['step']);
} else {
	die ('Access denied.');
}


if (file_exists ('../conf/info.php')) {
	die ('Already installed.');
}


if ($step == 1) {
	$rslt2 = class_exists ('PDO') ? 1 : 0;
	$rslt3 = extension_loaded ('Zlib') ? 1 : 0;
	$rslt4 = function_exists ('curl_init') ? 1 : 0;

	if ($rslt2 == 0) {
		$rslt5 = 0;
	} else {
		$PDOSuppored = PDO :: getAvailableDrivers ();
		$rslt5 = (in_array ('sqlite', $PDOSuppored) || in_array ('mysql', $PDOSuppored)) ? 1 : 0;
	} 
	$rslt6 = $rslt2 * $rslt3 * $rslt4 * $rslt5;
	die (json_encode (array ('rslt2' => $rslt2, 'rslt3' => $rslt3, 'rslt4' => $rslt4, 'rslt5' => $rslt5, 'rslt6' => $rslt6)));
} 

if ($step == 2) {
	if (isset ($_POST['instd'])) {
		$instd = dataFilter (array('siteAuthor', 'siteKey', 'dbType', 'dbName', 'dbAddr', 'dbUser', 'dbPass'), $_POST['instd']);
		$dbConfContent = $instd['dbType'] == 'SQLite' ? "<?php \r\ndefine ('DBTYPE', 'SQLite');\r\ndefine ('DBNAME', '{$instd['dbName']}');" : "<?php \r\ndefine ('DBTYPE', 'MySQL');\r\ndefine ('DBNAME', '{$instd['dbName']}');\r\ndefine ('DBADDR', '{$instd['dbAddr']}');\r\ndefine ('DBUSERNAME', '{$instd['dbUser']}');\r\ndefine ('DBPASSWORD', '{$instd['dbPass']}');";
		$writeResult = @file_put_contents ('../conf/dbcon.php', $dbConfContent);
		$siteURL = curPageURL ();
		$siteAuthor = htmlspecialchars ($instd['siteAuthor'], ENT_QUOTES, 'UTF-8');
		$siteKey = sha1 ($instd['siteKey']);

		$infoConfContent = "<?php
\$conf=array (
  'siteName' => 'bW Blog',
  'siteURL' => '{$siteURL}',
  'authorName' => '{$siteAuthor}',
  'authorIntro' => 'Yet another bW blog.',
  'siteKey' => '{$siteKey}',
  'timeZone' => 'Asia/Shanghai',
  'siteTheme' => 'default',
  'siteLang' => 'en',
  'perPage' => '3',
  'linkPrefixIndex' => 'index.php',
  'linkPrefixCategory' => 'category.php',
  'linkPrefixArticle' => 'read.php',
  'linkPrefixTag' => 'tag.php',
  'social-sina-weibo' => '',
  'social-weixin' => '',
  'social-douban' => '',
  'social-instagram' => '',
  'social-renren' => '',
  'social-linkedin' => '',
  'externalLinks' => 'http://bw.bo-blog.com=bW Home',
);";
		$writeResult = $writeResult && @file_put_contents ('../conf/info.php', $infoConfContent);

		$servicesConfContent = "<?php
\$conf+=array (
  'duoshuoID' => '',
  'qiniuAKey' => '',
  'qiniuSKey' => '',
  'qiniuBucket' => '',
  'qiniuSync' => '0',
  'qiniuUpload' => '0',
  'qiniuDomain' => '',
);";
		$writeResult = $writeResult && @file_put_contents ('../conf/services.php', $servicesConfContent);

		if (!$writeResult) {
			$rslt7 = $rslt8 = $rslt9 = 0;
			$rslt10 = 'Please check and make sure folder "conf/" is writable.';
		} else {
			$rslt7 = 1; 
			define ('P', '../');
			include (P . 'inc/db.php');
			$db = new bwDatabase;
			$dbInitBind=dbInitBind ();
			foreach (dbInit ($instd['dbType']) as $i=>$dbInit) {
				if ($dbInitBind[$i]) {
					$db->dbExec ($dbInit, $dbInitBind[$i]);
				} else {
					$db->dbExec ($dbInit);
				}
			}
			$rslt8 = $rslt9 = 1;
			$rslt10 = 'Congratulation! Installation succeeded.';
		}
		$errorStatus=$rslt7*$rslt8*$rslt9 ? 0 : 1;
		die (json_encode (array ('error' => $errorStatus, 'rslt7' => $rslt7, 'rslt8' => $rslt8, 'rslt9' => $rslt9, 'rslt10' => $rslt10)));
	} 
} 

function dataFilter ($reservedKeys, $submitData)
{
	$reservedArray = array_fill_keys($reservedKeys, null);
	$returnArray = array_intersect_key ($submitData, $reservedArray);
	return array_merge ($reservedArray, $returnArray);
} 

function dbInit ($dbType)
{
	if ($dbType=='SQLite') {
		$return=array (
			'CREATE TABLE IF NOT EXISTS articles (aID VARCHAR(255) PRIMARY KEY  NOT NULL , aTitle VARCHAR(255) NOT NULL , aCateURLName VARCHAR(255) NOT NULL , aTime DATETIME NOT NULL , aTags TEXT, aReads INTEGER NOT NULL  DEFAULT 0, aContent TEXT, aCustom TEXT)',
			'CREATE TABLE IF NOT EXISTS cache (caID CHAR (32) PRIMARY KEY  NOT NULL , caContent TEXT)',
			'CREATE TABLE IF NOT EXISTS categories (aCateURLName VARCHAR (255) NOT NULL  UNIQUE , aCateDispName TEXT NOT NULL , aCateCount INTEGER NOT NULL  DEFAULT 0, aCateOrder INTEGER)',
			'CREATE TABLE IF NOT EXISTS extensions (extID VARCHAR (255) PRIMARY KEY  NOT NULL , extDesc TEXT , extHooks TEXT NOT NULL , extActivate BOOL NOT NULL , extOrder INTEGER, extStorage TEXT)',
			'CREATE TABLE IF NOT EXISTS statistics (pageURL TEXT PRIMARY KEY  NOT NULL  UNIQUE , sNum INTEGER DEFAULT 0, lastView DATETIME)',
			'CREATE TABLE IF NOT EXISTS tags (tValue VARCHAR (255) PRIMARY KEY  NOT NULL  UNIQUE , tList TEXT, tCount INTEGER NOT NULL  DEFAULT 0)',
		);
	}
	elseif ($dbType=='MySQL') {
		$return=array (
			'CREATE TABLE IF NOT EXISTS articles (aID VARCHAR(255) PRIMARY KEY  NOT NULL , aTitle VARCHAR(255) NOT NULL , aCateURLName VARCHAR(255) NOT NULL , aTime DATETIME NOT NULL , aTags TEXT, aReads INTEGER UNSIGNED  DEFAULT 0, aContent TEXT, aCustom TEXT) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci',
			'CREATE TABLE IF NOT EXISTS cache (caID CHAR (32) PRIMARY KEY  NOT NULL , caContent TEXT) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci',
			'CREATE TABLE IF NOT EXISTS categories (aCateURLName VARCHAR (255) NOT NULL  UNIQUE , aCateDispName TEXT NOT NULL , aCateCount INTEGER NOT NULL  DEFAULT 0, aCateOrder INTEGER) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci',
			'CREATE TABLE IF NOT EXISTS extensions (extID VARCHAR (255) PRIMARY KEY  NOT NULL , extDesc TEXT, extHooks TEXT NOT NULL , extActivate TINYINT NOT NULL , extOrder INTEGER UNSIGNED, extStorage TEXT) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci',
			'CREATE TABLE IF NOT EXISTS statistics (pageURL VARCHAR(255) PRIMARY KEY  NOT NULL , sNum INTEGER UNSIGNED DEFAULT 0, lastView DATETIME) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci',
			'CREATE TABLE IF NOT EXISTS tags (tValue VARCHAR (255) PRIMARY KEY  NOT NULL  UNIQUE , tList TEXT, tCount INTEGER UNSIGNED  DEFAULT 0) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci'
		);
	}
	if ($dbType=='SQLite' || $dbType=='MySQL') {
		$return[]='INSERT INTO articles VALUES (? ,?, ?, ?, ?, ?, ?, ?)';
		$return[]='INSERT INTO categories VALUES (? ,?, ?, ?)';
		$return[]='INSERT INTO extensions VALUES (? ,?, ?, ?, ?, ?)';
	}
	else {
		$return=array ();
	}
	return $return;
}

function dbInitBind ()
{
	$return=array (
		false,
		false,
		false,
		false,
		false,
		false,
		array ('hello-world', 'Hello, World!', 'default', date ('Y-m-d H:i:s'), null, 0, "Welcome to bW.\r\n\r\nThis is the first article that **bW** published on your behalf.\r\n\r\nIf you need help, please do not hesitate to visit our [Official Website](http://bw.bo-blog.com)!", null),
		array ('default', 'Uncategorized', 1, 1),
		array ('hello_world', "name='Hello, world'\r\nintro='Test extension.'\r\nauthor='bW'\r\nurl='http://bw.bo-blog.com'", 'header,footer,textParser,generateOutputDone', 0, 1, null),
	);
	return $return;
}

function curPageURL ()
{
    $pageURL = 'http';
    if (isset ($_SERVER["HTTPS"])) {
		if ($_SERVER["HTTPS"]=='on') {
	        $pageURL .= "s";
		}
    }
    $pageURL .= "://";

    if (isset ($_SERVER["SERVER_PORT"])) {
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}
    } else {
		$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
	}
	$pageURL = str_replace ('/install/install_action.php?step=2', '', $pageURL);
    return $pageURL;
}

function stopError ($err)
{
	die (json_encode (array ('error'=>1, 'rslt7' => 1, 'rslt8' => 0, 'rslt9' => 0, 'rslt10' => 'Database error: '.$err)));
}