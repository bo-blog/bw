<?php
/**
*
* @link http://bw.bo-blog.com
* @copyright (c) 2017 bW Development Team
* @license MIT
*/
if (!defined ('P')) {
	die ('Access Denied.');
}

?>

<div class="adminArea">
<form id="smtForm" action="post">
<h2><span class="icon-list"></span> [[=admin:item:CommentOpt]] <span class="adminSANew"><a href='##' onclick="shBatch();"><span class="icon-wrench"></span> [[=admin:btn:Batch]]</a> <a href='[[::siteURL]]/[[::linkPrefixAdmin]]/articles/[[::linkConj]]CSRFCode=[[::navCSRFCode]]'><span class="icon-archive"></span> [[=admin:sect:Articles]]</a></span></h2>
<div id="adminSAB" style="display: block">
<span class="go-yes">
<a href='##' onclick="shSelAll();">[[=admin:btn:SelectAll]]</a>  &nbsp; <a href='##' onclick="shDeSelAll();">[[=admin:btn:DeSelectAll]]</a> &nbsp; <a href='##' onclick="shBlock();">[[=page:BlockItem]]</a> &nbsp; <a href='##' onclick="shBlockIP();">[[=page:BlockIP]]</a>
</span>
<span class="go-no">
<a href='##' onclick="shSelAll();">[[=admin:btn:SelectAll]]</a>  &nbsp; <a href='##' onclick="shDeSelAll();">[[=admin:btn:DeSelectAll]]</a> &nbsp; <a href='##' onclick="shDel();">[[=admin:btn:Delete]]</a> &nbsp; <a href='##' onclick="shRestore();">[[=admin:btn:Restore]]</a>
</span> &nbsp;
<select id="filterby" class="selectLine inputLine inputMiddle" style="width: 110px; font-size: 12px"><option value="yes">[[=admin:opt:Blocked]]</option><<option value="no">[[=admin:opt:NonBlocked]]</option></select>
</div>
<p>
<ul id="artList">
[[::loop, comments]]
<li class="adminSingleArticle adminSAL" title="[[=admin:msg:Select]]" data-comid="[[::comID]]" data-aid="[[::comArtID]]" style="margin: 10px auto 10px auto">
<span class="adminSAT"><strong style="font-size: 16px" title="[[::comIP1]]">[[::comName]]</strong>  [[::comTime, dateFormat, Y-m-d]] [[=admin:msg:Regarding]] <em>[[::aTitle]]</em> <br/>[[::comContent]]<br/></span></li>
[[::/loop]]
</ul>
</p>
<br/>
<div id="adminSAPage">[[::pagination]]</div>

[[::ext_adminComments]]



<script type="text/javascript">

$("#filterby").val('[[::filtered]]');
$(".go-[[::filtered]]").hide ();

$("#filterby").change (function () {
	window.location = "[[::siteURL]]/[[::linkPrefixAdmin]]/comments/comments/[[::linkConj]]blocked=" + $("#filterby").val() + "&CSRFCode=[[::navCSRFCode]]";
});

$("#artList .adminSAL").click(function(){
	$(this).toggleClass("adminSAChosen");
});
$("#admArticles").addClass("activeNav");

function shBatch() {
	$('#adminSAB').fadeToggle (200);
}
function shSelAll() {
	$("#artList .adminSAL").addClass("adminSAChosen");
}
function shDeSelAll() {
	$("#artList .adminSAL").removeClass("adminSAChosen");
}

function shBlock () {
	if ($(".adminSAChosen").length>0) {
		if (confirm("[[=admin:msg:ConfirmBlock]]")) {
			var comID=new Array();
			var comArtID=new Array();
			$(".adminSAChosen").each (function () {
				comID.push($(this).data("comid"));
				comArtID.push($(this).data("aid"));
			});
			var smtURL="[[::siteURL]]/[[::linkPrefixAdmin]]/comments/batchblockitem/[[::linkConj]]ajax=1&CSRFCode=[[::navCSRFCode]]";
			$.post(smtURL, {comID: comID.join('<'), comArtID: comArtID.join('<')}, function(data) {
				if (data.error==1) {
					alert (data.returnMsg);
				}
				else {
					window.location.reload();
				}
			}, "json");
		}
	}
}

