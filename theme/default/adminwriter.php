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
<input type="hidden" name="smt[originID]" id="originID" value="[[::aID]]" />
<h2><span class="icon-book2"></span> [[=admin:sect:Writer]]</h2>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:ATitle]]<br/><input type="text" class="inputLine inputLarge" name="smt[aTitle]" value="[[::aTitle]]" id="aTitle" /> <span id="gotoAID"></span>
</p>

<p>
<span class="icon-arrow-right5"></span> [[=admin:item:AID]]<br/><input type="text" class="inputLine inputLarge" name="smt[aID]" value="[[::aID]]" placeholder="[[=admin:msg:AID]]" id="aID" /><br/><span class="adminExplain">[[=admin:msg:AID2]]</span>
</p>

<p id="editorBody">
<span class="icon-arrow-right5"></span> [[=admin:item:AContent]]<span class="adminUploader"><a href="##" id="adminUploader"><span class="icon-pictures"> </span><span id="adminUpAdd">[[=admin:btn:AddPic]]</span></a> <a href="##" id="adminPreview"><span class="icon-screen2"> </span><span id="adminPrevAdd">[[=admin:btn:StartPreview]]</span></a> <select id="tplSel" class="selectLine inputLine inputMiddle" style="width: 90px; font-size: 12px"><option value="">[[=admin:btn:TextHelper]]</option>[[::loop, articleTemplate]]<option value="[[::file]]">[[::name]]</option>[[::/loop]]</select>
</span> <br/><textarea type="text" class="inputLine inputLarge textareaLine" name="smt[aContent]" id="aContent" />[[::aContent]]</textarea><div id="previewArea" class="inputLine inputLarge textareaLine details"></div>
</p>

<p class="articleOnly">
<span class="icon-arrow-right5"></span> [[=admin:item:SetTag]]<br/><input type="text" id='eTags' class="inputLine inputLarge" name="smt[aTags]" value="[[::aTags]]" placeholder="[[=admin:msg:SetTag]]" /><div id="taghint"></div></p>

<p class="articleOnly">
<span class="icon-arrow-right5"></span> [[=admin:item:ACate]]<br/>
<select name="smt[aCateURLName]" id="aCateURLName" class="selectLine">
[[::loop, admincatelist]]<option value="[[::aCateURLName]]">[[::aCateDispName]]</option>[[::/loop]]
<option value="_trash">[[=admin:item:TrashBin]]</option><option value="<new>">[+] [[=admin:btn:NewCate]]</option>
</select>
<div id="adminSCInputNew" style="display:none;">
<input type="text" class="inputLine inputSmall" value="" placeholder="[[=admin:msg:NewCate]]"  id="adminSCInputNewItemName" /> <input type="text" class="inputLine inputSmall" value="" placeholder="ID"  id="adminSCInputNewItemID" />
<a href='##' onclick="addCategory('[[::siteURL]]/[[::linkPrefixAdmin]]/articles/newcatenow/');"><span class="icon-disk"></span> [[=admin:btn:Add]]</a><br/>
</div>
</p>
<p>

<span class="icon-arrow-right5"></span> [[=admin:item:ATime]]<br/><input type="text" class="inputLine inputLarge" name="smt[aTime]" value="[[::aTime]]" placeholder="[[=admin:msg:ATime]]" id="aTime" /><br/>
<div id="floatATime">
<select id="timesY" class="selectLine2"></select> - <select id="timesM" class="selectLine2"></select> - <select id="timesD" class="selectLine2"></select> &nbsp; &nbsp; <br class="brATime"/><select id="timesH" class="selectLine2"> : </select> : <select id="timesMM" class="selectLine2"></select> : <select id="timesS" class="selectLine2"></select> <a href="##"><span class="icon-newspaper2" id="aTimeUp"></span></a>
</div>
<span class="adminExplain">yyyy-mm-dd hh:mm:ss</span></p>

