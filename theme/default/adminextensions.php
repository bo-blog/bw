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

<h2><span class="icon-star2"></span> [[=admin:sect:Modules]]</h2>
[[::loop, wgtListHtmlhead]]<span class="adminLaidItem" onclick="fillWgtHtmlHead ('addWgtHtmlhead', '[[::extID]]', '[[::value, safeConvert]]', [[::extOrder]], [[::extActivate]])">[[::extID]]</span>[[::/loop]]
<span class="adminLaidItem" onclick="fillWgtHtmlHead ('addWgtHtmlhead', '', '', -1, -1)">[+] [[=admin:btn:NewWidget]]</span>
<br style="clear: both"/>
<article id="addWgtHtmlhead" style="display: none;">
[[=admin:sect:EditModules]]<br/>
<input type="text" class="wgtCol inputLine inputLarge wgtID" name="wgtID" placeholder="Widget Name" /><br class="smallBr"/>
<textarea class="wgtCol inputLine inputLarge textareaLine textareaMiddle wgtvalue" name="wgtvalue" placeholder="HTML Value" />
</textarea>
<br class="smallBr"/>
<input type="hidden" class="wgtCol extOrder" name="extOrder" value="" />
<input type="hidden" class="wgtCol extHooks" name="extHooks" value="wghtmlhead" />
<span class="details">
<span class="btnAddWgt"><a href='##' onclick="addWidget('addWgtHtmlhead');"><span class="icon-disk"></span> [[=admin:btn:Save]]</a> &nbsp; &nbsp; </span>
<span class="btnCancelEdit"><a href='##' onclick="$('#addWgtHtmlhead').fadeOut();"><span class="icon-ccw"></span> [[=admin:msg:Close]]</a> &nbsp; &nbsp; </span>
<span class="btnEnable"><a href="##" onclick="makeEnabled($('#addWgtHtmlhead .wgtID').val());"><span class="icon-plus2"></span> [[=admin:opt:Enable]]</a> &nbsp; &nbsp; </span>
<span class="btnDisable"><a href="##" onclick="makeDisabled($('#addWgtHtmlhead .wgtID').val());"><span class="icon-minus2"></span> [[=admin:opt:Disable]]</a> &nbsp; &nbsp; </span>
<span class="btnRemoveWgt"><a href="##" onclick="removeExt($('#addWgtHtmlhead .wgtID').val());"><span class="icon-cross3"></span> [[=admin:msg:Remove]]</a></span>
</span>
<p class="wgtPromptError"></p>
</article>
<br/>

<span class="icon-arrow-right5"></span> [[=admin:item:WgtInterfaceName]] [[=admin:msg:WgtHeader]]<br/>
[[::loop, wgtListHeader]]<span class="adminLaidItem" onclick="fillWgtHeader ('addWgtHeader', '[[::extID]]', '[[::text]]', '[[::url]]', '[[::target]]', '[[::title]]', [[::extOrder]], [[::extActivate]])">[[::extID]]</span>[[::/loop]]
<span class="adminLaidItem" onclick="fillWgtHeader ('addWgtHeader', '', '', 'http://', '_self', '', -1, -1)">[+] [[=admin:btn:NewWidget]]</span>
<br style="clear: both"/>
<article id="addWgtHeader" style="display: none;">
[[=admin:sect:EditModules]]<br/>
<input type="text" class="wgtCol inputLine inputLarge wgtID" name="wgtID" placeholder="Widget Name" /><br class="smallBr"/>
<input type="text" class="wgtCol inputLine inputLarge wgttext" name="wgttext" placeholder="Link Text" /><br class="smallBr"/>
<input type="text" class="wgtCol inputLine inputLarge wgturl" name="wgturl" placeholder="Link URL" /><br class="smallBr"/>
<input type="text" class="wgtCol inputLine inputLarge wgttitle" name="wgttitle" placeholder="Mouseover Prompt" /><br class="smallBr"/>
<select class="wgtCol selectLine inputLarge wgttarget" name="wgttarget"><option value="_self">Open Link in Current Window</option><option value="_blank">Open Link in New Window</option><option value="_parent">Open Link in Parent Window</option></select><br/>
<input type="hidden" class="wgtCol extOrder" name="extOrder" value="" />
<input type="hidden" class="wgtCol extHooks" name="extHooks" value="wgheader" />
<span class="details">
<span class="btnAddWgt"><a href='##' onclick="addWidget('addWgtHeader');"><span class="icon-disk"></span> [[=admin:btn:Save]]</a> &nbsp; &nbsp; </span>
<span class="btnCancelEdit"><a href='##' onclick="$('#addWgtHeader').fadeOut();"><span class="icon-ccw"></span> [[=admin:msg:Close]]</a> &nbsp; &nbsp; </span>
<span class="btnEnable"><a href="##" onclick="makeEnabled($('#addWgtHeader .wgtID').val());"><span class="icon-plus2"></span> [[=admin:opt:Enable]]</a> &nbsp; &nbsp; </span>
<span class="btnDisable"><a href="##" onclick="makeDisabled($('#addWgtHeader .wgtID').val());"><span class="icon-minus2"></span> [[=admin:opt:Disable]]</a> &nbsp; &nbsp; </span>
<span class="btnRemoveWgt"><a href="##" onclick="removeExt($('#addWgtHeader .wgtID').val());"><span class="icon-cross3"></span> [[=admin:msg:Remove]]</a></span>
</span>
<p class="wgtPromptError"></p>
</article>
<br/>

