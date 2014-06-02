<?php
//Copyright: Byke

if (!defined ('P')) {
	die ('Access Denied.');
}

?>

<!DOCTYPE html>
<html lang="[[=page:code]]">
<head>
<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" /> 
<meta name="robots" content="none" />
<link href="[[::siteURL]]/theme/default/font.css" media="all" rel="stylesheet" type="text/css" />
<link href="[[::siteURL]]/theme/default/style.css" media="all" rel="stylesheet" type="text/css" />
<title>[[=page:Admin]] | [[::siteName]]</title>
<script src="http://lib.sinaapp.com/js/jquery/2.0.3/jquery-2.0.3.min.js"></script>
<script>
!window.jQuery && document.write ('<script src="[[::siteURL]]/inc/script/jquery.min.js"><\/script>');
var lng={
	RememberFail : '[[=js:RememberFail]]',
	AjaxFail : '[[=js:AjaxFail]]'
};
</script>
</head>
<body>
<div id="overallContainer">
<header class="admHeader">
<span class="icon-newicon iconLogo"><h1><a href="[[::siteURL]]/">[[::siteName]]</a></h1></span>
<span id="menuDown"><a href="#" onclick="$('nav').toggle('fast');"><span class="icon-list2 menuDownIcon"></span> </a></span>
<nav>
<ul>
<li class="adminList" id="admPanel"><a href="[[::siteURL]]/admin.php/dashboard/" title="Dashboard"><span class="icon-bars"></span> <span class="adminItems">[[=admin:Dashboard]]</span></a></li>
<li class="adminList" id="admCenter"><a href="[[::siteURL]]/admin.php/center/" title="Settings"><span class="icon-cog"></span> <span class="adminItems">[[=admin:Settings]]</span></a></li>
<li class="adminList" id="admArticles"><a href="[[::siteURL]]/admin.php/articles/" title="Articles"><span class="icon-pencil"></span> <span class="adminItems">[[=admin:Articles]]</span></a></li>
<li class="adminList" id="admServices"><a href="[[::siteURL]]/admin.php/services/" title="Services"><span class="icon-cloud"></span> <span class="adminItems">[[=admin:Services]]</span></a></li>
<li class="adminList"><a href="[[::siteURL]]/admin.php/login/logout/" title="Logout"><span class="icon-logout"></span> <span class="adminItems">[[=admin:Logout]]</span></a></li>
</ul>
</nav>
</header>

<div id="mainArea" class="admMainArea">
[[::admindashboard]][[::admincenter]][[::adminarticles]][[::adminwriter]][[::adminservices]]
</div>
<div id="copyright" class="admFooter"><a href="http://bw.bo-blog.com/" target="_blank">Powered by bW</a></div>
</div>
<div id="UI-loading"><img src="[[::siteURL]]/theme/default/loading.gif"></div>
</body>
</html>