<p class="adminCommand"><br/>
<button type="button" class="buttonLine" id="btnSubmit" onclick="saveArticle('smtForm', '[[::siteURL]]/[[::linkPrefixAdmin]]/articles/');"><span class="icon-disk"></span></button> [[=admin:btn:Save]]
<button type="button" class="buttonLine" onclick="document.getElementById('smtForm').reset(); $('#aCateURLName').val('[[::aCateURLName]]');"><span class="icon-ccw"></span></button> [[=admin:btn:Restore]]
<span class="articleOnly"><button type="button" class="buttonLine" onclick="$('#aCateURLName').val('_trash'); $('#btnSubmit').click();"><span class="icon-suitcase"></span></button> [[=admin:btn:SaveAsDraft]]</span>
<span id="btnDel"><button type="button" class="buttonLine" onclick="deleteArticle('[[::siteURL]]/[[::linkPrefixAdmin]]/articles/delete/');"><span class="icon-cross"></span></button> <span style="color: #FF2626">[[=admin:btn:Delete]]</span></span>
<p id="adminPromptError"></p><p id="adminPromptSuccess"></p>
</p>
[[::ext_adminWriter]]
</form>
<div id="adminUploadContainer" data-upurl="[[::siteURL]]/[[::linkPrefixAdmin]]/articles/getqiniuuploadpart/[[::linkConj]]CSRFCode=[[::upCSRFCode]]">
[[::adminqiniuupload]][[::adminaliyunupload]][[::admincommonupload]]
</div>

<iframe id="execPicTarget" name="execPicTarget" style="display: none;"></iframe>
<script type="text/javascript" src="[[::siteURL]]/[[::linkPrefixAdmin]]/articles/getautocomplete/"></script>
<script type="text/javascript" src="[[::siteURL]]/inc/script/main.js"></script>
<script type="text/javascript" src="[[::siteURL]]/inc/script/timers/jquery.timers.js"></script>

<script type="text/javascript">
var clearAutoID=false;
var callCustomEditor=false;
var inAutoSave=false;

if ($("#aID").val()=='')
{
	$("#aID").val('post-[[::aTime, dateFormat, YmdHi]]');
	clearAutoID=true;
}

$("#admArticles").addClass("activeNav");

$("#aID").blur(function() {
	if ($("#aID").val()=='')
	{
		$("#aID").addClass("inputLineWarn");
	}
	$("#aID").click(function() {
		$("#aID").removeClass("inputLineWarn");
	});
});

$("#aID").click(function() {
	if (clearAutoID)
	{
		$("#aID").val("");
		clearAutoID=false;
	}
});

function padAZero (i) {
	i=i<10 ? '0'+i.toString() : i;
	return i;
}
if ($("#aID").val()=='')
{
	var ddate = new Date();
	var cYYYY = ddate.getFullYear ();
	var cMM = ddate.getMonth () + 1;
	var cDD = ddate.getDate ();
	var cHH = ddate.getHours ();
	var cMMM = ddate.getMinutes ();
	var cSS = ddate.getSeconds ();
} else {
	var cYYYY = [[::aTime, dateFormat, Y]];
	var cMM = [[::aTime, dateFormat, m]];
	var cDD = [[::aTime, dateFormat, d]];
	var cHH = [[::aTime, dateFormat, H]];
	var cMMM = [[::aTime, dateFormat, i]];
	var cSS = [[::aTime, dateFormat, s]];
}
for (var i=cYYYY-20; i<cYYYY+21; i++)
{
	$('#timesY').append ('<option value="'+i+'">'+i+'</option>');
}
$('#timesY').val(cYYYY);
for (var i=1; i<13; i++)
{
	i=padAZero (i);
	$('#timesM').append ('<option value="'+i+'">'+i+'</option>');
}
cMM=padAZero (cMM);
$('#timesM').val(cMM);
for (var i=1; i<31; i++)
{
	i=padAZero (i);
	$('#timesD').append ('<option value="'+i+'">'+i+'</option>');
}
cDD=padAZero (cDD);
$('#timesD').val(cDD);
for (var i=0; i<24; i++)
{
	i=padAZero (i);
	$('#timesH').append ('<option value="'+i+'">'+i+'</option>');
}
cHH=padAZero (cHH);
$('#timesH').val(cHH);
for (var i=0; i<60; i++)
{
	i=padAZero (i);
	$('#timesMM').append ('<option value="'+i+'">'+i+'</option>');
}
cMMM=padAZero (cMMM);
$('#timesMM').val(cMMM);
for (var i=0; i<60; i++)
{
	i=padAZero (i);
	$('#timesS').append ('<option value="'+i+'">'+i+'</option>');
}
cSS=padAZero (cSS);
$('#timesS').val(cSS);

function setLeaveWarning () {
	window.onbeforeunload=function (){
		return  ("[[=admin:msg:LeaveConfirm]]");
	};
}

function clearLeaveWarning () {
	window.onbeforeunload=null;
}

$("#adminUploader").click(function() {
	$("#uploadPicFile").click();
});

if ("[[::aCateURLName]]")
{
	$("#aCateURLName").val("[[::aCateURLName]]");
}

