<?php
//Copyright: Byke

?>

<div class="adminArea">
<form id="smtForm" action="post">
<h2><span class="icon-chat"></span> [[=admin:sect:CommentServices]]</h2> 
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:DuoshuoID]]<br/><input type="text" class="inputLine inputLarge" name="smt[duoshuoID]" value="[[::duoshuoID]]" id="duoshuoID" /><br/><span class="adminExplain">[[=admin:msg:Duoshuo]]</span>
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:DisqusID]]<br/><input type="text" class="inputLine inputLarge" name="smt[disqusID]" value="[[::disqusID]]" id="disqusID" /><br/><span class="adminExplain">[[=admin:msg:Disqus]]</span>
</p>
<!--
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:BaiduAK]]<br/><input type="text" class="inputLine inputLarge" name="smt[baiduAKey]" value="[[::baiduAKey]]" id="baiduAKey" /><br/><span class="adminExplain">[[=admin:NotReady]] [[=admin:msg:BaiduAPI]]</span>
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:BaiduSK]]<br/><input type="text" class="inputLine inputLarge" name="smt[baiduSKey]" value="[[::baiduSKey]]" id="baiduSKey" />
</p>
-->
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:SinaAK]]<br/><input type="text" class="inputLine inputLarge" name="smt[sinaAKey]" value="[[::sinaAKey]]" id="sinaAKey" /><br/><span class="adminExplain">[[=admin:msg:SinaAPI]]</span>
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:SinaSK]]<br/><input type="text" class="inputLine inputLarge" name="smt[sinaSKey]" value="[[::sinaSKey]]" id="sinaSKey" />
</p>

<p><br/><br/></p>

<h2><span class="icon-upload2"></span> [[=admin:sect:Qiniu]]</h2>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:QiniuAK]]<br/><input type="text" class="inputLine inputLarge" name="smt[qiniuAKey]" value="[[::qiniuAKey]]" id="qiniuAKey" /><br/><span class="adminExplain">[[=admin:msg:Qiniu]]</span>
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:QiniuSK]]<br/><input type="text" class="inputLine inputLarge" name="smt[qiniuSKey]" value="[[::qiniuSKey]]" id="qiniuSKey" />
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:QiniuBucket]]<br/><input type="text" class="inputLine inputLarge" name="smt[qiniuBucket]" value="[[::qiniuBucket]]" id="qiniuBucket" />
</p>
<!--
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:QiniuSync]]<br class="smallBr"/><span class="buttonLine buttonGroup buttonGroupFirst qiniuSync" data-reflect="0"><span class="icon-cross"></span> [[=admin:opt:Off]]</span> <span class="buttonLine buttonGroup buttonGroupLast qiniuSync" data-reflect="1"><span class="icon-checkmark"></span> [[=admin:opt:On]]</span> <input type="hidden" value="[[::qiniuSync]]" name="smt[qiniuSync]" id="qiniuSync"/><br/><span class="adminExplain">[[=admin:msg:QiniuSync]]</span>
</p>
-->
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:QiniuUpload]]<br class="smallBr"/><span class="buttonLine buttonGroup buttonGroupFirst qiniuUpload" data-reflect="0"><span class="icon-cross"></span> [[=admin:opt:Off]]</span> <span class="buttonLine buttonGroup buttonGroupLast qiniuUpload" data-reflect="1"><span class="icon-checkmark"></span> [[=admin:opt:On]]</span> <input type="hidden" value="[[::qiniuUpload]]" name="smt[qiniuUpload]" id="qiniuUpload"/><br/><span class="adminExplain">[[=admin:msg:QiniuUpload]]</span>
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:QiniuDomain]]<br/><input type="text" class="inputLine inputLarge" name="smt[qiniuDomain]" value="[[::qiniuDomain]]" id="qiniuDomain" /><br/><span class="adminExplain">[[=admin:msg:QiniuDomain]]</span>
</p>
<!--
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:Sync]]<br class="smallBr"/><span class="adminGoSync"><a href="[[::siteURL]]/[[::linkPrefixAdmin]]/services/sync/" target="_blank"><span class="icon-cycle"></span> [[=admin:btn:Sync]] </a></span>
</p>
-->

<p id="admBackup"><br/><br/></p>
<h2><span class="icon-cone"></span> [[=admin:sect:OtherServices]]</h2>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:Backup]]<br class="smallBr"/><span class="adminGoSync"><a href="[[::siteURL]]/[[::linkPrefixAdmin]]/services/backup/[[::linkConj]]CSRFCode=[[::serviceCSRFCode]]" target="_blank"><span class="icon-export"></span> [[=admin:btn:Backup]] </a></span>
<br class="smallBr"/><span class="adminGoSync"><a href="[[::siteURL]]/[[::linkPrefixAdmin]]/services/sync/[[::linkConj]]CSRFCode=[[::serviceCSRFCode]]"><span class="icon-export"></span> [[=admin:btn:Sync]] </a></span>
</p>

<p class="adminCommand"><br/>
<button type="button" class="buttonLine" id="btnSubmit" onclick="saveConf('smtForm', '[[::siteURL]]/[[::linkPrefixAdmin]]/services/store/');"><span class="icon-disk"></span></button> [[=admin:btn:Save]]
<button type="button" class="buttonLine" onclick="window.location=window.location;"><span class="icon-ccw"></span></button> [[=admin:btn:Restore]]
<p id="adminPromptError"></p><p id="adminPromptSuccess"></p>
</p>
[[::ext_adminServices]]

<script type="text/javascript">

$("#admServices").addClass("activeNav");

function goQiniuSync() {
	var qiniuSyncCD=$("#qiniuSync").val();
	$(".qiniuSync").each(function(){
		var qiniuSyncD=$(this).data('reflect');
		if (qiniuSyncD==qiniuSyncCD)
		{
			$(this).addClass("buttonGroupSelected");
		}
		else {
			$(this).removeClass("buttonGroupSelected");
		}
	});
}

$(".qiniuSync").click(function(){
	var qiniuSyncD=$(this).data('reflect');
	$("#qiniuSync").val(qiniuSyncD);
	goQiniuSync();
});
goQiniuSync();

function goQiniuUpload() {
	var qiniuUploadCD=$("#qiniuUpload").val();
	$(".qiniuUpload").each(function(){
		var qiniuUploadD=$(this).data('reflect');
		if (qiniuUploadD==qiniuUploadCD)
		{
			$(this).addClass("buttonGroupSelected");
		}
		else {
			$(this).removeClass("buttonGroupSelected");
		}
	});
}

$(".qiniuUpload").click(function(){
	var qiniuUploadD=$(this).data('reflect');
	$("#qiniuUpload").val(qiniuUploadD);
	goQiniuUpload();
});
goQiniuUpload();

function saveConf(formID, smtURL) {
	$("#UI-loading").fadeIn(500);
	$.post(smtURL+"[[::linkConj]]ajax=1&CSRFCode=[[::serviceCSRFCode]]", $('#'+formID).serialize(), function(data) {
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
[[::ext_adminServicesEnding]]