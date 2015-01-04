<?php
//Copyright: Byke

?>

<div class="adminArea">
<form id="smtForm" action="post">
<input type="hidden" name="smt[originID]" id="originID" value="[[::aID]]" />
<h2><span class="icon-book2"></span> [[=admin:sect:Writer]]</h2>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:ATitle]]<br/><input type="text" class="inputLine inputLarge" name="smt[aTitle]" value="[[::aTitle]]" id="aTitle" />
</p>

<p>
<span class="icon-arrow-right5"></span> [[=admin:item:AID]]<br/><input type="text" class="inputLine inputLarge" name="smt[aID]" value="[[::aID]]" placeholder="[[=admin:msg:AID]]" id="aID" /><br/><span class="adminExplain">[[=admin:msg:AID2]]</span>
</p>

<p>
<span class="icon-arrow-right5"></span> [[=admin:item:AContent]] <span class="adminUploader"><a href="##" id="adminUploader"><span class="icon-pictures"> </span><span id="adminUpAdd">[[=admin:btn:AddPic]]</span></a> <a href="##" id="adminGeoLoc"><span class="icon-location"> </span><span id="adminGeoLocAdd">[[=admin:btn:GeoLoc]]</span></a></span><br/><textarea type="text" class="inputLine inputLarge textareaLine" name="smt[aContent]" id="aContent" />[[::aContent]]</textarea>
</p>

<p>
<span class="icon-arrow-right5"></span> [[=admin:item:SetTag]]<br/><input type="text" id='eTags' class="inputLine inputLarge" name="smt[aTags]" value="[[::aTags]]" placeholder="[[=admin:msg:SetTag]]" /><div id="taghint"></div></p>

<p>
<span class="icon-arrow-right5"></span> [[=admin:item:ACate]]<br/>
<select name="smt[aCateURLName]" id="aCateURLName" class="selectLine">
[[::loop, admincatelist]]<option value="[[::aCateURLName]]">[[::aCateDispName]]</option>[[::/loop]]
</select>
</p>
<script type="text/javascript">
</script>
<p>

<span class="icon-arrow-right5"></span> [[=admin:item:ATime]]<br/><input type="text" class="inputLine inputLarge" name="smt[aTime]" value="[[::aTime]]" placeholder="[[=admin:msg:ATime]]" /></p>

<p class="adminCommand"><br/>
<button type="button" class="buttonLine" id="btnSubmit" onclick="saveArticle('smtForm', '[[::siteURL]]/admin.php/articles/');"><span class="icon-disk"></span></button> [[=admin:btn:Save]]
<button type="button" class="buttonLine" onclick="document.getElementById('smtForm').reset(); $('#aCateURLName').val('[[::aCateURLName]]');"><span class="icon-ccw"></span></button> [[=admin:btn:Restore]]
<span id="btnDel"><button type="button" class="buttonLine" onclick="deleteArticle('[[::siteURL]]/admin.php/articles/delete/');"><span class="icon-cross"></span></button> <span style="color: #FF2626">[[=admin:btn:Delete]]</span></span>
<p id="adminPromptError"></p><p id="adminPromptSuccess"></p>
</p>
[[::ext_adminWriter]]
</form>
<div id="adminUploadContainer" data-upurl="[[::siteURL]]/admin.php/articles/getqiniuuploadpart/?CSRFCode=[[::upCSRFCode]]">
[[::adminqiniuupload]][[::admincommonupload]]
</div>
<iframe id="execPicTarget" name="execPicTarget" style="display: none;"></iframe>
<script type="text/javascript">
if ($("#aID").val()=='')
{
	var myDate = new Date();
	$("#aID").val('post-[[::aTime, dateFormat, YmdHi]]');
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
	$("#aTitle").click(function() {
		$("#aTitle").removeClass("inputLineWarn");
	});
});

$("#aContent").click (function () {
	setLeaveWarning ();
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
	var targetURL=$('#adminUploadContainer').data('upurl');

	$.get(targetURL+"&ajax=1", function (data){
		if (data.error!=1) {
			$("#adminUploadContainer").html (data.returnMsg);
		}
	}, "json");
	$("#UI-loading").fadeOut(200);
}

function doPicUp() {
	if($("#uploadPicFile").val() != '') {
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
		$("#UI-loading").fadeIn(500);
		var allTags=new Array();
		$(".admSingleTag i").each(function(){
			allTags.push ($(this).text());
		});
		$('#eTags').val(allTags.join(','));

		var pURL=($("#originID").val()=='')  ? "store/" : "update/"
		/*
		$('#'+formID).attr("action", smtURL+pURL);
		$('#'+formID).submit();
		*/
		$.post(smtURL+pURL+"?ajax=1&CSRFCode=[[::articleCSRFCode]]", $('#'+formID).serialize(), function(data) {
			$("#UI-loading").fadeOut(200);
			if (data.error==1) {
				$("#adminPromptError").text (data.returnMsg);
				$("#adminPromptError").fadeIn(400).delay(1500).fadeOut(600);
				$('#eTags').val('');
			}
			else {
				clearLeaveWarning ();
				if ($("#originID").val()=='')
				{
					window.location="[[::siteURL]]/[[::linkPrefixArticle]]/"+$("#aID").val()+"/";
				}
				else
				{
					$("#adminPromptSuccess").text (data.returnMsg);
					$("#adminPromptSuccess").fadeIn(400).delay(1500).fadeOut(600);
					$("#originID").val($("#aID").val());
				}
			}
		}, "json");

	}
}

function deleteArticle (smtURL)
{
	if (confirm("[[=admin:msg:Delete]]"))
	{
		clearLeaveWarning ();
		window.location=smtURL+"?aID="+$("#originID").val()+"&CSRFCode=[[::articleCSRFCode]]";
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

<!--
$('#eTags').AutoComplete({
'data': "[[::siteURL]]/admin.php/articles/getautocomplete/",
'ajaxDataType': 'json',
'afterSelectedHandler': function(data) {
	$("#taghint").append('<span class="admSingleTag" onclick="$(this).remove();"><i>'+data.value+'</i><span class="icon-cross admSingleTagDel"></span></span>');
	$('#eTags').val('');
}
});
-->

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

if ($("#aID").val())
{
	$("#aID").attr ('readonly', 'readonly');
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

if ($(window).width()>800)
{
	$("<link>").attr({rel:"stylesheet", type:"text/css", href: "[[::siteURL]]/inc/script/editor/themes/default/default.css"}).appendTo("head");
	$("<sc"+"ript>"+"</sc"+"ript>").attr({src: "[[::siteURL]]/inc/script/editor/jquery.markbar.js"}).appendTo("head");

	$(function() {
		$('#aContent').markbar();
	});
}

</script>

</div>
[[::ext_adminWriterEnding]]