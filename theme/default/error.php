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

?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" /> 
<meta name="robots" content="none" />
<link href="[[::siteURL]]/theme/default/font.css" media="all" rel="stylesheet" type="text/css" />
<link href="[[::siteURL]]/theme/default/style.css" media="all" rel="stylesheet" type="text/css" />
<title>[[::siteName]]</title>
</head>
<body class="errorMainArea">
<div id="overallContainer">

<div id="mainArea">
<div id="errorArea">
<p><span class="icon-cross3 errorSign"></span></p>
<p>[[=page:Error]] [[::errorMessage]]</p>
<p><br/></p>
<p><a href="javascript: history.go(-1)">[[=page:ErrorBack]]</a> | <a href="[[::siteURL]]/index.php">[[=page:ErrorToHome]]</a></p>
</div>
</div>

</div>
</body>
</html>