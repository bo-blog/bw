<?php
//Copyright: Byke

?>

<div class="adminArea">
<h2><span class="icon-house"></span> [[=admin:sect:Welcome]], [[::authorName]]!</h2>
<p></p>
<div id="admStatBlock0">
<div class="admStatBlock1">
<h2>[[=admin:item:AtAGlance]]</h2>
<span class='admStatNum'>[[::totalArticles]]</span> [[=admin:item:xxArticles]]<br/>
<span class='admStatNum'>[[::totalReads]]</span> [[=admin:item:xxArticleViews]]<br/>
<span class='admStatNum'>[[::totalPVs]]</span> [[=admin:item:xxPageViews]]<br/>
[[=admin:item:Sincexx]] <span class='admStatNum'>[[::sinceWhen, dateFormat, Y.m.j]]</span><br/>
</div>
<div class="admStatBlock2">
<h2>[[=admin:item:StartHere]] </h2>
<span class="icon-arrow-right5"></span> <a href="[[::siteURL]]/admin.php/articles/new/?CSRFCode=[[::newCSRFCode]]">[[=admin:item:WriteArticle]] </a><br/>
<span class="icon-arrow-right5"></span> <a href="[[::siteURL]]/admin.php/center/?CSRFCode=[[::navCSRFCode]]">[[=admin:item:ChangeSetting]] </a><br/>
<span class="icon-arrow-right5"></span> <a href="[[::siteURL]]/admin.php/services/?CSRFCode=[[::navCSRFCode]]#admBackup">[[=admin:item:BackupData]] </a><br/>
<!-- <span class="icon-arrow-right5"></span> <a href="[[::siteURL]]/admin.php/market/?CSRFCode=[[::navCSRFCode]]">[[=admin:item:GoMarket]] </a><br/>-->
<span class="icon-arrow-right5"></span> <a href="[[::siteURL]]/">[[=admin:item:BackHome]] </a><br/>
</div>
<div class="admStatBlock3">
<h2>[[=admin:item:MostPopular]]</h2>
<ul>
[[::whatsHottest]]
</ul>
</div>
<div class="admStatBlock1">
<h2>[[=admin:item:RecentView]]</h2>
<ul>
[[::latestViews]]
</ul>
</div>
</div>

<p style="clear:both"><br/><br/></p>
<h2><span class="icon-help"></span> [[=admin:sect:SysInfo]]</h2>
<span class="icon-arrow-right5"></span> bW (ver [[::thisVersion]])<br/>
<span class="icon-arrow-right5"></span> [[::serverInfo]]<br/>
<span class="icon-arrow-right5"></span> PHP Version [[::PHPVersion]]
<br/><br/>
<h2><span class="icon-upload2"></span> [[=admin:sect:AutoUpdate]]</h2>
<div id="ins"><span class="adminGoSync"><a href="##" onclick="checkUpdates();"><span class="icon-export"></span> [[=admin:btn:CheckUpdate]] </a></span><br/>
</div>
</div>
[[::ext_adminDashboard]]
<script type="text/javascript">

function checkUpdates () {
	$("#ins").html (" [[=admin:msg:CheckingUpdate]]");
	$("<sc"+"ript>"+"</sc"+"ript>").attr({src: "<?=bwUpdate?>cver/v<?=bwInternalVersion?>.js"}).appendTo("head");
}

function addUpdateBtn (URL, md5) {
	$("#ins").append("<div><span class='adminGoSync'><a id='dlURL' href='##' onclick=\"gotoUpdate('[[::siteURL]]/admin.php/dashboard/update/?CSRFCode=[[::updateCSRFCode]]&dlURL=', '"+URL+"', '"+md5+"');\"><span class=\"icon-arrow-right5\"></span> [[=admin:btn:DoUpdate]] </a></span><br/></div>");
}

function gotoUpdate (desAddr, dlURL, md5) {
	if (confirm ("[[=admin:msg:DoUpdate]]"))
	{
		window.location=desAddr+dlURL+"&hash="+md5;
	} else {
	}
}

if (window.location.hash == '#UpdateSuccess')
{
	$("#ins").html (" [[=admin:msg:UpdateDone]]");
	alert ("[[=admin:msg:UpdateDone]]");
}
$("#admPanel").addClass("activeNav");
</script>
[[::ext_adminDashboardEnding]]

