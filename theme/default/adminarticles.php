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
<h2><span class="icon-list"></span> [[=admin:sect:Articles]]<span class="adminSANew"><a href='[[::siteURL]]/[[::linkPrefixAdmin]]/articles/new/[[::linkConj]]CSRFCode=[[::newCSRFCode]]'><span class="icon-plus2"></span> [[=admin:btn:NewArticle]]</a> <a href='##' onclick="shBatch();"><span class="icon-wrench"></span> [[=admin:btn:Batch]]</a> <a href='##' onclick="shSearch();"><span class="icon-help"></span> [[=page:Search]]</a></span></h2> 
<div id="adminSAB"><a href='##' onclick="shSelAll();">[[=admin:btn:SelectAll]]</a> &nbsp; <a href='##' onclick="shDeSelAll();">[[=admin:btn:DeSelectAll]]</a> &nbsp; <a href='##' onclick="shDel();">[[=admin:btn:Delete]]</a> &nbsp; <a href='##' onclick="shDraft();">[[=admin:btn:MoveDraft]]</a></div>
<div id="adminSAS"><input type="text" id='eTags' class="inputLine inputMiddle" value="[[::aTags]]" /><div id="taghint"></div></div>
<p>
<ul id="artList">
[[::loop, adminarticlelist]]<li class="adminSingleArticle adminSAL" title="[[=admin:msg:Select]]" data-aid="[[::aID]]"><a href="[[::siteURL]]/[[::linkPrefixArticle]]/[[::aID]]/" title="[[=admin:msg:Open]]"><span class="icon-export"></span></a> <span class="adminSAT" data-aid="[[::aID]]" title="[[=admin:msg:Modify]]">[[::aTitle]]</span> <span class="adminSADate">[[::aTime]]</span> </li>
[[::/loop]]
</ul>
</p>
<br/>
<div id="adminSAPage">[[::pagination]]</div>



<h2><span class="icon-archive"></span> [[=admin:sect:Categories]]<span class="adminSANew"><a href='##' onclick='createEdit();'><span class="icon-plus2"></span> [[=admin:btn:NewCate]]</a> <a href='##' onclick="saveCategoryChanges('[[::siteURL]]/[[::linkPrefixAdmin]]/articles/savecategories/');"><span class="icon-disk"></span> [[=admin:btn:Save]]</a></span></h2>
<p><ul class="adminCateList" id="adminCateList">
[[::loop, admincatelist]]
<li class="adminSingleArticle adminSCL" data-cid="[[::aCateURLName]]" id="adminSCL-[[::aCateURLName]]">
<a href="##" onclick='$("#adminSCL-[[::aCateURLName]]").remove();'><span class="icon-cross3"></span></a> <a href="##" title="[[=admin:msg:Up]]" class="adminSCLUp" data-cid="[[::aCateURLName]]"><span class="icon-arrow-up3"></span></a> <a href="##" title="[[=admin:msg:Down]]" class="adminSCLDown" data-cid="[[::aCateURLName]]"><span class="icon-arrow-down4"></span></a> 
<span id="adminSCLine-[[::aCateURLName]]" class="adminSCLine" data-cid="[[::aCateURLName]]" data-cname="[[::aCateDispName]]" data-ctheme="[[::aCateTheme]]">[[::aCateDispName]]</span>
</li>
[[::/loop]]
</ul>
<span id="adminSCInputNew">

<input type="text" class="inputLine inputSmall" value="" placeholder="[[=admin:msg:NewCate]]"  id="adminSCInputNewItemName" /> <input type="text" class="inputLine inputSmall" value="" placeholder="ID"  id="adminSCInputNewItemID" />

<select id="adminSCInputNewItemTheme" class="selectLine"><option value="">[[=admin:opt:NoCustomTheme]]</option>
[[::loop, themeList]]<option value="[[::themeDir]]">[[=admin:opt:CustomTheme]] [[::themeName]]</option>[[::/loop]]
</select><br/>
<span id="adminSCInputNewItemGo"><a href='##'><span class="icon-disk"></span> OK</a></span>
<a href="##" onclick='$("#adminSCInputNew").fadeOut();'><span class="icon-arrow-up4"></span> [[=admin:msg:Close]]</a>
<br/> </span>
<span class="adminExplain">[[=admin:msg:Categories]]</span></p>
<p class="adminCommand">
<p id="adminPromptError"></p><p id="adminPromptSuccess"></p>
</p>
[[::ext_adminArticles]]

