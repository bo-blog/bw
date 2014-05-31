<?php
//Copyright: Byke

?>

<div class="adminArea">
<form id="smtForm" action="post">
<input type="hidden" name="smt[originID]" id="originID" value="[[::aID]]" />
<h2><span class="icon-pie"></span> [[=admin:sect:Writer]]</h2>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:ATitle]]<br/><input type="text" class="inputLine inputLarge" name="smt[aTitle]" value="[[::aTitle]]" id="aTitle" />
</p>
<script type="text/javascript">

window.onbeforeunload=function (){
	return  ("[[=admin:msg:LeaveConfirm]]");
};

function clearLeaveWarning () {
	window.onbeforeunload=null;
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
</script>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:AID]]<br/><input type="text" class="inputLine inputLarge" name="smt[aID]" value="[[::aID]]" placeholder="[[=admin:msg:AID]]" id="aID" /><br/><span class="adminExplain">[[=admin:msg:AID2]]</span>
</p>
<script type="text/javascript">
$("#aID").blur(function() {
	if ($("#aID").val()=='')
	{
		$("#aID").addClass("inputLineWarn");
	}
	$("#aID").click(function() {
		$("#aID").removeClass("inputLineWarn");
	});
});
</script>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:AContent]] <span class="adminUploader"><a href="##" id="adminUploader"><span class="icon-pictures"> </span><span id="adminUpAdd">[[=admin:btn:AddPic]]</span></a></span><br/><textarea type="text" class="inputLine inputLarge textareaLine" name="smt[aContent]" id="aContent" />[[::aContent]]</textarea>
</p>
<script type="text/javascript">
$("#adminUploader").click(function() {
	$("#uploadPicFile").click();
});
</script>


<p>
<span class="icon-arrow-right5"></span> [[=admin:item:ACate]]<br/>
<select name="smt[aCateURLName]" id="aCateURLName" class="selectLine">
[[::admincatelist]]
</select>
</p>
<script type="text/javascript">
if ("[[::aCateURLName]]")
{
	$("#aCateURLName").val("[[::aCateURLName]]");
}
</script>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:ATime]]<br/><input type="text" class="inputLine inputLarge" name="smt[aTime]" value="[[::aTime]]" placeholder="[[=admin:msg:ATime]]" /></p>



<p class="adminCommand"><br/>
<button type="button" class="buttonLine" id="btnSubmit" onclick="saveArticle('smtForm', '[[::siteURL]]/admin.php/articles/');"><span class="icon-disk"></span></button> [[=admin:btn:Save]]
<button type="button" class="buttonLine" onclick="document.getElementById('smtForm').reset(); $('#aCateURLName').val('[[::aCateURLName]]');"><span class="icon-ccw"></span></button> [[=admin:btn:Restore]]
<span id="btnDel"><button type="button" class="buttonLine" onclick="deleteArticle('[[::siteURL]]/admin.php/articles/delete/');"><span class="icon-cross"></span></button> <span style="color: #FF2626">[[=admin:btn:Delete]]</span></span>
<p id="adminPromptError"></p><p id="adminPromptSuccess"></p>
</p>
<script type="text/javascript">
$("#admArticles").addClass("activeNav");

</script>
</form>
<div id="adminUploadContainer" data-upurl="[[::siteURL]]/admin.php/articles/getqiniuuploadpart/">
[[::adminqiniuupload]][[::admincommonupload]]
</div>
<iframe id="execPicTarget" name="execPicTarget" style="display: none;"></iframe>
<script type="text/javascript">
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

	$.get(targetURL+"?ajax=1", function (data){
		if (data.error!=1) {
			$("#adminUploadContainer").html (data.returnMsg);
		}
	}, "json");
	$("#UI-loading").fadeOut(200);
}

/*
$("#uploadPicFile").change(function(){
	doPicUp();
});
*/

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
		var pURL=($("#originID").val()=='')  ? "store/" : "update/"
		/*
		$('#'+formID).attr("action", smtURL+pURL);
		$('#'+formID).submit();
		*/
		$.post(smtURL+pURL+"?ajax=1", $('#'+formID).serialize(), function(data) {
			$("#UI-loading").fadeOut(200);
			if (data.error==1) {
				$("#adminPromptError").text (data.returnMsg);
				$("#adminPromptError").fadeIn(400).delay(1500).fadeOut(600);
			}
			else {
				if ($("#originID").val()=='')
				{
					clearLeaveWarning ();
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
		window.location=smtURL+"?aID="+$("#originID").val();
	}
}
</script>

</div>

