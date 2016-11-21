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

<div class="adminArea">
<form id="smtForm" action="post">
<h2><span class="icon-cogs"></span> [[=admin:sect:BasicInfo]]</h2>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:SiteName]]<br/><input type="text" class="inputLine inputLarge" name="smt[siteName]" value="[[::siteName]]" />
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:SiteURL]]<br/><input type="text" class="inputLine inputLarge" name="smt[siteURL]" value="[[::siteURL]]" /><br/><span class="adminExplain">[[=admin:msg:SiteURL]]</span>
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:TimeZone]]<br/><input type="text" class="inputLine inputLarge" name="smt[timeZone]" value="[[::timeZone]]" /><br/><span class="adminExplain">[[=admin:msg:TimeZone]]</span>
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:Cache]]<br class="smallBr"/><span class="buttonLine buttonGroup buttonGroupFirst pageCache" data-reflect="0"><span class="icon-cross"></span> [[=admin:opt:Off]]</span> <span class="buttonLine buttonGroup buttonGroupLast pageCache" data-reflect="1"><span class="icon-checkmark"></span> [[=admin:opt:On]]</span> <input type="hidden" value="[[::pageCache]]" name="smt[pageCache]" id="pageCache"/><br/><span class="adminExplain">[[=admin:msg:Cache]]</span>
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:CommentOpt]]<br class="smallBr"/>
<select name="smt[commentOpt]" id="commentOpt" class="selectLine">
<option value="0">[[=admin:opt:NoComment]]</option>
<!--<option value="1">[[=admin:opt:AllowComment]]</option>
<option value="2">[[=admin:opt:OnlyLoginComment]]</option>-->
<option value="3">[[=admin:opt:ThirdPartyComment]]</option>
</select>
<br/><span class="adminExplain">[[=admin:msg:CommentOpt]]</span></p>
<!-- <p>
<span class="icon-arrow-right5"></span> [[=admin:item:CommentFrequency]]<br/><input type="text" class="inputLine inputLarge" name="smt[comFrequency]" value="[[::comFrequency]]" /><br/><span class="adminExplain">[[=admin:msg:CommentFrequency]]</span>
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:CommentBreak]]<br/><input type="text" class="inputLine inputLarge" name="smt[comPerLoad]" value="[[::comPerLoad]]" />
</p> -->
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:AutoSave]]<br class="smallBr"/><span class="buttonLine buttonGroup buttonGroupFirst autoSave" data-reflect="0"><span class="icon-cross"></span> [[=admin:opt:Off]]</span> <span class="buttonLine buttonGroup buttonGroupLast autoSave" data-reflect="1"><span class="icon-checkmark"></span> [[=admin:opt:On]]</span> <input type="hidden" value="[[::autoSave]]" name="smt[autoSave]" id="autoSave"/><br/><span class="adminExplain">[[=admin:msg:AutoSaveNotice]]</span>
</p>

<p><br/><br/></p>

