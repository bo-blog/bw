<?php
//Copyright: Byke

?>

<div class="adminArea">
<form id="smtForm" data-adminurl="[[::siteURL]]/admin.php/extensions/">
<h2><span class="icon-list"></span> [[=admin:sect:InstalledExt]]</h2> 
<p>
[[::loop, extList]]<article>
<h2 title="[[=admin:msg:Hook]] [[::extHooks]]">[[::extName]]</h2>
[[::extIntro]] 
<h3>[[=admin:msg:By]] <a href="[[::extURL]]">[[::extAuthor]]</a> | <span class="extStatus[[::extActivate]]">[[=admin:msg:ExtStatus[[::extActivate]]]]</span></h3>
<span class="details"><a href="##" onclick="makeEnabled('[[::extID]]');"><span class="icon-plus2"></span> [[=admin:opt:Enable]]</a> &nbsp; &nbsp; <a href="##" onclick="makeDisabled('[[::extID]]');"><span class="icon-minus2"></span> [[=admin:opt:Disable]]</a> &nbsp; &nbsp; <a href="##" onclick="removeExt('[[::extID]]');"><span class="icon-cross3"></span> [[=admin:msg:Remove]]</a></span>
</article>[[::/loop]]
</p>
<br/>
<h2><span class="icon-plus2"></span> [[=admin:sect:InstallExt]]</h2>
<article>
<span class="icon-arrow-right5"></span> [[=admin:item:NewExtDir]]<br/> /extension/<input type="text" class="inputLine inputMiddle" id="newDir" value="" /> <br/><span class="details"><a href='##' onclick="addExt();"><span class="icon-disk"></span> [[=admin:btn:NewExt]]</a></span><br/>
<span class="extStatus0"><span class="icon-warning"></span> [[=admin:msg:ExtSafety]]</span><br/>
</article>


<h2><span class="icon-paperclip"></span> [[=admin:sect:CustomizedHTML]]</h2> 
<span class="adminExplain">[[=admin:msg:CustomizedHTML]]</span>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:InterfaceName]] htmlhead<br/>
<span class="adminExplain">[[=admin:msg:HookHTMLHead]]</span>
<br class="smallBr"/>
<textarea type="text" class="inputLine inputLarge textareaLine textareaSmall" name="smt[htmlhead]" />[[::insert_htmlhead]]</textarea>
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:InterfaceName]] header<br/>
<span class="adminExplain">[[=admin:msg:HookHeader]]</span>
<br class="smallBr"/>
<textarea type="text" class="inputLine inputLarge textareaLine textareaSmall" name="smt[header]" />[[::insert_header]]</textarea>
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:InterfaceName]] intro<br/>
<span class="adminExplain">[[=admin:msg:HookIntro]]</span>
<br class="smallBr"/>
<textarea type="text" class="inputLine inputLarge textareaLine textareaSmall" name="smt[intro]" />[[::insert_intro]]</textarea>
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:InterfaceName]] mainAreaEnd<br/>
<span class="adminExplain">[[=admin:msg:HookMainAreaEnd]]</span>
<br class="smallBr"/>
<textarea type="text" class="inputLine inputLarge textareaLine textareaSmall" name="smt[mainAreaEnd]" />[[::insert_mainAreaEnd]]</textarea>
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:InterfaceName]] footer<br/>
<span class="adminExplain">[[=admin:msg:HookFooter]]</span>
<br class="smallBr"/>
<textarea type="text" class="inputLine inputLarge textareaLine textareaSmall" name="smt[footer]" />[[::insert_footer]]</textarea>
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:InterfaceName]] beforeEnd<br/>
<span class="adminExplain">[[=admin:msg:HookBeforeEnd]]</span>
<br class="smallBr"/>
<textarea type="text" class="inputLine inputLarge textareaLine textareaSmall" name="smt[beforeEnd]" />[[::insert_beforeEnd]]</textarea>
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:InterfaceName]] summaryDetail<br/>
<span class="adminExplain">[[=admin:msg:HookSummaryDetail]]</span>
<br class="smallBr"/>
<textarea type="text" class="inputLine inputLarge textareaLine textareaSmall" name="smt[summaryDetail]" />[[::insert_summaryDetail]]</textarea>
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:InterfaceName]] articleDetail<br/>
<span class="adminExplain">[[=admin:msg:HookArticleDetail]]</span>
<br class="smallBr"/>
<textarea type="text" class="inputLine inputLarge textareaLine textareaSmall" name="smt[articleDetail]" />[[::insert_articleDetail]]</textarea>
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:InterfaceName]] commentArea<br/>
<span class="adminExplain">[[=admin:msg:HookCommentArea]]</span>
<br class="smallBr"/>
<textarea type="text" class="inputLine inputLarge textareaLine textareaSmall" name="smt[commentArea]" />[[::insert_commentArea]]</textarea>
</p>


