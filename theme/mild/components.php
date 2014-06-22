<?php
//Copyright: Byke

if (!defined ('P')) {
	die ('Access Denied.');
}


$parts['pagination']=<<<eot
<div id="pageBar" class="padLeft textXS colorBlue">
[[::prevpage]][[::nextpage]]
</div>
eot;

$parts['nextpage']=<<<eot
<span id="goNextPage" class="toRight"><a href="[[::nextPageLink]]" class="pageLink">[[=page:NextPage]] <span class="icon-arrow-right5"></span></a></span>
eot;

$parts['prevpage']=<<<eot
<span id="goPrevPage"><a href="[[::prevPageLink]]" class="pageLink"><span class="icon-arrow-left5"></span> [[=page:PrevPage]]</a></span>
eot;

$parts['ajax-article-list']=<<<eot
[[::loop, articlesummary]][[::load, summary]][[::/loop]]
[[::pagination]]
<script src="[[::siteURL]]/inc/script/loader.js"></script>
eot;

$parts['adminlogin']=<<<eot
<div id="adminWrapper">
<p><img src="[[::siteURL]]/conf/profile.png" class="shadowWhite" /></p>
<p><br/></p><p>
<input type="password" id="s_token" placeholder="[[=admin:msg:PromptPsw]]" class="textS colorBlue bgWhite input shadowGrey" /> <button onclick="doLogin('s_token', '[[::siteURL]]/admin.php');" class="btn shadowGrey"><span class="icon-login shadowGrey"></span></button>
</p>
<p id="adminRemember" class="asNone">
<input type="checkbox"  id="s_token-rem" /> [[=page:MobileRem]]<br/>[[=page:MobileRem2]]
</p>
<p><br/>
<span id="s_token-failure" class="adminErrorMsg asNone colorRed"><span class="icon-minus2"></span> [[=admin:msg:WrongPsw]]</span>
</p>
<div id="UI-close"><a href="##" title="[[=admin:msg:Close]]" onclick="lightboxLoaderDestroy();"><span class="icon-cross3"></span></a></div>
</div>
eot;


?>