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
<span class="icon-arrow-right5"></span> <a href="[[::siteURL]]/admin.php/services/backup/?CSRFCode=[[::serviceCSRFCode]]">[[=admin:item:BackupData]] </a><br/>
<span class="icon-arrow-right5"></span> <a href="[[::siteURL]]/index.php/">[[=admin:item:BackHome]] </a><br/>
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
</div>
[[::ext_adminDashboard]]
<script type="text/javascript">
$("#admPanel").addClass("activeNav");
</script>
[[::ext_adminDashboardEnding]]