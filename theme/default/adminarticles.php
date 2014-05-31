<?php
//Copyright: Byke

?>

<div class="adminArea">
<form id="smtForm" action="post">
<h2><span class="icon-list"></span> [[=admin:sect:Articles]]<span class="adminSANew"><a href='[[::siteURL]]/admin.php/articles/new/'><span class="icon-plus2"></span> [[=admin:btn:NewArticle]]</a></span></h2> 
<p>
<ul>
[[::adminarticlelist]]
</ul>
</p>
<br/>
[[::pagination]]



<h2><span class="icon-tag"></span> [[=admin:sect:Categories]]<span class="adminSANew"><a href='##' onclick='$("#adminSCInputNew").toggle()'><span class="icon-plus2"></span> [[=admin:btn:NewCate]]</a> <a href='##' onclick="saveCategoryChanges('[[::siteURL]]/admin.php/articles/savecategories/');"><span class="icon-disk"></span> [[=admin:btn:Save]]</a></span></h2>
<p><ul class="adminCateList" id="adminCateList">
[[::admincategorylist]]
</ul>
<span id="adminSCInputNew"><input type="text" class="inputLine inputLarge" value="" placeholder="[[=admin:msg:NewCate]]"  id="adminSCInputNewItem" /> <a href='##' onclick="addCategory('[[::siteURL]]/admin.php/articles/validatecategory/');"><span class="icon-disk"></span> [[=admin:btn:Add]]</a><br/> </span>
<span class="adminExplain">[[=admin:msg:Categories]]</span></p>
<p class="adminCommand">
<p id="adminPromptError"></p><p id="adminPromptSuccess"></p>
</p>

<script type="text/javascript">
$(".adminSAL").click(function(){
	var aID=$(this).data("aid");
	window.location="[[::siteURL]]/admin.php/articles/modify/?aID="+aID;
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
	var newList=$("#adminSCInputNewItem").val();
	if (newList!='')
	{
		var nList=newList.split('=');
		if (nList[1]==null)
		{
			return false;
		}
		else
		{
			var smtURL=smtURL+"?ajax=1";	
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
	}
}

function saveCategoryChanges(smtURL) {
	$("#UI-loading").fadeIn(500);
	var finalList='';
	$('.adminSCL').each(function(){
		var cID=$(this).data("cid");
		finalList+=encodeURI("smt["+cID+"]="+$("#"+"adminSCLine-"+cID).html()+"&");
	});

	var smtURL=smtURL+"?ajax=1";	

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

