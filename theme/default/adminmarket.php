<?php
//Copyright: Byke

?>

<div class="adminArea">
<h2>[[=admin:market:Welcome]]</h2>
<div class="marketTop marketFeatured" id="marketCont-hero">
</div>
<p style="clear:both"></p>
<h2><span class="icon-pictures"></span> [[=admin:market:Themes]]</h2>
<div class="marketTop marketList" id="marketCont-theme">
</div>
<p style="clear:both"></p>
<h2><span class="icon-docs"></span> [[=admin:market:Extensions]]</h2>
<div class="marketTop marketList" id="marketCont-extension">
</div>
<p style="clear:both"></p>
<h2><span class="icon-newspaper2"></span> [[=admin:market:Bulletin]]</h2>
<div class="marketTop marketBulletin"><ul id="marketCont-bulletin">
</ul></div>
</div>

<div id="marketTpl-theme" style="display: none;">
<div id="mL-[::tid]" class="marketSubItem">
<img src="[::tthm]" />
<span class="marketSubDesc">
<h4>[::tn]</h4>
<h5>by [::ta]</h5>
<h5><span class="marketDown"><a href="[[::siteURL]]/[[::linkPrefixAdmin]]/market/detail/[[::linkConj]]dlu=[::tl]&CSRFCode=[[::installCSRFCode]]" class="marketSubject"><span class="icon-plus2"> [[=admin:market:Get]]</span></a></span></h5>
</span>
</div>
</div>
<div id="marketTpl-extension" style="display: none;">
<div id="mL-[::tid]" class="marketSubItem">
<img src="[::tthm]" />
<span class="marketSubDesc">
<h4>[::tn]</h4>
<h5>[::td]</h5>
<h5><span class="marketDown"><a href="[[::siteURL]]/[[::linkPrefixAdmin]]/market/detail/[[::linkConj]]dlu=[::tl]&CSRFCode=[[::installCSRFCode]]" class="marketSubject"><span class="icon-plus2"> [[=admin:market:Get]]</span></a></span></h5>
</span>
</div>
</div>
<div id="marketTpl-hero" style="display: none;">
<div id="mL-[::tid]" class="marketHero">
<a href="[[::siteURL]]/[[::linkPrefixAdmin]]/market/detail/[[::linkConj]]dlu=[::tl]&CSRFCode=[[::installCSRFCode]]" class="marketSubject"><img src="[::tthm]" /></a>
</div>
</div>
<div id="marketTpl-bulletin" style="display: none;">
<li>
<a href="[[::siteURL]]/[[::linkPrefixAdmin]]/market/detail/[[::linkConj]]dlu=[::tl]&CSRFCode=[[::installCSRFCode]]" class="marketSubject">[::tn]</a>
</li>
</div>


[[::ext_adminMarket]]
<script type="text/javascript">
$("#admMarket").addClass("activeNav");

function parseInfo (info, cate) {
	var patternHTML=$("#marketTpl-"+cate).html();
	var output='';
	$.each (info, function (i, val) {
		output+=patternHTML.replace(/\[\:\:(\w+)\]/g, function (word, kk) {
			return val[kk];
		});
	});
	$("#marketCont-"+cate).html(output);
}

//$("<sc"+"ript>"+"</sc"+"ript>").attr({src: "<?=bwUpdate?>cver/market.js"}).appendTo("body");
$("<sc"+"ript>"+"</sc"+"ript>").attr({src: "/bw/trash/update/featured/featured.js"}).appendTo("body");


</script>
[[::ext_adminDashboardEnding]]