<h2><span class="icon-user5"></span> [[=admin:sect:Author]]</h2>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:AuthorName]]<br/><input type="text" class="inputLine inputLarge" name="smt[authorName]" value="[[::authorName]]" />
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:AuthorIntro]]<br/><input type="text" class="inputLine inputLarge" name="smt[authorIntro]" value="[[::authorIntro]]" />
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:AuthorSocial]]<br/>
<span class="icon-sina-weibo"></span> <input type="text" class="inputLine inputMiddle" name="smt[social-sina-weibo]" value="[[::social-sina-weibo]]" placeholder="[[=page:social:Weibo]]" /><br/> 
<span class="icon-twitter"></span> <input type="text" class="inputLine inputMiddle" name="smt[social-twitter]" value="[[::social-twitter]]" placeholder="[[=page:social:Twitter]]" /><br/> 
<span class="icon-weixin"></span> <input type="text" class="inputLine inputMiddle" name="smt[social-weixin]" value="[[::social-weixin]]" placeholder="[[=page:social:WeChat]]" /><br/> 
<span class="icon-facebook"></span> <input type="text" class="inputLine inputMiddle" name="smt[social-facebook]" value="[[::social-facebook]]" placeholder="[[=page:social:Facebook]]" /><br/> 
<span class="icon-douban"></span> <input type="text" class="inputLine inputMiddle" name="smt[social-douban]" value="[[::social-douban]]" placeholder="[[=page:social:Douban]]" /><br/>
<span class="icon-instagram"></span> <input type="text" class="inputLine inputMiddle" name="smt[social-instagram]" value="[[::social-instagram]]" placeholder="[[=page:social:Instagram]]" /><br/>
<span class="icon-renren"></span> <input type="text" class="inputLine inputMiddle" name="smt[social-renren]" value="[[::social-renren]]" placeholder="[[=page:social:Renren]]" /><br/>
<span class="icon-linkedin"></span> <input type="text" class="inputLine inputMiddle" name="smt[social-linkedin]" value="[[::social-linkedin]]" placeholder="[[=page:social:Linkedin]]" /><br/>
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:ResetPsw]]<br/><input type="password" class="inputLine inputLarge" name="smt[siteKey]" value="" id="siteKey" placeholder="[[=admin:msg:BlankPsw]]" /><br/><span class="adminExplain">[[=admin:msg:LengthPsw]]</span>
</p>
<p id="siteKey2">
<span class="icon-arrow-right5"></span> [[=admin:item:RepeatPsw]]<br/><input type="password" class="inputLine inputLarge" value="" id="siteKey3" placeholder="[[=admin:msg:BlankPsw]]" />
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:UploadAvatar]]<br/><a href="##" onclick="$('#uploadPicFile').click();"><img src="[[::siteURL]]/conf/profile.png" style="width: 60px" id="avatarImg" /></a><br/><span class="adminExplain">[[=admin:msg:UploadAvatar]]</span>
</p>
<p id="authMobile">
<span class="icon-arrow-right5"></span> [[=admin:item:AuthMobile]]<br/>
[[::loop, mobileKeys]]<span id="mobLine-[[::seq]]"><span class="icon-mobile3"></span> [[::devID]] &nbsp; <a href="##" onclick="cancelAuth('[[::devID]]', '[[::siteURL]]/[[::linkPrefixAdmin]]/center/cancelauth/', '[[::seq]]');">[[[=admin:item:CancelAuthMobile]]]</a><br/></span>[[::/loop]]
<a href="##" onclick="$('.authMobileImg').toggle();"><span class="icon-plus2"></span> [[=admin:item:AddAuthMobile]]</a><br/><span class="adminExplain">[[=admin:msg:AuthMobile1]]<br/>[[=admin:msg:AuthMobile2]]</span>
<span class="authMobileImg" style=" display:none;" ><br/><img src="http://qr.liantu.com/api.php?text=[[::siteURL]]/[[::linkPrefixSend]]/na/" /><span class="adminExplain"><br/>[[=admin:msg:AuthMobile3]]<a href="[[::siteURL]]/[[::linkPrefixSend]]/na/" target="_blank">[[=admin:msg:AuthMobile4]]</a></span></span>
</p>

<p><br/><br/></p>

<h2><span class="icon-pictures"></span> [[=admin:sect:Looks]]</h2>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:SiteTheme]]<br/>
<select name="smt[siteTheme]" id="siteTheme" class="selectLine">
[[::loop, themeList]]<option value="[[::themeDir]]">[[::themeName]] ([[=admin:msg:By]] [[::themeAuthor]])</option>[[::/loop]]
</select>
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:SiteLang]] (Language)<br/>
<select name="smt[siteLang]" id="siteLang" class="selectLine">
<option value="zh-cn">[[=admin:opt:SimplifiedChinese]] | 简体中文</option>
<option value="zh-tw">[[=admin:opt:TraditionalChinese]] | 繁體中文</option>
<option value="en">[[=admin:opt:English]] | English</option>
</select>
</p>

