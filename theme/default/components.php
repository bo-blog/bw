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
<input type="password" class="inputLine" id="s_token" placeholder="[[=admin:msg:PromptPsw]]" /> <button class="buttonLine" onclick="doLogin('s_token', '[[::siteURL]]/admin.php');"><span class="icon-login"></span></button> <button class="buttonLine" onclick="window.location='[[::siteURL]]/send.php/gona/';"><span class="icon-mobile3" title="[[=page:MobileLogin]]"></span></button>
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

<span id="wb_connect_btn"></span>
<span id="comLoggedIn">[[=page:LoggedIn]] <span id="comLoggedInAs"></span> <span id="comLogout">[[[=page:LogOut]]]</span><br/></span>
<input type="text" name="smt[userName]" class="comInput comInputSmall" id="comUserName" placeholder="[[=page:NickName]]" /> <span><span class="icon-sina-weibo" id="comUseWeibo" title="Login with Sina Weibo"></span></span><br class="comLineChange"/>
<input type="text" name="smt[userURL]" class="comInput comInputSmall" id="comUserURL" placeholder="[[=page:URL]]" /><br class="comLineChange"/>
<textarea type="text" class="comInput comInputLarge" name="smt[userContent]" id="comUserContent" placeholder="[[=page:CommentContent]]" /></textarea>
<input type="hidden" name="smt[aID]" value="[[::aID]]" />
<input type="hidden" name="smt[comkey]" value="[[::comkey]]" />
<input type="hidden" id="comSocialStatus" name="smt[socialStatus]" value="" />
<input type="hidden" id="comSocialKey" name="smt[socialkey]" value="" />
<div id="comSubmit"><a href="##" id="comSubmitBtn"><span class="icon-forward4"></span> [[=page:SendComment]]</a> &nbsp; <a href="##" id="comClearBtn"><span class="icon-ccw"></span> [[=page:ClearComment]]</a>
<span id="comPromptError"></span>
<span id="comPromptSuccess"></span>
</div>
</form>
</div>

<span id="comAdm" data-adminurl="[[::siteURL]]/admin.php"></span>
<div id="comInsertNew"></div>
[[::ajaxcommentgroup]]
<div id="comInsertOld"></div>
</div>
<script>
makeComUserLink ();
commentBatches ('[[::siteURL]]/send.php/comments/load/', "[[::aID]]");
if ('[[::sinaAKey]]'=='' || '[[::sinaSKey]]'=='') {
	$('#comUseWeibo').hide();
}
if ([[::commentOpt]]==2) {
	$("#comUserName").attr('placeholder', '[[=page:LoginRequired]]');
	$("#comUserName").attr('readonly', 'readonly');
}

function errorPrompter (inputId)
{
	$('#'+inputId).addClass("comInputWarn");
	$('#'+inputId).click(function() {
		$('#'+inputId).removeClass("comInputWarn");
	});
}

$('#comClearBtn').click (function () {
	$("#comUserContent").val('');
	$("#comUserURL").val('');
});

$('#comSubmitBtn').click (function () {
	var stopSubmit=false;
	if (!$("#comUserName").val()) {
		errorPrompter('comUserName');
	}
	if ($("#comUserName").val()=="[[::authorName]]" && $('#comSocialKey').val()!='administrator') {
		errorPrompter("[[=page:NameViolation]]");
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
				$('#comClearBtn').click();
			}
		}, "json");
	}
});

$('#comUseWeibo').click (function () {
window.location="[[::siteURL]]/send.php/sina/start/?aID=[[::aID]]";
});

$.get("[[::siteURL]]/send.php/sina/check/?ajax=1", function(data) {
	if (data.error==0) { //Logged in with Sina Weibo
		$('#comLoggedInAs').html ('<span class="icon-sina-weibo"></span> '+data.returnMsg['screen_name']);
		$('#comLogout').click(function () {
			window.location="[[::siteURL]]/send.php/sina/end/?aID=[[::aID]]";
		});
		$('#comLoggedIn').show();
		$('#comUserName').removeAttr ('readonly');
		$('#comUserName').val (data.returnMsg['screen_name']);
		$('#comUserURL').val (data.returnMsg['url']);
		$('#comUserName').hide();
		$('#comUserURL').hide();
		$('#comUseWeibo').hide();
		$('.comLineChange').hide();
		$('#comSocialKey').val ('sina');
	}
}, "json");

$.get("[[::siteURL]]/send.php/comments/check/?ajax=1", function(data) {
	if (data.error==0) { //Logged in 
		$('#comLoggedInAs').html ("[[::authorName]]");
		$('#comLogout').hide();
		$('#comLoggedIn').show();
		$('#comUserName').val ("[[::authorName]]");
		$('#comUserURL').val ("[[::siteURL]]");
		$('#comUserName').hide();
		$('#comUserURL').hide();
		$('#comUseWeibo').hide();
		$('.comLineChange').hide();
		$('#comSocialKey').val ('administrator');
	}
}, "json");

</script>
eot;


$parts['ajaxcommentgroup']=<<<eot
[[::loop, comments]]
<div id="comWrap-[[::comID]]" class="comWrap">
<div class="comAvatar"><img src="[[::comAvatar]]" alt="User" /></div>
<div class="comName"><h6 data-usersource="[[::comSource]]"><a href="[[::comURL]]" target="_blank">[[::comName]]</a></h6><h5>[[::comTime, dateFormat, Y/m/d H:i]]</h5></div>
<div class="comContent"><pre>[[::comContent]]<div class="comBlockBar"><a href="##" onclick="blockComment ('comAdm', 'blockitem', '[[::comID]]', '[[::comArtID]]');"><span class="icon-cross"></span> [[=page:BlockItem]]</a> &nbsp; &nbsp; <a href="##" onclick="blockComment ('comAdm', 'blockip', '[[::comID]]', '[[::comArtID]]');"><span class="icon-minus3"></span> [[=page:BlockIP]]</a></div></pre></div>
</div>
[[::/loop]]
<div id="comLoadMore">
<a id="comLoadMoreA" href="##" data-currentbatch="[[::currentbatch]]" data-totalbatches="[[::totalbatches]]">[[=page:LoadMoreComments]]...</a>
</div>
eot;

