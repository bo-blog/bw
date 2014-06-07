<?php
//Copyright: Byke

?>

<div class="adminArea">
<form id="smtForm" action="post">
<h2><span class="icon-compass"></span> [[=admin:sect:BasicInfo]]</h2>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:SiteName]]<br/><input type="text" class="inputLine inputLarge" name="smt[siteName]" value="[[::siteName]]" />
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:SiteURL]]<br/><input type="text" class="inputLine inputLarge" name="smt[siteURL]" value="[[::siteURL]]" /><br/><span class="adminExplain">[[=admin:msg:SiteURL]]</span>
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:TimeZone]]<br/><input type="text" class="inputLine inputLarge" name="smt[timeZone]" value="[[::timeZone]]" /><br/><span class="adminExplain">[[=admin:msg:TimeZone]]</span>
</p>

<p><br/><br/></p>

<h2><span class="icon-user"></span> [[=admin:sect:Author]]</h2>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:AuthorName]]<br/><input type="text" class="inputLine inputLarge" name="smt[authorName]" value="[[::authorName]]" />
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:AuthorIntro]]<br/><input type="text" class="inputLine inputLarge" name="smt[authorIntro]" value="[[::authorIntro]]" />
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:AuthorSocial]]<br/>
<span class="icon-sina-weibo"></span> <input type="text" class="inputLine inputMiddle" name="smt[social-sina-weibo]" value="[[::social-sina-weibo]]" placeholder="[[=page:social:Weibo]]" /><br/> 
<span class="icon-weixin"></span> <input type="text" class="inputLine inputMiddle" name="smt[social-weixin]" value="[[::social-weixin]]" placeholder="[[=page:social:WeChat]]" /><br/> 
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
<script type="text/javascript">
$("#siteKey3").blur(function() {
	if ($("#siteKey3").val()!=$("#siteKey").val())
	{
		$("#siteKey3").addClass("inputLineWarn");
	}
	$("#siteKey3").click(function() {
		$("#siteKey3").removeClass("inputLineWarn");
	});
});
$("#siteKey").blur(function() {
	if ($("#siteKey").val()!='')
	{
		if ( !checkPassword($("#siteKey").val()) )
		{
			$("#siteKey").addClass("inputLineWarn");
			$("#siteKey").click(function() {
				$("#siteKey").removeClass("inputLineWarn");
			});
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
</script>

<p><br/><br/></p>

<h2><span class="icon-pictures"></span> [[=admin:sect:Looks]]</h2>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:SiteTheme]]<br/>
<select name="smt[siteTheme]" id="siteTheme" class="selectLine">
[[::loop, themeList]]<option value="[[::themeDir]]">[[::themeName]] ([[=admin:msg:By]] [[::themeAuthor]])</option>[[::/loop]]
</select>
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:SiteLang]]<br/>
<select name="smt[siteLang]" id="siteLang" class="selectLine">
<option value="zh-cn">[[=admin:opt:SimplifiedChinese]]</option>
<option value="en">[[=admin:opt:English]]</option>
</select>
</p>
<script type="text/javascript">
$("#siteTheme").val("[[::siteTheme]]");
$("#siteLang").val("[[::siteLang]]");
</script>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:PerPage]]<br class="smallBr"/><span class="buttonLine buttonGroup buttonGroupFirst perPage" data-reflect="3">[[=admin:opt:VeryFew]]</span> <span class="buttonLine buttonGroup perPage" data-reflect="5">[[=admin:opt:AFew]]</span>  <span class="buttonLine buttonGroup perPage" data-reflect="10">[[=admin:opt:Normal]]</span> <span class="buttonLine buttonGroup buttonGroupLast perPage" data-reflect="15">[[=admin:opt:Many]]</span> <input type="hidden" value="[[::perPage]]" name="smt[perPage]" id="perPage"/>
</p>
<script type="text/javascript">
function goPerPage() {
	var perPageCD=$("#perPage").val();
	$(".perPage").each(function(){
		var perPageD=$(this).data('reflect');
		if (perPageD==perPageCD)
		{
			$(this).addClass("buttonGroupSelected");
		}
		else {
			$(this).removeClass("buttonGroupSelected");
		}
	});
}

