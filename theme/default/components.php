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
<div class="commentArea">
<div class="comTitle">[[=page:AddComment]]</div>
<div class="comForm">
<form id="smtForm">
<input type="text" name="smt[userName]" class="comInput comInputSmall" id="comUserName" placeholder="[[=page:NickName]]" />
<input type="text" name="smt[userURL]" class="comInput comInputSmall" id="comUserURL" placeholder="[[=page:URL]]" />
<textarea type="text" class="comInput comInputLarge" name="smt[userContent]" id="comUserContent" placeholder="[[=page:CommentContent]]" /></textarea>
<input type="hidden" name="smt[aID]" value="[[::aID]]" />
<input type="hidden" name="smt[comkey]" value="[[::comkey]]" />
<div id="comSubmit"><a href="##" id="comSubmitBtn"><span class="icon-forward4"></span> [[=page:SendComment]]</a> &nbsp; <a href="##" id="comClearBtn"><span class="icon-ccw"></span> [[=page:ClearComment]]</a>
<span id="comPromptError"></span>
<span id="comPromptSuccess"></span>
</div>
</form>
</div>

<span id="comAdm" data-adminurl="[[::siteURL]]/admin.php"></span>
<div id="comInsertNew"></div>
[[::loop, comments]]
<div id="comWrap-[[::comID]]" class="comWrap">
<div class="comAvatar"><img src="[[::comAvatar]]" alt="User" /></div>
<div class="comName"><h6 data-userurl="[[::comURL]]" data-usersource="[[::comSource]]">[[::comName]]</h6><h5>[[::comTime, dateFormat, Y/m/d H:i]]</h5></div>
<div class="comContent"><pre>[[::comContent]]<div class="comBlockBar"><a href="##" onclick="blockComment ('comAdm', 'blockitem', '[[::comID]]', '[[::comArtID]]');"><span class="icon-cross"></span> [[=page:BlockItem]]</a> &nbsp; &nbsp; <a href="##" onclick="blockComment ('comAdm', 'blockip', '[[::comID]]', '[[::comArtID]]');"><span class="icon-minus3"></span> [[=page:BlockIP]]</a></div></pre></div>
</div>
[[::/loop]]

</div>
<script>
makeComUserLink ();

function errorPrompter (inputId)
{
	$('#'+inputId).addClass("comInputWarn");
	$('#'+inputId).click(function() {
		$('#'+inputId).removeClass("comInputWarn");
	});
}

$('#comClearBtn').click (function () {
	$("#comUserName").val('');
	$("#comUserContent").val('');
	$("#comUserURL").val('');
});

$('#comSubmitBtn').click (function () {
	var stopSubmit=false;
	if (!$("#comUserName").val() || $("#comUserName").val()=="[[::authorName]]") {
		errorPrompter('comUserName');
	}
	if (!$("#comUserContent").val() || $("#comUserContent").val().length<4) {
		errorPrompter('comUserContent');
	}
	$('.comInput').each(function() {
		if ($(this).hasClass('comInputWarn'))
		{
			stopSubmit=true;
		}
	});


	if (stopSubmit) {
		$("#comPromptError").text ('[[=page:ComError1]]');
		$("#comPromptError").fadeIn(400).delay(1500).fadeOut(600);
	} else {
		$("#UI-loading").fadeIn(500);
		var smtURL="[[::siteURL]]/send.php/comments/submit";
		//window.location=smtURL+"?ajax=1&"+$('#smtForm').serialize(); return false;

		$.post(smtURL+"?ajax=1", $('#smtForm').serialize(), function(data) {
			$("#UI-loading").fadeOut(200);
			if (data.error==1) {
				$("#comPromptError").text (data.returnMsg);
				$("#comPromptError").fadeIn(400).delay(1500).fadeOut(600);
			}
			else {
				$("#comPromptSuccess").text ("[[=page:ComSuccess]]");
				$("#comPromptSuccess").fadeIn(400).delay(1500).fadeOut(600);
				$("#comInsertNew").after(data.returnMsg);
				$(".comWrap:first").hide().delay(100).fadeIn(800);
				window.location.hash="comInsertNew";
				$('#comClearBtn').click();
			}
		}, "json");
	}
});
</script>
eot;

$parts['ajaxcomment']=<<<eot
<div id="comWrap-[[::comID]]" class="comWrap">
<div class="comAvatar"><img src="[[::comAvatar]]" alt="User" /></div>
<div class="comName"><h6 data-userurl="[[::comURL]]" data-usersource="[[::comSource]]">[[::comName]]</h6><h5>[[::comTime, dateFormat, Y/m/d H:i]]</h5></div>
<div class="comContent"><pre>[[::comContent]]<div class="comBlockBar"><a href="##" onclick="blockComment ('comAdm', 'blockitem', '[[::comID]]', '[[::comArtID]]');"><span class="icon-cross"></span> [[=page:BlockItem]]</a> &nbsp; &nbsp; <a href="##" onclick="blockComment ('comAdm', 'blockip', '[[::comID]]', '[[::comArtID]]');"><span class="icon-minus3"></span> [[=page:BlockIP]]</a></div></pre></div>
</div>
eot;


$parts['duoshuoarea']=<<<eot
<div class="commentArea">
<!-- Duoshuo start -->
<div class="ds-thread" data-thread-key="[[::aID]]" data-title="[[::aTitle]]" data-url="[[::siteURL]]/[[::linkPrefixArticle]]/[[::aID]]/" data-form-position="top" data-order="desc"></div>
<!-- Duoshuo end -->
<!-- Duoshuo JS start -->
<script type="text/javascript">
var duoshuoQuery = {short_name:"[[::duoshuoID]]"};
	(function() {
		var ds = document.createElement('script');
		ds.type = 'text/javascript';ds.async = true;
		ds.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') + '//static.duoshuo.com/embed.js';
		ds.charset = 'UTF-8';
		(document.getElementsByTagName('head')[0] 
		 || document.getElementsByTagName('body')[0]).appendChild(ds);
	})();
$("<link>").attr({rel:"stylesheet", type:"text/css", href: "[[::siteURL]]/theme/default/duoshuo.css"}).appendTo("head");

</script>
<!-- Duoshuo JS end -->
</div>
eot;

$parts['nocommentarea']='';


?>