$("#aTitle").blur(function() {
	if ($("#aTitle").val()=='')
	{
		$("#aTitle").addClass("inputLineWarn");
	} 
	else {
		if ("[[::aID]]" == '') {
			$.post ("[[::siteURL]]/admin.php/articles/getpinyin/[[::linkConj]]ajax=1", {str: $("#aTitle").val()}, function(data){
				if (data.error!=1) {
					$("#aID").val (data.returnMsg);
					clearAutoID=false;
				}
			}, 'json');
		}
	}
	$("#aTitle").click(function() {
		$("#aTitle").removeClass("inputLineWarn");
	});
});

$("#aContent").click (function () {
	setLeaveWarning ();
});

$("#adminPreview").click (function() {
	if ($('#previewArea').css("display")=="none")
	{
		if (!$('#aContent').val())
		{
			return false;
		}
		$("#adminPrevAdd").html("[[=admin:btn:EndPreview]]");
		$("#UI-loading").fadeIn(40);
		var loadURL="[[::siteURL]]/[[::linkPrefixAdmin]]/articles/getpreviewhtml/";
		$.post(loadURL+"[[::linkConj]]ajax=1&CSRFCode=[[::articleCSRFCode]]", $('#smtForm').serialize(), function(data) {
			$("#UI-loading").fadeOut(60);
			if (data.error==1) {
				alert (data.returnMsg);
			}
			else {
				$('#aContent').hide();
				$('#previewArea').html (data.returnMsg);
				$('#previewArea').show();
			}
		}, "json");
	}
	else {
		$("#adminPrevAdd").html("[[=admin:btn:StartPreview]]");
		$('#previewArea').hide();
		$('#aContent').show();
	}
});

$(function() {
	(function($) {
		$.fn
				.extend({
					insertContent : function(myValue, t) {
						var $t = $(this)[0];
						if (document.selection) { // ie
							this.focus();
							var sel = document.selection.createRange();
							sel.text = myValue;
							this.focus();
							sel.moveStart('character', -l);
							var wee = sel.text.length;
							if (arguments.length == 2) {
								var l = $t.value.length;
								sel.moveEnd("character", wee + t);
								t <= 0 ? sel.moveStart("character", wee - 2 * t
										- myValue.length) : sel.moveStart(
										"character", wee - t - myValue.length);
								sel.select();
							}
						} else if ($t.selectionStart
								|| $t.selectionStart == '0') {
							var startPos = $t.selectionStart;
							var endPos = $t.selectionEnd;
							var scrollTop = $t.scrollTop;
							$t.value = $t.value.substring(0, startPos)
									+ myValue
									+ $t.value.substring(endPos,
											$t.value.length);
							this.focus();
							$t.selectionStart = startPos + myValue.length;
							$t.selectionEnd = startPos + myValue.length;
							$t.scrollTop = scrollTop;
							if (arguments.length == 2) {
								$t.setSelectionRange(startPos - t,
										$t.selectionEnd + t);
								this.focus();
							}
						} else {
							this.value += myValue;
							this.focus();
						}
					}
				})
	})(jQuery);
});

function insertUpURLs (str)
{
	str=str.replace(/ /g, "\r\n");
	$('#aContent').insertContent (str);
	$('#adminUpAdd').html("[[=admin:btn:AddPic]]");
	$("#UI-loading").fadeOut(200);
}

function doPicUp() {
	if($("#uploadPicFile").val() != '') {
		$("#UI-loading").fadeIn(500);
		var targetURL=$('#adminUploadContainer').data('upurl');
		$.get(targetURL+"&ajax=1&fname="+encodeURI($("#uploadPicFile").val()), function (data){
			if (data.error!=1) {
				var dp = data.returnMsg.split ('<<<');
				$("#picFormToken").val (dp[0]);
				$("#picFormFname").val (dp[1]);
				$('#picForm').submit();
			}
		}, "json");
		$('#adminUpAdd').html("[[=admin:btn:Uploading]]");
	}
}

function doPicUp2() {
	if($("#uploadPicFile").val() != '') {
		$("#UI-loading").fadeIn(500);
		$('#picForm').submit();
		$('#adminUpAdd').html("[[=admin:btn:Uploading]]");
	}
}

function doPicUp3() {
	if($("#uploadPicFile").val() != '') {
		var f = document.getElementById("uploadPicFile").files;  
		var fn = f[0].name;
		$('#success_action_redirect').val("[[::siteURL]]/[[::linkPrefixAdmin]]/articles/aliyunuploader/[[::linkConj]]CSRFCode=[[::upCSRFCode]]&filename="+encodeURIComponent(fn));
		$("#UI-loading").fadeIn(500);
		$('#picForm').submit();
		$('#adminUpAdd').html("[[=admin:btn:Uploading]]");
	}
}

