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
<link rel="canonical" href="[[::canonicalURL]]" />
<link href="[[::siteURL]]/theme/default/font.css" media="all" rel="stylesheet" type="text/css" />
<link href="[[::siteURL]]/theme/mild/mild.css" media="all" rel="stylesheet" type="text/css" />
<link  href="[[::siteURL]]/rss.php" rel="alternate" type="application/rss+xml" title="RSS 2.0" />
<title>[[::pageTitle]][[::siteName]]</title>
<script src="http://lib.sinaapp.com/js/jquery/2.0.3/jquery-2.0.3.min.js"></script>
<script>
!window.jQuery && document.write ('<script src="[[::siteURL]]/inc/script/jquery.min.js"><\/script>');
var lng={
	RememberFail : '[[=js:RememberFail]]',
	AjaxFail : '[[=js:AjaxFail]]'
};
</script>
<script src="[[::siteURL]]/inc/script/main.js"></script>
[[::ext_htmlhead]]
</head>
<body>
<div id="overall">
<header class="bgWhite shadowGrey padLeft padRight inMiddle">
<h1 class="heavy textXL colorBlue toLeft"><a href="[[::siteURL]]/">[[::siteName]]</a></h1>
<h4 class="inMiddle textXS toRight colorGrey"><span class="heavy colorBlack">[[::authorName]]</span>: [[::authorIntro]]</h4>
[[::ext_header]]
</header>

<div id="main">

<div id="mainField">
[[::ext_mainAreaStart]]
<!-- ajax loaded section -->
<div id="ajax-article-list">
[[::loop, articlesummary]][[::load, summary]][[::/loop]]
[[::load, article]]
[[::pagination]]
<script src="[[::siteURL]]/inc/script/loader.js"></script>
</div>
<!-- end ajax section -->
[[::ext_mainAreaEnd]]
</div>

<div id="sidebar" class="padLeft padRight">

<div class="sidebarItem textXS">
<h3 class="colorRed textS">[[=page:Navigation]]</h3>
<div class="bgGrey sidebarInner shadowWhite">
<nav class="colorBlack">
<ul>
<li id="nav-index"><a href="[[::siteURL]]/">[[=page:Home]]</a></li>
[[::loop, navigation]]
<li id="nav-[[::aCateURLName]]"><a href="[[::siteURL]]/[[::linkPrefixCategory]]/[[::aCateURLName]]/">[[::aCateDispName]]</a></li>
[[::/loop]]
<li><a href="##"><span class="adminSign" data-adminurl="[[::siteURL]]/admin.php" data-adminid="[[::aID]]">[[=page:Admin]]</span></a></li>
</ul>
</nav>
</div>
</div>

<div class="sidebarItem textXS">
<h3 class="colorGreen textS">[[=page:Connect]]</h3>
<div class="bgGrey sidebarInner shadowWhite">
<nav class="colorBlack">
<ul>
[[::loop, sociallink]]
<li><a href="[[::socialLinkURL]]" target="_blank"><span class="icon-[[::socialLinkID]] fol"></span> [[::socialLinkName]]</a></li>
[[::/loop]]
</ul>
</nav>
</div>
</div>

<div class="sidebarItem textXS">
<h3 class="colorPurple textS">[[=page:Tags]]</h3>
<div class="bgGrey sidebarInner shadowWhite colorBlack smallPad">
[[::loop, tagClound]]
<span class="oneTag"><a href="[[::siteURL]]/[[::linkPrefixTag]]/[[::tValue, URLEncode]]">[[::tValue]]</a></span>
[[::/loop]]
</div>
</div>


<div class="sidebarItem textXS">
<h3 class="colorOrange textS">[[=page:Links]]</h3>
<div class="bgGrey sidebarInner shadowWhite">
<nav class="colorBlack">
<ul>
[[::loop, externallink]]
<li><a href="[[::linkURL]]" target="_blank">[[::linkName]]</a></li>
[[::/loop]]
</ul>
</nav>
</div>
</div>

</div>
</div>

<footer class="padLeft textXS">
[[::ext_footer]]
<div id="copyright" class="colorGreen"><a href="http://bw.bo-blog.com/" target="_blank">Powered by bW</a></div>
</footer>

<div id="UI-loading"><img src="[[::siteURL]]/theme/default/loading.gif"></div>
<div id="UI-lightbox"></div>
</body>
</html>