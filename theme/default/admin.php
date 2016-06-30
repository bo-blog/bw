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
<html lang="[[=page:code]]">
<head>
<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" /> 
<meta name="robots" content="none" />
<link href="[[::siteURL]]/theme/default/font.css" media="all" rel="stylesheet" type="text/css" />
<link href="[[::siteURL]]/theme/default/style.css?ver=05292016" media="all" rel="stylesheet" type="text/css" />
<link href="[[::siteURL]]/theme/default/admin.css?ver=05292016" media="all" rel="stylesheet" type="text/css" />
<title>[[=page:Admin]] | [[::siteName]]</title>
<script src="//lib.sinaapp.com/js/jquery/2.0.3/jquery-2.0.3.min.js"></script>
<script>
!window.jQuery && document.write ('<script src="[[::siteURL]]/inc/script/jquery.min.js"><\/script>');
if(window !=parent) {
	parent.location=window.location.href;
	return false;
}
var lng={
	RememberFail : '[[=js:RememberFail]]',
	AjaxFail : '[[=js:AjaxFail]]'
};
</script>
[[::ext_adminhtmlhead]]
</head>
<body>
<div id="overallContainer">
<header class="admHeader">
<span class="icon-newicon iconLogo"><h1><a href="[[::siteURL]]/" title="[[=admin:item:BackHome]]">[[::siteName]]</a></h1></span>
<span id="menuDown"><a href="#" onclick="$('nav').toggle('fast');"><span class="icon-list4 menuDownIcon"></span> </a></span>
<nav>
<ul>
<li class="adminList" id="admPanel"><a href="[[::siteURL]]/[[::linkPrefixAdmin]]/dashboard/[[::linkConj]]CSRFCode=[[::navCSRFCode]]" title="Dashboard"><span class="icon-gauge"></span> <span class="adminItems">[[=admin:Dashboard]]</span></a></li>
<!-- <li class="adminList" id="admMarket"><a href="[[::siteURL]]/[[::linkPrefixAdmin]]/market/[[::linkConj]]CSRFCode=[[::navCSRFCode]]" title="Market"><span class="icon-cd"></span> <span class="adminItems">[[=admin:Markets]]</span></a></li> -->
<li class="adminList" id="admCenter"><a href="[[::siteURL]]/[[::linkPrefixAdmin]]/center/[[::linkConj]]CSRFCode=[[::navCSRFCode]]" title="Settings"><span class="icon-cog3"></span> <span class="adminItems">[[=admin:Settings]]</span></a></li>
<li class="adminList" id="admArticles"><a href="[[::siteURL]]/[[::linkPrefixAdmin]]/articles/[[::linkConj]]CSRFCode=[[::navCSRFCode]]" title="Articles"><span class="icon-pencil"></span> <span class="adminItems">[[=admin:Articles]]</span></a></li>
<li class="adminList" id="admServices"><a href="[[::siteURL]]/[[::linkPrefixAdmin]]/services/[[::linkConj]]CSRFCode=[[::navCSRFCode]]" title="Services"><span class="icon-cloud2"></span> <span class="adminItems">[[=admin:Services]]</span></a></li>
<li class="adminList" id="admExtensions"><a href="[[::siteURL]]/[[::linkPrefixAdmin]]/extensions/[[::linkConj]]CSRFCode=[[::navCSRFCode]]" title="Extensions"><span class="icon-docs"></span> <span class="adminItems">[[=admin:Extensions]]</span></a></li>
<li class="adminList admLastList"></li>
</ul>
</nav>
[[::ext_adminheader]]
</header>

<div id="mainArea" class="admMainArea inAdmin">
[[::ext_adminMainAreaStart]]
[[::load, admindashboard]][[::load, admincenter]][[::load, adminarticles]][[::load, adminwriter]][[::load, adminservices]][[::load, adminextensions]][[::load, adminmarket]][[::adminplainpage]]
</div>
<div id="copyright" class="admFooter"> <a href="[[::siteURL]]/[[::linkPrefixAdmin]]/login/logout/[[::linkConj]]CSRFCode=[[::logoutCSRFCode]]" title="Logout"><span class="icon-logout"></span> [[=admin:Logout]]</a> &nbsp; <a href="#"><span class="icon-arrow-up4"></span>TOP</a><br/><br/>Powered by bW <a href="http://bw.bo-blog.com/" target="_blank"><span class="icon-earth2"></span></a> <a href="https://github.com/bo-blog/bw" target="_blank"><span class="icon-github6"></span></a></div>
[[::ext_adminMainAreaEnd]]
</div>
<div id="UI-loading"><img src="[[::siteURL]]/theme/default/loading.gif"></div>
<div id="UI-lightbox"></div>
[[::ext_adminfooter]]
<script>
$("#mainArea").fadeIn (500);
</script>
</body>
</html>