if ($("#originID").val()=='') {
	$("#btnDel").hide();
}

function saveArticle(formID, smtURL) {
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
		!inAutoSave && $("#UI-loading").fadeIn(500);
		var allTags=new Array();
		$(".admSingleTag i").each(function(){
			allTags.push ($(this).text());
		});
		$('#eTags').val(allTags.join(','));

		var pURL=($("#originID").val()=='')  ? "store/" : "update/";
		smtURL=smtURL+pURL+"[[::linkConj]]ajax=1&CSRFCode=[[::articleCSRFCode]]";
		if ("[[::writermode]]" == "singlepage") {
			smtURL+='&ispage=1';
		}
		if (inAutoSave) {
			smtURL+='&autosave=1';
		}
		$.post(smtURL, $('#'+formID).serialize(), function(data) {
			!inAutoSave && $("#UI-loading").fadeOut(200);
			if (data.error==1) {
				$("#adminPromptError").text (data.returnMsg);
				$("#adminPromptError").fadeIn(400).delay(1500).fadeOut(600);
				$('#eTags').val('');
			}
			else {
				clearLeaveWarning ();
				if ($('#aCateURLName').val()!='_trash' && !inAutoSave)
				{
					window.location="[[::writermode]]" == "article" ? "[[::siteURL]]/[[::linkPrefixArticle]]/"+$("#aID").val()+"/" : "[[::siteURL]]/[[::linkPrefixPage]]/"+$("#aID").val()+"/";
				}
				else
				{
					$("#adminPromptSuccess").text (data.returnMsg);
					$("#adminPromptSuccess").fadeIn(400).delay(1500).fadeOut(600);
					$("#originID").val($("#aID").val());
				}
				inAutoSave && $("#originID").val($("#aID").val());
				inAutoSave = false;
			}
		}, "json");

	}
}

function deleteArticle (smtURL)
{
	if (confirm("[[=admin:msg:Delete]]"))
	{
		clearLeaveWarning ();
		window.location=smtURL+"[[::linkConj]]aID="+$("#originID").val()+"&CSRFCode=[[::articleCSRFCode]]";
	}
}

$("<link>").attr({rel:"stylesheet", type:"text/css", href: "[[::siteURL]]/inc/script/autocomplete/jquery.autocomplete.css"}).appendTo("head");
$("<sc"+"ript>"+"</sc"+"ript>").attr({src: "[[::siteURL]]/inc/script/autocomplete/jquery.autocomplete.min.js"}).appendTo("head");

if ($('#eTags').val())
{
	$.each ($('#eTags').val().split(','), function(index, value) {
		$("#taghint").append('<span class="admSingleTag" onclick="$(this).remove();"><i>'+value+'</i><span class="icon-cross admSingleTagDel"></span></span>');
	});
	$('#eTags').val('');
}


$('#eTags').AutoComplete({
'data': lastTags,
'ajaxDataType': 'json',
'afterSelectedHandler': function(data) {
	$("#taghint").append('<span class="admSingleTag" onclick="$(this).remove();"><i>'+data.value+'</i><span class="icon-cross admSingleTagDel"></span></span>');
	$('#eTags').val('');
}
});


$('#eTags').keyup (function(event) {
	if (event.keyCode==188)
	{
		$("#taghint").append('<span class="admSingleTag" onclick="$(this).remove();"><i>'+$('#eTags').val().slice(0, -1)+'</i><span class="icon-cross admSingleTagDel"></span></span>');
		$('#eTags').val('');
	}
	if (event.keyCode==191 || event.keyCode==220)
	{
		$('#eTags').val($('#eTags').val().slice(0, -1));
	}
});

if ("[[::aID]]")
{
	$("#aID").attr ('readonly', 'readonly');
	if ("[[::writermode]]" == "singlepage") {
		$("#gotoAID").html ('<a href="[[::siteURL]]/[[::linkPrefixPage]]/[[::aID]]/" title="[[=admin:msg:Open]]" target="_blank"><span class="icon-export"></span></a>');
	} else {
		$("#gotoAID").html ('<a href="[[::siteURL]]/[[::linkPrefixArticle]]/[[::aID]]/" title="[[=admin:msg:Open]]" target="_blank"><span class="icon-export"></span></a>');
	}
	if ($("#aCateURLName").val()=="_trash") {
		$("#gotoAID").hide();
	}
}

