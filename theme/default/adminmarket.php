<?php
//Copyright: Byke

?>

<div class="adminArea">
<h2><span class="icon-cd"></span> [[=admin:market:Welcome]]</h2>
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
<h5><span class="marketDown"><a href="##" data-dlu="[[::siteURL]]/admin.php/market/detail/?dlu=[::tl]&CSRFCode=[[::installCSRFCode]]" class="marketSubject"><span class="icon-plus2"> [[=admin:market:Get]]</span></a></span></h5>
</span>
</div>
</div>
<div id="marketTpl-extension" style="display: none;">
<div id="mL-[::tid]" class="marketSubItem">
<img src="[::tthm]" />
<span class="marketSubDesc">
<h4>[::tn]</h4>
<h5>[::td]</h5>
<h5><span class="marketDown"><a href="##" data-dlu="[[::siteURL]]/admin.php/market/detail/?dlu=[::tl]&CSRFCode=[[::installCSRFCode]]" class="marketSubject"><span class="icon-plus2"> [[=admin:market:Get]]</span></a></span></h5>
</span>
</div>
</div>
<div id="marketTpl-hero" style="display: none;">
<div id="mL-[::tid]" class="marketHero">
<a href="##" data-dlu="[[::siteURL]]/admin.php/market/detail/?dlu=[::tl]&CSRFCode=[[::installCSRFCode]]" class="marketSubject"><img src="[::tthm]" /></a>
</div>
</div>
<div id="marketTpl-bulletin" style="display: none;">
<li>
<a href="##" data-dlu="[[::siteURL]]/admin.php/market/detail/?dlu=[::tl]&CSRFCode=[[::installCSRFCode]]" class="marketSubject">[::tn]</a>
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

function renderMarket () {
	$(".marketSubject").click (function() {
		var dlu=$(this).data("dlu");
		window.location=dlu;
		lightboxLoader (dlu);
		return false;
	});
}

//$("<sc"+"ript>"+"</sc"+"ript>").attr({src: "<?=bwUpdate?>cver/market.js"}).appendTo("body");
$("<sc"+"ript>"+"</sc"+"ript>").attr({src: "/bw/trash/update/featured/featured.js"}).appendTo("body");

function lightboxLoader (URL) {
	$("#UI-lightbox").fadeIn(500);
	$("#UI-lightbox").append( "<div id='lightbox-message' class='marketLH'></div>" );
	var windowWidth=$(window).width();
	var windowHeight=$(window).height();
	$("#lightbox-message").css("width", "500px");
	$("#lightbox-message").css("max-width", "95%");
	$("#lightbox-message").css("height", "600px");
	$("#lightbox-message").css("max-height", "60%");

	var finalWidth=$("#lightbox-message").width();
	var finalHeight=$("#lightbox-message").height();

	$("#lightbox-message").css("position", "fixed");
	$("#lightbox-message").css("left", Math.floor((windowWidth-finalWidth)/2));
	$("#lightbox-message").css("top", Math.floor((windowHeight-finalHeight)/2));

	$("#lightbox-message").load(URL);
}



</script>
[[::ext_adminDashboardEnding]]

