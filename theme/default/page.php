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
<link href="[[::siteURL]]/theme/default/style.css?ver=201808202303" media="all" rel="stylesheet" type="text/css" />
<link href="[[::siteURL]]/theme/default/font.css" media="all" rel="stylesheet" type="text/css" />
<link  href="[[::siteURL]]/rss.php" rel="alternate" type="application/rss+xml" title="RSS 2.0" />
<title>[[::pageTitle]][[::siteName]] - [[::authorIntro]]</title>
<script src="//lib.sinaapp.com/js/jquery/2.0.3/jquery-2.0.3.min.js"></script>
<script>
!window.jQuery && document.write ('<script src="[[::siteURL]]/inc/script/jquery.min.js"><\/script>');
var lng={
	RememberFail : '[[=js:RememberFail]]',
	AjaxFail : '[[=js:AjaxFail]]',
	BlockIP : '[[=js:BlockIP]]'
};
</script>
<script src="[[::siteURL]]/inc/script/main.js?ver=201808192158"></script>
[[::ext_htmlhead]]
[[::widget_wghtmlhead]]
</head>
<body>
<div id="overallContainer">
<header>
<span class="iconLogo"><span class="adminSign" data-adminurl="[[::siteURL]]/admin.php" data-adminid="[[::aID]]"><a href="##"><img src="[[::siteURL]]/conf/profile.png" id="profileImg" valign="middle" title="[[::authorName]] - [[::authorIntro]]"/></span> <a href="[[::siteURL]]/">[[::siteName]]</a></span>
<span id="menuDown"><a href="#" onclick="$('nav').fadeToggle('slow');"><span class="icon-list4 menuDownIcon"></span> </a></span>
<div id="searchIcon"><a href="#" onclick="$('#searchBar').toggleClass('searchBarFocus'); $('nav').fadeToggle();">&#9906;</a></div>
<div id="searchBar"><input type="text" placeholder="[[=page:EnterToSearch]]" id="searchVal" class="inputLine inputSmall" data-searchurl="[[::searchEngine]]" data-searchquery="+site%3a[[::siteURL, URLEncode]]"></div>
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

<div id="mainArea">
[[::ext_mainAreaStart]]
<!-- ajax loaded section -->
<div id="ajax-article-list">
[[::loop, articlesummary]][[::load, summary]][[::/loop]]
[[::load, article]][[::load, singlepage]][[::listContent]]
[[::pagination]]
<script src="[[::siteURL]]/inc/script/loader.js?ver=201704222316"></script>
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
<div id="copyright"><a href="http://bw.bo-blog.com/" target="_blank"><span class="icon-newicon"></span> Powered by Bo-blog Wind</a> | <span class="adminSign" data-adminurl="[[::siteURL]]/admin.php" data-adminid="[[::aID]]"><a href="##">[[=page:Admin]]</a></span></div>
[[::widget_wgfooter]]
</footer>

<div id="UI-loading"><img src="[[::siteURL]]/theme/default/loading.gif"></div>
<div id="UI-lightbox"></div>
<script type="text/javascript">
$('#nav-[[::activeNav]]').addClass('activeNav');
$('.adminSign').click(function (){checkLogin('adminSign');});
if ($("#ajax-article-list article").length==0) {$("#ajax-article-list").html("[[=page:NoArticleAtAll]]");}
if ("[[::searchEngine]]" == "") {$("#searchIcon").remove();}
</script>
[[::ext_beforeEnd]]
</body>
</html>
