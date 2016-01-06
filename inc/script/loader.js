$( document ).ajaxError(function() {
	alert (lng['AjaxFail']);
});

$('.pageLink').click(function () {

	var targetURL = $(this).attr('href');
	if (history && history.pushState) {
		history.pushState(null, document.title, targetURL);
	}

	targetURL=targetURL+'?ajax=1';

	$.get(targetURL, function (data) {
		if (data.error==1) {
			alert(data.errorMsg);
		}
		else {
			$("#UI-loading").fadeIn(400, function() {
				$("#ajax-article-list").html(data.returnMsg);
			});
			$("#UI-loading").fadeOut(400, function () {
				scrollToTop ();
			});
		}
	}, "json");
		
	return false;

});

$('.icon-share').click(function () {
	var aID=$(this).attr('id');
	$('#'+aID+'-layer').fadeToggle(200);
});

$('.shareLayer').click(function () {
	$(this).fadeToggle(200);
});

$('article img').click(function () {
	var picURL=$(this).attr('src');
	lightboxImage (picURL);
});

$('article .videoFrame').each (function () {
	var iWidth=$(this).width();
	var iHeight=Math.floor(iWidth*9/16);
	$(this).height(iHeight);
});

$('article .xiamiLoader').each (function () {
	var xmID=$(this).data('src');
	var root=$(this).data('root');
	$(this).html("<iframe class='xmLoader' frameborder='0' id='xm-"+xmID+"' name='xm-"+xmID+"' src='"+root+"/inc/script/xiami.php?xmID="+xmID+"' height='50'></iframe>");
});

changeNav ();