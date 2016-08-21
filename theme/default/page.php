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
<head profile="http://www.w3.org/2005/10/profile">
<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" /> 
<meta name="description" content="[[::authorIntro]]" />
<meta name="keywords" content="[[::metaData]]" />
<link rel="icon" type="image/png" href="[[::siteURL]]/theme/default/logo.png" />
<link rel="canonical" href="[[::canonicalURL]]" />
<link href="[[::siteURL]]/theme/default/font.css" media="all" rel="stylesheet" type="text/css" />
<link href="[[::siteURL]]/theme/default/style.css?ver=02282016" media="all" rel="stylesheet" type="text/css" />
<link  href="[[::siteURL]]/rss.php" rel="alternate" type="application/rss+xml" title="RSS 2.0" />
<title>[[::pageTitle]][[::siteName]]</title>
<script src="//lib.sinaapp.com/js/jquery/2.0.3/jquery-2.0.3.min.js"></script>
<script>
!window.jQuery && document.write ('<script src="[[::siteURL]]/inc/script/jquery.min.js"><\/script>');
var lng={
	RememberFail : '[[=js:RememberFail]]',
	AjaxFail : '[[=js:AjaxFail]]',
	BlockIP : '[[=js:BlockIP]]'
};
</script>
<script src="[[::siteURL]]/inc/script/main.js"></script>
[[::ext_htmlhead]]
[[::widget_wghtmlhead]]
</head>
<body>
<div id="overallContainer">
<header>
<span class="icon-newicon iconLogo"><h1><a href="[[::siteURL]]/">[[::siteName]]</a></h1></span>
<span id="menuDown"><a href="#" onclick="scrollToTop(); $('nav').toggle('fast');"><span class="icon-list menuDownIcon"></span> </a></span>
<nav>
<ul>
<li id="nav-index"><a href="[[::siteURL]]/">[[=page:Home]]</a></li>
[[::loop, navigation]]
<li id="nav-[[::aCateURLName]]"><a href="[[::siteURL]]/[[::linkPrefixCategory]]/[[::aCateURLName]]/">[[::aCateDispName]]</a></li>
[[::/loop]]
[[::widget_wgheader]]
<li class="menuLastList"></li>
</ul>
</nav>
[[::ext_header]]
</header>
<div id="profileArea">
<img src="[[::siteURL]]/conf/profile.png" id="profileImg"/>
<div id="profile">
<p id="profileAuthor">[[::authorName]]</p>
<p id="profileIntro">[[::authorIntro]]</p>
[[::ext_intro]]
</div>
</div>

<div id="mainArea">
[[::ext_mainAreaStart]]
<!-- ajax loaded section -->
<div id="ajax-article-list">
[[::loop, articlesummary]][[::load, summary]][[::/loop]]
[[::load, article]][[::load, singlepage]][[::listContent]]
[[::pagination]]
<script src="[[::siteURL]]/inc/script/loader.js"></script>
</div>
<!-- end ajax section -->
[[::ext_mainAreaEnd]]
</div>
</div>

<footer>
[[=page:Connect]]<br/>
<a href="[[::siteURL]]/rss.php" target="_blank"><span class="icon-rss fol"></span></a>
[[::loop, sociallink]]<a href="[[::socialLinkURL]]" target="_blank"><span class="icon-[[::socialLinkID]] fol"></span></a>[[::/loop]]
<br/>
[[=page:Links]]<br/>
[[::loop, externallink]]<span class="lnk"><a href="[[::linkURL]]" target="_blank">[[::linkName]]</a></span>[[::/loop]]
[[::ext_footer]]
<div id="copyright"><a href="http://bw.bo-blog.com/" target="_blank">Powered by Bo-blog Wind</a> | <span class="adminSign" data-adminurl="[[::siteURL]]/admin.php" data-adminid="[[::aID]]"><a href="##">[[=page:Admin]]</a></span></div>
[[::widget_wgfooter]]
</footer>

<div id="UI-loading"><img src="[[::siteURL]]/theme/default/loading.gif"></div>
<div id="UI-lightbox"></div>
<script type="text/javascript">
$('#nav-[[::activeNav]]').addClass('activeNav');
$('.adminSign').click(function (){checkLogin('adminSign');});
if ($("#ajax-article-list article").length==0) {$("#ajax-article-list").html("[[=page:NoArticleAtAll]]");}
</script>
[[::ext_beforeEnd]]
</body>
</html>