<p>
<span class="icon-arrow-right5"></span> [[=admin:item:PerPage]]<br class="smallBr"/><span class="buttonLine buttonGroup buttonGroupFirst perPage" data-reflect="3">[[=admin:opt:VeryFew]]</span> <span class="buttonLine buttonGroup perPage" data-reflect="5">[[=admin:opt:AFew]]</span>  <span class="buttonLine buttonGroup perPage" data-reflect="10">[[=admin:opt:Normal]]</span> <span class="buttonLine buttonGroup buttonGroupLast perPage" data-reflect="15">[[=admin:opt:Many]]</span> <input type="hidden" value="[[::perPage]]" name="smt[perPage]" id="perPage"/>
</p>
<script type="text/javascript">
</script>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:Links]]<br class="smallBr"/>
[[::loop, allLinks]]
<span id="lnkItem-[[::linkID]]"><span class="icon-keyboard2"></span> <input type="text" class="inputLine inputSmall lnk" name="smt[externalLinks][lnkname][]" value="[[::linkName]]" placeholder="[[=admin:item:LinkText]]" /> <span class="icon-link2"></span><input type="text" class="inputLine inputSmall lnk" name="smt[externalLinks][lnkurl][]" value="[[::linkURL]]" placeholder="[[=admin:item:LinkURL]]" /> <a href="##" onclick="$('#lnkItem-[[::linkID]]').remove();"><span class="icon-cross2"></span></a><br/></span>
[[::/loop]]
<div	id="newLink"></div><div	id="newLinkTpl" style="display:none;"><span id="lnkItem-rndid"><span class="icon-keyboard2"></span> <input type="text" class="inputLine inputSmall lnk" name="smt[externalLinks][lnkname][]" value="" placeholder="[[=admin:item:LinkText]]" /> <span class="icon-link2"></span><input type="text" class="inputLine inputSmall lnk" name="smt[externalLinks][lnkurl][]" value="" placeholder="[[=admin:item:LinkURL]]" /> <a href="##" onclick="$('#lnkItem-rndid').remove();"><span class="icon-cross2"></span></a><br/></span></div>
<a href="##" onclick="$('#newLink').append($('#newLinkTpl').html().replace(/rndid/g, Math.floor(Math.random() * 8999 + 1000)));"><span class="icon-plus2"></span> [[=admin:btn:NewCate]]</a>
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:URLRewrite]]<br class="smallBr"/><span class="buttonLine buttonGroup buttonGroupFirst linkPrefixIndex" data-reflect="index.php"><span class="icon-cross"></span> [[=admin:opt:Off]]</span> <span class="buttonLine buttonGroup buttonGroupLast linkPrefixIndex" data-reflect="index"><span class="icon-checkmark"></span> [[=admin:opt:On]]</span> <input type="hidden" value="[[::linkPrefixIndex]]" name="smt[linkPrefixIndex]" id="linkPrefixIndex"/><input type="hidden" value="[[::linkPrefixCategory]]" name="smt[linkPrefixCategory]" id="linkPrefixCategory"/><input type="hidden" value="[[::linkPrefixArticle]]" name="smt[linkPrefixArticle]" id="linkPrefixArticle"/><input type="hidden" value="[[::linkPrefixTag]]" name="smt[linkPrefixTag]" id="linkPrefixTag"/>
<br/><span class="adminExplain">[[=admin:msg:URLRewrite]]</span>
</p>

<p class="adminCommand"><br/>
<button type="button" class="buttonLine" id="btnSubmit" onclick="saveConf('smtForm', '[[::siteURL]]/[[::linkPrefixAdmin]]/center/store/');"><span class="icon-disk"></span></button> [[=admin:btn:Save]]
<button type="button" class="buttonLine" onclick="window.location=window.location;"><span class="icon-ccw"></span></button> [[=admin:btn:Restore]]
<p id="adminPromptError"></p><p id="adminPromptSuccess"></p>
</p>
[[::ext_adminCenter]]
</form>

</div>

<form id="picForm" method="post" action="[[::siteURL]]/[[::linkPrefixAdmin]]/center/avatarupload/[[::linkConj]]CSRFCode=[[::upCSRFCode]]" enctype="multipart/form-data" target="execPicTarget">
<input type="file" style="display: none; height: 1px;" name="uploadFile" id="uploadPicFile" onchange="$('#picForm').submit();"/>
</form>
<iframe id="execPicTarget" name="execPicTarget" style="display: none;" onload="$('#avatarImg').attr('src', '[[::siteURL]]/conf/profile.png?'+Math.random());"></iframe>

<script type="text/javascript">
$("#siteTheme").val("[[::siteTheme]]");
$("#siteLang").val("[[::siteLang]]");
$("#commentOpt").val("[[::commentOpt]]");

function errorPrompter (inputId)
{
	$('#'+inputId).addClass("inputLineWarn");
	$('#'+inputId).click(function() {
		$('#'+inputId).removeClass("inputLineWarn");
	});
}

$("#siteKey3").blur(function() {
	if ($("#siteKey3").val()!=$("#siteKey").val()) {
		errorPrompter('siteKey3');
	}
});

