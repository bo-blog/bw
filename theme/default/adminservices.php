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
<h2><span class="icon-chat"></span> [[=admin:sect:CommentServices]]</h2> 
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:DuoshuoID]]<br/><input type="text" class="inputLine inputSmall" name="smt[duoshuoID]" value="[[::duoshuoID]]" id="duoshuoID" />.duoshuo.com<br/><span class="adminExplain">[[=admin:msg:Duoshuo]]</span>
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:DisqusID]]<br/><input type="text" class="inputLine inputSmall" name="smt[disqusID]" value="[[::disqusID]]" id="disqusID" />.disqus.com<br/><span class="adminExplain">[[=admin:msg:Disqus]]</span>
</p>
<p><br/><br/></p>
<h2><span class="icon-upload2"></span> [[=admin:sect:CloudDrive]]</h2> 
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:CloudUpload]]<br class="smallBr"/><span class="buttonLine buttonGroup buttonGroupFirst qiniuUpload" data-reflect="0"><span class="icon-cross"></span> [[=admin:opt:Off]]</span> <span class="buttonLine buttonGroup qiniuUpload" data-reflect="1"><span class="icon-cloud2"></span> [[=admin:sect:Qiniu]]</span>  <span class="buttonLine buttonGroup buttonGroupLast qiniuUpload" data-reflect="2"><span class="icon-cloud2"></span> [[=admin:sect:Aliyun]]</span> <input type="hidden" value="[[::qiniuUpload]]" name="smt[qiniuUpload]" id="qiniuUpload"/><br/><span class="adminExplain">[[=admin:msg:CloudUpload]]</span>
</p>

<p>
<span class="icon-arrow-right5"></span> [[=admin:Settings]]<br class="smallBr"/></p>

<div class="admStatBlock1">
<h3>[[=admin:sect:Qiniu]]</h3>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:QiniuAK]]<br/><input type="text" class="inputLine inputLarge" name="smt[qiniuAKey]" value="[[::qiniuAKey]]" id="qiniuAKey" /><br/><span class="adminExplain">[[=admin:msg:Qiniu]]</span>
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:QiniuSK]]<br/><input type="text" class="inputLine inputLarge" name="smt[qiniuSKey]" value="[[::qiniuSKey]]" id="qiniuSKey" />
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:QiniuBucket]]<br/><input type="text" class="inputLine inputLarge" name="smt[qiniuBucket]" value="[[::qiniuBucket]]" id="qiniuBucket" />
</p>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:QiniuDomain]]<br/><input type="text" class="inputLine inputLarge" name="smt[qiniuDomain]" value="[[::qiniuDomain]]" id="qiniuDomain" /><br/><span class="adminExplain">[[=admin:msg:QiniuDomain]]</span>
</p>
</div>

<div class="admStatBlock2">
<h3>[[=admin:sect:Aliyun]] (BETA)</h3>
<p>
<span class="icon-arrow-right5"></span>AccessKeyId<br/><input type="text" class="inputLine inputLarge" name="smt[aliyunAKey]" value="[[::aliyunAKey]]" id="aliyunAKey" /><br/><span class="adminExplain">[[=admin:msg:Aliyun]]</span>
</p>
<p>
<span class="icon-arrow-right5"></span>AccessKeySecret<br/><input type="text" class="inputLine inputLarge" name="smt[aliyunSKey]" value="[[::aliyunSKey]]" id="aliyunSKey" />
</p>
<p>
<span class="icon-arrow-right5"></span>Bucket<br/><input type="text" class="inputLine inputLarge" name="smt[aliyunBucket]" value="[[::aliyunBucket]]" id="aliyunBucket" />
</p>
<p>
<span class="icon-arrow-right5"></span>Region<br/><input type="text" class="inputLine inputLarge" name="smt[aliyunRegion]" value="[[::aliyunRegion]]" id="aliyunRegion" /><br/><span class="adminExplain">[[=admin:msg:AliyunRegion]]</span>
</p>
</div>

<p style="clear: both;"><br/><br/></p>
<h2><span class="icon-upload2"></span> [[=admin:sect:API]] (BETA)</h2>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:APIOpen]]<br class="smallBr"/><span class="buttonLine buttonGroup buttonGroupFirst APIOpen" data-reflect="0"><span class="icon-cross"></span> [[=admin:opt:Off]]</span> <span class="buttonLine buttonGroup buttonGroupLast APIOpen" data-reflect="1"><span class="icon-checkmark"></span> [[=admin:opt:On]]</span> <input type="hidden" value="[[::APIOpen]]" name="smt[APIOpen]" id="APIOpen"/>
</p>

<p>
<span class="icon-arrow-right5"></span> [[=admin:item:BasicAPI]]<br/>
<span class="adminExplain">[[=admin:msg:BasicAPI]]<br/>[[=admin:btn:APIAddr]]: [[::siteURL]]/api.php</span><br/>
[[::loop, basicAPI]]<div class="adminSANew" style="float: none;">
<div id="lnkItem-[[::apiID]]" data-apiid='[[::apiKey]]' class="newBasicAPI">
<p><b>API Key:</b> [[::apiKey]]<br><b>API Secret:</b> [[::apiSecret]]</p>
<a href="##" onclick="$('#lnkItem-[[::apiID]]').remove();"><span class="icon-cross2"></span> [[=admin:msg:Remove]]</a><br/>
</div>
</div>[[::/loop]]
<div	id="newLink-basic"></div>
<div id="newLinkTpl-basic" style="display:none;">
<span class="adminSANew" style="float: none;">
<span id="lnkItem-rndid" data-apiid='' class="newBasicAPI">
<span class="newContent"></span>
<a href="##" onclick="$('#lnkItem-rndid').remove();"><span class="icon-cross2"></span> [[=admin:msg:Remove]]</a><br/>
</span>
</span>
</div>
<input type="hidden" id="basicAPI" name="smt[basicAPI]" />
<a href="##" onclick="newAPIKey('[[::siteURL]]/[[::linkPrefixAdmin]]/services/getnewapikey/', true);"><span class="icon-plus2"></span> [[=admin:btn:AddKey]]</a>
</p>

