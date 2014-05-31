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
<link href="[[::siteURL]]/theme/default/style.css" media="all" rel="stylesheet" type="text/css" />
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
<div id="overallContainer">
<header>
<span class="icon-newicon iconLogo"><h1><a href="[[::siteURL]]/">[[::siteName]]</a></h1></span>
<span id="menuDown"><a href="#" onclick="$('nav').toggle('fast');"><span class="icon-list2 menuDownIcon"></span> </a></span>
<nav>
<ul>
<li id="nav-index"><a href="[[::siteURL]]/">[[=page:Home]]</a></li>
[[::navigation]]
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
[[::summary]][[::article]]
[[::pagination]]
<script src="[[::siteURL]]/inc/script/loader.js"></script>
</div>
<!-- end ajax section -->
[[::ext_mainAreaEnd]]
<div id="adminIcon"><span class="adminSign" data-adminurl="[[::siteURL]]/admin.php" data-adminid="[[::aID]]"><span class="icon-cog"></span> [[=page:Admin]]</span></div>
</div>
</div>

<footer>
[[=page:Connect]]<br/>[[::sociallink]]
<br/>
[[=page:Links]]<br/>
[[::externallink]]
[[::ext_footer]]
<div id="copyright">Powered by bitWinds</div>
</footer>

<div id="UI-loading"><img src="[[::siteURL]]/theme/default/loading.gif"></div>
<div id="UI-lightbox"></div>
<script type="text/javascript">
$('#nav-[[::activeNav]]').addClass('activeNav');
$('.adminSign').click(function (){checkLogin('adminSign');});

var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F78dbd4727b01db1227afa104ae4c5d9a' type='text/javascript'%3E%3C/script%3E"));
</script>
</body>
</html>