$("#adminGeoLoc").click(function() {
	var URLSrc="http://api.map.baidu.com/geocoder/v2/?ak=NTbCXsEXUnd2BczNRlVdOBGP&output=json&pois=0&callback=insertGeoLoc&location=";

	if(navigator.geolocation){
		navigator.geolocation.getCurrentPosition (
			function(p){
				var latitude = p.coords.latitude;
				var longitude = p.coords.longitude;
				URLSrc=URLSrc+latitude+','+longitude;
				$("<sc"+"ript>"+"</sc"+"ript>").attr({src: URLSrc}).appendTo("head");
			},
			function(e){
				var msg = e.code+"\n"+e.message;
			}
		);
	}
});

function insertGeoLoc (data) {
	var str="\r\n\r\n!~!"+data.result.business+' ('+data.result.addressComponent.city+' '+data.result.addressComponent.district+")[location]";
	$('#aContent').insertContent (str);
}

[[::ext_customEditor]]

if (!callCustomEditor)
{
	$("<link>").attr({rel:"stylesheet", type:"text/css", href: "[[::siteURL]]/inc/script/editor/themes/default/default.css"}).appendTo("head");
	$("<sc"+"ript>"+"</sc"+"ript>").attr({src: "[[::siteURL]]/inc/script/editor/jquery.markbar.js"}).appendTo("head");
	$(function() {
		$('#aContent').markbar();
	});
}

$('#aTime').click (function (){
	$('#floatATime').toggle(500);
});
$('#aTimeUp').click (function (){
	var uYYYY=$('#timesY').val();
	var uMM=$('#timesM').val();
	var uDD=$('#timesD').val();
	var uHH=$('#timesH').val();
	var uMMM=$('#timesMM').val();
	var uSS=$('#timesS').val();
	$('#aTime').val(uYYYY+'-'+uMM+'-'+uDD+' '+uHH+':'+uMMM+':'+uSS);
	$('#floatATime').hide(500);
});

$('#aCateURLName').change (function() {
	if ($('#aCateURLName').val() == '<new>') {
		$('#adminSCInputNew').show(300);
	} else {
		$('#adminSCInputNew').hide();
	}
});

function addCategory(smtURL) {
	if ($("#adminSCInputNewItemName").val()=='')
	{
		alert ("[[=admin:msg:ErrorCorrection]]");
		$("#adminSCInputNewItemName").focus();
		return false;
	}
	if ($("#adminSCInputNewItemID").val()=='')
	{
		alert ("[[=admin:msg:ErrorCorrection]]");
		$("#adminSCInputNewItemID").focus();
		return false;
	}

	var newList=$("#adminSCInputNewItemID").val()+'='+$("#adminSCInputNewItemName").val();
	var nList=newList.split('=');
	var smtURL=smtURL+"[[::linkConj]]ajax=1&CSRFCode=[[::cateCSRFCode]]";	
	var sVal=encodeURI("smt[aCateURLName]="+nList[0]+"&smt[aCateDispName]="+nList[1]);
	$.post(smtURL, sVal, function(data) {
		if (data.error==1) {
			alert (data.returnMsg);
		}
		else {
			$("#adminSCInputNewItemID").val('');
			$("#adminSCInputNewItemName").val('');
			$("#adminSCInputNew").hide();
			$('#aCateURLName').prepend('<option value="'+nList[0]+'">'+nList[1]+'</option>');
			$('#aCateURLName').val(nList[0]);
		}
	}, "json");
}

if ("[[::writermode]]" == "singlepage") {
	$('.articleOnly').hide();
} else {
	$('.spOnly').hide();
}

$("#tplSel").change (function() {
	var selTpl = $("#tplSel").val();
	if (selTpl == '')
	{
	} else {
		lightboxLoader ("[[::siteURL]]/[[::linkPrefixAdmin]]/articles/loadtpl/[[::linkConj]]tpl="+selTpl);
		$("#tplSel").val('');
	}
});

function autosave () {
	if ($("#aTitle").val() != '' && $("#aContent").val() != '' && "[[::writermode]]" != "singlepage")
	{
		inAutoSave = true;
		saveArticle('smtForm', '[[::siteURL]]/[[::linkPrefixAdmin]]/articles/');
	}
}
if ("[[::autoSave]]" == '1') {
	$('body').everyTime('60s', autosave);
}

</script>

</div>
[[::ext_adminWriterEnding]]