$(".perPage").click(function(){
	var perPageD=$(this).data('reflect');
	$("#perPage").val(perPageD);
	goPerPage();
});

goPerPage();
</script>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:Links]]<br class="smallBr"/><textarea type="text" class="inputLine inputLarge textareaLine textareaMiddle" name="smt[externalLinks]" id="externalLinks" />[[::externalLinks]]</textarea>
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:URLRewrite]]<br class="smallBr"/><span class="buttonLine buttonGroup buttonGroupFirst linkPrefix" data-reflect="index.php"><span class="icon-cross"></span> [[=admin:opt:Off]]</span> <span class="buttonLine buttonGroup buttonGroupLast linkPrefix" data-reflect="index"><span class="icon-checkmark"></span> [[=admin:opt:On]]</span> <input type="hidden" value="[[::linkPrefixIndex]]" name="smt[linkPrefixIndex]" id="linkPrefixIndex"/><input type="hidden" value="[[::linkPrefixCategory]]" name="smt[linkPrefixCategory]" id="linkPrefixCategory"/><input type="hidden" value="[[::linkPrefixArticle]]" name="smt[linkPrefixArticle]" id="linkPrefixArticle"/><input type="hidden" value="[[::linkPrefixTag]]" name="smt[linkPrefixTag]" id="linkPrefixTag"/>
<br/><span class="adminExplain">[[=admin:msg:URLRewrite]]</span>
</p>
<script type="text/javascript">
function goPrefix() {
	var linkPrefixCD=$("#linkPrefixIndex").val();
	$(".linkPrefix").each(function(){
		var linkPrefixD=$(this).data('reflect');
		if (linkPrefixD==linkPrefixCD)
		{
			$(this).addClass("buttonGroupSelected");
		}
		else {
			$(this).removeClass("buttonGroupSelected");
		}
	});
}

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

$(".linkPrefix").click(function(){
	var linkPrefixD=$(this).data('reflect');
	$("#linkPrefixIndex").val(linkPrefixD);
	goPrefix();
	syncPrefix();
});

goPrefix();
</script>

<p class="adminCommand"><br/>
<button type="button" class="buttonLine" id="btnSubmit" onclick="saveConf('smtForm', '[[::siteURL]]/admin.php/center/store/');"><span class="icon-disk"></span></button> [[=admin:btn:Save]]
<button type="button" class="buttonLine" onclick="window.location=window.location;"><span class="icon-ccw"></span></button> [[=admin:btn:Restore]]
<p id="adminPromptError"></p><p id="adminPromptSuccess"></p>
</p>
</form>

</div>

<script type="text/javascript">

function saveConf(formID, smtURL) {
	var stopSubmit=false;
	$('.inputLine').each(function() {
		if ($(this).hasClass('inputLineWarn'))
		{
			$("#adminPromptError").text ('[[=admin:msg:ErrorCorrection]]');
			$("#adminPromptError").fadeIn(400).delay(1500).fadeOut(600);
			stopSubmit=true;
		}
	});

	if (!stopSubmit)
	{
		$("#UI-loading").fadeIn(500);
		$.post(smtURL+"?ajax=1", $('#'+formID).serialize(), function(data) {
			$("#UI-loading").fadeOut(200);
			if (data.error==1) {
				$("#adminPromptError").text (data.returnMsg);
				$("#adminPromptError").fadeIn(400).delay(1500).fadeOut(600);
			}
			else {
				$("#adminPromptSuccess").text (data.returnMsg);
				$("#adminPromptSuccess").fadeIn(400).delay(1500).fadeOut(600);
			}
		}, "json");
	}
}

$('.adminSign').click(function (){checkLogout('adminSign');});

function checkLogout (oj) {
	var rootURL= $('.'+oj).data('adminurl');
	window.location=rootURL+"/login/logout";
}

$("#admCenter").addClass("activeNav");
</script>
