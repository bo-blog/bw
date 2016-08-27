$( document ).ajaxError(function(event, jqxhr, settings, thrownError) {
	alert (lng['AjaxFail']+'\r'+jqxhr.responseText);
});

$('.pageLink').click(function () {

	var targetURL = $(this).attr('href');
	if (history && history.pushState) {
		history.pushState(null, document.title, targetURL);
	}

	targetURL=conj (targetURL, 'ajax=1');

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
	if ($(this).hasClass ('ImgAlbum'))
	{
		var AlbumID = $(this).data('album');
		var ImgGroups=new Array();
		var ImgDesc=new Array();
		var i = 0;
		var ImgSeq = 0;
		$('article .Alb'+AlbumID).each (function () {
			ImgGroups[i] = $(this).attr('src');
			ImgDesc[i] = $(this).data ('desc');
			if ($(this).attr('src') == picURL) {
				ImgSeq = i;
			}
			i++;
		});
		lightboxImageAlbum (picURL, ImgGroups, ImgDesc, ImgSeq);
	}
	else {
		lightboxImage (picURL, false);
	}
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