function shBlockIP () {
	if ($(".adminSAChosen").length>0) {
		if (confirm("[[=js:BlockIP]]")) {
			var comID=new Array();
			$(".adminSAChosen").each (function () {
				comID.push($(this).data("comid"));
			});
			var smtURL="[[::siteURL]]/[[::linkPrefixAdmin]]/comments/batchblockip/[[::linkConj]]ajax=1&CSRFCode=[[::navCSRFCode]]";
			$.post(smtURL, {comID: comID.join('<')}, function(data) {
				if (data.error==1) {
					alert (data.returnMsg);
				}
				else {
					window.location.reload();
				}
			}, "json");
		}
	}
}

function shDel() {
	if ($(".adminSAChosen").length>0) {
		if (confirm("[[=admin:msg:ConfirmDeleteComments]]")) {
			var comID=new Array();
			$(".adminSAChosen").each (function () {
				comID.push($(this).data("comid"));
			});
			var smtURL="[[::siteURL]]/[[::linkPrefixAdmin]]/comments/batchdel/[[::linkConj]]ajax=1&"+encodeURI("comID="+comID.join('<'))+"&CSRFCode=[[::navCSRFCode]]";
			$.post(smtURL, null, function(data) {
				if (data.error==1) {
					alert (data.returnMsg);
				}
				else {
					window.location.reload();
				}
			}, "json");
		}
	}
}

function shRestore() {
	if ($(".adminSAChosen").length>0) {
		if (confirm("[[=admin:msg:ConfirmUnBlock]]")) {
			var comID=new Array();
			var comArtID=new Array();
			$(".adminSAChosen").each (function () {
				comID.push($(this).data("comid"));
				comArtID.push($(this).data("aid"));
			});
			var smtURL="[[::siteURL]]/[[::linkPrefixAdmin]]/comments/unblock/[[::linkConj]]ajax=1&CSRFCode=[[::navCSRFCode]]";
			$.post(smtURL, {comID: comID.join('<'), comArtID: comArtID.join('<')}, function(data) {
				if (data.error==1) {
					alert (data.returnMsg);
				}
				else {
					window.location.reload();
				}
			}, "json");
		}
	}
}

function shDraft() {
	if ($(".adminSAChosen").length>0) {
		var aID=new Array();
		$(".adminSAChosen").each (function () {
			aID.push($(this).data("aid"));
		});
		var smtURL="[[::siteURL]]/[[::linkPrefixAdmin]]/articles/batchdraft/[[::linkConj]]ajax=1&"+encodeURI("aID="+aID.join('<'))+"&CSRFCode=[[::oldCSRFCode]]";
		$.post(smtURL, null, function(data) {
			if (data.error==1) {
				alert (data.returnMsg);
			}
			else {
				window.location.reload();
			}
		}, "json");
	}
}
if ($("#draftList .adminSAL").length==0) {
	$("#draftList").html("<li class=\"adminSingleArticle adminSAL\">[[=admin:msg:EmptyTrashBin]]</li>");
}
if ($("#spList .adminSAL").length==0) {
	$("#spList").html("<li class=\"adminSingleArticle adminSAL\">[[=admin:msg:EmptySinglePage]]</li>");
}
function bindUpDown () {
	$('.adminSCLUp').click(function() {
		var cID="adminSCL-"+$(this).data("cid");
		if ($("#"+cID).prev().length>0)
		{
			$("#"+cID).prev().before($("#"+cID));
		}
	});

	$('.adminSCLDown').click(function() {
		var cID="adminSCL-"+$(this).data("cid");
		if ($("#"+cID).next().length>0)
		{
			$("#"+cID).next().after($("#"+cID));
		}
	});
	$('.adminSCLine').click(function() {
		var cID=$(this).data("cid");
		var cname=$(this).data("cname");
		var ctheme=$(this).data("ctheme");
		$("#adminSCInputNew").fadeIn();
		$("#adminSCInputNewItemName").val(cname);
		$("#adminSCInputNewItemTheme").val(ctheme);
		$("#adminSCInputNewItemID").val(cID);
		$("#adminSCInputNewItemID").prop("readonly", "readonly");
		$("#adminSCInputNewItemGo").unbind("click");
		$("#adminSCInputNewItemGo").click(function () {
			$('#adminSCLine-'+cID).data("cname", $("#adminSCInputNewItemName").val());
			$('#adminSCLine-'+cID).data("ctheme", $("#adminSCInputNewItemTheme").val());
			$('#adminSCLine-'+cID).html($("#adminSCInputNewItemName").val());
			$("#adminSCInputNew").fadeOut();
		});
	});
}
bindUpDown ();


