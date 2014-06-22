<?php
//Copyright: Byke

if (!defined ('P')) {
	die ('Access Denied.');
}


$parts['pagination']=<<<eot
<div id="pageBar">
[[::prevpage]][[::nextpage]]
</div>
eot;

$parts['nextpage']=<<<eot
<span id="goNextPage"><a href="[[::nextPageLink]]" class="pageLink" title="[[=page:NextPage]]"><span class="icon-arrow-right5"></span></a></span>
eot;

$parts['prevpage']=<<<eot
<span id="goPrevPage"><a href="[[::prevPageLink]]" class="pageLink" title="[[=page:PrevPage]]"><span class="icon-arrow-left5"></span></a></span>
eot;

$parts['firstpage']=<<<eot
<span id="goFirstPage"><a href="[[::firstPageLink]]" class="pageLink"></a></span>
eot;

$parts['finalpage']=<<<eot
<span id="goFinalPage"><a href="[[::finalPageLink]]" class="pageLink"></a></span>
eot;

$parts['gotopage']=<<<eot
<span id="gotoPage"><a href="[[::gotoPageLink]]" class="pageLink"></a></span>
eot;

$parts['ajax-article-list']=<<<eot
[[::loop, articlesummary]][[::load, summary]][[::/loop]]
[[::pagination]]
<script src="[[::siteURL]]/inc/script/loader.js"></script>
eot;

$parts['adminlogin']=<<<eot
<div class="adminWrapper">
<img src="[[::siteURL]]/conf/profile.png" id="adminLoginImg"/>
<p>[[=admin:msg:Login]]</p>
<p>
<input type="password" class="inputLine" id="s_token" placeholder="[[=admin:msg:PromptPsw]]" /> <button class="buttonLine" onclick="doLogin('s_token', '[[::siteURL]]/admin.php');"><span class="icon-login"></span></button>
</p>
<p id="adminRemember">
<input type="checkbox"  id="s_token-rem"/> [[=page:MobileRem]]<br/>[[=page:MobileRem2]]
</p>
<p>
<span id="s_token-failure" class="adminErrorMsg"><span class="icon-minus2"></span> [[=admin:msg:WrongPsw]]</span>
</p>
<div id="UI-close"><a href="##" title="[[=admin:msg:Close]]" onclick="lightboxLoaderDestroy();"><span class="icon-cross3"></span></a></div>
</div>
eot;

$parts['admincommonupload']=<<<eot
<form id="picForm" method="post" action="[[::siteURL]]/admin.php/articles/uploader/?CSRFCode=[[::upCSRFCode]]" target="execPicTarget" enctype="multipart/form-data">
<input type="file" style="display: none; height: 1px;" name="uploadFile[]" id="uploadPicFile" multiple="true" />
</form>
eot;

$parts['adminqiniuupload']=<<<eot
<form id="picForm" method="post" action="http://up.qiniu.com/" target="execPicTarget" enctype="multipart/form-data">
<input type="file" style="display: none; height: 1px;" name="file" id="uploadPicFile" onchange="doPicUp();"/>
<input name="token" type="hidden" value="[[::qiniuFileToken]]">
<input name="key" type="hidden" value="[[::qiniuKey]]">
</form>
eot;

$parts['adminuploadinsert']=<<<eot
<html><head></head><body><span id="upVals">[[::loop, adminuploaded]]![]([[::fileURL]]) [[::/loop]]</span>
<script type="text/javascript">
parent.insertUpURLs(document.getElementById('upVals').innerHTML);</script>
</body></html>
eot;

?>