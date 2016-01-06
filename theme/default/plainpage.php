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
<script src="http://lib.sinaapp.com/js/jquery/2.0.3/jquery-2.0.3.min.js"></script>
<script>
!window.jQuery && document.write ('<script src="[[::siteURL]]/inc/script/jquery.min.js"><\/script>');
var lng={
	RememberFail : '[[=js:RememberFail]]',
	RememberSuccess : '[[=js:RememberSuccess]]',
	AjaxFail : '[[=js:AjaxFail]]',
};
</script>
</head>
<body class="errorMainArea">
<div id="overallContainer">

<div id="mainArea">
<div id="plainArea">
[[::plainContent]]
</div>
<div>
<a href="[[::siteURL]]/">[[=page:Home]]</a>
</div>
</div>
</div>
</body>
</html>