<p>
<span class="icon-arrow-right5"></span> [[=admin:item:AdvancedAPI]]<br/>
<span class="adminExplain">[[=admin:msg:AdvancedAPI]]<br/>[[=admin:btn:APIAddr]]: [[::siteURL]]/api.php</span><br/>
[[::loop, advancedAPI]]<div class="adminSANew" style="float: none;">
<div id="lnkItem-[[::apiID]]" data-apiid='[[::apiKey]]' class="newAdvancedAPI">
<p><b>API Key:</b> [[::apiKey]]<br><b>API Secret:</b> [[::apiSecret]]</p>
<a href="##" onclick="$('#lnkItem-[[::apiID]]').remove();"><span class="icon-cross2"></span> [[=admin:msg:Remove]]</a><br/>
</div>
</div>[[::/loop]]
<div	id="newLink-advanced"></div><div id="newLinkTpl-advanced" style="display:none;"><span class="adminSANew" style="float: none;"><span id="lnkItem-rndid" data-apiid='' class="newAdvancedAPI"><span class="newContent"></span><a href="##" onclick="$('#lnkItem-rndid').remove();"><span class="icon-cross2"></span> [[=admin:msg:Remove]]</a><br/></span></span></div>
<input type="hidden" id="advancedAPI" name="smt[advancedAPI]" />
<a href="##" onclick="newAPIKey('[[::siteURL]]/[[::linkPrefixAdmin]]/services/getnewapikey/', false);"><span class="icon-plus2"></span> [[=admin:btn:AddKey]]</a>
</p>

<p id="admBackup"><br/><br/></p>
<h2><span class="icon-cone"></span> [[=admin:sect:OtherServices]]</h2>
<p>
<span class="icon-arrow-right5"></span> [[=admin:item:Backup]]<br class="smallBr"/><span class="adminGoSync"><a href="[[::siteURL]]/[[::linkPrefixAdmin]]/services/backup/[[::linkConj]]CSRFCode=[[::serviceCSRFCode]]" target="_blank"><span class="icon-export"></span> [[=admin:btn:Backup]] </a></span>
<br class="smallBr"/><span class="adminGoSync"><a href="[[::siteURL]]/[[::linkPrefixAdmin]]/services/sync/[[::linkConj]]CSRFCode=[[::serviceCSRFCode]]"><span class="icon-export"></span> [[=admin:btn:Sync]] </a></span>
<br/><div id="resetPermission" style="display: none;"><br/>
<span class="icon-arrow-right5"></span> [[=admin:item:ResetPermission]]<br class="smallBr"/><span class="adminGoSync"><a href="[[::siteURL]]/[[::linkPrefixAdmin]]/services/reset/[[::linkConj]]CSRFCode=[[::serviceCSRFCode]]"><span class="icon-export"></span> [[=admin:btn:DoReset]] </a></span></div>
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

function goAPIOpen() {
	var APIOpenCD=$("#APIOpen").val();
	$(".APIOpen").each(function(){
		var APIOpenD=$(this).data('reflect');
		if (APIOpenD==APIOpenCD)
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
$(".APIOpen").click(function(){
	var APIOpenD=$(this).data('reflect');
	$("#APIOpen").val(APIOpenD);
	goAPIOpen();
});
goAPIOpen();

function saveConf(formID, smtURL) {
	$("#UI-loading").fadeIn(500);
	var basicAPIs = new Array ();
	$(".newBasicAPI").each (function () {
		basicAPIs.push ($(this).data('apiid'));
	});
	var advancedAPIs = new Array ();
	$(".newAdvancedAPI").each (function () {
		advancedAPIs.push ($(this).data('apiid'));
	});
	$("#basicAPI").val(basicAPIs.join('<>'));
	$("#advancedAPI").val(advancedAPIs.join('<>'));

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

function newAPIKey(smtURL, isbasic) {
	$("#UI-loading").fadeIn(500);
	var pos = isbasic ? 'basic' : 'advanced';
	$.post(smtURL+"[[::linkConj]]ajax=1&CSRFCode=[[::serviceCSRFCode]]", function(data) {
		$("#UI-loading").fadeOut(200);
		if (data.error==1) {
		}
		else {
			var keysecret = data.returnMsg.split ('-');
			var cID = Math.floor(Math.random() * 8999 + 1000);
			$('#newLink-'+pos).append($('#newLinkTpl-'+pos).html().replace(/rndid/g, cID));
			$('#lnkItem-'+cID+' .newContent').html ("<p><b>API Key:</b> "+keysecret[0]+"<br><b>API Secret:</b> "+keysecret[1]+"</p>");
			$('#lnkItem-'+cID).data('apiid', keysecret[0]);
		}
	}, "json");
}

if (window.location.hash == '#reset')
{
	$("#resetPermission").show();
}

</script>
</form>

</div>
[[::ext_adminServicesEnding]]