$("#adminSCInputNew").hide();

function addCategory(smtURL) {
	if ($("#adminSCInputNewItemName").val()=='')
	{
		alert ("[[=admin:msg:ErrorCorrection]]");
		return false;
	}

	var newList=$("#adminSCInputNewItemID").val()+'='+$("#adminSCInputNewItemName").val()+'='+$("#adminSCInputNewItemTheme").val();
	var nList=newList.split('=');
	var smtURL=smtURL+"[[::linkConj]]ajax=1&CSRFCode=[[::cateCSRFCode]]";
	var sVal=encodeURI("smt[aCateURLName]="+nList[0]+"&smt[aCateDispName]="+nList[1]+"&smt[aCateTheme]="+nList[2]);
	$.post(smtURL, sVal, function(data) {
		if (data.error==1) {
			$("#adminPromptError").text (data.returnMsg);
			$("#adminPromptError").fadeIn(400).delay(1500).fadeOut(600);
		}
		else {
			$("#adminCateList").append(data.returnMsg);
			$( '.adminSCLUp').unbind("click");
			$( '.adminSCLDown').unbind("click");
			$( '.adminSCLine').unbind("click");
			bindUpDown ();
			$("#adminSCInputNewItem").val('');
			$("#adminSCInputNew").hide();
		}
	}, "json");
}

function saveCategoryChanges(smtURL) {
	$("#UI-loading").fadeIn(500);
	var finalList='';
	$('.adminSCL').each(function(){
		var cID=$(this).data("cid");
		finalList+=encodeURI("smt["+cID+"]="+$("#adminSCLine-"+cID).data("cname")+"&smt2["+cID+"]="+$("#adminSCLine-"+cID).data("ctheme"))+"&";
	});

	var smtURL=smtURL+"[[::linkConj]]ajax=1&CSRFCode=[[::cateCSRFCode]]";

	$.post(smtURL, finalList, function(data) {
		$("#UI-loading").fadeOut(200);
		if (data.error==1) {
			$("#adminPromptError").text (data.returnMsg);
			$("#adminPromptError").fadeIn(400).delay(1500).fadeOut(600);
		}
		else {
			$("#adminPromptSuccess").text (data.returnMsg);
			$("#adminPromptSuccess").fadeIn(400).delay(1500).fadeOut(600);
			$("#adminSCInputNew").hide();
		}
	}, "json");
}

function createEdit () {
	$("#adminSCInputNew").fadeIn();
	$("#adminSCInputNewItemName").val('');
	$("#adminSCInputNewItemTheme").val('');
	$("#adminSCInputNewItemID").val('');
	$("#adminSCInputNewItemID").removeAttr("readonly");
		$("#adminSCInputNewItemGo").unbind("click");
		$("#adminSCInputNewItemGo").click(function () {
			addCategory('[[::siteURL]]/[[::linkPrefixAdmin]]/articles/validatecategory/');
		});
}

</script>
</form>

</div>

[[::ext_adminCommentsEnding]]
