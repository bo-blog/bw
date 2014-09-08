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
<input type="file" style="display: none; height: 1px;" name="uploadFile[]" id="uploadPicFile" multiple="true" onchange="doPicUp();"/>
</form>
eot;

$parts['adminqiniuupload']=<<<eot
<form id="picForm" method="post" action="http://up.qiniu.com/" target="execPicTarget" enctype="multipart/form-data">
<input type="file" style="display: none; height: 1px;" name="file" id="uploadPicFile" onchange="doPicUp();"/>
<input name="token" type="hidden" value="[[::qiniuFileToken]]">
<input name="key" type="hidden" value="[[::qiniuKey]]">
</form>
eot;


$parts['admincategorylist']=<<<eot
<li class="adminSingleArticle adminSCL" data-cid="[[::aCateURLName]]" id="adminSCL-[[::aCateURLName]]"><a href="##" title="[[=admin:msg:Up]]" class="adminSCLUp" data-cid="[[::aCateURLName]]"><span class="icon-arrow-up3"></span></a> <a href="##" title="[[=admin:msg:Down]]" class="adminSCLDown" data-cid="[[::aCateURLName]]"><span class="icon-arrow-down4"></span></a> <span id="adminSCLine-[[::aCateURLName]]" class="adminSCLine" data-cid="[[::aCateURLName]]">[[::aCateDispName]]</span><span class="adminSCLModify" id="adminSCM-[[::aCateURLName]]"><input type="text" class="inputLine inputLarge" value="[[::aCateDispName]]" id="adminSCInput-[[::aCateURLName]]"> <br/><a href="##" onclick='$("#adminSCM-[[::aCateURLName]]").fadeToggle();$("#adminSCL-[[::aCateURLName]]").remove();'><span class="icon-cross3"></span> [[=admin:msg:Remove]]</a> &nbsp; <a href="##" onclick='$("#adminSCM-[[::aCateURLName]]").fadeToggle();$("#adminSCLine-[[::aCateURLName]]").html($("#adminSCInput-[[::aCateURLName]]").val());$("#adminSCLine-[[::aCateURLName]]").toggle();'><span class="icon-arrow-up4"></span> [[=admin:msg:Close]]</a></span></li>
eot;

$parts['adminuploadinsert']=<<<eot
<html><head></head><body><span id="upVals">[[::loop, adminuploaded]]![]([[::fileURL]]) [[::/loop]]</span>
<script type="text/javascript">
parent.insertUpURLs(document.getElementById('upVals').innerHTML);</script>
</body></html>
eot;

$parts['adminwidgetlist']=<<<eot
eot;


$parts['wgheader']=<<<eot
<li><a href="[[::url]]" target="[[::target]]" title="[[::title]]">[[::text]]</a></li>
eot;

$parts['wgfooter']=$parts['wghtmlhead']=<<<eot
[[::value]]
eot;

$parts['commentarea']=<<<eot
<input type="text" name="smt[userName]" class="inputLine inputMiddle" placeholder="Nick Name" /><br/>
<input type="text" name="smt[userEmail]" class="inputLine inputMiddle" placeholder="Email (will not be displayed)" /><br/>
<input type="text" name="smt[userURL]" class="inputLine inputMiddle" placeholder="Homepage URL (optional)" /><br/>
<textarea type="text" class="inputLine inputMiddle textareaLine textareaMiddle" name="smt[userContent]" placeholder="Your comment" /></textarea>
eot;

?>