<p><br/></p>
<h2><span class="icon-suitcase"></span> [[=admin:item:TrashBin]]</h2> 
<p>
<ul id="draftList">
[[::loop, admindraftlist]]<li class="adminSingleArticle adminSAL" data-aid="[[::aID]]"><a href="#"><span class="icon-popup"></span></a> <span class="adminSAT" data-aid="[[::aID]]" title="[[=admin:msg:Modify]]">[[::aTitle]]</span> <span class="adminSADate">[[::aTime]]</span> </li>
[[::/loop]]
</ul>
</p>

<p><br/></p>
<h2><span class="icon-newspaper2"></span> [[=admin:sect:Pages]]<span class="adminSANew"><a href='[[::siteURL]]/[[::linkPrefixAdmin]]/articles/newpage/[[::linkConj]]CSRFCode=[[::newCSRFCode]]'><span class="icon-plus2"></span> [[=admin:btn:NewPage]]</a></span></h2> 
<p>
<ul id="spList">
[[::loop, adminsinglepagelist]]<li class="adminSingleArticle adminSAL" data-aid="[[::aID]]"><a href="[[::siteURL]]/[[::linkPrefixPage]]/[[::aID]]/" title="[[=admin:msg:Open]]"><span class="icon-export"></span></a> <span class="adminSAT" data-aid="[[::aID]]" title="[[=admin:msg:Modify]]">[[::aTitle]]</span> <span class="adminSADate">[[::aTime]]</span> </li>
[[::/loop]]
</ul>
</p>

<script src="[[::siteURL]]/inc/script/html5sortable/html.sortable.min.js"></script>
<script type="text/javascript" src="[[::siteURL]]/[[::linkPrefixAdmin]]/articles/gettitlelist/"></script>

<script type="text/javascript">
sortable('.adminCateList');

$(".adminSAT").click(function(){
	var aID=$(this).data("aid");
	window.location="[[::siteURL]]/[[::linkPrefixAdmin]]/articles/modify/[[::linkConj]]aID="+aID+"&CSRFCode=[[::oldCSRFCode]]";
});
$("#artList .adminSAL").click(function(){
	$(this).toggleClass("adminSAChosen");
});
$("#admArticles").addClass("activeNav");

function shBatch() {
	$('#adminSAB').fadeToggle (200);
}
function shSearch() {
	$('#adminSAS').fadeToggle (200);
}

function shSelAll() {
	$("#artList .adminSAL").addClass("adminSAChosen");
}
function shDeSelAll() {
	$("#artList .adminSAL").removeClass("adminSAChosen");
}
function shDel() {
	if ($(".adminSAChosen").length>0) {
		if (confirm("[[=admin:msg:DeleteBatch]]")) {
			if (confirm("[[=admin:msg:DeleteBatch2]]")) {
				var aID=new Array();
				$(".adminSAChosen").each (function () {
					aID.push($(this).data("aid"));
				});
				var smtURL="[[::siteURL]]/[[::linkPrefixAdmin]]/articles/batchdel/[[::linkConj]]ajax=1&"+encodeURI("aID="+aID.join('<'))+"&CSRFCode=[[::oldCSRFCode]]";
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

$("<link>").attr({rel:"stylesheet", type:"text/css", href: "[[::siteURL]]/inc/script/autocomplete/jquery.autocomplete.css"}).appendTo("head");
$("<sc"+"ript>"+"</sc"+"ript>").attr({src: "[[::siteURL]]/inc/script/autocomplete/jquery.autocomplete.min.js"}).appendTo("head");

$('#eTags').AutoComplete({
'data': allTitles,
'ajaxDataType': 'json',
'afterSelectedHandler': function(data) {
	var aID=allFullList[data.value];
	if (aID != 'undefined')
	{
		window.location="[[::siteURL]]/[[::linkPrefixAdmin]]/articles/modify/[[::linkConj]]aID="+aID+"&CSRFCode=[[::oldCSRFCode]]";
	}
}
});

</script>
</form>

</div>

[[::ext_adminArticlesEnding]]