<span class="icon-arrow-right5"></span> [[=admin:item:WgtInterfaceName]] [[=admin:msg:WgtSidebar]]<br/>
[[::loop, wgtListSiderbar]]<span class="adminLaidItem" onclick="fillWgtSidebar ('addWgtSidebar', '[[::extID]]', '[[::title]]', '[[::value, safeConvert]]', [[::extOrder]], [[::extActivate]])">[[::extID]]</span>[[::/loop]]
<span class="adminLaidItem" onclick="fillWgtSidebar ('addWgtSidebar', '', '', '', -1, -1)">[+] [[=admin:btn:NewWidget]]</span>
<br style="clear: both"/>
<article id="addWgtSidebar" style="display: none;">
[[=admin:sect:EditModules]]<br/>
<input type="text" class="wgtCol inputLine inputLarge wgtID" name="wgtID" placeholder="Widget Name" /><br class="smallBr"/>
<input type="text" class="wgtCol inputLine inputLarge wgttitle" name="wgttitle" placeholder="Sidebar Title" /><br class="smallBr"/>
<textarea class="wgtCol inputLine inputLarge textareaLine textareaMiddle wgtvalue" name="wgtvalue" placeholder="HTML Value" />
</textarea>
<br class="smallBr"/>
<input type="hidden" class="wgtCol extOrder" name="extOrder" value="" />
<input type="hidden" class="wgtCol extHooks" name="extHooks" value="wgsidebar" />
<span class="details">
<span class="btnAddWgt"><a href='##' onclick="addWidget('addWgtSidebar');"><span class="icon-disk"></span> [[=admin:btn:Save]]</a> &nbsp; &nbsp; </span>
<span class="btnCancelEdit"><a href='##' onclick="$('#addWgtSidebar').fadeOut();"><span class="icon-ccw"></span> [[=admin:msg:Close]]</a> &nbsp; &nbsp; </span>
<span class="btnEnable"><a href="##" onclick="makeEnabled($('#addWgtSidebar .wgtID').val());"><span class="icon-plus2"></span> [[=admin:opt:Enable]]</a> &nbsp; &nbsp; </span>
<span class="btnDisable"><a href="##" onclick="makeDisabled($('#addWgtSidebar .wgtID').val());"><span class="icon-minus2"></span> [[=admin:opt:Disable]]</a> &nbsp; &nbsp; </span>
<span class="btnRemoveWgt"><a href="##" onclick="removeExt($('#addWgtSidebar .wgtID').val());"><span class="icon-cross3"></span> [[=admin:msg:Remove]]</a></span>
</span>
<p class="wgtPromptError"></p>
</article>
<br/>

<span class="icon-arrow-right5"></span> [[=admin:item:WgtInterfaceName]] [[=admin:msg:WgtFooter]]<br/>
[[::loop, wgtListFooter]]<span class="adminLaidItem" onclick="fillWgtFooter ('addWgtFooter', '[[::extID]]', '[[::value, safeConvert]]', [[::extOrder]], [[::extActivate]])">[[::extID]]</span>[[::/loop]]
<span class="adminLaidItem" onclick="fillWgtFooter ('addWgtFooter', '', '', -1, -1)">[+] [[=admin:btn:NewWidget]]</span>
<br style="clear: both"/>
<article id="addWgtFooter" style="display: none;">
[[=admin:sect:EditModules]]<br/>
<input type="text" class="wgtCol inputLine inputLarge wgtID" name="wgtID" placeholder="Widget Name" /><br class="smallBr"/>
<textarea class="wgtCol inputLine inputLarge textareaLine textareaMiddle wgtvalue" name="wgtvalue" placeholder="HTML Value" />
</textarea>
<br class="smallBr"/>
<input type="hidden" class="wgtCol extOrder" name="extOrder" value="" />
<input type="hidden" class="wgtCol extHooks" name="extHooks" value="wgfooter" />
<span class="details">
<span class="btnAddWgt"><a href='##' onclick="addWidget('addWgtFooter');"><span class="icon-disk"></span> [[=admin:btn:Save]]</a> &nbsp; &nbsp; </span>
<span class="btnCancelEdit"><a href='##' onclick="$('#addWgtFooter').fadeOut();"><span class="icon-ccw"></span> [[=admin:msg:Close]]</a> &nbsp; &nbsp; </span>
<span class="btnEnable"><a href="##" onclick="makeEnabled($('#addWgtFooter .wgtID').val());"><span class="icon-plus2"></span> [[=admin:opt:Enable]]</a> &nbsp; &nbsp; </span>
<span class="btnDisable"><a href="##" onclick="makeDisabled($('#addWgtFooter .wgtID').val());"><span class="icon-minus2"></span> [[=admin:opt:Disable]]</a> &nbsp; &nbsp; </span>
<span class="btnRemoveWgt"><a href="##" onclick="removeExt($('#addWgtFooter .wgtID').val());"><span class="icon-cross3"></span> [[=admin:msg:Remove]]</a></span>
</span>
<p class="wgtPromptError"></p>
</article>