$parts['ajaxcomment']=<<<eot
<div id="comWrap-[[::comID]]" class="comWrap">
<div class="comAvatar"><img src="[[::comAvatar]]" alt="User" /></div>
<div class="comName"><h6 data-usersource="[[::comSource]]"><a href="[[::comURL]]" target="_blank">[[::comName]]</a></h6><h5>[[::comTime, dateFormat, Y/m/d H:i]]</h5></div>
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

$parts['disqusarea']=<<<eot
<div class="commentArea">
<!-- Disqus start -->
<div id="disqus_thread"></div>
<script>
var disqus_config = function () {
this.page.url = "[[::siteURL]]/[[::linkPrefixArticle]]/[[::aID]]/"; // Replace PAGE_URL with your page's canonical URL variable
this.page.identifier = "[[::aID]]"; // Replace PAGE_IDENTIFIER with your page's unique identifier variable
};
(function() { // DON'T EDIT BELOW THIS LINE
var d = document, s = d.createElement('script');

s.src = '//[[::disqusID]].disqus.com/embed.js';

s.setAttribute('data-timestamp', +new Date());
(d.head || d.body).appendChild(s);
})();
</script>
<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript" rel="nofollow">comments powered by Disqus.</a></noscript>
<!-- Disqus end -->
</div>
eot;

$parts['nocommentarea']='';

$parts['authmobile']=<<<eot
<article><h2><span class="icon-mobile3"></span> [[=admin:item:AuthMobile]]</h2>
<h3><p>[[=admin:msg:Login]]</p>
<p>
<form action="[[::siteURL]]/send.php/nado/" method="post">
<input type="text" class="inputLine" name="s_myname" placeholder="[[=page:MobileRem]]" value="[[::deviceName]]" />
<br/><input type="password" class="inputLine" id="s_token" name="s_token" placeholder="[[=admin:msg:PromptPsw]]" /> <br/><br/><button type="submit" class="buttonLine" id="btnSubmit"><span class="icon-login"></span></button>
</form>
</p>
</h3>
</article>
<script type="text/javascript">
if (!window.localStorage) {
$("#btnSubmit").hide();
}
</script>
eot;

$parts['authmobilefinish']=<<<eot
<article><h2><span class="icon-mobile3"></span> [[=admin:item:AuthMobile]]</h2>
<h3><p>[[=page:MobileAuth1]] [[::deviceName]]</p>
</h3>
<h3>[[=page:MobileAuth2]]</h3>

</article>
<script type="text/javascript">
localStorage.mobileToken="[[::deviceMobileToken]]";
</script>
eot;

$parts['authmobilego']=<<<eot
<article><h2><span class="icon-mobile3"></span> [[=page:MobileLogin]]</h2>
<h3><p>[[=page:MobileAuth3]]</p>
<p>
<img src="http://qr.liantu.com/api.php?text=[[::siteURL]]/send.php/nalogin/[[::ipPC]]/" />
</p>
</h3>
<h3>[[=page:MobileAuth4]]</h3>
</article>
<script type="text/javascript" src="[[::siteURL]]/inc/script/timers/jquery.timers.js"></script>
<script type="text/javascript">

function checkPermission () {
	var chURL="[[::siteURL]]/send.php/nasearch/?ajax=1";
	$.get(chURL, { inPC: '[[::ipPC]]' }, function (data) {
		if (data.error==1) { //Wrong token
		}
		else {
			var CSRFCode = data.returnMsg;
			$('body').stopTime ();
			window.location="[[::siteURL]]/admin.php/dashboard/?CSRFCode="+CSRFCode;
		}
	}, "json");
} 
$('body').everyTime('1s', checkPermission);
</script>
eot;

$parts['authmobileconfirm']=<<<eot
<article><h2><span class="icon-mobile3"></span> [[=page:MobileLogin]]</h2>
<h3><p>[[=page:MobileAuth5]]</p>
<p>
<br/><button type="button" class="buttonLine" id="btnSubmit"><span class="icon-login"></span></button> OK
</p>
</h3>
</article>
<script type="text/javascript">
$("#btnSubmit").click (function () {
	if (!window.localStorage) {
		alert (lng['RememberFail']);
		return false;
	}
	if (!localStorage.mobileToken) {
		alert (lng['RememberFail']);
		return false;
	}
	$.get("[[::siteURL]]/send.php/nacheck/[[::ipPC]]/?ajax=1", { s_token: localStorage.mobileToken }, function (data) {
		if (data.error==1) { //Wrong token
			alert (lng['RememberFail']);
		}
		else {
			alert (lng['RememberSuccess']);
			$("#btnSubmit").fadeOut();
		}
	}, "json");
});

</script>
eot;

$parts['groupcolumn']=<<<eot
<article id="[[::columnID]]">
<h2>[[::columnName]] ([[::columnCount]])</h2>
<div class="details"><ul>
[[::loop, groupedArticles]]
<li><a href="[[::siteURL]]/[[::linkPrefixArticle]]/[[::aID]]/">[[::aTitle]]</a> ([[::aTime, dateFormat, Y/m/d]])</li>
[[::/loop]]
</ul></div>
</article>
eot;
?>