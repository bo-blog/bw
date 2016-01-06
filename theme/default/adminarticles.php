<?php
//Copyright: Byke

?>

<div class="adminArea">
<form id="smtForm" action="post">
<h2><span class="icon-list"></span> [[=admin:sect:Articles]]<span class="adminSANew"><a href='[[::siteURL]]/admin.php/articles/new/?CSRFCode=[[::newCSRFCode]]'><span class="icon-plus2"></span> [[=admin:btn:NewArticle]]</a></span></h2> 
<p>
<ul>
[[::loop, adminarticlelist]]<li class="adminSingleArticle adminSAL" data-aid="[[::aID]]"><a href="[[::siteURL]]/[[::linkPrefixArticle]]/[[::aID]]/" title="[[=admin:msg:Open]]"><span class="icon-export"></span> </a> [[::aTitle]] <span class="adminSADate">[[::aTime]]</span></li>
[[::/loop]]
</ul>
</p>
<br/>
<div id="adminSAPage">[[::pagination]]</div>



<h2><span class="icon-archive"></span> [[=admin:sect:Categories]]<span class="adminSANew"><a href='##' onclick='$("#adminSCInputNew").toggle()'><span class="icon-plus2"></span> [[=admin:btn:NewCate]]</a> <a href='##' onclick="saveCategoryChanges('[[::siteURL]]/admin.php/articles/savecategories/');"><span class="icon-disk"></span> [[=admin:btn:Save]]</a></span></h2>
<p><ul class="adminCateList" id="adminCateList">
[[::loop, admincatelist]]<li class="adminSingleArticle adminSCL" data-cid="[[::aCateURLName]]" id="adminSCL-[[::aCateURLName]]"><a href="##" title="[[=admin:msg:Up]]" class="adminSCLUp" data-cid="[[::aCateURLName]]"><span class="icon-arrow-up3"></span></a> <a href="##" title="[[=admin:msg:Down]]" class="adminSCLDown" data-cid="[[::aCateURLName]]"><span class="icon-arrow-down4"></span></a> <span id="adminSCLine-[[::aCateURLName]]" class="adminSCLine" data-cid="[[::aCateURLName]]">[[::aCateDispName]]</span><span class="adminSCLModify" id="adminSCM-[[::aCateURLName]]"><input type="text" class="inputLine inputLarge" value="[[::aCateDispName]]" id="adminSCInput-[[::aCateURLName]]"> <br/><a href="##" onclick='$("#adminSCM-[[::aCateURLName]]").fadeToggle();$("#adminSCL-[[::aCateURLName]]").remove();'><span class="icon-cross3"></span> [[=admin:msg:Remove]]</a> &nbsp; <a href="##" onclick='$("#adminSCM-[[::aCateURLName]]").fadeToggle();$("#adminSCLine-[[::aCateURLName]]").html($("#adminSCInput-[[::aCateURLName]]").val());$("#adminSCLine-[[::aCateURLName]]").toggle();'><span class="icon-arrow-up4"></span> [[=admin:msg:Close]]</a></span></li>
[[::/loop]]
</ul>
<span id="adminSCInputNew">

<input type="text" class="inputLine inputSmall" value="" placeholder="[[=admin:msg:NewCate]]"  id="adminSCInputNewItemName" /> <input type="text" class="inputLine inputSmall" value="" placeholder="ID"  id="adminSCInputNewItemID" />

<a href='##' onclick="addCategory('[[::siteURL]]/admin.php/articles/validatecategory/');"><span class="icon-disk"></span> [[=admin:btn:Add]]</a><br/> </span>
<span class="adminExplain">[[=admin:msg:Categories]]</span></p>
<p class="adminCommand">
<p id="adminPromptError"></p><p id="adminPromptSuccess"></p>
</p>
[[::ext_adminArticles]]

<script type="text/javascript">
$(".adminSAL").click(function(){
	var aID=$(this).data("aid");
	window.location="[[::siteURL]]/admin.php/articles/modify/?aID="+aID+"&CSRFCode=[[::oldCSRFCode]]";
});

$("#admArticles").addClass("activeNav");

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
		$("#"+"adminSCM-"+cID).fadeToggle();
		$("#"+"adminSCLine-"+cID).toggle();
	});
}
bindUpDown ();


$("#adminSCInputNew").hide();

function addCategory(smtURL) {
	if ($("#adminSCInputNewItemID").val()=='' || $("#adminSCInputNewItemName").val()=='')
	{
		alert ("[[=admin:msg:ErrorCorrection]]");
		return false;
	}


	var newList=$("#adminSCInputNewItemID").val()+'='+$("#adminSCInputNewItemName").val();
	var nList=newList.split('=');
	var smtURL=smtURL+"?ajax=1&CSRFCode=[[::cateCSRFCode]]";	
	var sVal=encodeURI("smt[aCateURLName]="+nList[0]+"&smt[aCateDispName]="+nList[1]);
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
		finalList+=encodeURI("smt["+cID+"]="+$("#"+"adminSCLine-"+cID).html()+"&");
	});

	var smtURL=smtURL+"?ajax=1&CSRFCode=[[::cateCSRFCode]]";	

	$.post(smtURL, finalList, function(data) {
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

[[::ext_adminArticlesEnding]]