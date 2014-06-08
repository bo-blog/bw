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
<span class="icon-arrow-right5"></span> [[=admin:item:NewExtDir]]<br/> /extension/<input type="text" class="inputLine inputMiddle" id="newDir" value="" /> <br/><span class="details"><a href='##' onclick="addExt();"><span class="icon-disk"></span> [[=admin:btn:NewExt]]</a></span>
</article>

<h2><span class="icon-warning"></span> [[=admin:sect:ExtSafety]]</h2> 
<p>[[=admin:msg:ExtSafety]]</p>

<p class="adminCommand">
<p id="adminPromptError"></p><p id="adminPromptSuccess"></p>
</p>

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

</script>
</form>

</div>