$("#siteKey").blur(function() {
	if ($("#siteKey").val()!='')	{
		if (!checkPassword($("#siteKey").val())){
			errorPrompter('siteKey');
		}		
	}
});

function checkPassword (str){
	if (str.length<8)
	{
		return false;
	}
	var specialCharacters = "~!@#$%^&*()_+-=[]{}\\|;:'\",.<>/? ";
	for (var i=0; i<specialCharacters.length-1; i=i+1)
	{
	
		if (str.indexOf(specialCharacters.charAt(i)) != -1)
		{
			return true;
		}
	}
	return false;
}

function goSelector(selID) {
	var selIDVal=$('#'+selID).val();
	$('.'+selID).each(function(){
		var selIDValD=$(this).data('reflect');
		if (selIDValD==selIDVal)
		{
			$(this).addClass("buttonGroupSelected");
		}
		else {
			$(this).removeClass("buttonGroupSelected");
		}
	});

	$('.'+selID).click(function(){
		$('#'+selID).val($(this).data('reflect'));
		goSelector(selID);
	});
}

goSelector ('perPage');
goSelector ('pageCache');
goSelector ('autoSave');
var tmpLinkPrefixIndex = $("#linkPrefixIndex").val().split('?');
$("#linkPrefixIndex").val(tmpLinkPrefixIndex[0]);
goSelector ('linkPrefixIndex');
function syncPrefix() {
	var linkPrefixD=$("#linkPrefixIndex").val();
	if (linkPrefixD=='index.php')
	{
		$("#linkPrefixCategory").val('category.php');
		$("#linkPrefixArticle").val('read.php');
		$("#linkPrefixTag").val('tag.php');
	}
	else
	{
		$("#linkPrefixCategory").val('category');
		$("#linkPrefixArticle").val('post');
		$("#linkPrefixTag").val('tag');
	}
}

function saveConf(formID, smtURL) {
	syncPrefix();
	var stopSubmit=false;
	$('.inputLine').each(function() {
		if ($(this).hasClass('inputLineWarn'))
		{
			$("#adminPromptError").text ('[[=admin:msg:ErrorCorrection]]');
			$("#adminPromptError").fadeIn(400).delay(1500).fadeOut(600);
			stopSubmit=true;
		}
	});
 	$('.lnk:visible').each(function() {
		if ($(this).val() == '')
		{
			$(this).focus();
			$("#adminPromptError").text ('[[=admin:msg:ErrorCorrection]]');
			$("#adminPromptError").fadeIn(400).delay(1500).fadeOut(600);
			stopSubmit=true;
			return false;
		}
	});

	if (!stopSubmit)
	{
		$("#UI-loading").fadeIn(500);
		//window.location=smtURL+"[[::linkConj]]ajax=1&CSRFCode=[[::CSRFCode]]&"+$('#'+formID).serialize();
		$.post(smtURL+"[[::linkConj]]ajax=1&CSRFCode=[[::CSRFCode]]", $('#'+formID).serialize(), function(data) {
			$("#UI-loading").fadeOut(200);
			if (data.error==1) {
				$("#adminPromptError").text (data.returnMsg);
				$("#adminPromptError").fadeIn(400).delay(1500).fadeOut(600);
			}
			else {
				$("#adminPromptSuccess").text (data.returnMsg);
				$("#adminPromptSuccess").fadeIn(400).delay(1500).fadeOut(600);
				if ("[[::siteLang]]" != $('#siteLang').val())
				{
					window.location=window.location;
				}
			}
		}, "json");
	}
}

$('.adminSign').click(function (){checkLogout('adminSign');});

function checkLogout (oj) {
	var rootURL= $('.'+oj).data('adminurl');
	window.location=rootURL+"/login/logout";
}

function cancelAuth (devID, smtURL, seq) {
	$("#UI-loading").fadeIn(500);
	$.post(smtURL+"[[::linkConj]]ajax=1&CSRFCode=[[::CSRFCode]]", {devID : devID}, function(data) {
		$("#UI-loading").fadeOut(200);
		if (data.error==1) {
		}
		else {
			$("#mobLine-"+seq).fadeOut();
			$("#mobLine-"+seq).remove();
		}
	}, "json");
}

$("#admCenter").addClass("activeNav");
</script>
[[::ext_adminCenterEnding]]