<p class="adminCommand"><br/>
<button type="button" class="buttonLine" id="btnSubmit" onclick="saveOpenHooks();"><span class="icon-disk"></span></button> [[=admin:btn:Save]]
<button type="button" class="buttonLine" onclick="window.location=window.location;"><span class="icon-ccw"></span></button> [[=admin:btn:Restore]]
<p id="adminPromptError"></p><p id="adminPromptSuccess"></p>
</p>
[[::ext_adminExtensions]]

<script type="text/javascript">
$("#admExtensions").addClass("activeNav");
var smtURL=$("#smtForm").data('adminurl');

function makeEnabled (extID) {
	makeEnabledDisabled (extID, 1);
}

function makeDisabled (extID) {
	makeEnabledDisabled (extID, 0);
}

function makeEnabledDisabled (extID, extActivate) {
	$("#UI-loading").fadeIn(500);
	var targetURL=smtURL+"modify/?ajax=1&CSRFCode=[[::extCSRFCode]]";

	$.post(targetURL, {extID : extID, extActivate : extActivate}, function(data) {
		$("#UI-loading").fadeOut(200);
		if (data.error==1) {
			$("#adminPromptError").text (data.returnMsg);
			$("#adminPromptError").fadeIn(400).delay(1500).fadeOut(600);
		}
		else {
			window.location=smtURL+"?CSRFCode=[[::navCSRFCode]]";
		}
	}, "json");
}

function removeExt (extID) {
	$("#UI-loading").fadeIn(500);
	var targetURL=smtURL+"remove/?ajax=1&CSRFCode=[[::extCSRFCode]]";

	$.post(targetURL, {extID : extID}, function(data) {
		$("#UI-loading").fadeOut(200);
		if (data.error==1) {
			$("#adminPromptError").text (data.returnMsg);
			$("#adminPromptError").fadeIn(400).delay(1500).fadeOut(600);
		}
		else {
			window.location=smtURL+"?CSRFCode=[[::navCSRFCode]]";
		}
	}, "json");
}

function addExt () {
	$("#UI-loading").fadeIn(500);
	var targetURL=smtURL+"add/?ajax=1&CSRFCode=[[::newCSRFCode]]";

	$.post(targetURL, {extID : $('#newDir').val()}, function(data) {
		$("#UI-loading").fadeOut(200);
		if (data.error==1) {
			$("#adminPromptError").text (data.returnMsg);
			$("#adminPromptError").fadeIn(400).delay(1500).fadeOut(600);
		}
		else {
			window.location=smtURL+"?CSRFCode=[[::navCSRFCode]]";
		}
	}, "json");
}

function saveOpenHooks () {
	$("#UI-loading").fadeIn(500);
	var targetURL=smtURL+"savehooks/?ajax=1&CSRFCode=[[::extCSRFCode]]";

	$.post(targetURL, $("#smtForm").serialize(), function(data) {
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

</script>
</form>

</div>
[[::ext_adminExtensionsEnding]]