function scrollToTop () {
	$('html,body').animate({scrollTop: '0px'}, 400);
}

if (location.href.indexOf('?cleartoken')!=-1)
{
	if (window.localStorage)
	{
		localStorage.clear();
	}
}


function lightboxImage (imgSrc) {
	$("#UI-lightbox").fadeIn();
	var winH=$(window).height();
	var img=new Image;
	img.src=imgSrc;
	$("#UI-lightbox").append( "<div id='lightbox-image'><img id='lightboxImage' src='"+imgSrc+"' alt='' /></div>" );
	$('#lightboxImage').load(function(){
		var windowWidth=$(window).width();
		var windowHeight=winH;
		var picWidth=img.width;
		var picHeight=img.height;

		var finalWidth;
		var finalHeight;

		var idealWidth=Math.floor(windowWidth*0.8)-20;
		var idealHeight=Math.floor(windowHeight*0.8)-20;


		if (picWidth<idealWidth && picHeight<idealHeight)
		{
			finalWidth=picWidth;
			finalHeight=picHeight;
		}

		else if (picWidth<idealWidth && picHeight>idealHeight)
		{
			finalHeight=idealHeight;
			finalWidth=Math.floor((picWidth/picHeight)*idealHeight);
		}

		else if (picWidth>idealWidth && picHeight<idealHeight)
		{
			finalWidth=idealWidth;
			finalHeight=Math.floor((picHeight/picWidth)*idealWidth);
		}


		else {
			if (picWidth>=picHeight)
			{
				finalWidth=idealWidth;
				finalHeight=Math.floor((picHeight/picWidth)*idealWidth);
				if (finalHeight>idealHeight)
				{
					finalHeight=idealHeight;
					finalWidth=Math.floor((picWidth/picHeight)*idealHeight);		
				}
			}
			if (picHeight>picWidth)
			{
				finalHeight=idealHeight;
				finalWidth=Math.floor((picWidth/picHeight)*idealHeight);
				if (finalWidth>idealWidth)
				{
					finalWidth=idealWidth;
					finalHeight=Math.floor((picHeight/picWidth)*idealWidth);
				}
			}
		}

		$("#lightboxImage").css("width", finalWidth+"px");
		$("#lightboxImage").css("height", finalHeight+"px");
		$("#lightbox-image").css("position", "fixed");
		$("#lightbox-image").css("top", Math.floor((windowHeight-finalHeight)/2));
		$("#lightbox-image").css("left", Math.floor((windowWidth-finalWidth)/2));
	});
	$('#lightboxImage').css("cursor", "pointer");
	$('#lightboxImage').click(function (){
		window.open(imgSrc);
	});
	$('#UI-lightbox').click(function (){
		$('#UI-lightbox').fadeOut();
		$("#UI-lightbox").html('');
		$("#UI-lightbox").unbind();
	});
}


function checkLogin (oj) {
	var rootURL= $('.'+oj).data('adminurl');
	var targetURL = rootURL+"?ajax=1";

	if (window.localStorage)
	{
		targetURL=targetURL+"&mobileToken="+localStorage.mobileToken;
	}


	$.get(targetURL, function (data) {
		if (data.error==1) { //Not log in
			lightboxLoader (rootURL+"/login/");
		}
		else {
			var cact=location.pathname;
			if (cact.indexOf("post/")!=-1 || cact.indexOf("read.php/")!=-1)
			{
				var aID=$('.'+oj).data('adminid');
				window.location=rootURL+"/articles/modify/?aID="+aID;
			}
			else
			{
				window.location=rootURL+"/dashboard/";
			}
		}
	}, "json");
}

function doLogin (oj, rootURL) {
	var s_token= $('#'+oj).val();
	var isRem = $('#'+oj+'-rem').is(':checked');

	if (s_token=='')
	{
		promptLoginError (oj);
		return false;
	}
	targetURL = rootURL+"/login/verify/?ajax=1";

	if (isRem)
	{
		try {
			localStorage.test=1;
		} catch (e) {
			alert (lng['RememberFail']);
			isRem=false;
		}
		targetURL=targetURL+'&isRem=1';
	}
	else
	{
		targetURL=targetURL+'&isRem=0';
	}
	$.get(targetURL, { s_token: s_token }, function (data) {
		if (data.error==1) { //Wrong token
			promptLoginError (oj);
			return false;
		}
		else {
			if (isRem && window.localStorage)
			{
				localStorage.clear();
				localStorage.mobileToken=data.returnMsg;
			}

			var cact=location.pathname;
			if (cact.indexOf("post/")!=-1 || cact.indexOf("read.php/")!=-1)
			{
				var aID=$('.adminSign').data('adminid');
				window.location=rootURL+"/articles/modify/?aID="+aID;
			}
			else
			{
				window.location=rootURL+"/dashboard/";
			}
		}
	}, "json");
}

function promptLoginError (oj) {
	$('#'+oj).val('');
	$('#'+oj).addClass('inputLineWarn');
	$('#'+oj+'-failure').fadeIn();
	$('#'+oj).click(function (){
		$('#'+oj).removeClass('inputLineWarn');
		$('#'+oj+'-failure').fadeOut();
	});
}


function lightboxLoader (loadURL) {
	$(".commentArea").hide();
	$("#UI-lightbox").fadeIn(500);
	$("#UI-lightbox").append( "<div id='lightbox-message'></div>" );
	var windowWidth=$(window).width();
	var windowHeight=$(window).height();

	var finalWidth=$("#lightbox-message").width();
	var finalHeight=$("#lightbox-message").height();

	$("#lightbox-message").css("position", "fixed");
	$("#lightbox-message").css("top", Math.floor((windowHeight-finalHeight)/2));
	$("#lightbox-message").css("left", Math.floor((windowWidth-finalWidth)/2));

	$("#lightbox-message").load(loadURL);
}

function lightboxLoaderDestroy () {
	$(".commentArea").show();
	$('#UI-lightbox').fadeOut();
	$("#UI-lightbox").html('');
	$("#UI-lightbox").unbind();
}

function changeNav ()
{
	var aPos=$(window).scrollTop();
	var bPos=$("#mainArea").position().top;
	if (aPos>bPos)
	{
		$("header").addClass('headerShrink');
	}
	else {
		$("header").removeClass('headerShrink');
	}
	t=setTimeout ("changeNav()", 600);
}