<br class="smallBr"/>

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
			location.reload();
		}
	}, "json");
}

function fillWgtHeader (formID, extID, text, url, target, title, extOrder, extActivate) {
	$('#'+formID+' .wgtID').val(extID);
	$('#'+formID+' .wgttext').val(text);
	$('#'+formID+' .wgturl').val(url);
	$('#'+formID+' .wgttitle').val(title);
	$('#'+formID+' .wgttarget').val(target);
	$('#'+formID+' .extOrder').val(extOrder);
	wgtBtns (formID, extActivate);
	$('#'+formID).fadeIn('fast');
}


function fillWgtHtmlHead (formID, extID, value, extOrder, extActivate) {
	$('#'+formID+' .wgtID').val(extID);
	$('#'+formID+' .wgtvalue').val(value);
	$('#'+formID+' .extOrder').val(extOrder);
	wgtBtns (formID, extActivate);
	$('#'+formID).fadeIn('fast');
}

function fillWgtSidebar (formID, extID, title, value, extOrder, extActivate) {
	$('#'+formID+' .wgtID').val(extID);
	$('#'+formID+' .wgttitle').val(title);
	$('#'+formID+' .wgtvalue').val(value);
	$('#'+formID+' .extOrder').val(extOrder);
	$('#'+formID).fadeIn('fast');
}

function fillWgtFooter (formID, extID, value, extOrder, extActivate) {
	fillWgtHtmlHead (formID, extID, value, extOrder, extActivate);
}

function wgtBtns (formID, extActivate) {
	if (extActivate == 1) {
		$('#'+formID+' .wgtID').attr('readonly', 'readonly');
		$('#'+formID+' .btnEnable').hide();
		$('#'+formID+' .btnDisable').show();
		$('#'+formID+' .btnRemoveWgt').show();
	}
	if (extActivate == 0) {
		$('#'+formID+' .wgtID').attr('readonly', 'readonly');
		$('#'+formID+' .btnEnable').show();
		$('#'+formID+' .btnDisable').hide();
		$('#'+formID+' .btnRemoveWgt').show();
	}
	if (extActivate == -1) {
		$('#'+formID+' .wgtID').removeAttr('readonly');
		$('#'+formID+' .btnEnable').hide();
		$('#'+formID+' .btnDisable').hide();
		$('#'+formID+' .btnRemoveWgt').hide();
	}
}

function addWidget (formID) {
	$("#UI-loading").fadeIn(500);
	var finalResult='';
	var targetURL=smtURL+"widget/?ajax=1&CSRFCode=[[::newCSRFCode]]";
	$('#'+formID+' .wgtCol').each (function () {
		finalResult+=$(this).attr('name')+'='+encodeURIComponent($(this).val())+"&";
	});
	$.post(targetURL, finalResult, function(data) {
		$("#UI-loading").fadeOut(200);
		if (data.error==1) {
			$('#'+formID+' .wgtPromptError').text (data.returnMsg);
			$('#'+formID+' .wgtPromptError').fadeIn(400).delay(1500).fadeOut(600);
		}
		else {
			location.reload();
		}
	}, "json");
}

function removeExt (extID) {
	if (confirm("[[=admin:msg:RemoveExtension]]"))
	{
		$("#UI-loading").fadeIn(500);
		var targetURL=smtURL+"remove/?ajax=1&CSRFCode=[[::extCSRFCode]]";

		$.post(targetURL, {extID : extID}, function(data) {
			$("#UI-loading").fadeOut(200);
			if (data.error==1) {
				$("#adminPromptError").text (data.returnMsg);
				$("#adminPromptError").fadeIn(400).delay(1500).fadeOut(600);
			}
			else {
				location.reload();
			}
		}, "json");
	}
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